FROM node:22.5.1

WORKDIR /app

COPY package*.json ./

RUN npm ci --ignore-scripts

COPY . .
