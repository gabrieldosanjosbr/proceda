# Arquitetura

## Visão geral

O Proceda é uma biblioteca PHP para leitura de arquivos de largura fixa
no padrão PROCEDA. A escrita é oferecida separadamente pelo PROCEDA Writer.

A arquitetura separa a definição declarativa dos layouts, sua
representação compilada, a leitura e escrita dos registros e os objetos
de domínio.

Os principais conceitos são:

-   layouts declarativos em YAML;
-   compilação das definições em `Schema` e `Field`;
-   `SchemaManager` como acesso à estrutura compilada do layout;
-   objetos de domínio como representação dos documentos PROCEDA;
-   `Reader` para interpretação de arquivos;
-   `Writer` para materialização de arquivos;
-   validação associada ao processamento de campos, schemas e linhas.

``` text
                 Layout YAML
                     ↓
                YamlSource
                     ↓
                 Compiler
                     ↓
               SchemaManager
                     ↓
                  Schema
             ┌───────┴───────┐
             ↓               ↓
          Field[]         children[]
                             ↓
                  ┌──────────┴──────────┐
                  ↓                     ↓
                Reader                Writer
                  ↓                     ↓
         Objetos de domínio           Line[]
                  ↓                     ↓
                Result                Result
```

------------------------------------------------------------------------

## YamlSource

`Proceda\Infrastructure\YamlSource` (`src/Infrastructure/YamlSource.php`)
carrega as definições declarativas dos layouts em arquivos YAML.

A classe pode receber o caminho de um arquivo ou de um diretório. Quando
recebe um diretório, percorre seus subdiretórios, considera os arquivos
com extensão `.yaml` e os carrega em ordem alfabética de caminho. A lista
`exclude` permite ignorar arquivos pelo nome.

Também é possível informar um caminho de sobrescrita (`overridePath`).
Quando esse caminho existe, suas definições são carregadas depois das
definições base, permitindo que a etapa seguinte processe os ajustes
específicos do perfil.

```text
Arquivos YAML do layout
          ↓
      YamlSource
          ├── definições base
          └── definições do perfil (opcionais)
          ↓
   getDefinitions()
          ↓
       Compiler
```

A fábrica `YamlSource::file()` resolve o caminho do arquivo a partir do
tipo e da versão do layout, podendo incluir um perfil por meio de
`ProfilePath`.

`YamlSource` **carrega e disponibiliza definições**; não produz, por si
só, a árvore compilada de `Schema` e `Field`. Essa responsabilidade é do
`Compiler`.

------------------------------------------------------------------------

## Compiler

`Proceda\Schema\Compiler` (`src/Schema/Compiler.php`) transforma as definições declarativas em uma árvore
normalizada de `Schema` e `Field`.

A compilação resolve informações estruturais que não precisam ser
recalculadas durante a leitura ou escrita, incluindo relações entre
schemas, paths e dimensões necessárias para posicionamento dos campos.

``` text
definitions
    ↓
Compiler
    ↓
Schema
├── fields: Field[]
└── children: Schema[]
```

Definições podem referenciar ou expandir outras definições. A composição
é resolvida durante a compilação, de forma que Reader e Writer trabalhem
sobre uma estrutura já pronta para uso.

### Path

Cada `Schema` possui um `path` que identifica sua posição estrutural
dentro do layout.

Exemplos:

``` text
nota-fiscal
nota-fiscal/entrega-casada
conhecimento/nota-fiscal
```

O `path` representa a estrutura do layout e é utilizado para distinguir
schemas mesmo quando propriedades ou registros isolados não seriam
suficientes para identificar seu contexto.

### Span

`span` representa o espaço total ocupado por uma ocorrência de uma
estrutura.

O valor é calculado durante a compilação a partir dos campos que compõem
o schema e pode ser utilizado para calcular o deslocamento de
ocorrências representadas sequencialmente dentro do mesmo registro.

------------------------------------------------------------------------

## Schema Manager

`Proceda\Schema\Manager` (`src/Schema/Manager.php`) é responsável por manter e disponibilizar a estrutura compilada de um layout.

O schema raiz representa a estrutura completa utilizada pelo Reader e pelo Writer, evitando a necessidade de reinterpretar as definições YAML durante o processamento de cada documento.

```text
YamlSource
    ↓
Compiler
    ↓
Manager
    ↓
Schema (raiz)
```

O `Manager` também disponibiliza operações para localizar schemas e resolver seus contextos a partir das informações estruturais produzidas durante a compilação.

------------------------------------------------------------------------

## Schema

`Schema` representa uma estrutura do layout.

Um `Schema` pode representar um objeto, uma coleção ou outra estrutura
composta e mantém separadamente:

-   os `Field` pertencentes à estrutura;
-   os `Schema` filhos;
-   o registro associado à estrutura;
-   o `path`;
-   metadados necessários para construção e posicionamento;
-   o `span` calculado;
-   limites de ocorrências inline, quando aplicáveis.

A separação entre campos e estruturas filhas é explícita:

``` text
Schema
├── fields
│   ├── Field
│   ├── Field
│   └── Field
│
└── children
    ├── Schema
    └── Schema
```

Um `Schema` não é tratado como um `Field`. Estruturas compostas
pertencem à árvore de schemas, enquanto valores escalares pertencem à
coleção de fields.

### Ocorrências inline

`inline` define quantas ocorrências de uma coleção podem ser representadas
na mesma linha física. `occurs` estabelece a cardinalidade máxima da
relação definida pelo schema, independentemente do número de linhas.

O espaço ocupado por cada ocorrência é determinado pelo `span` calculado
para a estrutura.

Por exemplo, considere uma coleção com `occurs: 12`, `inline: 4` e
`span` calculado de 52 posições:

``` text
offset + (índice × span)

ocorrência 1 → offset 0
ocorrência 2 → offset 52
ocorrência 3 → offset 104
ocorrência 4 → offset 156
```

Nesse exemplo, cada linha comporta até quatro itens, cada um ocupando
52 posições; a coleção admite até doze itens no total. Quando
`overflow: true`, os itens que ultrapassam o limite `inline` podem
continuar em linhas adicionais, conforme o comportamento do Writer.

O `span` é calculado na compilação a partir da definição dos campos e
não precisa ser declarado manualmente no layout.

------------------------------------------------------------------------

## Field

`Field` representa um campo individual de largura fixa.

Ele contém a definição necessária para ler, escrever e validar o valor
correspondente, como:

-   `property`;
-   `offset`;
-   `length`;
-   obrigatoriedade;
-   tipo, quando definido;
-   restrições `allowed` e `excluded`;
-   demais metadados específicos do campo.

`Field` é uma definição compilada e não armazena estado de uma execução.
Erros encontrados durante Reader ou Writer referenciam o `Field` que
originou a validação, mas não são armazenados nele.

``` text
Field
  ↑
  │ referência
FieldError
```

Isso permite reutilizar o mesmo schema compilado em diferentes operações
sem misturar resultados de validação com a definição do layout.

------------------------------------------------------------------------

## Registros

`Registro` representa a definição de um tipo de linha do layout e é
utilizado para relacionar o conteúdo físico do arquivo aos schemas
correspondentes.

Reader utiliza a identificação presente na linha para resolver sua
estrutura. Writer utiliza o registro associado ao schema para
materializar a linha correspondente.

A relação entre registro e schema é estrutural: o registro identifica a
linha física, enquanto o schema descreve como aquela estrutura deve ser
interpretada ou escrita.

Nos arquivos `registros.yaml`, cada layout e versão pode declarar:

- `parent`: hierarquia estrutural entre registros físicos;
- `useParentAsContext`: compartilhamento de contexto de construção e
  interpretação com o registro pai;
- `required`: indica se a linha física deve existir;
- `deferred`: controla o adiamento da emissão pelo Writer, sem alterar
  a interpretação do Reader.

O relacionamento físico `parent` não precisa reproduzir exatamente a
árvore de objetos descrita pelos Schemas. Da mesma forma, `required`
em um `Registro` difere de `Schema.required` (construção/anexação da
estrutura) e de `Field.required` (obrigatoriedade de um valor quando
o respectivo contexto é aplicável).

Consulte também [Registros](registros.md) e [Modelo de domínio](domain.md).

------------------------------------------------------------------------

## Line

`Line` representa uma linha física interpretada ou produzida durante o processamento.

É um objeto imutável que contém o conteúdo da linha, o registro associado, um identificador único (`id`) e, opcionalmente, seu número na sequência do arquivo.

```text
Line
├── id
├── content
├── registro
└── number
```

O identificador `id` permite distinguir uma linha independentemente da sua posição no arquivo.

A numeração pode ser atribuída após operações que alterem a ordem das linhas, como buffering, flush ou reordenação.

As informações de validação não são armazenadas em `Line`. Os erros são representados separadamente por `FieldError` e `LayoutError` e agrupados em `Validation`.

------------------------------------------------------------------------

## Reader

`Reader` percorre as linhas de um arquivo PROCEDA, resolve os registros
e utiliza o schema compilado para interpretar seus campos e estruturas.
No fluxo atual, após o processamento das linhas, a validação de
registros ausentes ocorre separadamente, antes da obtenção do resultado.
A construção de objetos de domínio não exige uma chamada adicional a `finish()` para
reprocessar toda a árvore.

``` text
Arquivo TXT
    ↓
Line
    ↓
RegistroResolver
    ↓
Schema
    ↓
Field
    ↓
Builder
    ↓
objeto de domínio
```

A leitura utiliza os `Field` para extrair e converter valores de largura
fixa. Quando um campo possui um type definido, a resolução é delegada à
camada de types.

Erros de conteúdo podem ser registrados durante a própria interpretação
sem impedir necessariamente a construção do restante do documento.

O resultado da leitura contém o `Intercambio` materializado e os erros
que não pertencem diretamente a uma linha ou campo específico.

------------------------------------------------------------------------

## Builder

`Builder` é responsável por construir os objetos de domínio a partir das
informações interpretadas pelo Reader.

Builders são localizados pelo `BuilderRegistry`. Estruturas filhas podem
ser associadas aos respectivos builders conforme a árvore de schemas é
processada.

A construção dos objetos permanece separada da interpretação física dos
campos:

``` text
Line
    ↓
Field / Schema
    ↓
Reader
    ↓
Builder
    ↓
objeto de domínio
```

## BuilderRegistry

O `BuilderRegistry` é responsável por criar e gerenciar as instâncias de `Builder` utilizadas durante a construção dos objetos.

Os builders criados são mantidos e reutilizados pelo registry conforme
o contexto de leitura. O registry também gerencia instâncias e coleções
pendentes de associação durante a construção dos objetos de domínio.

```text
Reader
    ↓
BuilderRegistry
    ├── cria Builder
    ├── mantém Builder
    └── reutiliza Builder
            ↓
           objeto de domínio
```

Além de centralizar o ciclo de vida dos builders, o `BuilderRegistry` permite que o `Reader` obtenha a instância correspondente a uma estrutura sem precisar gerenciar diretamente sua criação ou reutilização.

------------------------------------------------------------------------

## Resultados

Reader e Writer produzem resultados próprios porque representam
operações diferentes. O Writer é disponibilizado separadamente como
componente proprietário; compartilha as definições públicas de layout,
Schema, Field e os modelos de domínio utilizados pelo Reader.

### Reader Result

O resultado do Reader contém o objeto de domínio materializado e as
informações de validação da leitura:

```text
Result
├── intercambio
└── validation
    └── errors[]
```

A propriedade `validation` é uma instância de `Proceda\Error\Validation`,
responsável por armazenar os erros encontrados durante a leitura e
validação do arquivo.

------------------------------------------------------------------------

## Validação

A validação faz parte do próprio processo de interpretação e escrita do
layout. Não existe a necessidade de uma segunda engine que reinterprete
o schema apenas para validar o documento.

Os erros implementam a interface `Proceda\Error\Error` e são especializados
conforme a origem da regra violada.

```text
Error (interface)
├── FieldError
└── LayoutError
```

Os erros encontrados são armazenados em `Validation`, que disponibiliza
a coleção `errors` e métodos para consultar o resultado da validação.

### FieldError

`FieldError` representa uma regra violada por um valor associado a um
`Field`.

Exemplos de erros de campo incluem:

```text
MISSING_REQUIRED_VALUE
TYPE_NOT_ALLOWED
VALUE_EXCEEDS_MAXIMUM_LENGTH
INVALID_TIME
```

O erro referencia o próprio `Field`, permitindo acessar informações como
property, offset, length, type e demais restrições sem duplicá-las no
objeto de erro.

### LayoutError

`LayoutError` representa uma regra estrutural associada a um `Layout`.

Exemplos incluem:

```text
MISSING_REQUIRED_REGISTRO
INLINE_LIMIT_EXCEEDED
OCCURS_LIMIT_EXCEEDED
```

Um erro estrutural pode pertencer ao resultado como um todo ou estar
associado a uma `Line`, dependendo de onde a violação ocorreu.

Por exemplo, a ausência de um registro obrigatório pode existir sem haver uma 
linha correspondente, enquanto o excesso de ocorrências inline
acontece durante a materialização de uma linha concreta.

------------------------------------------------------------------------

## Types

Valores cuja representação depende do layout são definidos em arquivos YAML compartilhados, como `layout/type/ocorrencia.yaml`, e resolvidos por `TypeResolver`.

```text
layout/type/*.yaml
    ↓
TypeResolver
    ↓
TypeCollection
    ↓
Type
```

Um `Field` pode referenciar um tipo e restringir seus valores por meio de `allowed` e `excluded`.

Essas restrições permitem reutilizar uma definição de tipo entre diferentes versões de um layout, respeitando as particularidades de cada versão.

Por exemplo, o código de ocorrência `0` (`ProcessoTransporteIniciado`) é definido no catálogo compartilhado de ocorrências, mas não é válido no OCOREN v31, pois foi introduzido no OCOREN v50.

Dessa forma, o campo correspondente no OCOREN v31 pode excluir esse código por meio de `excluded`, sem modificar o catálogo compartilhado.

A resolução considera o tipo referenciado e as restrições declaradas no campo, determinando o conjunto de valores aceitos para aquele contexto.

Quando um valor não pertence ao conjunto permitido, a validação pode produzir `TYPE_NOT_ALLOWED`.

Tipos com regras próprias de representação, como `time` (`HHMM`), também podem receber validações específicas.

Enums PHP continuam sendo apropriados para conceitos estáveis do domínio que não dependem das definições externas dos layouts.

------------------------------------------------------------------------

## Modelo de domínio

Os objetos de domínio representam os documentos PROCEDA e permanecem desacoplados da
estrutura física dos arquivos de largura fixa.

``` text
Arquivo
  ↓
Reader
  ↓
objetos de domínio
  ↓
Writer
  ↓
Arquivo
```

Detalhes como offset, comprimento, registro, span e limites inline
pertencem ao schema, não aos objetos de domínio.

Essa separação permite que os mesmos objetos de domínio sejam utilizados
em diferentes versões de layout quando suas diferenças puderem ser
resolvidas pela camada declarativa.

------------------------------------------------------------------------

## Contextos compartilhados e fallbacks

Alguns layouts distribuem informações de uma mesma estrutura entre
registros diferentes.

`useParentAsContext` permite que campos associados a registros físicos
distintos contribuam para o mesmo contexto de objeto, sem que cada
registro precise representar uma nova instância do domínio.
`parent`, por si só, indica hierarquia física, não compartilhamento
de contexto.

Reader e Writer podem aplicar regras complementares de resolução e
fallback quando o layout representa o mesmo dado em posições
alternativas.

Essas regras permanecem na camada de schema/processamento e não são
incorporadas aos objetos de domínio.

------------------------------------------------------------------------

## Separação de responsabilidades

A arquitetura procura manter cada conceito responsável por uma única
camada do processamento:

``` text
YamlSource
    ↓
carregamento das definições YAML

Compiler
    ↓
compilação e normalização

Manager (Schema)
    ↓
acesso ao layout compilado

Schema
    ↓
estrutura e relações

Field
    ↓
definição dos campos

TypeResolver
    ↓
valores definidos pelo layout

Reader
    ↓
arquivo → objetos

Builder
    ↓
construção dos objetos

Writer
    ↓
objetos → arquivo

Line
    ↓
representação física das linhas

FieldError / LayoutError
    ↓
representação dos erros encontrados

Validation
    ↓
agrupamento dos erros de validação

Objetos de domínio
    ↓
representação do domínio
```

Essa separação permite evoluir Reader, Writer, layouts, types e
validações sem transferir detalhes de infraestrutura para os objetos de
domínio.

------------------------------------------------------------------------

## Extensibilidade

Novas versões do padrão PROCEDA e customizações específicas por empresa podem ser adicionadas principalmente por meio das definições declarativas em YAML.

A compilação centraliza a transformação dessas definições em uma estrutura normalizada, permitindo que Reader e Writer compartilhem os mesmos conceitos de `Schema`, `Field`, `Registro` e `Type`.

As personalizações podem ser organizadas em perfis específicos, como `profiles/{empresa-acme}/type/*.yaml`, permitindo adaptar as definições de tipos sem modificar os arquivos originais dos layouts.

A arquitetura também permite evoluir componentes específicos, como o carregamento de definições YAML, a criação de builders e a resolução de tipos, sem exigir que os objetos de domínio conheçam essas particularidades.
