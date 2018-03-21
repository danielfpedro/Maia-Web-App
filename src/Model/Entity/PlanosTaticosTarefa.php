<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PlanosTaticosTarefa Entity
 *
 * @property int $id
 * @property string $descricao
 * @property \Cake\I18n\FrozenDate $prazo
 * @property int $andamento
 * @property \Cake\I18n\FrozenTime $criado_em
 * @property int $planos_tatico_id
 *
 * @property \App\Model\Entity\PlanosTatico $planos_tatico
 */
class PlanosTaticosTarefa extends Entity
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

    public function _getPrazoPlaceholder()
    {
        return (isset($this->_properties['prazo'])) ? $this->_properties['prazo']->format('d/m/Y') : null;
    }
}
