FROM node:14-alpine

WORKDIR /usr/src/app

RUN npm install -g typescript
COPY package*.json ./
RUN npm install

COPY . .

RUN npm run build

CMD ["node", "dist/src/app.js"]