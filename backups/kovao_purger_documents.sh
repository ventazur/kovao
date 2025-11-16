#! /bin/bash

PURGES="$(/usr/bin/php /var/www/kovao.com/main/public/index.php cli purger_documents)"
DATE="$(date)"

echo "${DATE}" "${PURGES}" >> /home/seb/logs/kovao_purges_documents.log
