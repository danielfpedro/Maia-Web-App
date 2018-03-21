<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
/**
 * ArquivoDeProdutos component
 */
class ArquivoDeProdutosComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    protected $_extensoesValidas = ['txt', 'xml'];
    protected $_maximoTamanhoArquivoPermitido = 3; // Em MBs
    protected $_maximoProdutosPermitido = 3000;
    // Arquivo já instanciado com a classe File o cakephp
    protected $_file;

    // o array do arquivo quem vem do form com o daods ['filename', 'tmp_name'...]
    public $fileField;
    // Extensão do arquivo
    public $ext;
    // Coteúdo puro que vem no arquivo
    public $rawData;
    // Conteúdo do arquivo após ser tratado com os produtos em array
    public $data = [];
    // Total dos produtos do arquivo
    public $totalProdutos;
    // Separar dos itens, só serve para os casos do arquivo ser txt
    public $txtSeparator = ',';

    /**
     * Pega o array do arquivo que vem do form e seta na classe seus dados básession_get_cookie_params
     * @param array $fileField
     */
    public function setFile($fileField)
    {
        $this->fileField = $fileField;
        // Pego a extensão
        $this->ext = pathinfo($fileField['name'], PATHINFO_EXTENSION);
        // Checo se a extensão é valida
        if (!in_array($this->ext, $this->_extensoesValidas)) {
            throw new \Exception(__("Extensão do arquivo ({0}) inválida. Válidas ({1})", $this->ext, implode(',', $this->_extensoesValidas)));
        }

        $this->getFileContent();
        $this->rawDataToArray();
    }

    /**
     * Pego o conteúdo puro do arquivo
     */
    public function getFileContent()
    {
        // Instancio o arquivo na classe File do cake
        $this->file = new File($this->fileField['tmp_name']);
        // Calculo o tamanho em mb pois o limite é especificado em mb
        $tamanhoEmMB = round($this->file->size() / 1024 / 1024,1);
        // Checo o tamanho referente ao limite
        if ($tamanhoEmMB > $this->_maximoTamanhoArquivoPermitido) {
            throw new \Exception(__('O arquivo enviado contém {0}MB mas deve ter no máximo {1}MB.', $tamanhoEmMB, $this->_maximoTamanhoArquivoPermitido));
        }
        // Seto os dados crús do arquivo
        $this->rawData = $this->file->read();
    }

    /**
     * Checo o tipo da extensão e chamo o método especifico que vai converter os
     * dados crus para um array de dados php.
     */
    public function rawDataToArray()
    {
        switch ($this->ext) {
            case 'txt':
                $this->txtRawDataToArray();
                break;

            default:
                throw new \Exception("Extensão inválida para fazer o parse dos dados.");

                break;
        }
    }

    /**
     * Converto os dados crus do txt para um array de dados php
     */
    public function txtRawDataToArray()
    {
        // Separa uma linha do array pra cada linha, sse código consigo prever
        // tipos dferentes de quebrada de linha
        $tempData = preg_split("/\\r\\n|\\r|\\n/", $this->rawData);
        $i = 0;

        foreach ($tempData as $key => $value) {
            // Divido cada linha por seu separar e agora dentro de cada linha tb
            // tenho um array com os campos do produto divididos
            $value = explode($this->txtSeparator, $value);

            if (count($value) == 3) {
                $this->data[$i]['ean'] = $value[0];
                $this->data[$i]['nome'] = $value[1];
                $this->data[$i]['setor'] = $value[2];
            }

            $i++;
        }

        $this->totalProdutos = count($this->data);

        // Aqui aproveito e checo se o total de produtos está acima do permitido
        if ($this->totalProdutos > $this->_maximoProdutosPermitido) {
            throw new \Exception(__("O arquivo possui {0} produtos para deve conter no máximo {1}", $this->totalProdutos, $this->_maximoProdutosPermitido));
        }
    }
    public function getTotalProdutos()
    {
        return $this->totalProdutos;
    }
    public function getData() {
        return $this->data;
    }
}
