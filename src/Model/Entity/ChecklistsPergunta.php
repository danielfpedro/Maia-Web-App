<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChecklistsPergunta Entity
 *
 * @property int $id
 * @property string $pergunta
 * @property int $checklist_id
 *
 * @property \App\Model\Entity\Checklist $checklist
 */
class ChecklistsPergunta extends Entity
{

    protected $_folder;

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

    protected function _getFolderPath(int $grupoId)
    {
        return WWW_ROOT . 'files' . DS . 'grupos' . DS . $grupoId . DS .'imagens_referencia' . DS;
    }

    function getResposta($respostas)
    {
        foreach ($respostas as $resposta) {
            if ($resposta->checklists_pergunta_id == $this->_properties['id']) {
                return $resposta;
            }
        }
        return null;
    }

}
