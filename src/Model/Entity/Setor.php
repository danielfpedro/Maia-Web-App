<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Setore Entity
 *
 * @property int $id
 * @property string $nome
 * @property \Cake\I18n\Time $criado_em
 * @property int $grupo_id
 */
class Setor extends Entity
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
        '*' => false,
        'nome' => true,
        'ativo' => true,
        'lojas' => true,
    ];
}
