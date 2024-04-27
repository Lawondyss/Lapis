FROM node:lts-alpine AS builder

WORKDIR /app

COPY frontend ./frontend
COPY .env ./
COPY package.json ./
COPY svelte.config.js ./
COPY tsconfig.json ./
COPY vite.config.ts ./
COPY yarn.lock ./

RUN yarn --dev --frozen-lockfile \
 && yarn cache clean

RUN yarn build


FROM nginx:1-alpine-slim

# configuration
COPY docker/preview/nginx.conf /etc/nginx/nginx.conf

WORKDIR /srv

# builded files for preview
COPY --from=builder /app/build ./www

