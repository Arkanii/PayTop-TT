# PayTop Technical Test API

[![gitmoji badge](https://img.shields.io/badge/gitmoji-%20😜%20😍-FFDD67.svg?style=flat-square)](https://github.com/carloscuesta/gitmoji)

> A technical test for [PayTop](https://corporate.paytop.com/) company.

## Requirements

- Docker and docker-compose
- PHP 8 
- [Symfony CLI](https://symfony.com/download)
- Composer

## How to install ?

```shell
$ git clone https://github.com/Arkanii/PayTop-TT.git
$ cd PayTop-TT
$ make init
```

After initialization, you just need to run :

```shell
$ make start
```

And all is started for you ! :tada:

## How to open ... ?

- the website : [here](https://127.0.0.1:8000/docs)
- webhook.site website: [here](https://webhook.site/#!/1e943e77-c1db-4ae7-8969-2fc51e5eee5c)
- the RabbitMQ admin interface :

```shell
$ make open-rabbitmq-admin
```

## How to ... ?

- see all available commands :

```shell
$ make help
```

- consume all RabbitMQ messages :

```shell
$ make consume
```
