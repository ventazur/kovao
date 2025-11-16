#! /bin/bash

# ---------------------------------------------------------------------
#
# KOVAO
#
# Purger les items (evaluations, blocs, variables, questions et reponses)
# effaces.
#
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
#
# Config
#
# ---------------------------------------------------------------------

PHP_EXEC=/usr/bin/php
PUBLIC_DIR=/var/www/kovao.com/main/public
LOG_FILE=/home/seb/logs/kovao_purger_items.log

# ---------------------------------------------------------------------
#
# Purger les items effaces
#
# ---------------------------------------------------------------------

PURGES="$(${PHP_EXEC} ${PUBLIC_DIR}/index.php cli purger_items)"

# ---------------------------------------------------------------------
#
# Enregistrer une trace de l'execution
#
# ---------------------------------------------------------------------

DATE="$(date)"

echo "${DATE}" ": ${PURGES}" >> ${LOG_FILE}
