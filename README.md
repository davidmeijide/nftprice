# NFTprice

## Descripción

NFTprice es una herramienta que envía mensajes via Telegram cuando un NFT llega al precio deseado. Permite filtrar  por atributos y por precio.

[NFTprice.app](https:/nftprice.app/)

## Tecnologías

- Ubuntu Server 22
- Contenedor Docker - docker-compose
    + Apache2
    + MySQL
- PHP 7.2
- JavaScript
- CSS & Bootstrap 5

## Instalación / Puesta en marcha

### Requisitos

- ^PHP 7.2
- MySQL
- Docker & docker-compose

### Instalación en local

Como la instalación se realizará en local, se omite la sección SSL.
1. Crea una carpeta `mkdir nftprice && cd nftprice` 
2. Crea un archivo `Dockerfile` con el siguiente contenido:
```
FROM toasterlint/php-apache-mysql
RUN docker-php-ext-install pdo pdo_mysql

# Instamos cron
RUN apt update && apt install -y cron

# Copiamos el archivo al contenedor
COPY cron/scripts-cron /etc/cron.d/cron

# Le damos permisos de ejecución
RUN chmod 0644 /etc/cron.d/cron

#Arrancamos el proceso
RUN crontab /etc/cron.d/cron

# Asociamos la salida a la estándar de docker para poder ver logs con 'docker logs nftprice-server -t'
RUN ln -s /dev/stdout /var/log/cron
RUN sed -i 's/^exec /service cron start\n\nexec /' /usr/local/bin/apache2-foreground

```

3. Crea un archivo `docker-compose.yml`
```
version: "3"
 
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
     MYSQL_USER: usuario
     MYSQL_PASSWORD: abc123.
    volumes:
     - ./dbdata:/var/lib/mysql
    ports:
     - 9906:3306
```

Ejecútalo con `docker-compose up -d`
Para detener el contenedor: `docker-compose down`

4. Navega a `php/src/` y clona este repositorio `git clone https://gitlab.iessanclemente.net/dawd/a20davidmd.git`

5. Migración de la Base de Datos:
    1. Ve a (http://localhost:8080/index.php)[http://localhost:8080/index.php]
    2. Loguéate con las mismas credenciales del `docker-compose.yml`  
    En este caso:
        - Server: nftprice-db
        - Username: root
        - Password: root
    3. Selecciona la Base de Datos creada por defecto `nftprice`
    4. Ve a la pestaña `import` -> `File to import` -> `Browse`
    5. Selecciona el archivo `/app/sql/migracion.sql`

6. Crea una carpeta `/private` que contenga los siguientes archivos:
  + `config.php` - Contiene las contraseñas y usuarios de la BD, así como otras variables de entorno.
  ```
  $DB_HOST = "nftprice-db";
  $DB_NAME = "nftprice";
  $DB_USER = "root";
  $DB_PASSWORD = "root";

  $BASE_URL = 'localhost:8000';
  ```
  + `API_KEYS.php` - Contiene las claves privadas para acceder a las API's que se utilizan:
  ```
  $TELEGRAM_API_KEY = ##############;
  $CMC_API_KEY = ############;
  ```
7. Crea el archivo `.htaccess`:

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

6. Accede a NFTprice a en (http://localhost:8000)[http://localhost:8000/]

## Uso

- Crea alertas de tus NFT's más deseados y recibe notificaciones en tu Telegram con menos de 3 minutos de retraso.
- Las alertas permiten filtrar por atributos y precio.

## Sobre el autor

David Meijide es un programador web con una preferencia clara por el back-end y la complejidad del manejo de datos.
Las tecnologías que mejor maneja son PHP / Laravel, JavaScript, MySQL, Java y Python.

Contacto: (davidmeijided@gmail.com)[davidmeijided@gmail.com]

## Licencia

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)


## Índice

1. Anteproyecto
    * 1.1. [Idea](doc/templates/1_idea.md)
    * 1.2. [Necesidades](doc/templates/2_necesidades.md)
2. [Diseño](doc/templates/3_deseño.md)
3. [Implantación](doc/templates/4_implantacion.md)

## Guía de contribución

Se puede contribuir al proyecto añadiendo nuevas funcionalidades como un explorador de colecciones NFT, implementar alertas en Discord, o corrección de errores y optimizaciones.

## Links

- [API MagicEden](https://api.magiceden.dev/) - Los datos de precios y disponibilidad se obtienen de esta API.
- [API HowRare](https://howrare.is/api) - Información sobre rareza y atributos de cada NFT.
- [API Telegram](https://core.telegram.org/bots/api) - Envío automatizado de mensajes a los usuarios
- [API CoinMarketCap - Cambios de divisa](https://coinmarketcap.com/api/documentation/v1/#section/Quick-Start-Guide)
- [VPS en OVH Cloud](https://www.ovhcloud.com/es/)
- [Certificado SSL - Let's Encrypt](https://letsencrypt.org/)