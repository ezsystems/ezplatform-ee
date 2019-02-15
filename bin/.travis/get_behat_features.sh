#!/bin/bash

bin/behat --suite=$1 --list-scenarios | awk '{ gsub(/:[0-9]+/,"",$1); print $1 }' | uniq -c | sort | awk '{ print $2 }'