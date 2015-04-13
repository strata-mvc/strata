---
layout: docs
title: Server
permalink: /docs/server/
---

WMVC ships with a server on which you can test your application. This server is not meant to be used in production. It is packaged only as a mean to lower the amount of prerequisites needed to start developping.

## Starting the server

Using the command line, run the `server` command from your project's base directory.

~~~ sh
$ bin/mvc server
~~~

It will kickoff a running instance of your Wordpress installation availlable at `http://127.0.0.1:5454/`. There is no way of changing this url for the moment. Also, instances launched with the bin/mvc script will always load the development.ini variables.

## Customizing the server

We ship a simple basic [Vagrant](http://vagrantup.com) configuration with the solution. You can modify the `VagrantFile` and the `bin\vagrant\provision.sh` as you wish so the image better suits your needs.

There are a few settings that must be made to your image to make sure everything runs properly:

* Apache's root directory and DocumentRoot must be : /vagrant/webroot/
* Apache's logs must point to : /vagrant/log
* Apache's user and group must be : www-data:vagrant

Here is the code we use in our default `provision.sh` file to set up these variables. You can add it to your own provision file.

~~~ sh
sed -i "s#DocumentRoot /var/www/public#DocumentRoot /vagrant/webroot/#g" /etc/apache2/sites-available/000-default.conf
sed -i "s#Directory /var/www/#Directory /vagrant/webroot/#g" /etc/apache2/apache2.conf
sed -i "s#\${APACHE_LOG_DIR}#/vagrant/log#g" /etc/apache2/sites-available/000-default.conf
sed -i "s#APACHE_RUN_USER=www-data#APACHE_RUN_USER=vagrant#g" /etc/apache2/envvars
sed -i "s#APACHE_RUN_GROUP=www-data#APACHE_RUN_GROUP=vagrant#g" /etc/apache2/envvars
~~~
