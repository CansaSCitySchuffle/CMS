#!/bin/bash

if [ "$1" = "--help" ]; then
  echo "usage:"
  echo "  ./install.sh [sourcedir]"
  exit 0
fi

if [ -z "$1" ]
  then
  sourcedir="$(pwd)/features/testapp/web"
else
  sourcedir=$1
fi


echo "linking $sourcedir with apache"
port=7237
  sudo apt-get update
  sudo apt-get install -y apache2 php5
  sudo sed -i "s,/var/www,$sourcedir," /etc/apache2/sites-available/default
  sudo sed -i "s,*:80,*:$port," /etc/apache2/sites-available/default
  sudo sed -i "s,Listen 80,Listen $port,i" /etc/apache2/ports.conf
  sudo /etc/init.d/apache2 restart
