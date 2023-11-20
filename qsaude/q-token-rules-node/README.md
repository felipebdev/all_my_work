# q-token-rules-node

## Ajustes e melhorias

O projeto ainda está em desenvolvimento e as próximas atualizações serão voltadas nas seguintes tarefas:

- [ ] `<task>`

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:

- Você instalou a versão `nodejs16`
- Você instalou a versão mais recente de `npm`
- Você tem uma máquina `<Windows / Linux / Mac>`

## ☕ Clonando e Instalando `<q-token-rules-node>`

Para clonar o repositório `<q-token-rules-node>`, siga estas etapas:

```bash
git clone <vcs>/q-token-rules-node.git
```

Para instalar as dependências `<q-token-rules-node>`, siga estas etapas:

```bash
npm run install
```

## ⚙️ Configurando ambiente `<q-token-rules-node>`

Utilizar o arquivo `example.env` como base para a criação dos seguintes arquivos de configuração:

- `.env`
- `test.env`

> Para as configurações dos módulos definir `src/<module>/configs/<module>.config.ts`, seguindo `src/common/configs/app.config.ts` como base.

## 🚀 Usando `<q-token-rules-node>`

Para usar `<q-token-rules-node>`, siga estas etapas:

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
