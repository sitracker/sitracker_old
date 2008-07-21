#!/bin/bash
if [ -z $1 ]; then
echo "usage: ./indent.sh file.php";
exit
fi
if [ -z `which indent` ]; then
echo "requires GNU indent";
exit
fi
indent -bap -bbo -nbc -bl -bli0 -c33 -cd33 -ncdb -ci4 -cli0 -cp33 -cs -d0 -di1
-nfc1 -nfca -hnl -i4 -ip0 -l75 -lp -npcs -nprs -npsl -saf -sai -saw -nsc -nsob
-nss -nut $1
sed 's/< ? php/<?php/' $1
sed 's/? >/?>/' $1
