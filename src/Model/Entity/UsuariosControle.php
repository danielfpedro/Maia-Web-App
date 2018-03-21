<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * UsuariosControle Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property \Cake\I18n\FrozenTime $criado_em
 * @property \Cake\I18n\FrozenTime $modificado_em
 */
class UsuariosControle extends Entity
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

    protected function _setSenha($senha)
    {
        if (strlen($senha) > 0) {
          return (new DefaultPasswordHasher)->hash($senha);
        }
    }
}
