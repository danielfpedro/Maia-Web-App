<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Collection\Collection;

/**
 * Checklist Entity
 *
 * @property int $id
 * @property string $nome
 * @property int $grupo_Id
 * @property \Cake\I18n\Time $criado_em
 * @property string $ativo
 */
class Checklist extends Entity
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

    protected $_virtual = ['nome_status'];

    protected function _getNomeStatus()
    {
        if (!isset($this->_properties['nome']) || !isset($this->_properties['ativo'])) {
            return null;
        }
        return $this->_properties['nome'] . (($this->_properties['ativo']) ? '' : ' (Inativa)');
    }

    public function getPerguntasPorSetoresOrdenados()
    {
        $perguntasAgrupadasPorSetor = new Collection($this->_properties['perguntas']);
        $perguntasAgrupadasPorSetor = $perguntasAgrupadasPorSetor->groupBy('setor_id')->toArray();

        $setoresOrdenados = $this->_properties['ordem_setores'];
        $setoresOrdenados = new Collection($setoresOrdenados);
        $setoresOrdenados = $setoresOrdenados->extract('setor')->toArray();

        // debug($setoresOrdenados);
        if ($setoresOrdenados) {
            foreach ($setoresOrdenados as $setorOrdenado) {
                if (isset($perguntasAgrupadasPorSetor[$setorOrdenado->id])) {
                    $setorOrdenado->perguntas = $perguntasAgrupadasPorSetor[$setorOrdenado->id];
                }
            }
        }

        return $setoresOrdenados;
        //return $perguntasAgrupadasPorSetor->toArray();
    }

    // public function setPerguntasNosSetores()
    // {
    //     $perguntasCollection = new Collection($this->_properties['perguntas']);
    //     $perguntasOrdenaasPo
    // }

}
