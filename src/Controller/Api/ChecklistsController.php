<?php
namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Network\Exception\BadRequestException;

class ChecklistsController extends AppController
{
    public function getSemAgendamentoELojasDoUsuario($value='')
    {
        $this->loadModel('Usuarios');
        
        $xiboda = $this->Usuarios->get($this->Auth->user('id'), [
            'contain' => [
                'GruposDeAcessos'
            ]
        ]);

        $cargosIds = array_map(function($value) {
            return $value['id'];
        }, $this->Auth->user('cargos'));

        $gruposDeAcessosIds = array_map(function($value) {
            return $value['id'];
        }, $this->Auth->user('grupos_de_acessos'));

        // $checklists = $this->Checklists->todosVivosEAtivosDoMeuGrupo('all', $this->Auth->user())
        $checklists = $this->Checklists->todosVivosEAtivosDoMeuGrupo('all', $this->Auth->user())
            ->find('dosMeusGruposDeAcessos', ['grupo_id' => $this->Auth->user('grupo_id'), 'cargos_ids' => $cargosIds, 'grupos_de_acessos_ids' => $gruposDeAcessosIds])
            ->select([
                'Checklists.id',
                'Checklists.nome',
            ])
            ->where([
                'Checklists.sem_agendamento_flag' => true,
                'Checklists.deletado' => false
            ])
            ->order(['Checklists.nome']);

        $usuario = $this->Usuarios->get($this->Auth->user('id'), [
            'fields' => [
                    'Usuarios.id'
            ],
            'contain' => [
                'Lojas' => function($query) {
                    return $query
                        ->select([
                            'Lojas.id',
                            'Lojas.nome'
                        ])
                        ->where(['Lojas.deletado' => false])
                        ->order(['Lojas.nome']);
                }
            ]
        ]);

        $lojas = $usuario->lojas;

        $this->set(compact('checklists', 'lojas'));
        $this->set('_serialize', ['checklists', 'lojas']);
    }

}
