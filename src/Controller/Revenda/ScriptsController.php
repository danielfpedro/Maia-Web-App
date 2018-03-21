<?php

namespace App\Controller\Controle;

use App\Controller\Controle\AppController;

use Cake\I18n\Time;

class ScriptsController extends AppController
{

	public function migrarCargoParaCargos()
	{
		$this->loadModel('Usuarios');
		$usuarios = $this->Usuarios->find();

		dd($usuarios);
	}
	
	// Gera o cod para as visitas que ainda não tem cod
	public function gerarCodParaVisitasQueAindaNaoTem()
	{
		$this->loadModel('Visitas');
		$query = $this->Visitas->find()
			->where(['Visitas.cod' => '']);

		$visitas = $query->all();
		$totalVisitas = $query->count();

		if ($visitas) {
			foreach ($visitas as $visita) {
				$visita->set('cod', $this->Visitas->generateUniqueCodOnGrupo($visita->grupo_id));
				$this->Visitas->save($visita);
			}
		}

		$this->set(compact('totalVisitas', 'visitas'));
		$this->set('_serialize', ['totalVisitas', 'visitas']);
	}

	public function migrarEmailsDasLojasParaGruposDeEmails()
	{
		$this->loadModel('Lojas');
		$this->loadModel('GruposDeEmails');

		$lojas = $this->Lojas->find()
			->select([
				'Lojas.id',
				'Lojas.nome',
				'Lojas.grupo_id',
				'Lojas.emails_receber_resultado',
				'Lojas.emails_criticos'
			])
			->contain([
				'Grupos' => function($query) {
					return $query
						->select([
							'Grupos.id',
							'Grupos.nome'
						]);
				}
			])
			->where(['Lojas.deletado' => false]);

		$lojas = $lojas->groupBy('grupo_id')->toArray();

		foreach ($lojas as $lojasDoGrupo) {
			$gruposDeEmails = [];
			foreach ($lojasDoGrupo as $loja) {
				if ($loja->emails_receber_resultado || $loja->emails_criticos) {
					$gruposDeEmails[] = [
						'nome' => $loja->nome,
						'grupo_id' => $loja->grupo_id,
						'emails_resultados' => $loja->emails_receber_resultado,
						'emails_criticos' => $loja->emails_criticos,
						'lojas' => [
							'_ids' => [$loja->id]
						]
					];
				}
			}
			
			$entities = $this->GruposDeEmails->newEntities($gruposDeEmails, ['associated' => ['Lojas']]);

			$salvos = [];

			foreach ($entities as $entity) {

				// Vejo se já existe um grupo com o nome naquela grupo_id
				// caso sim não salvo

				$existe = $this->GruposDeEmails->find()
					->where([
						'nome' => $entity->nome,
						'grupo_id' => $entity->grupo_id,
						'deletado' => false
					])
					->first();

				if (!$existe) {
					if (!$this->GruposDeEmails->save($entity)) {
						dd($entity->errors());
					} else {
						$salvos[] = $entity;
					}
				}
			}
		}

		$this->set(compact('salvos'));
	}

	public function adicionarGruposNasVisitasCriadas()
	{

		$this->loadModel('Visitas');

		$visitas = $this->Visitas->find('all')
			->where([
				'Visitas.deletado' => false,
				[
					'OR' => [
						'Visitas.dt_encerramento IS' => null,
						'Visitas.dt_encerramento =' => '',
					]
				],
				[
					'OR' => [
						'Visitas.prazo IS' => null, 
						'Visitas.prazo' => '',
						'Visitas.prazo >=' => Time::now()->format('Y-m-d')
					]
				]
			]);

		// dd($visitas->toArray());

		foreach ($visitas as $visita) {
			
			$gruposDestaVisita = $this->Visitas->GruposDeEmails->todosDoMeuGrupo('all', ['grupo_id' => $visita->grupo_id])
				 ->matching('Lojas', function($query) use ($visita) {
				 	return $query
				 		->where(['Lojas.id' => $visita->loja_id]);
				 });

			if ($gruposDestaVisita) {
				$data['grupos_de_emails']['_ids'] = $gruposDestaVisita->extract('id')->toArray();
				$visita = $this->Visitas->patchEntity($visita, $data);

				if (!$this->Visitas->save($visita)) {
					dd($visita->errors());
				}
			}
			
		}

		$response = $visitas;

		$this->set(compact('response'));
	}


	public function cargosUmParaMuitosEstrutura()
	{
		$this->loadModel('Usuarios');
		// Pego todos os usuarios possiveis
		$usuarios = $this->Usuarios->find();

		foreach ($usuarios as $usuario) {
			$usuarioEntity = $this->Usuarios->get($usuario->id, ['contain' => 'Cargos']);
			
			$cargos = [
				'_ids' => [$usuario->cargo_id]
			];

			$usuarioEntity = $this->Usuarios->patchEntity($usuarioEntity, ['cargos' => $cargos]);

			if (!$this->Usuarios->save($usuarioEntity)) {
				debug('Deu ruim ao salvar');
				dd($usuarioEntity->errors);
			}
		}

		// dd($usuarios->extract('cargo_id')->toArray());

		$response = ['ok'];

		$this->set(compact('response'));
	}

    // Importar checklists
    public function importarChecklists()
    {
        $this->loadModel('Checklists');

        $from = $this->request->query('from');
        $to = $this->request->query('to');

        if (!$from) {
            throw new BadRequestException("Você deve informar o from na querystring da url");
        }
        if (!$to) {
            throw new BadRequestException("Você deve informar o to na querystring da url");
        }

        $checklists = $this->Checklists->find()
          ->where([
            'deletado' => false,
            'grupo_id' => $from
          ])
          ->contain([
            'Perguntas' => function($query) {
              return $query
                ->contain([
                  'Setores',
                  'Alternativas',
                  // Nâo imagens por enquanto
                  // 'Imagens'
                ]);
            },
          ]);

        foreach ($checklists as $checklist) {

            unset($checklist->id);
            unset($checklist->grupo_id);
            unset($checklist->criado_em);
            unset($checklist->modificado_em);
            unset($checklist->culpado_modificacao_id);
            unset($checklist->culpado_novo_id);

            foreach ($checklist->perguntas as $pergunta) {
                unset($pergunta->id);
                unset($pergunta->dt_modificado);
                unset($pergunta->checklist_id);

                // Crio o setor se existir
                $setorExisteNoTo = $this->Checklists->Perguntas->Setores->find()
                  ->where([
                    'nome' => $pergunta->setor->nome,
                    'grupo_id' => $to
                  ])
                  ->first();

                  if ($setorExisteNoTo) {
                    $pergunta->setor_id = $setorExisteNoTo->id;
                  } else {
                    $dataToPatch = $pergunta->setor;
                    $dataToPatch = $dataToPatch->toArray();

                    unset($dataToPatch['id']);
                    $dataToPatch['grupo_id'] = $to;

                    $novoSetor = $this->Checklists->Perguntas->Setores->newEntity($dataToPatch);
                    $this->Checklists->Perguntas->Setores->saveOrFail($novoSetor);
                    // dd($dataToPatch);
                    // dd($novoSetor);
                    $pergunta->setor_id = $novoSetor->id;
                  }

                // Imagens
                // Não fazer por enquanto
                // foreach ($pergunta->imagens as $imagem) {
                //     unset($imagem->id);
                //     // unset($imagem->checklists_pergunta_id);
                //     unset($imagem->criado_em);
                // }

                unset($pergunta->setor);

                foreach ($pergunta->alternativas as $alternativa) {
                    $alternativa->id = null;
                    unset($alternativa->id);
                    unset($alternativa->checklists_pergunta_id);
                }
            }
        }


        foreach ($checklists as $checklist) {
            $dataToPatch = $checklist->toArray();
            $dataToPatch['grupo_id'] = $to;
            // dd($dataToPatch);
            $newEntity = $this->Checklists->newEntity(null, ['contain' => ['Perguntas.Alternativas']]);
            $newEntity = $this->Checklists->patchEntity($newEntity, $dataToPatch, ['associated' => ['Perguntas.Alternativas']]);
            // dd($newEntity);
            $this->Checklists->saveOrFail($newEntity);
        }

        // Pego todas as checklists do grupo TO
        $checklists = $this->Checklists->find()
          ->where([
            'deletado' => false,
            'grupo_id' => $to
          ])
          ->contain(['Perguntas.Setores']);


        foreach ($checklists as $checklist) {
            $setoresOrdem = [];
            $ids = [];
            $ordem = 0;
            foreach ($checklist->perguntas as $pergunta) {
                if (!in_array($pergunta->setor_id, $ids)) {
                    $ids[] = $pergunta->setor_id;
                    $setoresOrdem[] = [
                        'setor_id' => $pergunta->setor_id,
                        'ordem' => $ordem
                    ];
                }

                $ordem++;
            }

            $check = $this->Checklists->get($checklist->id, ['contain' => ['OrdemSetores']]);
            $check = $this->Checklists->patchEntity($check, ['ordem_setores' => $setoresOrdem], ['associated' => ['OrdemSetores']]);
            //dd($check);

            $this->Checklists->saveOrFail($check);

        }

        // $currentChecklist = null;
        // $ordem = 0;
        // for ($i=0; $i < count($setoresOrdem); $i++) {
        //     if (!$currentChecklist || $currentChecklist != $setoresOrdem[$i]['checklist_id']) {
        //         $currentChecklist = $setoresOrdem[$i]['checklist_id'];
        //         $ordem = 0;
        //     }
        //     $setoresOrdem[$i]['ordem'] = $ordem;
        //     $ordem++;
        // }


        dd($setoresOrdem);

    }

}