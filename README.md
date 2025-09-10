# NFTprice
## Description
NFTprice is a tool that sends messages via Telegram when an NFT reaches the desired price. It allows you to filter by attributes and price.
[NFTprice.app](https:/nftprice.app/)
You can try the app by searching for “degods” in the search engine once you register.
You can also search for a collection from [magiceden](https://magiceden.io/) and paste the link directly into the search engine.
It is automated to the point that when a user searches for a collection that is not in the database, the new collection is inserted. From then on, all users will have more instant and suggested access in the search.
## Technologies
- Ubuntu Server 22
- Docker container - docker-compose
    + Apache2
    + MySQL
- PHP 7.2
- JavaScript
- CSS & Bootstrap 5
## Installation / Start-up
### Requirements
- ^PHP 7.2
- MySQL
- Docker & docker-compose
### Local installation
As the installation will be done locally, the SSL section is omitted.
1. Create a folder `mkdir nftprice && cd nftprice`
2. Create a `Dockerfile` file with the following content:
```
FROM toasterlint/php-apache-mysql
RUN docker-php-ext-install pdo pdo_mysql
# We install cron
RUN apt update && apt install -y cron
# We copy the file to the container
COPY cron/scripts-cron /etc/cron.d/cron
# We give it execution permissions
RUN chmod 0644 /etc/cron.d/cron
# We start the process
RUN crontab /etc/cron.d/cron
# Associate the output with the docker standard so you can view logs with ‘docker logs nftprice-server -t’
RUN ln -s /dev/stdout /var/log/cron
RUN sed -i ‘s/^exec /service cron start\n\nexec /’ /usr/local/bin/apache2-foreground
```
3. Create a `docker-compose.yml` file
```
version: “3”
 
services:
   
  nftprice-server:
    container_name: nftprice-server
    #image: toasterlint/php-apache-mysql
    build: .
    volumes:
      - ./php/src/:/var/www/html
    ports:
      - 8000:80
  nftprice-db:
    container_name: nftprice-db
    image: mysql
    restart: always
    environment:
     MYSQL_ROOT_PASSWORD: root
     MYSQL_DATABASE: nftprice
     MYSQL_USER: user
     MYSQL_PASSWORD: abc123.
volumes:
- ./dbdata:/var/lib/mysql
ports:
- 9906:3306
```
Run it with `docker-compose up -d`
To stop the container: `docker-compose down`
4. Navigate to `php/src/` and clone this repository `git clone https://gitlab.iessanclemente.net/dawd/a20davidmd.git`
5. Database migration:
1. Go to (http://localhost:8080/index.php)[http://localhost:8080/index.php]
    2. Log in with the same credentials as in `docker-compose.yml`
In this case:
- Server: nftprice-db
- Username: root
- Password: root
3. Select the default database `nftprice`
    4. Go to the `import` tab -> `File to import` -> `Browse`
5. Select the file `/app/sql/migracion.sql`
6. Create a folder `/private` containing the following files:
  + `config.php` - Contains the DB passwords and users, as well as other environment variables.
  ```
  $DB_HOST = “nftprice-db”;
  $DB_NAME = “nftprice”;
  $DB_USER = “root”;
  $DB_PASSWORD = “root”;
  $BASE_URL = ‘localhost:8000’;
  ```
  + `API_KEYS.php` - Contains the private keys to access the APIs used:
```
  $TELEGRAM_API_KEY = ##############;
  $CMC_API_KEY = ############;
  ```
7. Create the `.htaccess` file:
```
DirectoryIndex home.php
RewriteEngine on
RewriteCond %{THE_REQUEST} /([^.]+)\.php [NC]
RewriteRule ^ /%1 [NC,L,R]
  RewriteCond %{REQUEST_FILENAME}.php -f
  RewriteRule ^ %{REQUEST_URI}.php [NC,L]
  RewriteCond %{REQUEST_URI} !^/public/
  RewriteRule ^(.*)$ /public/$1 [L,QSA]
  <IfModule mod_security.c>
  SecFilterEngine Off
  SecFilterScanPOST Off
  </IfModule>
  ```
6. Access NFTprice at (http://localhost:8000)[http://localhost:8000/]
## Usage
- Create alerts for your most desired NFTs and receive notifications on your Telegram with less than a 3-minute delay.
- Alerts allow you to filter by attributes and price.
## About the author
David Meijide is a web programmer with a clear preference for back-end and complex data management.
The technologies he handles best are PHP / Laravel, JavaScript, MySQL, Java, and Python.
Contact: (davidmeijided@gmail.com)[davidmeijided@gmail.com]
## License
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

## Table of Contents
1. Preliminary Project
    * 1.1. [Idea](doc/templates/1_idea.md)
    * 1.2. [Requirements](doc/templates/2_requirements.md)
2. [Design](doc/templates/3_design.md)
3. [Implementation](doc/templates/4_implementation.md)
## Contribution guide
You can contribute to the project by adding new features such as an NFT collection explorer, implementing alerts in Discord, or bug fixes and optimizations.
