# Aplicação Checkout
---------------

## Enviroment: Develop

## Instalar depenências
  kubectl apply --namespace=checkout-dev -f checkout-config-env.yaml
  kubectl apply --namespace=checkout-dev -f checkout-secrets-env.yaml

### Instalar chart
  kubectl create namespace checkout-dev
  kubectl apply --namespace=checkout-dev -f registry/digitalocean.yaml
  helm install --namespace=checkout-dev --create-namespace --values=values/values.yaml --values=values/dev/values.yaml --values=values/dev/secrets.yaml checkout ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=checkout-dev --create-namespace --values=values/values.yaml --values=values/dev/values.yaml --values=values/dev/secrets.yaml checkout ./chart

## Enviroment: Production

### Instalar chart
  kubectl create namespace checkout-prod
  kubectl apply --namespace=checkout-prod -f registry/digitalocean.yaml
  helm install --namespace=checkout-prod --create-namespace --values=values/values.yaml --values=values/prod/values.yaml --values=values/prod/secrets.yaml checkout ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=checkout-prod --create-namespace --values=values/values.yaml --values=values/prod/values.yaml --values=values/prod/secrets.yaml checkout ./chart