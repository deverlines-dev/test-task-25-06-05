FROM node:23-alpine3.20

RUN npm install -g npm@11

RUN mkdir -p /run; \
    chown node:node /run && chmod +x /run

USER node

WORKDIR /var/www/laravel

CMD ["sleep", "infinity"]