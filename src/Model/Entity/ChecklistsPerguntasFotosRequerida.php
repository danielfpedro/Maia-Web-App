<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChecklistsPerguntasFotosRequerida Entity
 *
 * @property int $id
 * @property string $filename
 * @property int $checklists_pergunta_id
 * @property \Cake\I18n\Time $criado_em
 *
 * @property \App\Model\Entity\ChecklistsPergunta $checklists_pergunta
 */
class ChecklistsPerguntasFotosRequerida extends Entity
{

    private $_path = 'files/checklists/fotos_requeridas/';

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

    protected function _getPath() {
        return $this->_path;
    }

    protected function _getFullImageQuadradaPath()
    {
        if (isset($this->_properties['filename'])) {
            return $this->_properties['folder'] . 'quadrada_' . $this->_properties['filename'];
        }
        return null;
    }
    protected function _getFullImagePath()
    {
        if (isset($this->_properties['filename'])) {
            return $this->_properties['folder'] . $this->_properties['filename'];
        }
        return null;
    }
}
