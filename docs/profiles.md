# Perfis de customização (Profiles)

## Visão geral

Os **Profiles** permitem adaptar definições de layouts PROCEDA às particularidades de uma empresa, integração ou cenário operacional **sem alterar os arquivos oficiais do padrão**.

O layout e a versão continuam determinando a estrutura de referência. Um perfil, quando selecionado, fornece definições adicionais ou substituições específicas daquele contexto.

Exemplos de necessidades de personalização:

- adaptar a posição ou o tamanho de campos conforme uma integração acordada;
- ajustar definições de registros e estruturas de um layout;
- restringir ou adaptar códigos aceitos por um campo;
- disponibilizar definições de tipos específicas de um parceiro.

O uso de um Profile é opcional: **sem perfil, são consideradas apenas as definições oficiais**.

## Organização dos arquivos

As definições oficiais ficam em `layout/`. As personalizações são organizadas fora desse diretório, por identificador de perfil:

```text
layout/
├── notfis/
│   └── v50/
└── type/
    └── ocorrencia.yaml

profiles/
└── empresa-acme/
    ├── notfis/
    │   └── v50/
    ├── type/
    │   └── ocorrencia.yaml
    └── property.yaml
```

A árvore acima é **ilustrativa**. Cada perfil precisa conter apenas os arquivos necessários à sua personalização. O diretório `layout/` contém as definições do padrão, enquanto `profiles/` contém definições específicas das integrações.

O nome `empresa-acme` é somente um exemplo de identificador de perfil.

## Configuração

O diretório que contém os perfis pode ser disponibilizado à aplicação por meio da variável de ambiente `PROCEDA_PROFILES_DIR`.

A seleção do perfil é explícita na criação do layout:

```php
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;

$layout = Proceda::notfis(
    layoutVersion: LayoutVersion::V50,
    profile: 'empresa-acme',
);

$result = $layout->read('/caminho/para/notfis-personalizado.txt');
$intercambio = $result->intercambio;
```

Se o argumento `profile` não for informado, a leitura utiliza as definições oficiais. Um perfil não altera globalmente a interpretação dos demais layouts ou das integrações que não o selecionam.

O PROCEDA Writer, quando disponível, pode utilizar as mesmas definições de layout e perfil. Sua distribuição e execução são independentes do Reader.

## Como as definições são carregadas

O carregamento é feito por `Proceda\Infrastructure\YamlSource` (`src/Infrastructure/YamlSource.php`).

```text
YAMLs oficiais                YAMLs do perfil (opcionais)
       │                                 │
       └──────────────┬──────────────────┘
                      ↓
                  YamlSource
                      ↓
               getDefinitions()
                      ↓
             Schema\Compiler
                      ↓
            Estrutura compilada
```

O `YamlSource` aceita o caminho de um arquivo ou diretório e, opcionalmente, um `overridePath`:

```php
$source = YamlSource::fromPath(
    path: $basePath,
    overridePath: $profilePath,
);

$definitions = $source->getDefinitions();
```

Esse exemplo apresenta a API de carregamento; `$basePath` e `$profilePath` representam caminhos definidos pela aplicação.

O comportamento de carregamento é o seguinte:

1. As definições do caminho base são carregadas primeiro.
2. Quando `overridePath` existe, suas definições são carregadas em seguida.
3. Para diretórios, os arquivos `.yaml` são percorridos recursivamente e ordenados pelo caminho antes do carregamento.
4. `getDefinitions()` fornece as definições carregadas para processamento pela etapa de compilação.

**Importante:** `YamlSource` não realiza, por si só, a mesclagem dos conteúdos YAML. A interpretação das definições de personalização e sua composição com as definições oficiais ocorre na etapa posterior do processamento. Portanto, carregar um arquivo de perfil depois do arquivo base não significa, isoladamente, que todas as suas propriedades sejam sobrescritas de modo recursivo.

O método `YamlSource::file()` também pode resolver o caminho de um arquivo por tipo e versão de layout, utilizando `ProfilePath` quando um perfil é informado.

## Tipos compartilhados e particularidades de versão

Um dos usos da separação de definições é trabalhar com catálogos de tipos, como `layout/type/ocorrencia.yaml`, sem duplicar suas entradas para cada versão.

Por exemplo, um catálogo compartilhado pode conter o código de ocorrência `0` (`ProcessoTransporteIniciado`), introduzido no OCOREN v50. Esse código não deve ser aceito em campos correspondentes ao OCOREN v31.

### Tipos e restrições de valores

Os campos (`Field`) podem referenciar tipos definidos em catálogos YAML, como `layout/type/ocorrencia.yaml`.

As propriedades `allowed` e `excluded`, declaradas no próprio campo, permitem restringir os valores aceitos:

- `allowed`: define os valores permitidos para o campo.
- `excluded`: define os valores que não são aceitos pelo campo.

Exemplo — OCOREN v31;

```yaml
codigo:
  label: Código da Ocorrência
  type: Ocorrencia/Codigo
  length: 2
  offset: 29
  required: true
  allowed: 1..99
  excluded: [ 36, 37, 38, 39, 40 ]
```

Essas restrições também podem ser utilizadas em definições personalizadas por Profile, conforme as necessidades de cada integração.

Consulte [Schema](schema.md) para as propriedades de campos e [Registros](registros.md) para a estrutura física dos layouts.

## Recomendações de manutenção

- Mantenha os arquivos oficiais em `layout/` como referência do padrão.
- Separe as particularidades de cada parceiro em um perfil identificado de forma estável.
- Inclua somente as personalizações necessárias, evitando copiar um layout completo sem necessidade.
- Teste a leitura — e, quando aplicável, a escrita — com arquivos representativos de cada perfil.
- Ao atualizar a biblioteca, valide se os perfis continuam compatíveis com as definições oficiais e a versão do layout utilizada.

## Documentação relacionada

- [Arquitetura](arch.md) — carregamento, compilação e responsabilidades dos componentes.
- [Schema](schema.md) — estruturas e campos declarados nos layouts.
- [Registros](registros.md) — definições dos registros físicos.
- [Modelo de domínio](domain.md) — objetos construídos a partir das definições.
- [Reader](reader.md) — utilização do leitor.
