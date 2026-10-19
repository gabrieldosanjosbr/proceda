# Modelo de domínio

O PROCEDA separa a representação física dos arquivos EDI do **modelo de domínio** utilizado pelas aplicações PHP.

Cada versão de layout define registros, campos e posições próprios. O Reader utiliza os Schemas para interpretar essas informações e construir objetos PHP (DTOs), permitindo que a aplicação trabalhe com conceitos do domínio sem depender diretamente dos identificadores de registro.

```text
Arquivo EDI (registros de largura fixa)
                 ↓
               Schema
                 ↓
                Reader
                 ↓
        Modelo de domínio (DTOs)
```

## Intercâmbio

`Proceda\Intercambio` representa o intercâmbio processado e contém os documentos identificados no arquivo.

```text
Intercambio
└── documentos[]
    └── Documento
```

Um intercâmbio pode conter múltiplos documentos. A implementação concreta de cada documento depende do layout e é determinada pelo Schema correspondente.

## Documento

`Documento` é a abstração comum para os documentos de intercâmbio. As implementações especializadas representam relações próprias de cada layout, sem impor a todos os documentos propriedades que só fazem sentido em alguns deles.

```text
Documento
├── Conemb
├── Doccob
├── Ocoren
└── Notfis
```

As especializações são encontradas no namespace `Proceda\Documento`.

### CONEMB

No CONEMB, o documento possui um transportador, ao qual estão associados os conhecimentos de transporte.

```text
Conemb
└── transportador
    └── conhecimentos[]
```

### DOCCOB

No DOCCOB, o documento possui um transportador, ao qual estão associadas as cobranças.

```text
Doccob
└── transportador
    └── cobrancas[]
```

### OCOREN

No OCOREN, o documento possui um transportador, ao qual estão associadas as ocorrências de entrega.

```text
Ocoren
└── transportador
    └── ocorrencias[]
```

### NOTFIS

O **NOTFIS (Notas Fiscais)** é utilizado principalmente pelas **embarcadoras** para transmitir às **transportadoras** as informações das notas fiscais referentes às mercadorias que serão transportadas.

Diferentemente dos layouts CONEMB, OCOREN e DOCCOB, que são normalmente enviados pelas transportadoras às embarcadoras, o NOTFIS representa o fluxo de informações no sentido inverso.

No modelo de domínio, o documento NOTFIS contém uma coleção de embarcadoras, que organizam os destinatários e suas respectivas notas fiscais.

```text
Notfis
└── embarcadoras[]
    └── destinatarios[]
        └── notasFiscais[]
```

A estrutura reflete a organização dos dados transmitidos pela embarcadora, sem exigir uma associação direta com um `Transportador` no nível do documento.

Informações sobre responsáveis pelo frete, consignatários e transportadoras envolvidas em redespacho podem ser representadas nas estruturas associadas às notas fiscais, conforme o layout e sua versão.
## Modelo de domínio e estrutura física

A árvore dos DTOs **não precisa reproduzir literalmente** a hierarquia física dos registros EDI.

As definições em `registros.yaml` descrevem os tipos de linha, seus tamanhos e suas relações estruturais. Os Schemas descrevem como os dados dessas linhas são associados às propriedades e aos objetos do domínio.

```text
Registro físico ──┐
Registro físico ──┼── Schema ──> DTO e suas propriedades
Registro físico ──┘
```

Por exemplo, informações que aparecem em mais de um registro físico podem alimentar o mesmo objeto PHP. Da mesma forma, um registro físico pode representar uma estrutura associada a outro objeto do domínio.

Essa separação permite manter uma API de leitura consistente mesmo quando identificadores, campos e posições mudam entre versões.

## Papel dos Schemas

O Schema define, entre outros aspectos:

- A classe PHP a ser construída para uma estrutura do layout.
- O caminho da estrutura no modelo de objetos (`path`).
- A correspondência entre campos físicos e propriedades dos DTOs.
- As relações e coleções que serão preenchidas durante a leitura.

O Reader interpreta os registros de acordo com essas definições e utiliza os builders para compor os objetos e seus relacionamentos.

## Leitura entre versões

O mesmo tipo de documento pode ser representado por conjuntos diferentes de registros em cada versão do layout. Por exemplo, as versões v31 e v50 do NOTFIS utilizam identificadores e estruturas físicas diferentes, mas ambas são interpretadas por meio do modelo de domínio correspondente ao NOTFIS.

O objetivo é que as aplicações consumidoras trabalhem com os DTOs, enquanto as particularidades de cada versão permanecem concentradas nas definições do layout.

## Documentação relacionada

- [Arquitetura](arch.md) — organização geral dos componentes.
- [Registros](registros.md) — propriedades e relações dos registros físicos.
- [Reader](reader.md) — processo de leitura e construção dos objetos.

As regras específicas de mapeamento, cardinalidade e coleções devem ser consultadas na documentação dos Schemas e nas definições de cada layout.
