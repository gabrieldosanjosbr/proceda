# Schema

## Visão geral

Os **Schemas** descrevem como os registros físicos de um layout PROCEDA são mapeados para os objetos de domínio. As definições declarativas em YAML são processadas por `Proceda\Schema\Compiler` e disponibilizadas como uma árvore de `Schema` e `Field` por `Proceda\Schema\Manager`.

Um `Schema` descreve uma estrutura ou relação entre objetos; um `Field` descreve um valor de largura fixa. Já um `Registro`, definido em `registros.yaml`, representa um **tipo de linha física** do arquivo.

```text
registros.yaml ──→ Registro (linha física)
                         │
Schemas YAML ──→ Compiler ──→ Schema
                              ├── fields: Field[]
                              └── children: Schema[]
                                          ↓
                                    Objetos de domínio
```

A hierarquia dos objetos não precisa reproduzir exatamente a hierarquia dos registros físicos. Consulte também [Arquitetura](arch.md), [Registros](registros.md) e [Modelo de domínio](domain.md).

## Schema: estrutura e propriedades

Um `Schema` estabelece o contexto de construção de um objeto e seus relacionamentos. Os campos escalares pertencem a `fields`; as estruturas aninhadas são representadas por Schemas filhos.

| Propriedade | Finalidade                                                                                                                     |
| --- |--------------------------------------------------------------------------------------------------------------------------------|
| `class` | Classe do objeto de domínio representado pelo Schema.                                                                          |
| `path` | Identificação do contexto estrutural do Schema na árvore compilada.                                                            |
| `property` | Propriedade pela qual a estrutura se relaciona com o objeto proprietário, quando aplicável.                                    |
| `registro` | Registro físico ao qual a estrutura está associada.                                                                            |
| `collection` | Indica uma relação composta por múltiplas ocorrências.                                                                         |
| `inline` | Quantidade de ocorrências que podem ocupar uma mesma linha física.                                                             |
| `overflow` | Permite continuar ocorrências excedentes ao limite `inline` em outras linhas físicas. Exclusivo do Writer.                     |
| `required` | Exige que o objeto seja construído e associado ao seu proprietário, mesmo sem campos preenchidos, quando o contexto se aplica. |
| `span` | Extensão ocupada por uma ocorrência inline, calculada durante a compilação a partir dos comprimentos (`length`) dos campos que compõem o Schema. |

### `path` e relacionamentos

O `path` identifica a posição de uma estrutura na árvore de Schemas compilada. Isso permite distinguir contextos que utilizam a mesma classe, mas representam relações diferentes.

Por exemplo, o consignatário é representado pela classe `Proceda\Empresa`, mas identificado na árvore pelo caminho `nota-fiscal/consignatario`:

```yaml
path: nota-fiscal/consignatario
class: Proceda\Empresa
registro: "513"
label: Consignatário
```

O caminho declarado no YAML **não precisa representar toda a hierarquia desde o objeto raiz**.

Por exemplo, em `layout/notfis/v50/nota_fiscal/carga.yaml`:

```yaml
path: nota-fiscal/carga
class: Proceda\NotaFiscal\Carga
registro: "505"
label: Carga
```

Não é necessário declarar o caminho completo `intercambio/documento/nota-fiscal/carga`. O `Compiler` utiliza as definições e os relacionamentos dos Schemas para construir a estrutura correspondente.

### `inline`, `overflow` e `span`

Essas propriedades controlam a representação de coleções inline nos registros físicos.

- **`inline`**: quantidade máxima de itens representados em uma mesma linha física.
- **`overflow`**: permite ao Writer distribuir os itens excedentes à capacidade `inline` em linhas físicas adicionais.
- **`span`**: espaço ocupado por cada item inline, calculado pelo `Compiler` a partir dos comprimentos (`length`) dos campos que compõem o Schema.

Durante a leitura e a escrita, o deslocamento de cada item dentro da linha é determinado por:

```text
offset do item = offset inicial + (índice do item na linha × span)
```

A propriedade `overflow` é utilizada exclusivamente pelo Writer. O Reader utiliza `inline` e `span` para interpretar os itens presentes em cada linha física.

Por exemplo:

```yaml
path: nota-fiscal/item
class: Proceda\NotaFiscal\Item
registro: "314"
collection: true
inline: 4
overflow: true
label: Item (Mercadoria)
```

Nesse exemplo, cada linha física comporta até quatro itens. Quando `overflow: true`, o Writer pode distribuir os itens excedentes em linhas adicionais.
## Field: campos de largura fixa

`Field` representa um valor escalar armazenado em posições determinadas de um registro.

| Propriedade | Finalidade |
| --- | --- |
| `registro` | Registro físico que contém o campo, quando indicado explicitamente. |
| `offset` | Posição inicial do campo no registro. |
| `length` | Quantidade de posições reservadas ao campo. |
| `decimals` | Quantidade de casas decimais implícitas em valores numéricos. |
| `required` | Exige um valor quando o registro e o contexto correspondentes são aplicáveis. |
| `type` | Tipo usado na interpretação e na escrita do valor. |
| `allowed` | Restringe o tipo aos valores explicitamente permitidos para esse campo. |
| `excluded` | Exclui valores do tipo que não são válidos naquele campo ou naquela versão. |
| `fallback` | Define uma alternativa de origem ou destino para um valor, conforme o layout. |

Exemplo ilustrativo de campos:

```yaml
fields:
  numero:
    offset: 7
    length: 8
    required: true

  valor:
    offset: 31
    length: 15
    decimals: 2
```

> O exemplo demonstra o significado das propriedades. A forma de agrupá-las deve seguir a convenção dos arquivos de Schema atuais do projeto.

### Posições e decimais

`length` corresponde ao espaço ocupado no arquivo de largura fixa. Quando `decimals: 2`, o separador decimal não precisa ser armazenado: um valor como `123.45` é representado pelos dígitos escalonados correspondentes, ajustados ao comprimento do campo.

O `offset` deve respeitar as posições declaradas na especificação da versão utilizada. A conversão entre a posição da documentação PROCEDA e a representação interna ocorre na camada de processamento.

### `type`, `allowed` e `excluded`

Os tipos podem ser definidos em catálogos YAML compartilhados, como `layout/type/ocorrencia.yaml`. Um mesmo catálogo pode ser usado por versões diferentes, com restrições específicas declaradas no campo.

Por exemplo, o código de ocorrência `0` (`ProcessoTransporteIniciado`) é definido no catálogo compartilhado, mas não pertence ao OCOREN v31, tendo sido introduzido no v50. O campo correspondente ao v31 pode, portanto, restringir esse código por `excluded`, sem duplicar o catálogo inteiro.

`allowed` e `excluded` servem para **restringir a validade do valor no contexto do campo**, não para modificar permanentemente o catálogo compartilhado.

### `fallback`

Permite declarar uma posição alternativa para um campo quando determinado layout admite que a mesma informação seja representada em registros físicos diferentes.

O fallback é definido diretamente no campo, preservando a estrutura do domínio e evitando regras específicas de posicionamento nos DTOs.

Por exemplo, no arquivo `layout/notfis/v50/nota_fiscal/frete.yaml`, o valor total do frete possui sua definição principal no registro `507` e uma posição alternativa no registro `506`:

```yaml
valor:
  label: Valor Total do Frete
  registro: "507"  # N 13,2 041 M
  length: 15
  offset: 041
  required: true
  decimals: 2
  fallback:
    registro: "506" # N 13,2 262 C
    length: 15
    offset: 262
    required: false
    decimals: 2
```

Nesse exemplo:

- **Posição principal:** registro `507`, offset `041`.
- **Posição alternativa:** registro `506`, offset `262`.
- **Propriedade de domínio:** `valor`, independentemente do registro físico utilizado.

As propriedades `required` são definidas individualmente para a posição principal e para a alternativa.

A resolução dessas posições é responsabilidade dos componentes de leitura e escrita, conforme as suas respectivas regras de processamento. O objeto de domínio permanece independente da localização física do campo.

## Três níveis de `required`

A propriedade `required` existe em camadas diferentes e **não possui o mesmo significado em todas elas**.

| Camada | Significado |
| --- | --- |
| **`Registro.required`** | A **linha física** correspondente precisa existir, conforme o contexto aplicável do layout. |
| **`Schema.required`** | O **objeto** deve ser construído e associado ao objeto proprietário, mesmo que nenhum dos seus `Field` esteja preenchido. |
| **`Field.required`** | O **valor** deve estar presente quando o respectivo registro/contexto for aplicável. |

### Exemplo conceitual

Considere um documento formado por um registro principal e um registro complementar:

```text
Documento
├── registro principal
└── registro complementar
```

- Marcar o **Registro** como obrigatório significa exigir a presença daquela linha física.
- Marcar o **Schema** como obrigatório significa exigir a materialização da relação/objeto correspondente, ainda que seus campos estejam vazios.
- Marcar um **Field** como obrigatório significa validar o seu valor **quando a linha e o contexto correspondentes devem ser interpretados**.

Essas obrigações são independentes. Em particular, `Field.required` não deve ser interpretado, por si só, como uma ordem para criar linhas físicas ausentes; e `Schema.required` não implica que todos os seus campos sejam obrigatórios.

## Registro físico e contexto de domínio

Alguns registros são complementares e contribuem para um mesmo objeto de domínio. As configurações do arquivo `registros.yaml` permitem distinguir essas situações:

| Configuração do registro | Significado                                                                                                                                                      |
| --- |------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `parent` | Indica a relação hierárquica entre registros físicos.                                                                                                            |
| `useParentAsContext` | Indica que o registro compartilha a mesma instância do objeto de domínio (DTO) associada ao registro pai, permitindo que ambos preencham campos do mesmo objeto. |
| `deferred` | Adia a emissão do registro pelo Writer, especialmente em registros de contabilização ou finalização de documentos que precisam ser escritos ao final do contexto. |

**`parent` não implica `useParentAsContext`.** Um registro pode ser filho de outro no arquivo físico e ainda corresponder a um objeto distinto.

Essas propriedades pertencem à definição de **Registro**, não ao Schema. Para a referência completa, consulte [Registros](registros.md).

## Compilação e processamento

```text
Definições YAML
      ↓
YamlSource
      ↓
Proceda\Schema\Compiler
      ↓
Schema raiz
 ├── Field[]
 └── Schema[]
      ↓
Proceda\Schema\Manager
      ↓
Reader / Writer
```

A compilação resolve previamente os relacionamentos e os metadados estruturais necessários à interpretação dos arquivos. O Reader utiliza o Schema compilado para localizar e interpretar os campos e construir os objetos de domínio. O Writer, disponibilizado separadamente, utiliza as mesmas definições para materializar o arquivo físico.

As definições de `Schema` e `Field` não devem carregar os erros de uma execução específica; as informações de validação pertencem aos resultados e aos objetos de erro produzidos durante o processamento.

## Documentação relacionada

- [Arquitetura](arch.md): visão geral dos componentes e suas responsabilidades.
- [Registros](registros.md): propriedades dos registros físicos, como `parent`, `required` e `deferred`.
- [Modelo de domínio](domain.md): classes e relacionamentos construídos a partir dos Schemas.
