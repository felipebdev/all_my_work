# Aplicação Platform
---------------

## Enviroment: Develop

### Instalar chart
  kubectl create namespace platform-dev
  kubectl apply --namespace=platform-dev -f registry/digitalocean.yaml
  helm install --namespace=platform-dev --create-namespace --values=values/values.yaml --values=values/dev/values.yaml --values=values/dev/secrets.yaml platform ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=platform-dev --create-namespace --values=values/values.yaml --values=values/dev/values.yaml --values=values/dev/secrets.yaml platform ./chart

## Enviroment: Production

### Instalar chart
  kubectl create namespace platform-prod
  kubectl apply --namespace=platform-prod -f registry/digitalocean.yaml
  helm install --namespace=platform-prod --create-namespace --values=values/values.yaml --values=values/prod/values.yaml --values=values/prod/secrets.yaml platform ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=platform-prod --create-namespace --values=values/values.yaml --values=values/prod/values.yaml --values=values/prod/secrets.yaml platform ./chart