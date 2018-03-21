<?php

namespace App\Controller\Painel;

Use App\Controller\Painel\AppController;
use Cake\Event\Event;

class GruposDeEmailsController extends AppController
{
	
    public function beforeFilter(Event $event)
    {
        /**
         * No edit e no delete eu pego o id e vejo se o usuario pode acessar
         * caso não meto um not found, não precisa ser não autorizado, pois se for
         * a pessoa vai saber que o registro existe mas ela não pode acessar,
         * o not found ela nem sabe se existe.
         */
        if (in_array($this->request->action, ['edit', 'delete'])) {

            if (!$this->GruposDeEmails->exists([
                'id' => (int)$this->request->gruposDeEmailId,
                'grupo_id' => (int)$this->Auth->user('grupo_id'),
                'deletado' => false
            ])) {
                throw new NotFoundException();
            }

        }

        parent::beforeFilter($event);
    }

	public function index()
	{
        // Breadcrumb
        // Limpa todo o breadcrumb para dar uma aliviada na sessão
        $this->request->session()->write('Breadcrumb', null);
        $this->breadcrumbSet('GruposDeEmails.index', ['controller' => 'GruposDeEmails', 'action' => 'index']);

		$find = $this->GruposDeEmails->todosVivosDoMeuGrupo('all', $this->Auth->user())
			->contain(['Lojas', 'Checklists']);

		if ($this->request->query('q')) {
			$q = '%' .str_replace(' ', '%', $this->request->query('q')). '%';
			
			$find->where([
				'OR' => [
					'GruposDeEmails.nome LIKE' => $q,
					'GruposDeEmails.emails_resultados LIKE' => $q,
					'GruposDeEmails.emails_criticos LIKE' => $q,
				]
			]);
		}

		// Filtro Lojas lojas
		if ($this->request->query('lojas')) {
			$find->matching('Lojas', function($query) {
					return $query
						->where(['Lojas.id IN' => $this->request->query('lojas')]);
				});
		}

		// Filtro Questionarios
		if ($this->request->query('questionarios')) {
			$find->matching('checklists', function($query) {
					return $query
						->where(['checklists.id IN' => $this->request->query('questionarios')]);
				});
		}

		$questionarios = $this->GruposDeEmails->Checklists->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user());
		$lojas = $this->GruposDeEmails->Lojas->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user());

		$gruposDeEmails = $this->paginate($find);

		$this->set(compact('gruposDeEmails', 'questionarios','lojas'));
	}

	public function add()
	{
		$breadcrumb['index'] = $this->breadcrumbRedirect('GruposDeEmails.index', ['action' => 'index']);

		$gruposDeEmail = $this->GruposDeEmails->newEntity();

		if ($this->request->is('post')) {
			$gruposDeEmail = $this->GruposDeEmails->patchEntity($gruposDeEmail, $this->request->getData());
			$gruposDeEmail->set('grupo_id', $this->Auth->user('grupo_id'));
            
            if ($this->GruposDeEmails->save($gruposDeEmail)) {

                $this->Flash->set('O Grupo de emails foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('GruposDeEmails.index', ['action' => 'index']));
            }
            $this->Flash->set('O Grupo de emails não foi salvo.', ['element' => 'Painel/error']);
		}

		$checklists = $this->GruposDeEmails->Checklists->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())
			->order(['Checklists.nome']);
		$lojas = $this->GruposDeEmails->Lojas->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())
			->order(['Lojas.nome']);

		$this->set(compact('gruposDeEmail', 'checklists', 'lojas', 'breadcrumb'));
		$this->viewBuilder()->template('form');
	}

	public function edit()
	{
		$breadcrumb['index'] = $this->breadcrumbRedirect('GruposDeEmails.index', ['action' => 'index']);

		$gruposDeEmail = $this->GruposDeEmails->get($this->request->gruposDeEmailId, ['contain' => ['Lojas', 'Checklists']]);

		if ($this->request->is(['post', 'patch', 'put'])) {

			$gruposDeEmail = $this->GruposDeEmails->patchEntity($gruposDeEmail, $this->request->getData());
			$gruposDeEmail->set('grupo_id', $this->Auth->user('grupo_id'));
            
            if ($this->GruposDeEmails->save($gruposDeEmail)) {

                $this->Flash->set('O Grupo de emails foi salvo.', ['element' => 'Painel/success']);

                return $this->redirect($this->breadcrumbRedirect('GruposDeEmails.index', ['action' => 'index']));
            }
            $this->Flash->set('O Grupo de emails não foi salvo.', ['element' => 'Painel/error']);
		}

		$checklists = $this->GruposDeEmails->Checklists->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())
			->order(['Checklists.nome']);
		$lojas = $this->GruposDeEmails->Lojas->todosVivosEAtivosDoMeuGrupo('list', $this->Auth->user())
			->order(['Lojas.nome']);

		$this->set(compact('gruposDeEmail', 'checklists', 'lojas', 'breadcrumb'));
		$this->viewBuilder()->template('form');
	}

	public function paraVisitas()
	{
		// Pego todos os grupos vivos e depois filtro a query
		$grupos = $this->GruposDeEmails->todosVivosDoMeuGrupo('all', $this->Auth->user())
			->select([
				'GruposDeEmails.id',
				'GruposDeEmails.nome',
				'GruposDeEmails.emails_criticos',
				'GruposDeEmails.emails_resultados',
			])
			->contain(['Lojas']);
		$grupos = $this->GruposDeEmails->filtrarPorChecklist($grupos, $this->request->query('checklist_id'));

		$this->set(compact('grupos'));
		$this->set('_serialize', 'grupos');
	}

	public function autocomplete()
	{
		$q = '%' . str_replace(' ', '%', $this->request->query('term')) . '%';
        $todosOsGruposDeEmailsRaw = $this->GruposDeEmails->todosDoMeuGrupo('all', $this->Auth->user())
            ->select(['nome'])
            ->where(['nome LIKE' => $q]);

        $todosOsGruposDeEmails = [];

        foreach ($todosOsGruposDeEmailsRaw as $grupo) {
            $todosOsGruposDeEmails[] = $grupo->nome;
        }

		$this->set(compact('todosOsGruposDeEmails'));
		$this->set('_serialize', 'todosOsGruposDeEmails');
	}

	public function delete()
	{
	    $this->request->allowMethod(['post', 'delete']);

	    $grupo = $this->GruposDeEmails->get($this->request->gruposDeEmailId);

	    if ($this->GruposDeEmails->delete($grupo)) {

            $this->Flash->set('O Grupo de emails foi deletado.', ['element' => 'Painel/success']);

            return $this->redirect($this->breadcrumbRedirect('GruposDeEmails.index', ['action' => 'index']));

	    }
	}

}