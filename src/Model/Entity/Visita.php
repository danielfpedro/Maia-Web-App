<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

use Cake\Utility\Text;

use Cake\Collection\Collection;
use Cake\I18n\Time;

/**
 * Visita Entity
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $checklist_id
 * @property int $grupo_id
 * @property int $loja_id
 * @property \Cake\I18n\Time $prazo
 * @property \Cake\I18n\Time $criado_em
 *
 * @property \App\Model\Entity\Usuario $usuario
 * @property \App\Model\Entity\Checklist $checklist
 * @property \App\Model\Entity\Grupo $grupo
 */
class Visita extends Entity
{

    protected $_requerimentoLocalizacaoOptions = [
        1 => 'Nenhum',
        2 => 'Somente Localização (Imprecisão de até 200 metros)',
        3 => 'Localização e Internet (Preciso)'
    ];

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];

    protected function _getEmailsResultadosExtrasAsArray()
    {
        if (isset($this->_properties['emails_resultados_extras'])) {
            return array_map('trim', array_filter(explode(',', $this->_properties['emails_resultados_extras'])));
        }

        return [];
    }

    protected function _getEmailsCriticosExtrasAsArray()
    {
        if (isset($this->_properties['emails_criticos_extras'])) {
            return array_map('trim', array_filter(explode(',', $this->_properties['emails_criticos_extras'])));
        }

        return [];
    }

    public function getUrlPublicaDoResultado()
    {
        if (!$this->_properties['dt_encerramento']) {
            throw new \Exception('É necessário que a visita contenha data de encerramento para gerar o link');
        }

        return [
            'controller' => 'ChecklistsPerguntasRespostas',
            'action' => 'viewPublic',
            'prefix' => 'painel',
            'visitaId' => $this->_properties['id'],
            'token' => $this->_properties['token_visualizar_publico'],
            'filialSlug' => strtolower(Text::slug($this->_properties['loja']['nome'])),
            'dtEncerramentoY' => $this->_properties['dt_encerramento']->format('Y'),
            'dtEncerramentoM' => $this->_properties['dt_encerramento']->format('m'),
            'dtEncerramentoD' => $this->_properties['dt_encerramento']->format('d')
        ];
    }

    public function getRequerimentoLocalizacaoOptions()
    {
        return $this->_requerimentoLocalizacaoOptions;
    }

    public function getRequerimentoLocalizacao()
    {
        return (isset($this->_requerimentoLocalizacaoOptions[$this->_properties['requerimento_localizacao']])) ? $this->_requerimentoLocalizacaoOptions[$this->_properties['requerimento_localizacao']] : 'desconhecido';
    }

    protected function _getVencida()
    {
        if (isset($this->_properties['prazo']) && !$this->_properties['prazo']) {
            return !($this->_properties['prazo']->format('Y-m-d') >= Time::now()->format('Y-m-d'));
        }
        return null;
    }

    public function isRespostaPrecisa()
    {
        return ($this->_properties['requerimento_localizacao'] == 3);
    }

    public function _getPrazoPlaceholder()
    {
        return (isset($this->_properties['prazo'])) ? $this->_properties['prazo']->format('d/m/Y') : null;
    }

    public function ordenaSetores()
    {
        $this->_properties['checklist']['setores'] = [];
        foreach ($this->_properties['checklist']['ordem_setores'] as $key => $ordem) {
            $this->_properties['checklist']['setores'][] = $this->_properties['checklist']['ordem_setores'][$key]->setor;
        }
    }

    public function getSetoresOrdenados()
    {
        $setores = [];
        foreach ($this->_properties['checklist']['ordem_setores'] as $setor) {
            $setores[] = $setor->setor;
        }

        return $setores;
    }

    public function getUltimaResposta()
    {
        $ultima = null;

        foreach ($this->_properties['respostas'] as $resposta) {

            if ($resposta->dt_resposta && (!$ultima || ($resposta->dt_resposta->format('Y-m-d H:i:s') > $ultima->dt_resposta->format('Y-m-d H:i:s'))))   {
                $ultima = $resposta;
            }
        }

        return $ultima;
    }

    public function getPrimeiraResposta()
    {
        $primeira = null;

        foreach ($this->_properties['respostas'] as $resposta) {
            if ($resposta->dt_resposta && (!$primeira || ($resposta->dt_resposta->format('Y-m-d H:i:s') < $primeira->dt_resposta->format('Y-m-d H:i:s')))) {
                $primeira = $resposta;
            }
        }

        return $primeira;
    }

    public function setRespostasFlags()
    {
        if ($this->_properties['respostas']) {
            $respostasByPergunta = (new Collection($this->_properties['respostas']))->groupBy('checklists_pergunta_id')->toArray();

            $perguntas = $this->_properties['checklist']['perguntas'];
            foreach ($this->_properties['checklist']['perguntas'] as $key => $pergunta) {
                if (isset($respostasByPergunta[$pergunta->id])) {

                    $this->_properties['checklist']['perguntas'][$key]->resposta = $respostasByPergunta[$pergunta->id][0];

                    if ($pergunta['tipo'] == 1) {
                        foreach ($pergunta->alternativas as $alternativa) {
                            if ($pergunta->resposta && $alternativa->id == $pergunta->resposta->checklists_perguntas_alternativa_id) {
                                $alternativa->selecionada = true;
                            } else {
                                $alternativa->selecionada = false;
                            }
                        }
                    }
                } else {
                    $this->_properties['checklist']['perguntas'][$key]->resposta = [];
                }
            }
        }
    }

    public function setPerguntasNosSetores()
    {
        $perguntasGroupBySetor = (new Collection($this->_properties['checklist']['perguntas']))->groupBy('setor_id')->toArray();
        // dd($perguntasGroupBySetor[5]);
        // $setores = new Collection($this->_properties['checklist']['setores']);

        foreach ($this->_properties['checklist']['setores'] as $setor) {

            // Uma loja pode não ter tal setor entao eu digo que o setor não foi respondido
            // ele começa com false e se o setor tiver uma resposta ao menos já recebe true
            $setor->respondido = false;

            $setor->atingido = 0;
            $setor->maximo_possivel = 0;

            $setor->tem_fotos_requeridas = false;
            $setor->tem_observacoes = false;
            $setor->tem_resposta_critica = false;

            $setor->primeiraResposta = null;
            $setor->ultimaResposta = null;

            if (isset($perguntasGroupBySetor[$setor->id])) {
                $setor->perguntas = $perguntasGroupBySetor[$setor->id];
            } else {
                $setor->perguntas = [];
            }
            // Perguntas deste Setor
            foreach ($setor->perguntas as $pergunta) {
                $max = 0;
                if ($pergunta->resposta) {
                    if ($pergunta->resposta->fotos_requeridas) {
                        $setor->tem_fotos_requeridas = true;
                    }
                    if ($pergunta->resposta->observacao) {
                        $setor->tem_observacoes = true;
                    }

                    if ($pergunta->resposta->dt_resposta) {
                        if (!$setor->primeiraResposta || ($pergunta->resposta->dt_resposta->format('Y-m-d H:i:s') < $setor->primeiraResposta->dt_resposta->format('Y-m-d H:i:s'))) {
                            $setor->primeiraResposta = $pergunta->resposta;
                        }
                        if (!$setor->ultimaResposta || ($pergunta->resposta->dt_resposta->format('Y-m-d H:i:s') > $setor->ultimaResposta->dt_resposta->format('Y-m-d H:i:s'))) {
                            $setor->ultimaResposta = $pergunta->resposta;
                        }
                    }

                }

                if ($pergunta->resposta) {
                    $setor->respondido = true;
                }

                if ($pergunta->tipo == 1) {
                    foreach ($pergunta->alternativas as $alternativa) {
                        if ($alternativa->valor > $max) {
                            $max = $alternativa->valor;
                        }
                        if ($alternativa->selecionada) {
                            $setor->atingido += $alternativa->valor;
                            if ($alternativa->item_critico) {
                                $setor->tem_resposta_critica = true;
                            }
                        }
                    }

                    // Só soma se a resposta da pergunta tenha sido de uma alternativa com valor
                    if ($pergunta->resposta && !is_null($pergunta->resposta->alternativa_selecionada->valor)) {
                        $setor->maximo_possivel += $max;
                    }
                }
            }

            $setor->atingido_porcentagem = 0;
            $setor->diferenca = 0;
            if ($setor->maximo_possivel > 0) {
                $setor->atingido_porcentagem = (100 * $setor->atingido) / $setor->maximo_possivel;

                $setor->diferenca = $setor->atingido_porcentagem - $this->_properties['checklist']['minimo_esperado'];
            }


            if ($setor->primeiraResposta && $setor->ultimaResposta) {
                if ($setor->primeiraResposta->dt_resposta) {
                    $setor->duracao = $setor->primeiraResposta->dt_resposta->diff($setor->ultimaResposta->dt_resposta);
                } else {
                    $setor->duracao = 0;
                }
            }

            // dd($perguntasGroupBySetor);
            //return $setor;
        }

        // $this->_properties['checklist']['setores_com_perguntas'] = $setoresComPerguntas->toArray();
    }

    public function getDuracao()
    {

        $primeiraResposta = $this->getPrimeiraResposta();
        $ultimaResposta = $this->getUltimaResposta();

        if (!$primeiraResposta || !$ultimaResposta) {
            return '0';
        }

        $inicio = $primeiraResposta->dt_resposta;
        $fim = $ultimaResposta->dt_resposta;
        $diferenca = $inicio->diff($fim);

        return $diferenca;
    }

    public function getDuracaoString($duracao)
    {
        $out = [];

        // $date1 = new \Datetime('2015-10-10 01:00:00');
        // $date2 = new \Datetime('2017-12-10 01:00:01');
        // $duracao = $date1->diff($date2);

        if (is_null($duracao)) {
            return ['-'];
        }


        if ($duracao->y > 0) {
            $out[] = $duracao->y . ' ' . (($duracao->y > 1) ? 'anos' : 'ano');
        }
        if ($duracao->m > 0) {
            $out[] = $duracao->m . ' ' . (($duracao->m > 1) ? 'mês' : 'meses');
        }
        if ($duracao->d > 0) {
            $out[] = $duracao->d . ' ' . (($duracao->d > 1) ? 'dias' : 'dia');
        }
        if ($duracao->h > 0) {
            $out[] = $duracao->h . ' ' . (($duracao->h > 1) ? 'horas' : 'hora');
        }
        if ($duracao->i > 0) {
            $out[] = $duracao->i . ' ' . (($duracao->i > 1) ? 'minutos' : 'minuto');
        }
        if ($duracao->s > 0) {
            $out[] = $duracao->s . ' ' . (($duracao->s > 1) ? 'segundos' : 'segundo');
        }

        return ($out) ? $out : ['1 segundo'];
    }

    public function setAtingimentos()
    {
        $out = [
            'maximo_possivel' => 0,
            'atingido' => 0
        ];

        foreach ($this->_properties['checklist']['perguntas'] as $pergunta) {
            if ($pergunta->resposta && $pergunta->tipo == 1) {
                $max = 0;
                if (isset($pergunta['alternativas'])) {
                    foreach ($pergunta['alternativas'] as $alternativa) {
                        $max = ($alternativa->valor > $max) ? $alternativa->valor : $max;
                    }
                    // Só somo se a resposta da pergunta foi de uma alternativa com valor
                    // se ele respondeu "nao se aplica(valor nulo)" não somo.
                    // Devo testar is null pois 0 conta com pontuação
                    // 
                    // OBS.: Só soma se a resposta tenha sido algo com valor caso contrario era um nao se aplica
                    // dd($pergunta->resposta->alternativa_selecionada);
                    if (!is_null($pergunta->resposta->alternativa_selecionada->valor)) {
                        $out['maximo_possivel'] += $max;
                    }
                }
            }
        }

        foreach ($this->_properties['respostas'] as $resposta) {
            if ($resposta->alternativa_selecionada) {
                $out['atingido'] += $resposta->alternativa_selecionada->valor;
            }
        }

        $out['atingido_porcentagem'] = ($out['maximo_possivel'] > 0) ? round((100 * $out['atingido']) / $out['maximo_possivel']) : 0;
        $out['diferenca'] = $out['atingido_porcentagem'] - $this->_properties['checklist']['minimo_esperado'];

        $this->_properties['atingimento'] = $out;
    }

    // Se setar a flag $pegarPerguntasComRespostas = true Tem que chamar setRespsotas nas perguntas antes (óbvio);
    // public function setPerguntasNosSetores($pegarPerguntasComRespostas = false)
    // {
    //     //dd($this->_properties['checklist']);
    //     $perguntasKey = ($pegarPerguntasComRespostas) ? 'perguntas_com_respostas' : 'perguntas';
    //     $perguntas = $this->_properties['checklist'][$perguntasKey];
    //
    //     $perguntasGroupBySetor = (new Collection($perguntas))->groupBy('setor_id')->toArray();
    //     // dd($perguntasGroupBySetor[5]);
    //     $setores = new Collection($this->_properties['checklist']['setores']);
    //
    //     $setoresComPerguntas = $setores->map(function($setor, $key) use ($perguntasGroupBySetor) {
    //         $setor->atingido = 0;
    //         $setor->maximo_possivel = 0;
    //
    //         if (isset($perguntasGroupBySetor[$setor->id])) {
    //             $setor->perguntas = $perguntasGroupBySetor[$setor->id];
    //         } else {
    //             $setor->perguntas = [];
    //         }
    //         foreach ($setor->perguntas as $pergunta) {
    //             $max = 0;
    //             if ($pergunta['tipo'] == 1) {
    //                 foreach ($pergunta['alternativas'] as $alternativa) {
    //                     if ($alternativa->valor > $max) {
    //                         $max = $alternativa->valor;
    //                     }
    //                     if ($alternativa->selecionada) {
    //                         $setor->atingido += $alternativa->valor;
    //                     }
    //                 }
    //                 $setor->maximo_possivel += $max;
    //             }
    //         }
    //
    //         $setor->atingido_porcentagem = 0;
    //         if ($setor->maximo_possivel > 0) {
    //             $setor->atingido_porcentagem = (100 * $setor->atingido) / $setor->maximo_possivel;
    //         }
    //
    //         // dd($perguntasGroupBySetor);
    //         return $setor;
    //     });
    //
    //     $this->_properties['checklist']['setores_com_perguntas'] = $setoresComPerguntas->toArray();
    // }

}
