<?php
namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Routing\Router;

/**
 * Sorter helper
 */
class SorterHelper extends Helper
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function sort($sort, $label)
    {
    	if (!$this->request->getParam('paging')) {
    		throw new \Exception("Você não pode usar Sort em uma página que não possui paginação");
    	}

    	$paging = array_values($this->request->getParam('paging'))[0];
    	// O caminho mais curto seria alterar or request->query mas isso não é bom
    	// pq ele pode ser usado em outra parte do sistema e será alterado
    	// 
    	if (!$this->request->query('direction')) {
    		$newDirection = $this->_opositeDirection($paging['directionDefault']);
    		$iconDirection = $this->_iconByDirection($paging['directionDefault']);
    	} else {
	    	$newDirection = $this->_opositeDirection($this->request->query('direction'));
	    	$iconDirection = $this->_iconByDirection($this->request->query('direction'));
    	}
    	

    	$icon = __('<span class="fa fa-chevron-{0}"></span>&nbsp;', $iconDirection);

    	$queryString = array_merge($this->request->query(), [
    		'sort' => $sort,
    		'direction' => $newDirection,
    	]);

    	if ((!$this->request->query('sort') && $paging['sortDefault'] != $sort) || $this->request->query('sort') && $this->request->query('sort') != $sort) {
    		$icon = '';
    	}
    	return __('<a href="{1}">{0}{2}</a>', $icon, Router::url(['?' => $queryString]), $label);
    }

    private function _opositeDirection($direction) {
    	if ($direction == 'asc') {
    		return 'desc';
    	}
    	return 'asc';
    }

    private function _iconByDirection($direction)
    {
    	if ($direction == 'asc') {
    		return 'up';
    	}
    	return 'down';
    }

    // private function _sortRaw($sort)
    // {
    // 	$arrayExploded = explode('.', $sort);
    // 	array_shift($arrayExploded);
    // 	return implode('.', $arrayExploded);
    // }
}
