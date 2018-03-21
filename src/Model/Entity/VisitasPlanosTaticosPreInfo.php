<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * VisitasPlanosTaticosPreInfo Entity
 *
 * @property int $id
 * @property int $visita_id
 * @property int $who_id
 * @property int $solicitante_id
 * @property \Cake\I18n\FrozenTime $criado_em
 *
 * @property \App\Model\Entity\Visita $visita
 * @property \App\Model\Entity\How $how
 * @property \App\Model\Entity\Solicitante $solicitante
 */
class VisitasPlanosTaticosPreInfo extends Entity
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
        'id' => false,
        'grupo_id' => false,
        'visita_id' => false,
        'criado_em' => false
    ];

    protected function _getWhenEndPlaceholder() {
        if (array_key_exists('when_end', $this->_properties)) {
            return $this->_properties['when_end']->format('d/m/Y');
        }
        return null;
    }
}
