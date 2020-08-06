	Atenção: este documento foi elaborado em consonância com a realidade de um orgão específico. Ao interpretar, usar o bom senso para os requisitos do seu orgão

# Banco de Dados

A base de dados do SEI deve ser vista a partir da união do banco de dados e dos arquivos anexos que ficam em filesystem.

Neste documento vamos denominar:
- bd: banco de dados do SEI em mysql
- arquivos anexos: arquivos de conteúdo diverso, binário ou não, que o SEI permite que seja feito o upload por seus usuários
- base de dados: união entre bd e arquivos anexos

O BD possui diversas informações estruturais e funcionais do sistema SEI, enquanto os arquivos anexos são documentos diversos que são subidos até a aplicação via upload.


## Requisitos

Tanto o banco de dados quanto o filesystem com os arquivos anexos possuem os seguintes requisitos:

- não é permitido, em caso de crash ou problemas, a perda de informações
- a principal ação deve estar na guarda e backup dos dados e logs binários do banco e arquivos anexos para garantir perda zero de informação
- a segunda principal ação deve estar relacionada a disponibilidade do serviço
- velocidade aceitável de leitura e escrita para a carga requerida do Ministério do Planejamento
    * 300 transações / segundo
    * 1.4 GB de crescimento / dia do BD
    * 11 GB / dia de crescimento da base de dados
- deve-se permitir, em caso de necessidade, recuperar os dados na forma point in time recovery, podendo voltar até 1 ano atrás da posição atual
- portanto os logs binários devem ser mantidos pelo período de pelo menos 1 ano

## Profissional Necessário

O profissional necessário para manter a solução deverá ter os conhecimentos em mysql server 5.7.

Dentre as suas competências deverão constar, no mínimo:
- instalação / sustentação de mysql server 5.7 em ambiente produtivo
- implementação de réplica em log binário (master/slave)
- tuning da base de dados de acordo com o ambiente e expectativas do cliente
- recuperação de desastres do mysql server
- point in time recovery de base de dados
- geração e otimização de dumps
- análise de logs com relatórios objetivos apontando gargalos e melhorias na infraestrutura
- médio conhecimento em linux

## Necessidades Específicas para a Base do SEI

O profissional responsável pela base deverá constantemente:
- garantir que a réplica esteja há menos de 5 segundos do master
- garantir que a rotina de rsync dos arquivos anexos esteja a menos de 3 minutos dos arquivos de produção
- garantir a disponibilidade de 99% da base de dados master (considerando dias úteis)
- informar a DTI sobre qualquer erro ou situação que possa comprometer o ambiente
- informar imediatamente a DTI sobre qualquer indisponibilidade ou lentidão


## Tecnologias

Atualmente o banco de dados é mysql 5.7 com uma réplica também em mysql 5.7.
Para os arquivos anexos, estamos usando Storage - EMC, com NFS ativado para leitura e escrita dos dados

## Arquitetura

- Arquivos Anexos:
    * 192.x.x.x
    * diretório NFS: /SEI_PROD_xx

- Mysql Master
    * 192.x.x.x
    * servidor físico com 24 cores e 64GB
    * servidor compartilhado com o PDCMP
    * data do mysql: /dados_banco/mysql/data
    * logs binários do mysql: /dados_banco/mysql/binlog

- Mysql Slave - Virtual
    * 192.x.x.x
    * servidor virtual 8 cores e 11GB
    * data do mysql: /dados_banco/mysql/data
    * logs binários do mysql: /dados_banco/mysql/binlog
    

- Mysql Slave - Física
    * 192.x.x.x
    * servidor físico 8 cores e 32GB
    * data do mysql: /replica_bd/mysql/data
    * logs binários do mysql: /replica_bd/mysql/binlog
    * diretório para backup - dumps: /replica_bd/backup/dumps (aponta para Storage VNX)
    * diretório para backup - logs binários: /replica_bd/backup/logbinarios (aponta para Storage VNX)


## Rotinas

Para garantir a guarda das informações, inclusive a reconstrução da base de dados em caso de crash, foram implementadas as seguintes ações:

- réplica nativa do mysql usando um master e um slave
- logs binários independentes do master e slave
- para os arquivos anexos há quatro rsync rodando, replicando todo o filesystem do diretório de origem para o diretório de backup
- toda terça e sábado roda um crontab que tira o dump da réplica e salva no diretório de backup

### Rotina dos arquivos anexos

- crontab agendado no root
    * crontab que roda de 5 em 5 segundos copiando todos os arquivos anexos do diretório de prododução para os diretórios de backup (note o flock para impedir a execução ao mesmo tempo de várias instâncias da rotina)

```
0        0,7,21     *   *    *   flock -n lock_file_prod -c "/root/scripts/copia_prod_replica.sh"
0        1,8,22     *   *    *   flock -n lock_file_prod_to_ibm -c "/root/scripts/copia_prod_to_ibm.sh"
*        *          *   *    *   flock -n lock_file_prod_dodia -c "/root/scripts/copia_prod_replica_dodia.sh"
*        *          *   *    *   flock -n lock_file_prod_to_ibm_dodia -c "/root/scripts/copia_prod_to_ibm_dodia.sh"
*        *          *   *    *   flock -n lock_file_replica_dodia -c "/root/scripts/copia_replica_sei_anexos_dodia.sh"
0        1,8,22     *   *    *   flock -n lock_file_replica -c "/root/scripts/copia_replica_sei_anexos.sh"
19 9,12,16,21,22,23 *   *    *   flock -n lock_file_bin -c "/copia_sei/scripts/sei_bkp_binlogs.sh"

``` 

* conteúdo do copia_prod_replica_dodia.sh

```

#!/bin/bash

#pegar ano mês e dia

PATTERN=$(date +%Y/%m/%d)

for i in $(seq 1 10)
do

    if [ -d "/SEI_PROD/sei_anexos/$PATTERN" ]; then

        if [ ! -d "/SEI_REPLICA/sei_anexos/$PATTERN" ]; then
            mkdir -p /SEI_REPLICA/sei_anexos/$PATTERN
            #echo "criei /tmp/dst/$PATTERN"
        fi

        #echo $PATTERN
        rsync -a /SEI_PROD/sei_anexos/$PATTERN/* /SEI_REPLICA/sei_anexos/$PATTERN/

    fi

sleep 5

done
``` 

### Rotina do Dump da Base

- crontab

``` 
0 0 * * 3,6 /copia_sei/scripts/sei_bkp_bd.sh
``` 

- arquivo sei_bkp_bd.sh

``` 
#!/bin/bash

#vUSER="USERNAME"
vPASS="<<password>>"
DATA=$(date +%Y-%m-%d-%H.%M.%S)

echo "" >> /copia_sei/scripts/result.log
echo "Parando Réplica: $DATA" >> /copia_sei/scripts/result.log

mysqladmin stop-slave -u$vUSER -p$vPASS

echo "Réplica parada. Iniciando Dump" >> /copia_sei/scripts/result.log

mysqldump -u$vUSER -p$vPASS --master-data=2 --max_allowed_packet=500M --databases sei sip  > /copia_sei/dumps_base/dmp_$DATA.dump

echo "Dump feito. Restartando Slave" >> /copia_sei/scripts/result.log

mysqladmin start-slave -u$vUSER -p$vPASS

echo "Slave Restartado. $(date +%Y-%m-%d-%H.%M.%S)" >> /copia_sei/scripts/result.log
``` 

## Rotacionamento dos dumps e Logs

O rotacionamento dos logs binários ainda não foi implementado. 

É feito manualmente.

Deve-se tomar o cuidado de rotacionar apenas o que já foi armazenado pelo netbackup e teve a guarda armazenada em fita.

Poderá, no futuro, implementar rotina automática para rotacionamento.


