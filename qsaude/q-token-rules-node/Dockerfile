FROM node:16-alpine as builder

RUN mkdir -p /usr/src/app

WORKDIR /usr/src/app

RUN npm install -G @nestjs/cli

COPY package.json ./

RUN npm install

COPY . .

RUN npm run build

FROM node:16-alpine

RUN apk update && apk add --upgrade apk-tools && apk upgrade --available
RUN apk --no-cache add curl

RUN mkdir -p /usr/src/app
RUN mkdir -p /usr/src/app/dist/data
RUN chown -R node:node /usr/src/app
RUN chmod -Rf 775 /usr/src/app/dist/data

WORKDIR /usr/src/app

USER node

COPY --from=builder /usr/src/app/dist ./dist
COPY --from=builder /usr/src/app/node_modules ./node_modules

CMD [ "node", "dist/main" ]