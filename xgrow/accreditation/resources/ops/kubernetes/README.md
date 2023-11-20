# Aplicação accreditation
---------------

## Enviroment: Develop

### Instalar chart
  kubectl create namespace accreditation-dev
  kubectl apply --namespace=accreditation-dev -f registry/digitalocean.yaml
  helm install --namespace=accreditation-dev --create-namespace --values=values/dev/values.yaml --values=values/dev/secrets.yaml accreditation ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=accreditation-dev --create-namespace --values=values/dev/values.yaml --values=values/dev/secrets.yaml accreditation ./chart

## Enviroment: Production

### Instalar chart
  kubectl create namespace accreditation-prod
  kubectl apply --namespace=accreditation-prod -f registry/digitalocean.yaml
  helm install --namespace=accreditation-prod --create-namespace --values=values/prod/values.yaml --values=values/prod/secrets.yaml accreditation ./chart

### Upgrade para atualizar alterações
  helm upgrade --namespace=accreditation-prod --create-namespace --values=values/prod/values.yaml --values=values/prod/secrets.yaml accreditation ./chart