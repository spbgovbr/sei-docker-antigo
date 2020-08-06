FROM centos:6.7

ENV SEI_HOST_URL localhost
ENV SEI_ORGAO MP

COPY files/install.sh /install.sh
COPY files/msttcore-fonts-2.0-3.noarch.rpm /usr/local/bin/

RUN bash /install.sh

COPY certs/ /icpbrasil
RUN wget https://curl.haxx.se/ca/cacert.pem && \
    mv cacert.pem /etc/ssl/certs/

# Ajustando a hora do container para TimeZone BRT
COPY files/Sao_Paulo /etc/localtime

# Copia dos fontes e customização dos Configuracao[SEI|Sip].php
COPY fontes/ /opt/  
COPY files/ConfiguracaoSEI.php /opt/sei/config/ConfiguracaoSEI.php
COPY files/ConfiguracaoSip.php /opt/sip/config/ConfiguracaoSip.php

COPY files/sei.ini /etc/php.d/sei.ini
COPY files/sei.conf /etc/httpd/conf.d/
COPY files/httpd.conf /etc/httpd/conf/

# Adição de redirects para conveniencia do usuario
COPY files/conferir/ /opt/sei/web/conferir/
COPY files/externo/ /opt/sei/web/externo/
COPY files/publicacao/ /opt/sei/web/publicacao/

# Instalação do Modulo Barramento
#COPY mod-sei-barramento/ /opt/sei/web/modulos/mod-sei-barramento
#RUN mv /opt/sei/web/modulos/mod-sei-barramento/sei_atualizar_versao_modulo_pen.php /opt/sei/scripts/sei_atualizar_versao_modulo_pen.php && \
#    mv /opt/sei/web/modulos/mod-sei-barramento/sip_atualizar_versao_modulo_pen.php /opt/sip/scripts/sip_atualizar_versao_modulo_pen.php
    
# Instalação do Modulo INCOM
#COPY incom/ /opt/sei/web/modulos/incom
#RUN mv /opt/sei/web/modulos/incom/scripts/sei_atualizar_versao_modulo_incom.php /opt/sei/scripts/sei_atualizar_versao_modulo_incom.php && \
#    mv /opt/sei/web/modulos/incom/scripts/sip_atualizar_versao_modulo_incom.php /opt/sip/scripts/sip_atualizar_versao_modulo_incom.php
    
# Instalação do Modulo Peticionamento Eletronico
# Copy feito via job
#COPY peticionamento/ /opt/sei/web/modulos/peticionamento
#RUN mv /opt/sei/web/modulos/peticionamento/scripts/sei_atualizar_versao_modulo_peticionamento.php /opt/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php && \
#    mv /opt/sei/web/modulos/peticionamento/scripts/sip_atualizar_versao_modulo_peticionamento.php /opt/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php

#COPY mod-wssei/ /opt/sei/web/modulos/mod-wssei

RUN mkdir -p /opt/sei/web/modulos/mp/
#COPY protocolo_integrado /opt/sei/web/modulos/mp/protocolo_integrado

# Correção da permissao e dono do arquivo para geracao de PDF
RUN chown -R 0:48 /opt/ && \
    chmod -R 750 /opt && \
    chmod -R 570 /opt/sei/temp && \
    chmod -R 570 /opt/sip/temp

COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 80
EXPOSE 443

#[RANCHER] contorno de problema no rancher e memcached
COPY files/InfraCache.php /opt/infra/infra_php/InfraCache.php
ENV CACHE_TIMEOUT 1

COPY files/httpd-foreground /usr/local/bin/
CMD ["httpd-foreground"]
