
include envlocal.env


DIR := ${CURDIR}
COMMMADCOMPOSE = docker-compose -f orquestrators/docker-compose/docker-compose.yml 





help:   ## Lista de comandos disponiveis e descricao. Voce pode usar TAB para completar os comandos
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

montar_volume_fontes: ## Monte o volume docker com os fontes que serao consumidos pelo projeto
	docker run --rm -v $(LOCALIZACAO_FONTES_SEI):/source -v local-fontes-storage:/opt -w /source alpine sh -c "cp -R infra sei sip /opt/"


build_docker_compose: ## Construa o docker-compose.yml baseado no arquivo envlocal.env
	rm -f orquestrators/docker-compose/docker-compose.yml

	envsubst < orquestrators/docker-compose/docker-compose-template.yml > orquestrators/docker-compose/docker-compose.yml


run: ## roda na sequencia build_docker_compose e up -d
	make build_docker_compose
	$(COMMMADCOMPOSE) up -d

stop: ## docker-compose stop e docker-compose rm -f
	make build_docker_compose
	$(COMMMADCOMPOSE) stop
	$(COMMMADCOMPOSE) rm -f

logs: ## docker-compose logs -f pressione ctrol+c para sair
	$(COMMMADCOMPOSE) logs -f

clear: ## para o projeto e remove tds os volumes criados
	make stop
	$(COMMMADCOMPOSE) down -v

clear_volume_fontes: ## Monte o volume docker com os fontes que serao consumidos pelo projeto
	docker volume rm $(VOLUME_FONTES)


