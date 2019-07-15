# Account

Account contains an OAuth2 Authentication & Identity server. Its intended usage is for providing single sign on and a user resource sharing framework.

The project contains a docker-compose file for easily setting up a local development environment. It contains an `nginx` webserver, a `mysql` server and a `php` server, all based on default images.

## Requirements

A working composer install, docker, and PHP

## Configuration

First, configure the environment.

```bash
cp env.template .env
```

Next, edit the .env file and supply your configuration. Create your OAuth keys with the instruction provided at https://oauth2.thephpleague.com/installation/

## Install

```bash
composer install
docker-compose up -d --force-recreate
```

This should install your application and start the webservers in the background.

## Enjoy

The webserver should be running on http://localhost:8000
