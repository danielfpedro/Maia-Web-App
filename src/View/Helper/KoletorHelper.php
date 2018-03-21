<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Koletor helper
 */
class KoletorHelper extends Helper
{

    public $helpers = ['Html', 'Form'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function labelBoolean($value, $first = 'Ativo', $second = 'Inativo')
    {
        $classe = 'success';
        $texto = $first;
        if (!(int)$value) {
            $classe = 'danger';
            $texto = $second;
        }
        return __('<span class="label label-{0}">{1}</span>', $classe, $texto);
    }

    public function btnSalvar($text = 'Salvar')
    {
        return '<button type="submit" class="btn btn-default btn-salvar"><span class="fa fa-check"></span> '.$text.'</button>';
    }

    public function btnAdicionar($text)
    {
        return $this->Html->link('<span class="fa fa-plus"></span> ' . $text, ['action' => 'add'], ['class' => 'btn btn-danger', 'escape' => false]);
    }

    public function tabelaBtnEditar($id)
    {
        return $this->Html->link($this->icon('pencil'), ['action' => 'edit', (int)$id], ['class' => 'btn btn-light btn-xs btn-icon', 'title' => 'Editar', 'escape' => false]);
    }
    public function tabelaBtnDeletar($tipo, $nome, $id)
    {
        return $this->Form->postLink($this->icon('remove'), ['action' => 'delete', (int)$id], ['class' => 'btn btn-light btn-xs btn-icon', 'title' => 'Deletar', 'escape' => false, 'confirm' => __('Você realmente deseja deletar {0} "{1}"?', h($tipo), h($nome))]);
    }
    public function campoData($name, $label = 'label')
    {
        $out = $this->Form->input('de', ['template' => '']);
        $out .= '<span class="fa fa-times" style="margin-top: -15px;"></span>';
        return $out;
    }

    public function icon($iconName)
    {
        return __('<span class="fa fa-{0}"></span>', $iconName);
    }

    public function propertyDefaultValue($object, $propertyName, $defaultValue = '-')
    {
        return ($object && $object->$propertyName) ? $object->$propertyName : $defaultValue;
    }

    public function defaultValue($value, $default)
    {
        return ($value) ? $value : $default;
    }

    public function arrayInArray($a, $b)
    {
        $out = false;

        foreach ($a as $v) {
            if (in_array($v, $b)) {
                $out = true;
                break;
            }
        }

        return $out;
    }

}

