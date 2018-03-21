<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Loja Entity
 *
 * @property int $id
 * @property string $nome
 * @property \Cake\I18n\Time $criado_em
 * @property \Cake\I18n\Time $modificado_em
 * @property int $grupo_de_loja_id
 *
 * @property \App\Model\Entity\GruposDeLoja $grupos_de_loja
 * @property \App\Model\Entity\Usuario[] $usuarios
 */
class Loja extends Entity
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
        '*' => false,
        'nome' => true,
        'cnpj' => true,
        'cep' => true,
        'endereco' => true,
        'bairro' => true,
        'cidade_id' => true,
        'lat' => true,
        'lng' => true,
        'ativo' => true,
        'setores'=> true
    ];

    public function getDistanceFromLoja($latitudeTo, $longitudeTo, $earthRadius = 6371000) {

        if (isset($this->_properties['lat']) && $this->_properties['lng'] && $this->_properties['lat'] && $this->_properties['lng']) {
            $latitudeFrom = $this->_properties['lat'];
            $longitudeFrom = $this->_properties['lng'];
        } else {
            return null;
        }

        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
          pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return round($angle * $earthRadius);
    }

    public function getDistanceWithUnit($distance) {
        if ($distance > 1000) {
            return round($distance / 1000, 1) . 'km';
        }
        return $distance . 'm';
    }

}
