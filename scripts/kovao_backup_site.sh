#! /bin/bash

# jour de la semaine
DATE="$(date '+%u')"

SOURCEDIR=/var/www/kovao.com/main/public
DESTDIR=/home/seb/backup

FILENAME=kovaosite
FILENAMEDATE=${FILENAME}_${DATE}

# --- NE PAS MODIFIER CI-BAS ---

cd $SOURCEDIR
tar Jcf $DESTDIR/$FILENAMEDATE.tar.xz . 
