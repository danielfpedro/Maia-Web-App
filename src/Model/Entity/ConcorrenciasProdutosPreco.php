<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ConcorrenciasProdutosPreco Entity
 *
 * @property int $id
 * @property int $concorrencias_produtos_id
 * @property float $preco
 * @property \Cake\I18n\Time $criado_em
 * @property string $promocao
 * @property int $concorrente_id
 *
 * @property \App\Model\Entity\ConcorrenciasProduto $concorrencias_produto
 * @property \App\Model\Entity\Concorrente $concorrente
 */
class ConcorrenciasProdutosPreco extends Entity
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
