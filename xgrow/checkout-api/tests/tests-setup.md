# Configuração dos Testes 

## Introdução

Dado que esse projeto **utiliza migrations de outro projeto**, tornou-se
necessária adotar soluções não canônicas para o funcionamento dos testes.

O projeto contendo as migrations é o `backoffice` e utiliza, no momento,
o Laravel versão 8. Com base nisso, foi criada uma estratégia que se utiliza
do "Squash" de Migrations (disponível no Laravel 8 e posteriores: 
https://laravel.com/docs/8.x/migrations#squashing-migrations).

## Obtenção do DB schema

1. fazer o "squash" no `backoffice`, que gera um dump da estrutura em SQL

2. importar dump

   - para Laravel 8 e posteriores:
     - copiar o "squash" gerado para o projeto em que estiver trabalhando 
     (colocando o arquivo em `database/schema).
   - para Laravel 7 e anteriores:
     - Criar uma Migration no projeto que estiver trabalhando e carregar 
     o dump do schema.

Passa a ser responsabilidade de cada projeto manter o schema do banco de 
dados de teste sincronizado com o do backoffice.

Isso deve ocorrer periodicamente ou sempre que for criada uma nova migration 
no `backoffice`:

- gera-se um novo squash do backoffice
- o arquivo de squash é substituido no projeto de trabalho
- caso seja uma nova feature sendo entregue, o schema atualizado deve ser 
enviado junto (preferencialmente com testes :-)

## Seeding

1. fazer um dump apenas com os dados indispensáveis para uma única plataforma.

2. Copiar o dump de dados e criar um Seeder que irá popular o DB
   (via `DB::unprepared`)

Passa a ser altamente recomendável utilizar Seeders para popular os demais 
dados do DB.

## PHPUnit

O PHPUnit irá procurar pelo aquivo de configuração `phpunit.xml` ou 
`phpunit.xml.dist` (nessa ordem) no diretório de projeto, **caso o primeiro 
exista**, o segundo **não irá ser carregado**.

Dessa forma, o arquivo `phpunit.xml.dist` DEVE ser utilizado para 
configurações padrão do PHPUnit, caso o desenvolvedor queira criar 
configurações locais personalizadas, basta copiar o `phpunit.xml.dist` para 
`phpunit.xml` e alterar conforme desejado (o arquivo `phpunit.xml` NÃO DEVE 
ser adicionado ao Git). 
