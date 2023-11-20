# Aplicação Backoffice
---------------

## Enviroment: Develop

### Instalar chart
  kubectl create namespace backoffice-dev
  kubectl apply --namespace=backoffice-dev -f registry/digitalocean.yaml
  helm install --namespace=backoffice-dev --create-namespace --values=values/values.yaml --values=values/dev/values.yaml --values=values/dev/secrets.yaml backoffice-api ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=backoffice-dev --values=values/values.yaml --values=values/dev/values.yaml backoffice-api ./chart

## Enviroment: Production

### Instalar chart
  kubectl create namespace backoffice-prod
  kubectl apply --namespace=backoffice-prod -f registry/digitalocean.yaml
  helm install --namespace=backoffice-prod --create-namespace --values=values/values.yaml --values=values/prod/values.yaml --values=values/prod/secrets.yaml backoffice-api ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=backoffice-prod --values=values/values.yaml --values=values/prod/values.yaml --values=values/prod/secrets.yaml backoffice-api ./chart