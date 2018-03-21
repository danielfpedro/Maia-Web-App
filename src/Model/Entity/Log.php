<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property string $table_name
 * @property int $ref
 * @property int $logs_tipo_id
 * @property int $modulo_id
 * @property \Cake\I18n\FrozenTime $criado_em
 * @property string $descricao
 *
 * @property \App\Model\Entity\LogsTipo $logs_tipo
 * @property \App\Model\Entity\Modulo $modulo
 */
class Log extends Entity
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
    ];
}
