# SEI

[Conteiner](#conteiner-de-aplicacao-para-o-sei)

[Base de Dados e Backup](#base-de-dados-do-seimp)


# Conteiner de Aplicação para o SEI

Versão inicial do conteiner de aplicação

Como o codigo fonte do SEI é restrito não poderá ficar exposto aqui nesse projeto público.

Aqui ficam os conteineres:
- com o Apache de aplicação
- bem como um segundo conteiner com o Apache mais o supervisor para rodar as rotinas do barramento PEN

Adicione à pasta "sei-docker/containeres/sei-app/fontes" os arquivos com o código fonte do SEI na versão desejada e também adicione o código fonte na respectiva pasta "sei-docker/containeres/sei-app/fontes/sei/web/modulos"

Os módulos são (e devem estar respectivamente com o seguinte nome):

- barramento (mod-sei-barramento')
- Aplicativo SEI (mod-wssei)
- Protocolo Integrado (mp/protocolo_integrado)
- Peticionamento Eletronico (peticionamento)


 Por favor sugerir correções e melhorias.
 

## Base de Dados do SEIMP
[Banco de Dados](docs/sei-banco.md)


## Backup do SEIMP

[Política de Backup](docs/sei-politica-backup.md)

