<p align="center">
  <img src="assets/logo-light.png" alt="Proceda" width="400">
</p>

<p align="center">
  <a href="https://github.com/gabrieldosanjosbr/proceda/actions/workflows/ci.yaml">
    <img src="https://github.com/gabrieldosanjosbr/proceda/actions/workflows/ci.yaml/badge.svg" alt="CI">
  </a>
  <a href="https://codecov.io/github/gabrieldosanjosbr/proceda" > 
    <img src="https://codecov.io/github/gabrieldosanjosbr/proceda/graph/badge.svg?token=Qne1xqxuSz" alt="Coverage"/> 
  </a>
</p>

Biblioteca PHP open source para **leitura e interpretação de arquivos EDI** no padrão **PROCEDA**, com layouts declarativos, suporte a múltiplas versões e extensão de schemas para variações específicas de cada empresa.

O Proceda transforma registros de largura fixa em um modelo de objetos PHP, preservando relacionamentos entre documentos, empresas, notas fiscais, conhecimentos, ocorrências, fretes e demais estruturas dos layouts.

Os layouts são definidos declarativamente e desacoplados do Reader, permitindo utilizar a mesma API PHP independentemente da versão física do layout.

```text
Arquivo PROCEDA
      ↓
    Reader
      ↓
    Schema
      ↓
   Builders
      ↓
     DTOs
```

Os layouts oficiais são definidos declarativamente em YAML. A arquitetura separa o carregamento das definições de sua compilação, permitindo fornecer schemas, registros, propriedades e tipos a partir de outras fontes.

## Recursos

* API unificada para diferentes versões dos layouts PROCEDA
* Leitura de arquivos Fixed Width
* Mapeamento automático para DTOs PHP
* Relacionamentos entre objetos reconstruídos automaticamente
* Layouts declarativos em YAML
* Suporte a múltiplas versões de um mesmo layout
* Profiles para personalizar schemas, registros, propriedades e tipos sem alterar os layouts oficiais
* Tipos e valores de layout normalizados entre versões
* Arquitetura extensível através de interfaces
* Separação entre definição do layout e modelo de domínio
* Suporte a estruturas compostas, coleções inline e fallback entre registros

## Requisitos

* PHP 8.3 ou superior
* Composer

## Instalação

```bash
composer require proceda/proceda
```

## Layouts suportados

| Layout | Versões  | Status         |
| ------ | -------- | -------------- |
| NOTFIS | v31, v50 | ✅ Implementado |
| OCOREN | v31, v50 | ✅ Implementado |
| DOCCOB | v30, v50 | ✅ Implementado |
| CONEMB | v31      | ✅ Implementado |

## Exemplo de utilização

O layout é criado através da facade `Proceda`, informando **explicitamente a versão** desejada. Não existe uma versão padrão implícita.

Por exemplo, para ler um arquivo NOTFIS v31:

```php
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;

$layout = Proceda::notfis(
    layoutVersion: LayoutVersion::V31,
);

$result = $layout->read('/caminho/para/notfis-v31.txt');
$intercambio = $result->intercambio;
```

Para trabalhar com NOTFIS v50, basta informar a outra versão na criação do layout:

```php
$layout = Proceda::notfis(
    layoutVersion: LayoutVersion::V50,
);

$result = $layout->read('/caminho/para/notfis-v50.txt');
$intercambio = $result->intercambio;
```

Também é possível ler conteúdo já disponível em memória:

```php
$result = $layout->readFromString($content);
$intercambio = $result->intercambio;
```

O modelo de objetos permanece consistente entre as versões suportadas; as diferenças de registros, campos e posições são resolvidas pelas definições do layout.


## Validação

Além do modelo de domínio, o Reader disponibiliza informações de validação através de `$result->validation`, permitindo identificar inconsistências encontradas durante a interpretação do arquivo PROCEDA.

```php
$result = $layout->read('/caminho/para/notfis-v50.txt');

$intercambio = $result->intercambio;
$validation = $result->validation;

if ($validation->hasErrors()) {
    foreach ($validation->getErrors() as $error) {
        echo $error->message() . PHP_EOL;
    }
}
```

As validações podem identificar problemas como valores obrigatórios ausentes, códigos não reconhecidos, formatos inválidos e registros obrigatórios não encontrados.

A presença de erros de validação não impede necessariamente a construção do modelo de domínio. Dessa forma, a aplicação pode consultar os dados interpretados e tratar as inconsistências conforme suas próprias regras de negócio.

Para informações detalhadas sobre validações, tipos de erro e tratamento dos resultados, consulte a [Validação e erros](docs/reader.md#validação-e-erros)

## Profiles

Os **Profiles** permitem adaptar um layout PROCEDA a particularidades de uma **empresa**, de uma **integração** ou de um **cenário operacional**, sem modificar os arquivos oficiais da biblioteca.

Um profile pode personalizar definições como campos, registros, limites de ocorrência, mapeamento de propriedades e tipos de valores. As definições oficiais continuam sendo a base, e as personalizações são aplicadas apenas quando um profile é selecionado explicitamente.

```php
use Proceda\Proceda;
use Proceda\Schema\LayoutVersion;

$layout = Proceda::notfis(
    layoutVersion: LayoutVersion::V50,
    profile: 'empresa-acme',
);

$result = $layout->read('/caminho/para/notfis-personalizado.txt');
```

Nesse exemplo, `empresa-acme` é um identificador **ilustrativo**. Para utilizá-lo, deve existir um diretório `empresa-acme` dentro de `profiles/`, disponibilizado à aplicação através da configuração `PROCEDA_PROFILES_DIR`.

A estrutura pode ser organizada da seguinte forma:

```text
profiles/
└── empresa-acme/
    ├── notfis/
    │   └── v50/
    ├── type/
    └── property.yaml
```

Os arquivos e diretórios de personalização são opcionais e devem ser criados conforme as necessidades da integração. As definições oficiais continuam sendo utilizadas como base.

Sem o argumento `profile`, o layout utiliza somente as definições oficiais.

A separação por profiles permite reutilizar os DTOs e a API do Reader para variações de parceiros e clientes, mantendo as personalizações isoladas. O Writer, quando utilizado, também pode trabalhar com o mesmo profile.

## Playground

O **[PROCEDA Playground](https://proceda-playground.pages.dev/)** é uma interface web interativa para explorar os layouts PROCEDA e experimentar as funcionalidades da biblioteca.

Além da leitura e validação de arquivos EDI, o Playground disponibiliza o **Layout Explorer**, uma ferramenta de visualização da estrutura dos layouts, permitindo navegar por registros, campos, propriedades e relacionamentos entre os schemas.

## Writer

A geração de arquivos PROCEDA é disponibilizada separadamente através do **PROCEDA Writer**.

O Writer realiza o processo inverso ao Reader, utilizando **o mesmo modelo de domínio, schemas e definições de layout** do Proceda:

```text
     Domain
      ↓
    Schema
      ↓
    Writer
      ↓
Arquivo PROCEDA
```

Isso permite utilizar o mesmo modelo de domínio para leitura e escrita, mantendo as diferenças entre versões e layouts isoladas na camada de Schema.

### Execução local e privacidade

O Writer pode ser executado on-premise, inteiramente na infraestrutura do cliente, sem necessidade de enviar documentos ou dados processados para serviços externos.

O runtime do Writer opera sem acesso à Internet. A comunicação externa necessária ao licenciamento é realizada exclusivamente pelo PROCEDA Agent, responsável pela validação e renovação periódica da autorização da instalação junto ao License Server.

Para garantir transparência, o [PROCEDA Agent](https://github.com/gabrieldosanjosbr/proceda-agent) possui código-fonte publicamente disponível para auditoria, permitindo que clientes e equipes de segurança verifiquem o seu funcionamento e as comunicações externas realizadas.

O código é disponibilizado exclusivamente para consulta e auditoria, sem contribuições externas ao projeto.

## Arquitetura

O Proceda separa a representação física dos arquivos do modelo utilizado pela aplicação.

Um mesmo objeto pode ser representado de formas diferentes entre versões do PROCEDA — inclusive através de registros adicionais, extensões, coleções ou diferentes posições — sem exigir que o código consumidor conheça essas diferenças.

```text
Layout físico
(registros, posições e campos)
             ↓
           Schema
             ↓
       Modelo de domínio
        (DTOs e relações)
```

Essa separação permite que o Reader e outras integrações trabalhem sobre um modelo consistente enquanto as particularidades de cada versão permanecem declaradas nos layouts.

Mais detalhes estão disponíveis na documentação de arquitetura.

## Estado do projeto

- [x] Parser de arquivos Fixed Width e layouts declarativos em YAML
- [x] Hidratação automática de objetos e modelo de domínio unificado
- [x] Suporte às versões de NOTFIS, OCOREN, DOCCOB e CONEMB listadas acima
- [x] Profiles para personalização de layouts, registros, propriedades e tipos
- [x] Integração com o PROCEDA Writer e testes de round-trip
- [x] Ampliação dos cenários de teste e dos arquivos TXT de exemplo
- [x] Playground desenvolvido, testado e publicado.
- [ ] Bibliotecas de integração para outras linguagens, incluindo Java (proceda-java) e C#/.NET (proceda-csharp)

Novos layouts, versões e integrações poderão ser avaliados futuramente conforme a demanda, sem cronograma anunciado.

## Documentação

A documentação técnica e os manuais utilizados como referência para implementação dos layouts PROCEDA estão disponíveis em [`docs/`](docs/).

* [Modelo de domínio](docs/domain.md)
* [Arquitetura](docs/arch.md)
* [Schema](docs/schema.md)
* [Registros](docs/registros.md)
* [Perfis de customização](docs/profiles.md)
* [Reader](docs/reader.md)
* [Manuais dos layouts PROCEDA](docs/manuals/)

## Open Source

O **Proceda é open source** e distribuído sob a licença MIT.

Este repositório contém o modelo de domínio, schemas, layouts, mecanismos de leitura e demais componentes necessários para interpretar arquivos PROCEDA.

O **PROCEDA Writer não é open source**. A geração de arquivos PROCEDA é fornecida separadamente através de um componente proprietário sujeito a licenciamento comercial.

A utilização do Writer não altera a licença deste projeto: o Proceda permanece disponível sob os termos da licença MIT.

## Contribuindo

Contribuições são bem-vindas.

Caso encontre algum problema ou tenha alguma sugestão, abra uma *Issue*.

*Pull Requests* também são muito bem-vindos.

Os testes do projeto podem ser executados com:

```bash
composer test
```

Alguns testes de integração utilizados pelo mantenedor validam adicionalmente a compatibilidade com o PROCEDA Writer proprietário e podem depender de componentes não distribuídos publicamente.

Esses testes não são necessários para desenvolver ou contribuir com o Proceda.

## Licença

O Proceda está licenciado sob a licença MIT. Consulte o arquivo `LICENSE` para mais informações.

A licença MIT deste repositório não se aplica ao **PROCEDA Writer**, que é distribuído separadamente sob termos próprios de licenciamento.

---

No switch statements were harmed — or written — in the making of this project.
