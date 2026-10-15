# PROCEDA Writer — Implantação Docker

Este diretório contém a configuração necessária para executar o **PROCEDA Writer** com validação de licença por meio do **PROCEDA Agent**.

## Pré-requisitos

- Docker e Docker Compose
- Acesso às imagens privadas do PROCEDA no GitHub Container Registry (GHCR)
- Token de ativação fornecido pela ANGELIC WEB SOLUTIONS

## 1. Configurar a instalação

Crie um arquivo `.env` neste diretório, utilizando o `.env.example` como referência:

```bash
cp .env.example .env
```

Configure o identificador da instância:

```dotenv
PROCEDA_INSTANCE_ID=writer-01
```

O identificador deve ser definido para a instância do Writer que será executada.

## 2. Baixar as imagens

Após autenticar o Docker no GHCR com as permissões necessárias:

```bash
docker compose pull
```

## 3. Ativar a licença

**A ativação deve ser realizada antes da primeira inicialização do Writer.**

Execute:

```bash
docker compose run --rm proceda-agent activate <TOKEN_DE_ATIVACAO>
```

Substitua `<TOKEN_DE_ATIVACAO>` pelo token fornecido para a instalação.

Quando a ativação for concluída, o Agent exibirá uma mensagem semelhante a:

```text
installation activated: <INSTALLATION_ID>
```

A identidade da instalação e a licença serão armazenadas no volume persistente `proceda-agent-data`.

**Importante:**

- O token de ativação não deve ser incluído no `docker-compose.yaml` ou versionado no Git.
- Não remova o volume `proceda-agent-data` após a ativação.
- A ativação não precisa ser repetida a cada inicialização dos containers.

## 4. Iniciar os serviços

Após a ativação:

```bash
docker compose up -d
```

Verifique o estado dos containers:

```bash
docker compose ps
```

Os serviços `proceda-agent` e `proceda-writer` devem permanecer em execução.

## 5. Verificar os logs

Para consultar os logs do Agent:

```bash
docker compose logs --tail=50 proceda-agent
```

Para consultar os logs do Writer:

```bash
docker compose logs --tail=50 proceda-writer
```

O Agent deverá obter uma autorização válida e disponibilizar seu socket Unix.

## 6. Integração com o Writer

O Writer disponibiliza um socket Unix em:

```text
/run/proceda/writer.sock
```

A aplicação cliente deve ter acesso ao volume compartilhado `proceda-runtime` e utilizar esse caminho para se comunicar com o Writer.

O Writer não precisa de acesso direto à internet. A comunicação com o License Server é realizada pelo PROCEDA Agent.

## 7. Atualização

Para atualizar as imagens:

```bash
docker compose pull
docker compose up -d
```

Os dados persistidos no volume do Agent devem ser preservados durante a atualização.

## 8. Encerramento

Para interromper e remover os containers:

```bash
docker compose down
```

Esse comando preserva os volumes nomeados por padrão.

**Não utilize `docker compose down -v`**, pois isso removerá também os volumes persistentes, incluindo os dados de ativação do Agent.