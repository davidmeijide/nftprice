# FASE DE IMPLANTACIÓN

## Instalación / Puesta en marcha

### Requisitos

- ^PHP 7.2
- MySQL
- Docker & docker-compose
- Ubuntu Server ^20
- Contenedor Docker - docker-compose
- Servidor en la nube OVH cloud (VPS - servidor virtual privado)

* Configuración inicial seguridade: devasa, control usuarios, rede.
Firewall activado con los puertos 443 (https), 80 (http) y 22 (SSH) abiertos.
Permisos de grupo y usuario sobre el código para el grupo www-data. 

* Carga inicial de datos en la base de datos:
    - En la carpeta `/app/sql/` ejecutar el archivo `nftprice-db.migration.sql`.
    - Comprobar que se está usando la base de datos correcta `USE nftprice`. 

* Usuarios del sistema: usuario local sudoer.
* Usuarios de la aplicación: todos tienen categoría 'user'. Se deja el campo creado para ampliaciones futuras.

### Automatización de las inserciones a la base de datos

Para mantener una imagen fiel de la realidad en nuestra aplicación, necesitamos hacer peticiones periódicas a las APIs.
Utilizaremos CRON para programar la ejecución del script.

Justo después de actualizar la base de datos, se comprueba si se ha cumplido alguna alerta y se envían los mensajes neesarios.
Estos scripts generan logs, que se pueden ver en la carpeta /app/logs/.

Para asegurarnos de que se ejecuten secuencialmente (1º `updateScript.php`, 2º `alertCheckScript.php`), ejecutaremos las órdenes desde un script en bash.
```
#!/bin/bash

# Ejecuta updateScript.php y guarda la salida en un archivo de logs
/usr/local/bin/php /var/www/html/src/updateScript.php >> /var/www/html/logs/updateScript.log

# Ejecuta alertCheckScript.php y guarda la salida en un archivo de logs
/usr/local/bin/php /var/www/html/src/alertCheckScript.php >> /var/www/html/logs/alertCheckScript.log

```
Ahora con crontab programamos su ejecución cada 1 minuto:

`crontab -e`
```
*/1 * * * * /var/www/html/run_scripts.sh > /proc/1/fd/1 2>/proc/1/fd/2
```





### Administración del sistema

* Copias de seguridad de la base de datos:

    - Primero creamos la carpeta:
        ```
        cd ~
        mkdir scripts
        cd scripts
        nano db_backup.sh
        ```

    - Diariamente. Se lanza un proceso `cron` con un script en bash:
        ```
        #!/bin/bash
        DIR=`date +%d-%m-%y`
        DEST=~/db_backups/$DIR
        mkdir $DEST

        mysqldump -h nftprice-db -u user  -p "abc123." nftprice > dbbackup.sql
        ```
    Después se le da permisos de ejecución al script: `chmod +x ~/scripts/db_backup.sh`
* Gestión de usuarios: La aplicación no tiene un apartado para administradores, por lo que todos los usuarios realizan las mismas operaciones. Dentro de la base de datos se limita el usuario a operaciones CRUD (Create, Read, Update, Delete).

* Gestión seguridad: Instalación del módulo SSL para permitir conexiones https:
En primer lugar, debemos instalar CertBot siguiendo [esta guía](https://certbot.eff.org/instructions?ws=apache&os=ubuntufocal) escogiendo la opción `sudo certbot certonly --apache`, para solo conseguir los certificados.
Una vez estén en nuestro servidor, buscamos su ubicación en `/etc/ssl/certs/`.


    En la carpeta donde está ubicado el contenedor, se crea otra carpeta `apache` que contenga los archivos que queremos clonar dentro del contenedor cuando se inicialice. 

    * `apache2.conf`: Se le añade al final `ServerName nftprice.app`
    * `default-ssl.conf`: Se añade el nombre del servidor y las rutas de los certificados:

    ```
    <IfModule mod_ssl.c>
            <VirtualHost *:443>
                    ServerAdmin webmaster@localhost
                    ServerName nftprice.app
                    DocumentRoot /var/www/html

                    ErrorLog ${APACHE_LOG_DIR}/error.log
                    CustomLog ${APACHE_LOG_DIR}/access.log combined

                    #   SSL Engine Switch:
                    #   Enable/Disable SSL for this virtual host.
                    SSLEngine on

                    #   A self-signed (snakeoil) certificate can be created by installing
                    #   the ssl-cert package. See
                    #   /usr/share/doc/apache2/README.Debian.gz for more info.
                    #   If both key and certificate are stored in the same file, only the
                    #   SSLCertificateFile directive is needed.
                    SSLCertificateFile      /etc/ssl/certs/cert1.pem
                    SSLCertificateKeyFile /etc/ssl/certs/privkey1.pem

                    #   Server Certificate Chain:
                    #   Point SSLCertificateChainFile at a file containing the
                    #   concatenation of PEM encoded CA certificates which form the
                    #   certificate chain for the server certificate. Alternatively
                    #   the referenced file can be the same as SSLCertificateFile
                    #   when the CA certificates are directly appended to the server
                    #   certificate for convinience.
                    SSLCertificateChainFile /etc/ssl/certs/fullchain1.pem

                    #SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire
                    <FilesMatch "\.(cgi|shtml|phtml|php)$">
                                    SSLOptions +StdEnvVars
                    </FilesMatch>
                    <Directory /usr/lib/cgi-bin>
                                    SSLOptions +StdEnvVars
                    </Directory>


            </VirtualHost>
    </IfModule>
    ```

* Gestión de incidencias: En caso de intrusión en la BD o en el VPS, se reiniciará la máquina desde OVH Cloud en modo rescate y se investigará la incidencia. Más tarde se hará uso de la copia de seguridad si los datos han sido borrados. 

### Información relativa al mantenimiento del sistema: 

* Corrección de errores e implementación de nuevas funcionalidades: A través de un repositorio de Gitlab, se hace un `pull` por ssh.
* El contenedor Docker se puede inicializar con versiones más modernas de MySQL o PHP si se requiere en el futuro. Con composer se actualizarán las dependencias.

## Protección de datos de carácter personal:

Solamente se almacenan correo electrónico y contraseña. Las contraseñas están encriptadas con `password_hash()`, un método de PHP que se actualiza con la versión más adecuada a medida que pasa el tiempo y disminuyen los tiempos de descifrado por fuerza bruta.


## Manual de usuario

Para usar ésta plataforma con un propósito real, es necesario tener un conocimiento del mundo de las cryptomonedas y los NFT's (Tokens no fungibles). Una buena guía introductoria es [esta](https://www.socios.com/es-es/como-y-donde-comprar-nfts/).

### FAQ:

* ¿Qué es nftprice? - Es una plataforma que te avisa en Telegram si un NFT ha llegado a cierto precio.
* ¿En qué red opera? - En la red de Solana, la segunda más grande del mundo NFT.
* ¿Cuanto cuesta? - ¡Es gratis!
* ¿Necesito una cartera de Solana? - No, solamente cuando quieras comprar un NFT en [MagicEden.io](MagicEden.io)
* ¿Hay un máximo de alertas por usuario? - Sí, 20 alertas como máximo.