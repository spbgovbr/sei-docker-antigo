#!/usr/bin/env bash

set -e

yum clean all
yum -y  update

# Instalação dos componentes básicos do servidor web apache
yum -y install httpd24u memcached openssl wget curl unzip gcc java-1.8.0-openjdk libxml2 cabextract xorg-x11-font-utils fontconfig mod_ssl

# Instalação do PHP e demais extenções necessárias para o projeto
yum -y install epel-release
wget https://mirror.webtatic.com/yum/el6/latest.rpm
rpm -Uvh latest.rpm
yum -y update

# Instalação do PHP e demais extenções necessárias para o projeto
yum -y install php56w php56w-common php56w-cli php56w-pear php56w-bcmath php56w-gd php56w-gmp php56w-imap php56w-intl php56w-ldap php56w-mbstring php56w-mysqli \
    php56w-odbc php56w-pdo php56w-pecl-apcu php56w-pspell php56w-zlib php56w-snmp php56w-soap php56w-xml php56w-xmlrpc php56w-zts php56w-devel \
    php56w-pecl-apcu-devel php56w-pecl-memcache php56w-calendar php56w-shmop php56w-intl php56w-mcrypt 

# Instalação do componentes UploadProgress
pecl install uploadprogress-1.0.3.1 && \
echo "extension=uploadprogress.so" >> /etc/php.d/uploadprogress.ini

# Configuração do pacote de línguas pt_BR
localedef pt_BR -i pt_BR -f ISO-8859-1

# Configuração das bibliotecas de fontes utilizadas pelo SEI
cd /usr/local/bin
rpm -Uvh msttcore-fonts-2.0-3.noarch.rpm
rm -f msttcore-fonts-2.0-3.noarch.rpm

yum -y clean all

exit 0

