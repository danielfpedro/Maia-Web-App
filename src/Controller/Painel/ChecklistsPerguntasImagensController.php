<?php
namespace App\Controller\Painel;

use App\Controller\Painel\AppController;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use WideImage\WideImage;

use Cake\Network\Exception\NotFoundException;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\BadRequestException;

/**
 * ChecklistsPerguntasImagens Controller
 *
 * @property \App\Model\Table\ChecklistsPerguntasImagensTable $ChecklistsPerguntasImagens
 */
class ChecklistsPerguntasImagensController extends AppController
{

    public function beforeFilter(Event $event)
    {
        // Eu tb checo a pergunta id no add pe alem de salvar eu deleto as imagens
        // que não vieram então eu preciso que o id da pergunta seja checado sua
        // integridade
        if ($this->Auth->user() && in_array($this->request->action, ['add'])) {
            $perguntaId = (int)$this->request->perguntaId;
            if (!$this->ChecklistsPerguntasImagens->Perguntas->possoAcessarPergunta($perguntaId, $this->Auth->user())) {
                throw new NotFoundException();
            }
        }

        // Checando se a imagem pode ser acessada pelo usuário logado
        if ($this->Auth->user() && in_array($this->request->action, ['delete'])) {
            $imagemId = (int)$this->request->imagemId;
            if (!$this->ChecklistsPerguntasImagens->possoAcessar($imagemId, $this->Auth->user())) {
                throw new NotFoundException();
            }
        }

        $this->Security->config('unlockedActions', ['upload', 'delete', 'add']);

        parent::beforeFilter($event);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $imagensIds = [];

        // Se eu deleto todas as imagens no form vai acontecer de não chegar o array imagens então
        // esta checagem é necessaria
        if (isset($this->request->data['imagens'])) {
            foreach ($this->request->data['imagens'] as $key => $imagem) {
                if ($this->ChecklistsPerguntasImagens->possoAcessar($imagem['id'], $this->Auth->user())) {

                    $checklistsPerguntasImagen = $this->ChecklistsPerguntasImagens->get($imagem['id']);
                    $checklistsPerguntasImagen = $this->ChecklistsPerguntasImagens->patchEntity($checklistsPerguntasImagen, $imagem);
                    $checklistsPerguntasImagen->ordem = $key;
                    $checklistsPerguntasImagen->salvo = 1;
                    $this->ChecklistsPerguntasImagens->save($checklistsPerguntasImagen);

                    $imagensIds[] = $checklistsPerguntasImagen->id;
                }
            }
        }

        // Deleto
        $conditions = [
            'checklists_pergunta_id' => $this->request->perguntaId,
            'salvo' => 1
        ];
        if ($imagensIds) {
            $conditions['id NOT IN'] = $imagensIds;
        }
        $imagensDeletar = $this->ChecklistsPerguntasImagens->find('all')
            ->select(['id'])
            ->where($conditions);

        foreach ($imagensDeletar as $imagem) {
            $this->ChecklistsPerguntasImagens->delete($imagem);
        }
    }

    public function delete()
    {
        $imagem = $this->ChecklistsPerguntasImagens->get($this->request->imagemId);
        $this->ChecklistsPerguntasImagens->delete($imagem);
    }

    public function upload()
    {
        //dd($this->_getImagensReferenciaFolder());
        $imagensReferenciaFolder = $this->_getImagensReferenciaFolder(DS, $this->request->checklistId);
        $folderDestino = new Folder(WWW_ROOT . $imagensReferenciaFolder, true, 0755);

        // dd($imagensReferenciaFolder);
        // throw new BadRequestException();

        $imagem = null;

        foreach ($this->request->data['files'] as $file) {
            if ($file['error'] != 0) {
                throw new BadRequestException("Imagem enviada falhou");
            }
            // Verifico se a extensão do arquivo é permitida.
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $this->ChecklistsPerguntasImagens->extensoesPermitidas)) {
                throw new ForbiddenException(__("Arquivo enviado é de extensão inválida. Enviado ({0}) Permitida ({1})", $ext, implode($this->ChecklistsPerguntasImagens->extensoesPermitidas, ' | ')));
            }

            $image = WideImage::load($file['tmp_name']);

            $w = $image->getWidth();
            $h = $image->getHeight();
            $maiorLado = ($w >= $h) ? $w : $h;
            if ($maiorLado > $this->ChecklistsPerguntasImagens->maxSideSize) {
                $image = $image->resize($this->ChecklistsPerguntasImagens->maxSideSize, $this->ChecklistsPerguntasImagens->maxSideSize, 'inside');
            }

            $fileName = md5((new \Datetime())->format('Y-m-d H:i:s') . $file['tmp_name']) . '.' . $ext;

            $image->saveToFile($folderDestino->path . DS . $fileName);

            $image
                ->resize($this->ChecklistsPerguntasImagens->thumbSize, $this->ChecklistsPerguntasImagens->thumbSize, 'outside')
                ->crop('center', 'center', $this->ChecklistsPerguntasImagens->thumbSize, $this->ChecklistsPerguntasImagens->thumbSize)
                ->saveToFile($folderDestino->path . DS . 'quadrada_' . $fileName);

            // Para pegar o tamanho final
            $file = new File($folderDestino->path . $fileName);

            $imagem = [
                'nome_arquivo' => $fileName,
                'folder' => str_replace('\\', '/', $imagensReferenciaFolder),
                'filesize' => $file->size() /1024
            ];

            // $entity = $this->ChecklistsPerguntasImagens->newEntity([
            //     'checklists_pergunta_id' => $this->request->perguntaId,
            //     'nome_arquivo' => $fileName,
            //     'salvo' => 0,
            //     // Pego o folder com separator / que nao é caminho fisico e é
            //     // assim que a gente precisa mesmo
            //     'folder' => str_replace('\\', '/', $imagensReferenciaFolder),
            //     // Tamanho em kb
            //     'filesize' => $file->size() / 1024
            // ]);

            // $this->ChecklistsPerguntasImagens->saveOrFail($entity);
        }

        // $imagem = $entity;

        $this->set(compact('imagem'));
        $this->set('_serialize', ['imagem']);
    }
}
