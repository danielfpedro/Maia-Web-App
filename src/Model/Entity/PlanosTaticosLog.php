<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlanosTaticosLog Entity
 *
 * @property int $id
 * @property int $planos_tatico_id
 * @property int $planos_taticos_logs_tipo_id
 * @property \Cake\I18n\FrozenTime $dt_criacao
 *
 * @property \App\Model\Entity\PlanosTatico $planos_tatico
 * @property \App\Model\Entity\PlanosTaticosLogsTipo $planos_taticos_logs_tipo
 */
class PlanosTaticosLog extends Entity
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
}
