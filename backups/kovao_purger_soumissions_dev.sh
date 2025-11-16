#! /bin/bash

PURGES="$(/usr/bin/php /var/www/kovao.dev/main/public/index.php cli purger_soumissions)"
DATE="$(date)"

echo "${DATE}" "${PURGES}" "purge(s)" >> /home/seb/logs/kovao_purges_soumissions_dev.log
