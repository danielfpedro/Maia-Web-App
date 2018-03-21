<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LojasSetor Entity
 *
 * @property int $id
 * @property int $setor_id
 * @property int $loja_id
 * @property \Cake\I18n\FrozenTime $criado_em
 *
 * @property \App\Model\Entity\Setor $setor
 * @property \App\Model\Entity\Loja $loja
 */
class LojasSetor extends Entity
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
