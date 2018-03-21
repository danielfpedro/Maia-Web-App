<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * LojasUsuario Entity
 *
 * @property int $loja_id
 * @property int $usuario_id
 *
 * @property \App\Model\Entity\Loja $loja
 * @property \App\Model\Entity\Usuario $usuario
 */
class LojasUsuario extends Entity
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
        'loja_id' => false,
        'usuario_id' => false
    ];
}
