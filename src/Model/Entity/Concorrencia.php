<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Concorrencia Entity
 *
 * @property int $id
 * @property string $descricao
 * @property \Cake\I18n\Time $criado_em
 * @property \Cake\I18n\Time $modificado_em
 * @property int $encerrado
 */
class Concorrencia extends Entity
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
        'maiores_precos' => true
    ];

    protected function _getIdentificacao()
    {
        if ($this->_properties['id']) {
            return str_pad((int)$this->_properties['id'], 8, '0', STR_PAD_LEFT);
        }
    }
}
