#!/bin/bash

# ENVIRONMENT VARIABLES
# $SEI_HOST_URL
# $SEI_ORGAO
# $SEI_DB_USERNAME
# $SEI_DB_PASSWORD
# $SMTP_SERVER
# $SMTP_PORT

    set -e
        
    # Direciona logs para saida padrão para utilizar docker logs
    ln -sf /dev/stdout /var/log/httpd/access_log
    ln -sf /dev/stdout /var/log/httpd/ssl_access_log
    ln -sf /dev/stdout /var/log/httpd/ssl_request_log
    ln -sf /dev/stderr /var/log/httpd/error_log
    ln -sf /dev/stderr /var/log/httpd/ssl_error_log

    # Update do cacert.pem com as cadeias da ICP-Brasil
    echo "Adicionando certificados ICP-Brasil ao TrustStore."
    for f in /icpbrasil/*.crt
    do
        cat $f >> /etc/ssl/certs/cacert.pem
        echo $f " adicionado."
    done

    echo "\n"
    # Gera certificados caso necessário para desenvolvimento    
    if [ ! -d "/etc/ssl/certs/sei/" ]; then
        echo "Certificados de produção não informados! Criando certificados auto-assinados"
        mkdir /etc/ssl/certs/sei
    fi

    if [ -z "$(ls -A /etc/ssl/certs/sei)" ]; then
        cd /etc/ssl/certs/sei

        echo "Criando CA"  
        openssl genrsa -out sei-ca-key.pem 2048
        openssl req -x509 -new -nodes -key sei-ca-key.pem \
            -days 10000 -out sei-ca.pem -subj "/CN=sei-dev"
        
        echo "Criando certificados para o dominio: $SEI_HOST_URL"
        openssl genrsa -out sei.key 2048
        openssl req -new -nodes -key sei.key \
            -days 10000 -out sei.csr -subj "/CN=$SEI_HOST_URL"
        openssl x509 -req -in sei.csr -CA sei-ca.pem \
            -CAkey sei-ca-key.pem -CAcreateserial \
            -out sei.crt -days 10000 -extensions v3_req
    
        cat /etc/ssl/certs/sei/sei-ca.pem >> /etc/ssl/certs/cacert.pem
        echo "Adicionada nova CA ao TrustStore\n"
    fi
    
    cd /etc/ssl/certs/sei
    cp sei.crt /etc/pki/tls/certs/sei.crt
    cp sei-ca.pem /etc/pki/tls/certs/sei-ca.pem
    cp sei.key /etc/pki/tls/private/sei.key 
    cat sei.crt sei.key >> /etc/pki/tls/certs/sei.pem

    echo "Incluindo TrustStore no sistema"
    cp /icpbrasil/*.crt /etc/pki/ca-trust/source/anchors/
    update-ca-trust extract
    update-ca-trust enable
  
    # Set etc/hosts
    if [ -z "$SEI_PROD" ]; then
        IP=`ifconfig | awk '/inet addr/{print substr($2,6)}' | head -1`
    
        echo $IP $SEI_HOST_URL >> /etc/hosts 
    fi
    
    #
    # Exemplo: SEI_MODULOS = 'PENIntegracao' => 'mod-sei-barramento', 'MdWsSeiRest' => 'mod-wssei'
    #
    if [ ! -z "$SEI_MODULOS" ]; then
        echo "Alterando os módulos ativos do SEI"
        sed -i '/Modulos/c\Modulos=> array('"$SEI_MODULOS"')' /opt/sei/config/ConfiguracaoSEI.php
    fi

exec "$@"
