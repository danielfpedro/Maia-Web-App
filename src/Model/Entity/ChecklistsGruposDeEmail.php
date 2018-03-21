<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChecklistsGruposDeEmail Entity
 *
 * @property int $checklist_id
 * @property int $grupos_de_email_id
 *
 * @property \App\Model\Entity\Checklist $checklist
 * @property \App\Model\Entity\GruposDeEmail $grupos_de_email
 */
class ChecklistsGruposDeEmail extends Entity
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
        'checklist_id' => false,
        'grupos_de_email_id' => false
    ];
}
