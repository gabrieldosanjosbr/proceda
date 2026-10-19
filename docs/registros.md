# Registros

Os arquivos `registros.yaml` descrevem os **tipos de registros físicos** de cada layout e versão do padrão PROCEDA. Cada definição identifica uma linha de largura fixa e pode declarar sua posição na hierarquia do arquivo, o compartilhamento de contexto com outro registro e regras de processamento.

As definições dos registros são específicas de cada layout e versão. Por exemplo, o NOTFIS v3.1 e o NOTFIS v5.0 possuem identificadores, tamanhos e relacionamentos entre registros diferentes, embora representem o mesmo tipo de documento EDI.

Exemplo (OCOREN v5.0, trecho simplificado):

```yaml
- identificacao: "000"
  length: 250
  required: true
  occurs: 1

- identificacao: "540"
  length: 250
  parent: "000"
  required: true
  occurs: 200

- identificacao: "541"
  length: 250
  parent: "540"
  build: false
  required: true
  occurs: 1

- identificacao: "542"
  length: 250
  parent: "541"
  required: true
  occurs: 9999

- identificacao: "543"
  length: 250
  parent: "542"
  useParentAsContext: true
  build: false
  required: false
  occurs: 1

- identificacao: "544"
  length: 250
  parent: "542"
  required: false
  occurs: 9999

- identificacao: "545"
  length: 250
  parent: "542"
  useParentAsContext: true
  build: false
  required: false
  occurs: 1

- identificacao: "549"
  length: 250
  parent: "540"
  useParentAsContext: true
  deferred: true
  build: false
  required: true
  occurs: 1
```

## Propriedades

| Propriedade | Finalidade |
|---|---|
| `identificacao` | Identificador do tipo de registro físico. |
| `length` | Quantidade de caracteres/posições da linha física. |
| `occurs` | Quantidade máxima de ocorrências físicas permitidas para o registro em seu contexto. |
| `parent` | Identificador do registro pai na hierarquia estrutural EDI. |
| `useParentAsContext` | Indica compartilhamento do contexto de construção e interpretação com o pai. |
| `required` | Indica se a linha física é obrigatória quando aplicável ao layout e ao seu contexto. |
| `deferred` | Indica tratamento diferido do registro. |
| `fallback` | Referência a outro registro utilizada para resolução alternativa de determinados campos. |
| `build` | Controla se a leitura da linha pode disparar a construção do contexto correspondente. |

### `identificacao`

Identifica o tipo de registro presente no arquivo. É representado como **string**, preservando zeros à esquerda.

```yaml
identificacao: "540"
```

A identificação é utilizada para selecionar as definições de campos e Schemas aplicáveis à linha.

### `length`

Define o tamanho total da linha física, em posições do layout.

```yaml
length: 250
```

O Reader utiliza essa definição para interpretar a linha; o Writer a utiliza na geração dos registros de largura fixa.

### `parent`

Declara a **hierarquia estrutural entre registros EDI**.

```yaml
- identificacao: "541"
  parent: "540"

- identificacao: "542"
  parent: "541"
```

Nesse exemplo, o registro `542` está subordinado ao `541`, que por sua vez está subordinado ao `540`.

```text
540 — Documento
└── 541 — Transportadora
    └── 542 — Ocorrência
```

**Importante:** `parent` não determina, sozinho, que os campos de um registro devem ser interpretados no mesmo objeto do pai. Também não substitui o caminho (`path`) definido pelo Schema: a hierarquia física e a organização dos DTOs são conceitos distintos.

O registro raiz de intercâmbio (`000`) não possui `parent`.

### `useParentAsContext`

Indica que o registro utiliza o contexto do registro pai, permitindo que ambos preencham a **mesma instância do objeto de domínio (DTO)**, em vez de criar uma nova instância para o registro filho.

```yaml
- identificacao: "542"
  length: 250
  parent: "541"

- identificacao: "543"
  length: 250
  parent: "542"
  useParentAsContext: true
```

Nesse exemplo, o `543` fornece informações complementares ao contexto do `542`.

```text
541 — Transportadora
└── 542 — Ocorrência (contexto próprio)
    └── 543 — Complemento (contexto do 542)
```

Sem `useParentAsContext: true`, a propriedade `parent` representa **somente a relação estrutural**. O valor padrão do sinalizador é `false`.

Um registro filho pode possuir um Schema próprio, ainda que seu registro tenha `build: false`. Portanto, `useParentAsContext` deve ser utilizado apenas quando houver compartilhamento real de contexto.

### `required`

Indica se a **linha física precisa existir** quando o registro é aplicável ao contexto do layout.

```yaml
- identificacao: "541"
  required: true

- identificacao: "543"
  required: false
```

O fato de um registro ser obrigatório não implica que deva existir exatamente uma ocorrência em todo o arquivo. A obrigatoriedade considera a estrutura e as ocorrências às quais o registro se aplica.

**Não confundir os níveis de obrigatoriedade:**

| Nível | Significado |
|---|---|
| `Registro.required` | A linha física precisa existir. |
| `Schema.required` | O objeto deve ser construído e associado mesmo sem campos preenchidos. |
| `Field.required` | O valor precisa existir quando o registro e o contexto são aplicáveis. |

Essas propriedades pertencem a camadas diferentes e não são intercambiáveis.

### `deferred`

Define se a emissão de um registro deve ser adiada pelo **Writer** até o fechamento do contexto ao qual pertence.

É utilizado principalmente em registros que complementam outros registros ou realizam a finalização de documentos, quando precisam ser emitidos após os demais registros do mesmo contexto.

```yaml
- identificacao: "519"
  length: 320
  parent: "500"
  useParentAsContext: true
  deferred: true
  required: true
```

Nesse exemplo, o registro `519` é emitido ao final do documento NOTFIS `500`, após o processamento dos registros que compõem seu conteúdo.

O valor padrão é `false`.

`deferred` controla exclusivamente a ordem de emissão no Writer. Não altera a interpretação dos registros pelo Reader.

### `build`

Controla se o processamento de determinada linha pode **disparar a construção do contexto** no Reader.

```yaml
- identificacao: "543"
  parent: "542"
  useParentAsContext: true
  build: false
```

`build: false` **não significa ignorar a linha**, nem significa que seus campos ou objetos filhos deixarão de ser processados. Apenas impede o disparo da construção naquela etapa.

A definição é distinta de `useParentAsContext`, e os dois sinalizadores não precisam ter o mesmo valor.

Na ausência de `build`, o comportamento padrão de construção é mantido; `build: false` é declarado explicitamente onde necessário.

## Exemplo de hierarquia: NOTFIS v3.1

O NOTFIS v3.1 ilustra a diferença entre hierarquia estrutural e compartilhamento de contexto:

```text
000 — Intercâmbio
└── 310 — Documento
    ├── 311 — Embarcadora
    │   └── 312 — Destinatário
    │       └── 313 — Nota fiscal
    │           ├── 333 — Complemento (contexto do 313)
    │           ├── 314 — Mercadorias
    │           ├── 315 — Consignatário (contexto do 313)
    │           ├── 316 — Redespacho
    │           └── 317 — Responsável pelo frete
    └── 318 — Totalizador (contexto do 310; deferred)
```

O `313` é filho estrutural do `312`, mas não utiliza seu contexto: a nota fiscal possui representação própria.

Já o `333` e o `315` utilizam `useParentAsContext: true` para complementar o contexto do `313` na configuração do layout.

## Boas práticas

- Declare `parent` conforme a estrutura documentada para a **versão específica** do layout.
- Use `useParentAsContext: true` apenas quando houver compartilhamento real de contexto; `parent` não implica esse comportamento.
- Defina `occurs` de acordo com os limites de ocorrências físicas previstos na especificação do layout.
- Preserve a distinção entre obrigatoriedade física (`Registro.required`), construção de objetos (`Schema.required`) e preenchimento de campos (`Field.required`).
- Não confunda `deferred` com `required: false` ou `build: false`.
- Valide mudanças nas definições com testes do Reader e, quando disponível, com testes de round-trip do Writer, especialmente para coleções e registros repetidos.

A documentação dos manuais de referência e dos layouts suportados está disponível em [`manuals/`](manuals/).