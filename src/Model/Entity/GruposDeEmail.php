<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GruposDeEmail Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $criado_em
 * @property string $modificado_em
 * @property string $emails_resultados
 * @property string $emails_criticos
 */
class GruposDeEmail extends Entity
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

protected $_virtual = ['emails_criticos_as_array', 'emails_resultados_as_array'];

    protected function _getEmailsCriticosAsArray($value)
    {
        return (isset($this->_properties['emails_criticos']) && $this->_properties['emails_criticos']) ? array_map('trim', explode(',', $this->_properties['emails_criticos'])) : [];
    }
    protected function _getEmailsResultadosAsArray($value)
    {
        return (isset($this->_properties['emails_resultados']) && $this->_properties['emails_resultados']) ? array_map('trim', explode(',', $this->_properties['emails_resultados'])) : [];
    }

    protected function _stringToArray($string)
    {

    }
}
