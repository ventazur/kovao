#! /bin/bash

PURGES="$(/usr/bin/php /var/www/kovao.com/main/public/index.php cli purger_sessions)"
DATE="$(date)"

echo "${DATE}" "${PURGES}" "purge(s)" >> /home/seb/logs/kovao_purges_sessions.log
