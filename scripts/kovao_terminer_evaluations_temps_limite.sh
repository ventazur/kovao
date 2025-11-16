#! /bin/bash

# ---------------------------------------------------------------------
#
# KOVAO
#
# Terminer les evaluations dont le temps limite des etudiants est echu.
#
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
#
# Config
#
# ---------------------------------------------------------------------

PHP_EXEC=/usr/bin/php
PUBLIC_DIR=/var/www/kovao.com/main/public
LOG_FILE=/home/seb/logs/kovao_terminer_evaluations_temps_limite.log

# ---------------------------------------------------------------------
#
# Terminer les evaluations
#
# ---------------------------------------------------------------------

DATE="$(date)"

${PHP_EXEC} ${PUBLIC_DIR}/index.php cli terminer_evaluations_temps_limite

# PURGES="$(/usr/bin/php /var/www/kovao.dev/main/public/index.php cli terminer_evaluations_expirees)"
# DATE="$(date)"
# echo "${DATE}" "${PURGES}" >> /home/seb/logs/kovao_purges_items_dev.log
# touch /home/seb/logs/kovao_terminer_evaluations_temps_limite.log

# ---------------------------------------------------------------------
#
# Enregistrer une trace de l'execution
#
# ---------------------------------------------------------------------

# echo "${DATE}" >> ${LOG_FILE}
touch ${LOG_FILE}
