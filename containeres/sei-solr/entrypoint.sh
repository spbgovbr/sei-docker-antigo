#!/usr/bin/env sh

if [ -z "$(ls -A /opt/solr/server/solr/dados)" ]; then 
  # Links para /opt/solr/server/solr/dados
  sh /tmp/sei-solr-6.1.0.sh

  /opt/solr/bin/solr start && sleep 10
  
  curl 'http://localhost:8983/solr/admin/cores?action=CREATE&name=sei-protocolos&instanceDir=/opt/solr/server/solr/dados/sei-protocolos&config=sei-protocolos-config.xml&schema=sei-protocolos-schema.xml&dataDir=/opt/solr/server/solr/dados/sei-protocolos/conteudo'
  curl 'http://localhost:8983/solr/admin/cores?action=CREATE&name=sei-bases-conhecimento&instanceDir=/opt/solr/server/solr/dados/sei-bases-conhecimento&config=sei-bases-conhecimento-config.xml&schema=sei-bases-conhecimento-schema.xml&dataDir=/opt/solr/server/solr/dados/sei-bases-conhecimento/conteudo'
  curl 'http://localhost:8983/solr/admin/cores?action=CREATE&name=sei-publicacoes&instanceDir=/opt/solr/server/solr/dados/sei-publicacoes&config=sei-publicacoes-config.xml&schema=sei-publicacoes-schema.xml&dataDir=/opt/solr/server/solr/dados/sei-publicacoes/conteudo'

  /opt/solr/bin/solr stop

fi

# Remover arquivos temporários
rm -rf /tmp/*

exec /opt/solr/bin/solr start -f -p 8983 "$@" 
