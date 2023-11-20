# NestJS GQL StartKit

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:

- Você instalou a versão `nodejs16`
- Você instalou a versão mais recente de `npm`
- Você tem uma máquina `<Windows / Linux / Mac>`

## ☕ Clonando e Instalando `<node-nestjs-gql-startkit>`

Para clonar o repositório `<node-nestjs-gql-startkit>`, siga estas etapas:

```bash
git clone <vcs>/node-nestjs-startkit.git
```

Para instalar as dependências `<node-nestjs-gql-startkit>`, siga estas etapas:

```bash
npm run install
```

## ⚙️ Configurando ambiente `<node-nestjs-gql-startkit>`

Utilizar o arquivo `example.env` como base para a criação dos seguintes arquivos de configuração:

- `.env`
- `test.env`

> Para as configurações dos módulos definir `src/<module>/configs/<module>.config.ts`, seguindo `src/common/configs/app.config.ts` como base.

## 🚀 Usando `<node-nestjs-gql-startkit>`

Para usar `<node-nestjs-gql-startkit>`, siga estas etapas:

```bash
# Unix users

# development
$ npm run start

# watch mode
$ npm run start:dev

# debug watch mode
$ npm run start:debug
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
npm run changelog # only changelog file
npm run changelog:minor # x.y.x
npm run changelog:major # y.x.x
npm run changelog:patch # x.x.y
npm run changelog:alpha # x.x.x-alpha.0
```
