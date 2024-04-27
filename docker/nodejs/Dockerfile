FROM node:lts

WORKDIR /srv

# configs
COPY .env ./
COPY svelte.config.js ./
COPY tsconfig.json ./
COPY vite.config.ts ./

# install dependencies include dev
COPY package.json ./
COPY yarn.lock ./
RUN yarn --dev --frozen-lockfile && yarn cache clean

# application
COPY ./frontend ./frontend

# generate svelte-kit files
RUN yarn check-kit

# expose port for Vite
EXPOSE 3000

# run for development with host for access to web app from outside
CMD ["yarn", "dev", "--host", "0.0.0.0"]