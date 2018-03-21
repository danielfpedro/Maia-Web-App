<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ModelosAlternativasAlternativa Entity
 *
 * @property int $id
 * @property string $alternativa
 * @property int $valor
 * @property string $tem_foto
 * @property string $item_critico
 * @property int $modelos_alternativa_id
 */
class ModelosAlternativasAlternativa extends Entity
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

    // protected function _getAlternativaCompleta()
    // {
    //     return $this->_properties['alternativa'] . ' '
    // }

}
