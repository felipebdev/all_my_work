## Intruções:

### Problemas com SSH:

* Gerar chave ssh e adicionar à conta: 
 
https://support.atlassian.com/bitbucket-cloud/docs/set-up-an-ssh-key/

* Iniciar o ssh agent (Git Bash (Windows) e Terminal (Linux)):

```eval `ssh-agent -s```

* Checkar se a chave está sendo carregada pelo ssh agent:

```ssh-add -l```

* Adicionar chave caso não esteja sendo carregada:

```ssh-add /caminho/da/chave-privada```

### Instalar localmente pela primeira vez:

* Necessário php 7.4+
* Copiar arquivo ```.env.example``` e renomear a cópia para ```.env```
* Criar banco de dados com o nome definido na variável ```DB_DATABASE``` do arquivo ```.env```
* Executar os seguintes comandos:

```composer install``` ou ```php composer.phar install```

```php artisan key:generate```

```php artisan migrate```

```php artisan db:seed```

```npm install``` ou ```yarn install```

### Rodar o projeto:

```php artisan serve```

### Compilar o frontend:

```npm run production``` ou ```yarn run production```

### Auditoria:

##### .env variáveis

```
AUDITING_ENABLED=true
AUDIT_QUEUE=true
AUDIT_HOST=localhost:9200
AUDIT_INDEX=laravel_auditing
AUDIT_TYPE=audits
AUDIT_USER=
AUDIT_PASSWORD=
```
### Criação de slug nas plataformas:

```
APP_URL_LEARNING_AREA=url.com
```
