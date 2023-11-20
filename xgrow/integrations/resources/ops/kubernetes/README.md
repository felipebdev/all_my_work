# Aplicação Integrations
---------------

## Enviroment: Develop

### Instalar chart
  kubectl create namespace integrations-dev
  kubectl apply --namespace=integrations-dev -f registry/digitalocean.yaml
  helm install --namespace=integrations-dev --create-namespace --values=values/dev/values.yaml --values=values/dev/secrets.yaml integrations ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=integrations-dev --create-namespace --values=values/dev/values.yaml --values=values/dev/secrets.yaml integrations ./chart

## Enviroment: Production

### Instalar chart
  kubectl create namespace integrations-prod
  kubectl apply --namespace=integrations-prod -f registry/digitalocean.yaml
  helm install --namespace=integrations-prod --create-namespace --values=values/prod/values.yaml --values=values/prod/secrets.yaml integrations ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=integrations-prod --create-namespace --values=values/prod/values.yaml --values=values/prod/secrets.yaml integrations ./chart