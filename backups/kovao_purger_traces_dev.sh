#! /bin/bash

PURGES="$(/usr/bin/php /var/www/kovao.dev/main/public/index.php cli purger_traces)"
DATE="$(date)"

echo "${DATE}" "${PURGES}" "purge(s)" >> /home/seb/logs/kovao_purges_traces_dev.log
