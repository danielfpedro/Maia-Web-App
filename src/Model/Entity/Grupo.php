<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Grupo Entity
 *
 *
 * @property \App\Model\Entity\Usuario[] $usuarios
 * @property \App\Model\Entity\Loja[] $lojas
 * @property \App\Model\Entity\Checklist[] $checklists
 */
class Grupo extends Entity
{

    public $filesDir = '/files/grupos/{{grupoId}}';
    public $logosDir = '/files/grupos/{{grupoId}}/logos';

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

    protected $_virtual = ['login_logo_path'];

    public function getFilesDir($separator = '/')
    {
        return str_replace('/', $separator, '/files/grupos/' . $this->_properties['id']);
    }
    public function getLogosDir($separator = '/')
    {
        return str_replace('/', $separator, $this->getFilesDir($separator) . $separator . 'logos');
    }

    protected function _filesFolderPath()
    {
        return '../files/grupos/' . $this->_properties['id'] . '/';
    }
    protected function _logosFolderPath()
    {
        return $this->_filesFolderPath() . "logos/";
    }

    // Logos Full Path
    
    public function _getAppNavbarLogoPath()
    {
        return $this->_logosFolderPath() . $this->_properties['app_navbar_logo'];
    }
    public function _getNavbarLogoPath()
    {
        return $this->_logosFolderPath() . $this->_properties['navbar_logo'];
    }
    public function _getLogoLoginPath()
    {
        return $this->_logosFolderPath() . $this->_properties['login_logo'];
    }
    public function _getLogoEmailPath()
    {
        return $this->_logosFolderPath() . $this->_properties['logo_email'];
    }

}
