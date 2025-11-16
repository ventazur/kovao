#! /bin/bash

repertoires='controllers models helpers views config'

for repertoire in $repertoires
do
    find application/$repertoire *.php -exec php -l {} \;
done
