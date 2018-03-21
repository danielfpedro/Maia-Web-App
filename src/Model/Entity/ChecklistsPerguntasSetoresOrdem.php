<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChecklistsPerguntasSetoresOrdem Entity
 *
 * @property int $id
 * @property int $checklist_id
 * @property int $setor_id
 * @property int $ordem
 *
 * @property \App\Model\Entity\Checklist $checklist
 * @property \App\Model\Entity\Setor $setor
 */
class ChecklistsPerguntasSetoresOrdem extends Entity
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
}
