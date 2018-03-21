<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChecklistsPerguntasAlternativa Entity
 *
 * @property int $id
 * @property string $alternativa
 * @property int $valor
 * @property string $tem_foto
 * @property string $item_critico
 * @property int $checklists_pergunta_id
 */
class ChecklistsPerguntasAlternativa extends Entity
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

    protected function _getTemFoto($value)
    {
        return (boolean)$value;
    }
    protected function _getItemCritico($value)
    {
        return (boolean)$value;
    }
}
