#!/usr/bin/env sh

set -e

# Instalação do pacote Java JDK e utilitários utilizados no provisionamento
#apk update && apk add lsof curl bash openjdk8-jre
yum -y update && yum -y install lsof curl wget java-1.8.0-openjdk

# Instalação de pacote de fontes do windows
rpm -Uvh /tmp/msttcore-fonts-2.0-3.noarch.rpm

# Configuração do pacote de línguas pt_BR
localedef pt_BR -i pt_BR -f ISO-8859-1

# Download do Solr, versão 6.1.0
# SOLR_URL=https://archive.apache.org/dist/lucene/solr/6.1.0/solr-6.1.0.tgz
# wget -v $SOLR_URL -O /tmp/solr-6.1.0.tgz

# Instalação do Apache Solr 6.1
tar -xf /tmp/solr-6.1.0.tgz -C /tmp

cp -Rf /tmp/solr-6.1.0 /opt/solr

# Remover arquivos temporários
yum clean all

# Configuração de permissões de execução no script de inicialização do container
chmod +x /entrypoint.sh

exit 0
