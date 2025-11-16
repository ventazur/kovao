#! /bin/bash

# ---------------------------------------------------------------------
#
# KOVAO
#
# Terminer les evaluations disponibles des enseignants lorsque
# la date/heure de fin est atteinte.
#
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
#
# Config
#
# ---------------------------------------------------------------------

PHP_EXEC=/usr/bin/php
PUBLIC_DIR=/var/www/kovao.com/main/public
LOG_FILE=/home/seb/logs/kovao_terminer_evaluations.log

# ---------------------------------------------------------------------
#
# Terminer les evaluations
#
# ---------------------------------------------------------------------

DATE="$(date)"

${PHP_EXEC} ${PUBLIC_DIR}/index.php cli terminer_evaluations_expirees

# /usr/bin/php /var/www/kovao.com/main/public/index.php cli terminer_evaluations_expirees
# PURGES="$(/usr/bin/php /var/www/kovao.dev/main/public/index.php cli terminer_evaluations_expirees)"

# ---------------------------------------------------------------------
#
# Enregistrer une trace de l'execution
#
# ---------------------------------------------------------------------

# echo "${DATE}" >> ${LOG_FILE}
touch ${LOG_FILE}
