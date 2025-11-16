#! /bin/bash

hash=$(/usr/bin/git log --pretty="%h" -n1 HEAD)
echo $hash
