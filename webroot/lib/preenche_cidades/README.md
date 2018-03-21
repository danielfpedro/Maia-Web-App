## Exemplo de uso

```html
<select id="estados">
    <option id="1">
        Bahia
    </option>
    <option id="2">
        Minas Gerais
    </option>
</select>

<select id="cidades" disabled></select>
```

```javascript
$('#estados').preencheCidades({
    targetSelector: '#cidades',
    source: 'retorna_cidades.php'
});
```

```php
// //////////////////////////////////////////////////////////////////////////////////////////////////////////
// OBS: Este código é meramente ilustrativo, nenhuma prática de segurança ou padrão de projeto foi aplicada.
// //////////////////////////////////////////////////////////////////////////////////////////////////////////

// Neste exemplo $_GET['value'] é o id do estado selecionado que o plugin enviou
$value = $_GET['value'];
// Pega as cidades no Banco de Dados baseado no id estado
$cidades = retorna_dados_do_bd("SELECT id, nome FROM cidades WHERE estado_id = ' . $value);

echo json_encode($cidades);
```

## Funcionamento
No evento `change` do `<select>` informado no plugin é feito uma requisição `ajax` para a url da opção `source` enviando
o valor atual do `<select>` com o nome de `value`. A página requisitada deverá devolver um json com os dados desejados.
O plugin pegará esses dados retornados e criará as opção no `<select>` especificado na opção `targetSelector` usando 
`value` para o `value` e `name` para o texto das opções do `<select>`.

## Opções

| Opção                   | Default | Descrição |
|------------------------|-------|------------------|
| targetSelector | (string) null | `Selector (id, class...)` do `<select>` que irá receber os dados. |
| source | (string) null | Url do local que irá retornar os dados. |
| targetDisabledText | (string) null | Texto do target para quando ainda não tiver nada selecionado. |
| targetBlankOption | (boolean) false | Diz se o target terá uma opção em branco. |
| targetBlankText | (string) null | O Texto que será exibido na opção em branco do target caso `targetBlankOption` este setada como `true`. |
| targetLoadingText | (string) Carregando... | Texto que será exibido no target enquando os dados são carregados. |
| value | (string) id | Nome da `key` do array que será usada no value de cada opção do `<select>`. |
| label | (string) name | Nome da `key` do array que será usada no no texto de cada opção do `<select>` |

## Passando dados extras
Você pode passar dados extras para a requisição utilizando a opção `extraData`, passando uma função que retorna um objeto de dados.

```javascript
$('#estados').preencheCidades({
    targetSelector: '#cidades',
    source: 'retorna_cidades.php',
    extraData: function() {
        return {
            algo_mais: 'foo',
            outra_coisa: $('#outra-coisa').val()
        }
    }
});
```

Supondo que o valor de `#estados` seja `1` e o valor de `#outra-coisa` seja `teste`, a requisição ficaria assim:
```bash
GET: retorna_cidades.php?value=1&algo_mais=foo&outra_coisa=teste
```

É importante dizer que a função será chamada exatamente antes da requisição ser feita, ou seja, caso vc utilize um valor 
que pode mudar como `$('#outra-coisa').val()`, será enviado sempre o valor atual do momento que a requisição é feita.

## Sobrescrevendo o método que cria as opções
Você pode alterar a forma que as opções são criadas com uma função na opção `_createOptions`.
A função recebe os dados da requisição e deve retornar as opções.

```javascript
$('#estados').preencheCidades({
    targetSelector: '#cidades',
    source: 'retorna_cidades.php',
    _createOptions: function(data) {
        var options = '';
        
        $.each(data, function(key, value) {
            options += '<option value="'+value.id+'">'+value.name+' - Adicionando coisas extras as opções</option>';
        });

        return options;
    }
});
```

## Performance
Toda nova requisição feita, caso haja uma requisição anterior ainda pendente ela será cancelada, evitando 
uma fila de requisições desnecessarias.