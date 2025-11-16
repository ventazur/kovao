#! /bin/bash

# PURGES="$(/usr/bin/php /var/www/kovao.dev/main/public/index.php cli terminer_evaluations_expirees)"
# DATE="$(date)"
# echo "${DATE}" "${PURGES}" >> /home/seb/logs/kovao_purges_items_dev.log

/usr/bin/php /var/www/kovao.dev/main/public/index.php cli terminer_evaluations_expirees

touch /home/seb/logs/kovao_terminer_evaluations_dev.log
