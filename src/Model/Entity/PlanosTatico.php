<?php
namespace App\Model\Entity;

use Cake\Collection\Collection;
use Cake\ORM\Entity;
use Cake\I18n\Time;

/**
 * PlanosTatico Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime $criado_em
 * @property string $what
 * @property string $why
 * @property string $who
 * @property string $how_much
 * @property string $how
 * @property \Cake\I18n\FrozenDate $when_start
 * @property \Cake\I18n\FrozenDate $when_end
 * @property string $where_
 * @property int $andamento
 * @property int $checklists_perguntas_resposta_id
 *
 * @property \App\Model\Entity\ChecklistsPerguntasResposta[] $checklists_perguntas_respostas
 */
class PlanosTatico extends Entity
{

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

    public function _getWhenStartPlaceholder()
    {
        return (array_key_exists('when_start', $this->_properties) && $this->_properties['when_start']) ? $this->_properties['when_start']->format('d/m/Y') : null;
    }
    public function _getWhenEndPlaceholder()
    {
        return (array_key_exists('when_end', $this->_properties) && $this->_properties['when_end']) ? $this->_properties['when_end']->format('d/m/Y') : null;
    }


    public function getComplementoTarefasClas()
    {
        $this->_checkContainTarefas();
        if ($this->getTotalTarefasCompletas() < $this->getTotalTarefas()) {
            return 'danger';
        } elseif($this->getTotalTarefas() == 0) {
            return 'default';
        }

        return 'success';
    }    

    public function isEncerrado()
    {
        return (in_array($this->getStatus(), [4, 5]));
    }

    public function getTotalTarefas()
    {
        $this->_checkContainTarefas();
        return count($this->_properties['tarefas']);
    }

    public function getTotalTarefasCompletas()
    {
        $this->_checkContainTarefas();
        $tarefasCompletas = 0;
        foreach ($this->_properties['tarefas'] as $tarefa) {
            if ($tarefa['dt_concluido']) {
                $tarefasCompletas++;
            }
        }
        return $tarefasCompletas;
    }

    private function _checkContainTarefas() {
        if (!array_key_exists('tarefas', $this->_properties)) {
            throw new \Exception("Planos Táticos não contém tarefas");
        }
    }

    public function getTitle($key)
    {
        $title = array_key_exists($key, $this->getTitles()) ? $this->getTitles()[$key] : null;
        if (!$title) {
            throw new \Exception(__("Não existe nenhum título com a key {0}", $key));
        }

        return $title;
    }

    public function getTitles(array $exclude = [])
    {
        $data = [
            'origem' => [
                'title' => 'Origem',
                'subtitle' => 'A resposta que originou este Plano de Ação',
                'icon' => 'link',
                'value' => ''
            ],
            'quem_criou' => [
                'title' => 'Quem Criou',
                'subtitle' => 'A usuário que criou este Plano de Ação',
                'icon' => 'circle',
                'value' => ''
            ],
            'solicitante' => [
                'title' => 'Responsável',
                'subtitle' => 'Acompanha e valida a execução do plano',
                'icon' => 'user',
                'value' => nl2br(h($this->_defaultValue('what', '-')))
            ],
            'what' => [
                'title' => 'O que?',
                'subtitle' => 'Objetivo | Meta',
                'icon' => 'list-alt',
                'value' => nl2br(h($this->_defaultValue('what', '-')))
            ],
            'why' => [
                'title' => 'Porque?',
                'subtitle' => 'Motivo | Benefício',
                'icon' => 'question-circle',
                'value' => nl2br(h($this->_defaultValue('why', '-')))
            ],
            'who' => [
                'title' => 'Executante',
                'subtitle' => 'Responsável pela elaboração de como fazer e execução deste Plano de Ação',
                'icon' => 'user-circle',
                'value' => ''
            ],
            // 'how_much' => [
            //     'title' => 'Quanto?',
            //     'subtitle' => 'Custo | Quantidade',
            //     'icon' => 'usd',
            //     'value' => nl2br(h($this->_defaultValue('how_much', '-')))
            // ],
            'how' => [
                'title' => 'Como?',
                'subtitle' => 'Atividades',
                'icon' => 'exclamation-circle',
                'value' => nl2br(h($this->_defaultValue('how', '-')))
            ],
            'when' => [
                'title' => 'Quando?',
                'subtitle' => 'Prazo para que seja resolvido',
                'icon' => 'clock',
                'value' => __('De {0} até {1}', $this->_defaultValue('when_start', '-'), $this->_defaultValue('when_end', '-'))
            ],
            'where' => [
                'title' => 'Aonde?',
                'subtitle' => 'Local | Departamento',
                'icon' => 'location-arrow',
                'value' => null
            ],
        ];


        $dataCollection = new Collection($data);

        if ($exclude) {
            return $dataCollection->reject(function($value, $key) use ($exclude) {
                return (in_array($key, $exclude));
            })->toArray();
        }

        return $dataCollection->toArray();
    }

    public function _defaultValue($value, $default)
    {
        return (array_key_exists($value, $this->_properties) && $this->_properties[$value]) ? $this->_properties[$value] : $default;
    }

    public function getStatusInfo()
    {
        if (!array_key_exists('tarefas', $this->_properties)) {
            throw new \Exception("Planos Táticos não contém tarefas");
        }

        if (!array_key_exists('when_end', $this->_properties)) {
            throw new \Exception("Planos Táticos não contém when_end");
        }
        if (!array_key_exists('dt_aprovado', $this->_properties)) {
            throw new \Exception("Planos Táticos não contém dt_aprovado");
        }
        if (!array_key_exists('dt_reprovado', $this->_properties)) {
            throw new \Exception("Planos Táticos não contém reprovado");
        }

        if (!$this->_properties['tarefas']) {
            return [
                'title' => 'Não iniciado',
                'class' => 'info',
                'icon' => 'pause',
                'extra' => null
            ];
        } elseif ($this->_properties['dt_aprovado']) {
            return  [
                'title' => 'Aprovado',
                'class' => 'success',
                'icon' => 'thumbs-o-up',
                'extra' => ($this->_properties['when_end'] && $this->_properties['dt_aprovado']->format('Y-m-d') > $this->_properties['when_end']->format('Y-m-d')) ? 'com atraso' : null
            ];
        } elseif ($this->_properties['dt_reprovado']) {
            return [
                'title' => 'Reprovado',
                'class' => 'danger',
                'icon' => 'thumbs-o-up',
                'extra' => ($this->_properties['when_end'] && $this->_properties['dt_reprovado']->format('Y-m-d') > $this->_properties['when_end']->format('Y-m-d')) ? 'com atraso' : null
            ];
        }

        return [
            'title' => 'Em andamento',
            'class' => 'warning',
            'icon' => 'play',
            'extra' => null
        ];
    }

    public function getStatus()
    {
        if (!array_key_exists('tarefas', $this->_properties)) {
            throw new \Exception("Tarefas deve estar contida no plano de ação");
            
        }
        if (!$this->_properties['tarefas']) {
            return 1;
        } elseif($this->isTodasTarefasConcluidas() && $this->emAndamento()) {
            return 2;
        } elseif($this->_properties['tarefas'] && $this->emAndamento()) {
            return 3;
        } elseif($this->aprovado()) {
            return 4;
        } elseif($this->reprovado()) {
            return 5;
        } else {
            throw new \Exception("GetStatus não encontrou nada");
        }
    }

    public function getStatusLabel()
    {
        switch ($this->getStatus()) {
            case 1:
                $color = 'warning';
                $label = 'Aguard. elaboração das ativ.';
                $icon = 'hourglass-start';
                break;
            case 3:
                $color = 'warning';
                $label = 'Em andamento';
                $icon = 'arrow-right';
                break;
            case 2:
                $color = 'default';
                $label = 'Finalizado, aguard. aprovação';
                $icon = 'hourglass-end';
                break;
            case 4:
                $color = 'success';
                $label = 'Aprovado';
                $icon = 'thumbs-up';
                break;
            case 5:
                $color = 'danger';
                $label = 'Reprovado';
                $icon = 'thumbs-down';
                break;
        }

        return __('<span class="label label-{0}"><span class="fa fa-{1}"></span> {2}</span>', $color, $icon, h($label));
    }

    public function isTodasTarefasConcluidas()
    {
        if (!array_key_exists('tarefas', $this->_properties)) {
            throw new \Exception("Tarefas deve estar contida no plano de ação");
        }

        $todasConcluidas = true;
        foreach ($this->_properties['tarefas'] as $tarefa) {
            if (!$tarefa->dt_concluido) {
                $todasConcluidas = false;
                break;
            }
        }

        return $todasConcluidas;
    }

    public function aprovado()
    {
        return ($this->validado() && $this->_properties['dt_aprovado']);
    }
    public function reprovado()
    {
        return ($this->validado() && $this->_properties['dt_reprovado']);
    }
    public function validado()
    {
        return ($this->_properties['dt_aprovado'] || $this->_properties['dt_reprovado']);
    }
    public function isVencido()
    {
        if ($this->_properties['when_end'] && $this->_properties['when_end'] < Time::now()) {
            return true;
        }

        return false;
    }
    public function emAndamento()
    {
        return (!$this->_properties['dt_aprovado'] && !$this->_properties['dt_reprovado']);
    }
    public function tempoParaVencer()
    {
        if (!$this->validado() && $this->_properties['when_end']) {
            if ($this->_properties['when_end'] < Time::now()) {
                return 'Vencido';
            }
            return 'Vence em ' . $this->_properties['when_end']->timeAgoInWords();
        }
        return null;
    }
}
