<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\I18n\Time;

/**
 * Logs Controller
 *
 * @property \App\Model\Table\LogsTable $Logs
 *
 * @method \App\Model\Entity\Log[] paginate($object = null, array $settings = [])
 */
class LogsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->loadModel('Usuarios');

        $finder = $this->Logs->todosDoMeuGrupo('all', $this->Auth->user())
            ->contain(['Modulos', 'LogsTipos', 'Autores']);

        /**
         * Filtro Autor
         */
         if ($this->request->query('autor')) {
             $finder->where([
                 'Logs.autor_id ' => (int)$this->request->query('autor')
             ]);
         }
         /**
          * Filtro Módulo
          */
          if ($this->request->query('modulo')) {
              $finder->where([
                  'Logs.modulo_id ' => (int)$this->request->query('modulo')
              ]);
          }
          /**
           * Filtro Tipo
           */
           if ($this->request->query('tipo')) {
               $finder->where([
                   'Logs.logs_tipo_id ' => (int)$this->request->query('tipo')
               ]);
           }
        /**
         * Filtro prazo de
         */
       if ($this->request->query('intervalo_de')) {
           $intervaloDe = Time::createFromFormat('d/m/Y', $this->request->query('intervalo_de'));

           $finder->where([
               'DATE(Logs.criado_em) >= ' => $intervaloDe->format('Y-m-d')
           ]);
       }
       /**
        * Filtro prazo até
        */
      if ($this->request->query('intervalo_ate')) {
          $intervaloAte = Time::createFromFormat('d/m/Y', $this->request->query('intervalo_ate'));

          $finder->where([
              'DATE(Logs.criado_em) <= ' => $intervaloAte->format('Y-m-d')
          ]);
      }

        $modulos = $this->Logs->Modulos->todosAtivos('list');
        $logsTipos = $this->Logs->LogsTipos->todosAtivos('list');
        $autoresCompleto = $this->Usuarios->todosDoMeuGrupo('all', $this->Auth->user());

        $autores = [];
        foreach ($autoresCompleto as $autor) {
            $name = $autor->short_name . (($autor->deletado) ? ' (Deletado)' : '');
            $autores[$autor->id] = $name;
        }

        $logs = $this->paginate($finder);

        $this->set(compact('logs', 'modulos', 'logsTipos', 'autores'));
    }
}
