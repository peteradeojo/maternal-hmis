FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteractive
ENV NODE_VERSION=16.20.2
ENV TZ=UTC+1

RUN apt-get update

RUN apt-get install -y software-properties-common curl

RUN add-apt-repository ppa:ondrej/php -y

RUN apt install -y php8.2-cli php8.2-bcmath php8.2-mbstring \
    php8.2-redis php8.2-mysql php8.2-sqlite3 php8.2-zip php8.2-curl \
    php8.2-xml php8.2-readline php8.2-gd php8.2-imagick

COPY ./getcomposer.sh /tmp/getcomposer.sh

RUN chmod +x /tmp/getcomposer.sh
RUN /tmp/getcomposer.sh

# Setup Node with NVM
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash
ENV NVM_DIR=/root/.nvm

RUN . "$NVM_DIR/nvm.sh" && nvm install ${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm use v${NODE_VERSION}
RUN . "$NVM_DIR/nvm.sh" && nvm alias default v${NODE_VERSION}
ENV PATH="/root/.nvm/versions/node/v${NODE_VERSION}/bin/:${PATH}"

RUN node -v
RUN npm -v && npm i -g yarn

RUN apt install supervisor -y

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

ADD package.json yarn.lock /app/
RUN yarn install

ADD composer.json composer.lock /app/
RUN composer install --no-scripts --no-interaction --no-autoloader --no-dev --prefer-dist

ADD . ./

RUN yarn build
RUN composer dump-autoload

EXPOSE 8000

RUN chmod +x /app/start-container.sh

CMD ["/app/start-container.sh"]
