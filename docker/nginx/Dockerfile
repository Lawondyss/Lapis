FROM nginx:1

# configuration, substituts an environment variables inside of file
COPY ./docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template

WORKDIR /srv

# static files for direct serving
COPY ./static ./www/static
