# Reader

O **PROCEDA Reader** interpreta arquivos de largura fixa do padrão PROCEDA e constrói objetos de domínio de acordo com as definições YAML do layout e da versão selecionados.

O ponto de entrada para a leitura é `Layout::read()`. A classe `Reader` executa o processamento das linhas e a construção dos objetos internamente.

## Leitura de um arquivo

Com uma instância de `Layout` previamente configurada para o tipo, a versão e, quando necessário, o Profile da integração:

```php
/** @var \Proceda\Layout $layout */
$result = $layout->read('/caminho/arquivo.txt');

$intercambio = $result->intercambio;
$validation = $result->validation;
```

A operação devolve um `Result` contendo o `Intercambio` construído e a `Validation` que reúne os erros encontrados.

## Fluxo de leitura

```text
Arquivo PROCEDA
    ↓
Layout::read()
    ↓
leitura das linhas (readFile)
    ↓
criação de Line (createLine)
    ↓
Reader::read(Line)
    ├── resolução do Schema pelo Registro
    ├── leitura e conversão dos Fields
    ├── preenchimento dos Builders
    └── construção e associação dos objetos nos registros com build
    ↓
Reader::validateRequiredRegistros()
    ↓
Reader::getResult()
    ↓
Result (Intercambio + Validation)
```

O método `Layout::read()` percorre as linhas fornecidas por `readFile()`. Linhas para as quais `createLine()` retorna `null` são ignoradas. As demais são encaminhadas ao Reader.

A utilização de `readFile()` é um detalhe importante da implementação atual: a documentação de uso não deve pressupor que os registros sejam processados na ordem de leitura convencional do arquivo.

## Registros e Schemas

Para cada `Line`, o Reader busca o Schema correspondente ao registro físico por meio do `Proceda\Schema\Manager`.

Quando encontra um Schema, interpreta os campos e as estruturas filhas correspondentes. O registro físico determina quais campos se aplicam à linha; o Schema determina onde seus valores serão armazenados na estrutura de objetos.

O Reader também utiliza a propriedade `build` do registro. Quando ela está habilitada, localiza o contexto associado ao registro e realiza a construção e associação dos objetos correspondentes.

A relação estrutural entre registros e o compartilhamento de contexto são conceitos diferentes. Consulte [Registros](registros.md) para `parent` e `useParentAsContext`, e [Schema](schema.md) para a árvore de objetos.

## Leitura dos campos

O Reader interpreta cada `Field` usando as informações compiladas do Schema e o `TypeResolver`.

Durante o processamento:

- verifica se o campo é aplicável ao registro da linha;
- extrai e converte o valor do conteúdo de largura fixa;
- aplica os deslocamentos necessários para estruturas inline;
- considera o `fallback`, quando definido e aplicável;
- preenche o Builder associado ao Schema;
- encaminha os erros detectados para `Validation`.

Um valor nulo não substitui o valor já armazenado no Builder. De modo geral, um valor já preenchido é preservado; o trecho atual permite uma exceção quando o valor existente é `0`.

## Construção dos objetos

Os Builders acumulam os valores interpretados das linhas. O `BuilderRegistry` cria e recupera os Builders pelo `path` do Schema e administra as coleções que serão associadas aos objetos proprietários.

Ao construir uma estrutura, o Reader processa os filhos, anexa as coleções pendentes e, quando aplicável, associa o objeto resultante ao Builder pai.

A associação respeita a propriedade `required` do Schema:

- com `required: true`, o objeto pode ser construído e associado mesmo que o Builder não tenha recebido valores de campos;
- sem obrigatoriedade, um Builder não preenchido não resulta em um objeto anexado.

Para coleções, o Reader procura no Builder pai um método `add{Propriedade}`; se não existir, tenta `add{NomeDaClasse}`.

## Obrigatoriedade: registro, objeto e campo

A obrigatoriedade existe em níveis distintos e não deve ser confundida:

| Configuração | Regra |
|---|---|
| `Registro.required` | A linha física correspondente deve existir quando exigida pelo layout |
| `Schema.required` | O objeto deve ser construído e anexado, mesmo sem campos preenchidos |
| `Field.required` | O valor deve estar presente quando o registro e o contexto do campo forem aplicáveis |

Após percorrer as linhas, `Layout::read()` chama `Reader::validateMissingRegistros()` para verificar a ausência de registros obrigatórios antes de produzir o resultado.

## Validação e erros

O Reader não precisa interromper todo o processamento a cada erro de conteúdo. Os erros encontrados são agregados em uma instância de `Proceda\Error\Validation`.

Os componentes atuais de erro incluem:

```text
Proceda\Error\Error (interface)
├── FieldError
└── LayoutError

Validation
└── errors
```

`FieldError` representa problemas relacionados a campos; `LayoutError`, problemas relacionados às regras do layout, como excesso de ocorrências de uma coleção. `FieldErrorType` classifica os erros de campo.

A forma exata de consultar ou apresentar os erros depende da API pública de `Validation` e `Result`, que deve ser conferida antes de adicionar exemplos de iteração ou formatação.

## Profiles e versões

A leitura utiliza as definições compiladas do layout selecionado. Os Profiles permitem fornecer definições específicas de uma integração sem alterar as definições originais.

O Reader trabalha com os Schemas e tipos resultantes dessa configuração, sem precisar conhecer o caminho físico dos arquivos YAML durante a interpretação das linhas.

Consulte [Perfis de customização](profiles.md), [Registros](registros.md) e [Modelo de domínio](domain.md).

## Responsabilidades

- **`Layout`**: ponto de entrada da leitura e preparação das linhas.
- **`Reader`**: interpretação de registros e controle da construção dos objetos.
- **`Schema\Manager`**: localização de Schemas e de contextos por registro.
- **`Field` / `TypeResolver`**: extração e conversão dos valores.
- **`Builder` / `BuilderRegistry`**: construção e associação de objetos e coleções.
- **`Validation`**: agrupamento dos erros encontrados.
- **`Result`**: entrega do `Intercambio` e da validação.
