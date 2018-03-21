<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChecklistsPerguntasResposta Entity
 *
 * @property int $id
 * @property int $checklists_pergunta_id
 * @property int $checklists_perguntas_alternativa_id
 * @property string $resposta_em_texto
 * @property int $usuario_id
 * @property string $observacao
 * @property \Cake\I18n\Time $criado_em
 *
 * @property \App\Model\Entity\ChecklistsPergunta $checklists_pergunta
 * @property \App\Model\Entity\ChecklistsPerguntasAlternativa $checklists_perguntas_alternativa
 * @property \App\Model\Entity\Usuario $usuario
 */
class ChecklistsPerguntasResposta extends Entity
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

    protected function _getTemLocalizacao()
    {
        if (isset($this->_properties['lat']) && isset($this->_properties['lng'])) {
            return ($this->_properties['lat'] && $this->_properties['lng']);
        }
        return false;
    }

}
