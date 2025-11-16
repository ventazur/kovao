#! /bin/bash

# ---------------------------------------------------------------------
#
# KOVAO
#
# Purger les sessions (fureteur) desuettes.
#
# ---------------------------------------------------------------------

# ---------------------------------------------------------------------
#
# Config
#
# ---------------------------------------------------------------------

PHP_EXEC=/usr/bin/php
PUBLIC_DIR=/var/www/kovao.com/main/public
LOG_FILE=/home/seb/logs/kovao_purger_sessions.log

# ---------------------------------------------------------------------
#
# Purger les soumissions effacees
#
# ---------------------------------------------------------------------

PURGES="$(${PHP_EXEC} ${PUBLIC_DIR}/index.php cli purger_sessions)"

# ---------------------------------------------------------------------
#
# Enregistrer une trace de l'execution
#
# ---------------------------------------------------------------------

DATE="$(date)"

echo "${DATE}" ": ${PURGES}" "purge(s)" >> ${LOG_FILE}
