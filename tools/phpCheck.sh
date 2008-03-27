#!/bin/bash

find $1 -type f -name \*.php -exec php -l {} \;
