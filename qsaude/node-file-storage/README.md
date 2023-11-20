# QSaúde Storage Microservice
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=node-file-storage&metric=alert_status&token=3f96c1232d7fe56862a0103147dba9535278a981)](https://sonarcloud.io/summary/new_code?id=node-file-storage)
## Ajustes e melhorias

O projeto ainda está em desenvolvimento e as próximas atualizações serão voltadas nas seguintes tarefas:

- [ ] `<task>`

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:

- Você instalou a versão `nodejs16`
- Você instalou a versão mais recente de `npm`
- Você tem uma máquina `<Windows / Linux / Mac>`

## ☕ Clonando e Instalando ``<node-file-storage>``

Para clonar o repositório `<node-file-storage>`, siga estas etapas:

```bash
git clone https://QsaudeDevOps@dev.azure.com/QsaudeDevOps/DigitalWorkPlace/_git/node-file-storage
```

Para instalar as dependências `<node-file-storage>`, siga estas etapas:

```bash
npm run install
```

## ⚙️ Configurando ambiente `<node-file-storage>`

Utilizar o arquivo `example.env` como base para a criação dos seguintes arquivos de configuração:

- `.env`
- `test.env`

> Para as configurações dos módulos definir `src/<module>/config/<module>.config.ts`, seguindo `src/main/config/main.config.ts` como base.

Definir as variáveis de ambiente para AWS:

- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_DEFAULT_REGION`
- `AWS_S3_BUCKET`

## 🚀 Usando `<node-file-storage>`

Para usar `<node-file-storage>`, siga estas etapas:

```bash
# Unix users

# development
$ npm run start

# watch mode
$ npm run start:dev

# debug watch mode
$ npm run start:debug

# ***Windows users***
Todos os comandos acima funcionam também no Windows utilizando o formato
$ npm run <command>:win:<action> 
$ npm run start:win:dev
```
## Testes

```bash
# unit tests
$ npm run test

# e2e tests
$ npm run test:e2e

# test coverage
$ npm run test:cov
```

## Gerando arquivo de change log

```bash
npm run changelog:minor # x.y.x
npm run changelog:major # y.x.x
npm run changelog:patch # x.x.y
npm run changelog:alpha # x.x.x-alpha.0
```
