<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\Time;
/**
 * Usuario Entity
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property \Cake\I18n\Time $criado_em
 * @property \Cake\I18n\Time $modificado_em
 */
class Usuario extends Entity
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
        'cargo_id' => false,
        'deletado' => false,
        'culpado_novo_id' => false,
        'culpado_modificacao_id' => false,
        'redefinir_senha_timestamp' => false,
        'redefinir_senha_email_hash' => false,
        'redefinir_senha_token' => false,
        'criado_em' => false,
        'modificado_em' => false,
        /**
         * Com isso nunca poderá alterado o grupo de loja pelo usuário, só por
         * programação que é o a gente faz antes de salvar o usuario
         */
        'grupo_de_loja_id' => false
    ];

    protected $_virtual = ['short_name'];

    protected function _setSenha($senha)
    {
        if (strlen($senha) > 0) {
          return (new DefaultPasswordHasher)->hash($senha);
        }
    }

    protected function _getShortName()
    {
        if (!isset($this->_properties['nome'])) {
            return null;
        }
        $explodido = explode(' ', $this->_properties['nome']);
        $total = count($explodido);

        if ($total > 1) {
            return $explodido[0] . ' ' . $explodido[$total - 1];
        }
        return $explodido[0];
    }

    protected function _getRedefinirSenhaIsExpirado()
    {
        $tokenTimestamp = $this->_properties['redefinir_senha_timestamp'];
        $tokenTimestamp = $tokenTimestamp->modify('+1 days');

        return !($tokenTimestamp->format('Y-m-d H:i:s') >= Time::now()->format('Y-m-d H:i:s'));
    }

    public function getCargosIds()
    {
        $out = [];

        if (!array_key_exists('cargos', $this->_properties)) {
            throw new \Exception('Usuario não contain cargos');
        }

        foreach ($this->_properties['cargos'] as $cargo) {
            $out[] = $cargo->id;
        }
        return $out;
    }

    public function getBuceta()
    {
        return 'oi';
    }


}
