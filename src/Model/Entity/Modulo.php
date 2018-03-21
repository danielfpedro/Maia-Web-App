<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Modulo Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $ativo
 * @property string $deletado
 * @property \Cake\I18n\FrozenTime $criado_em
 * @property \Cake\I18n\FrozenTime $modificado_em
 *
 * @property \App\Model\Entity\Log[] $logs
 */
class Modulo extends Entity
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
