<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GruposDeAcesso Entity
 *
 * @property int $id
 * @property string $nome
 * @property int $grupo_id
 * @property int $culpado_id
 * @property \Cake\I18n\FrozenTime $criado_em
 *
 * @property \App\Model\Entity\Grupo $grupo
 * @property \App\Model\Entity\Culpado $culpado
 */
class GruposDeAcesso extends Entity
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
        'criado_em' => false,
        'culpado_id' => false,
        'grupo_id' => false
    ];
}
