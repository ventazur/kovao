#! /bin/bash

PURGES="$(/usr/bin/php /var/www/kovao.dev/main/public/index.php cli purger_sessions)"
DATE="$(date)"

echo "${DATE}" "${PURGES}" "purge(s)" >> /home/seb/kovao_dev_purges_sessions.log
