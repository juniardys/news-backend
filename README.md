# News Backend
Backend API of News Magazine project

# How to run

## Production
### Dependencies
Install all dependecies below:
- [docker](https://www.docker.com)
### Setup Env
Copy `.env.docker` to `.env` and setup the configurations.
### First setup and start
Run the following command
```
make init-start
```
Project will be run in port `8080` (http://localhost:8080)
### Run app
Run the following command
```
make start
```
Project will be run in port `8080` (http://localhost:8080)
### List of command
| **Command**                   | **Description**                       |
|-------------------------------|---------------------------------------|
| make init-start               | Initialize setup and start services   |
| make start                    | Start services                        |
| make stop                     | Stop services                         |
| make restart                  | Restart services                      |
| make build                    | Build image and start services        |
| make rebuild                  | Rebuild image and start services      |
| make down                     | Remove services                       |
| make fresh-db                 | Fresh databases                       |
| make fetch-news               | Fetch news from api manually          |

## Development
### Dependencies
Install all dependecies below:
- [php](https://www.php.net/download)
- [composer](https://getcomposer.org/download/)
- [mysql](https://dev.mysql.com/doc/mysql-installation-excerpt/5.7/en/)
### Installing packages
Run the following command
```
composer install
```
### Setup Env
Copy `.env.example` to `.env` and setup the configurations.
### Generate key
Run the following command
```
php artisan key:generate
```
### Migrating DB
Run the following command
```
php artisan migrate --seed
php artisan passport:install
```
### Run app
Run the following command
```
php artisan serve
```
Project will be run in port `8000` (http://localhost:8000)

# Tech Stack
Laravel, PHP, Python, MySQL, Docker, Nginx

# Authors
- Juniardy Setiowidayoga ([@juniardys](https://github.com/juniardys))
