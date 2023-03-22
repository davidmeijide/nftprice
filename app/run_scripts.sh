#!/bin/bash

# Run the updateScript.php file and save the output to a log file
/usr/local/bin/php /var/www/html/src/updateScript.php >> /var/www/html/logs/updateScript.log

# Run the alertCheckScript.php file and save the output to a log file
/usr/local/bin/php /var/www/html/src/alertCheckScript.php >> /var/www/html/logs/alertCheckScript.log
