#! /bin/bash

# ---------------------------------------------------------------------
#
# KOVAO
#
# Purger les soumissions effacees.
#
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
#
# Config
#
# ---------------------------------------------------------------------

PHP_EXEC=/usr/bin/php
PUBLIC_DIR=/var/www/kovao.com/main/public
LOG_FILE=/home/seb/logs/kovao_purger_soumissions_effacees.log

# ---------------------------------------------------------------------
#
# Purger les soumissions effacees
#
# ---------------------------------------------------------------------

#PURGES="$(/usr/bin/php /var/www/kovao.com/main/public/index.php cli purger_soumissions)"
PURGES="$(${PHP_EXEC} ${PUBLIC_DIR}/index.php cli purger_soumissions)"

# ---------------------------------------------------------------------
#
# Enregistrer une trace de l'execution
#
# ---------------------------------------------------------------------

DATE="$(date)"

echo "${DATE}" ": ${PURGES}" "purge(s)" >> ${LOG_FILE}
