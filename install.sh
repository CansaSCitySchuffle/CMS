#!/bin/bash

  sudo apt-get update
  sudo apt-get install -y apache2 php5
  sudo sed -i 's,/var/www,/vagrant/features/testapp/web,' /etc/apache2/sites-available/default
  sudo sed -i 's,Listen 80,Listen 7237,' /etc/apache/httpd.conf
  sudo /etc/init.d/apache2 restart
