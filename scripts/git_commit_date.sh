#! /bin/bash

date=$(git log --pretty="%ci" -n1 HEAD)
echo $date
