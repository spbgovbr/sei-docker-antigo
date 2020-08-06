<?

class ConfiguracaoSEI extends InfraConfiguracao  {

 	private static $instance = null;

 	public static function getInstance(){
 	  if (ConfiguracaoSEI::$instance == null) {
 	    ConfiguracaoSEI::$instance = new ConfiguracaoSEI();
 	  }
 	  return ConfiguracaoSEI::$instance;
 	}

 	public function getArrConfiguracoes(){
 	  return array(

 	      'SEI' => array(
 	          'URL' => 'https://'.getenv('SEI_HOST_URL').'/sei',
 	          'Producao' => true,
 	          'RepositorioArquivos' => '/dados',
                  'Modulos' => array('PENIntegracao' => 'mod-sei-barramento', 'MdWsSeiRest' => 'mod-wssei', 'ProtocoloIntegradoIntegracao' => 'mp/protocolo_integrado/', 'PeticionamentoIntegracao' => 'peticionamento/', 'MdIncomIntegracao' => 'incom','MdEstatisticas' => 'mod-sei-estatisticas')
              ),

 	      'PaginaSEI' => array(
 	          'NomeSistema' => 'SEI',
 	          'NomeSistemaComplemento' => getenv('SEI_NOMECOMPLEMENTO'),
              'LogoMenu' => ''),

 	      'SessaoSEI' => array(
 	          'SiglaOrgaoSistema' => getenv('SEI_ORGAO'),
 	          'SiglaSistema' => 'SEI',
 	          'PaginaLogin' => 'https://'.getenv('SEI_HOST_URL').'/sip/login.php',
 	          'SipWsdl' => 'https://'.getenv('SEI_HOST_URL').'/sip/controlador_ws.php?servico=wsdl',
 	          'https' => true),
 	      
 	      'XSS' => array('NivelVerificacao' => 'A', //B=Básico, A=Avançado, N=Nenhum
               'ProtocolosExcecoes' => null, //lista temporária de documentos nao validados
               'NivelBasico' => array('ValoresNaoPermitidos' => null),
               'NivelAvancado' => array('TagsPermitidas' => null,
                                        'TagsAtributosPermitidos' => null,
                                        'FiltrarConteudoConsulta' => true //neste caso loga os erros e filtra o conteudo nas consultas
                                        )
                        ),
 	       
 	      'BancoSEI'  => array(
                  'Servidor' => 'sei-bd',
                  'Porta' => '3306',
                  'Banco' => 'sei',
                  'Usuario' => getenv('SEI_DB_USERNAME'),
                  'Senha' => getenv('SEI_DB_PASSWORD'),
                  'UsuarioScript' => getenv('SEI_DB_USERNAME'),
                  'SenhaScript' => getenv('SEI_DB_PASSWORD'),
                  'Tipo' => 'MySql'), //MySql ou SqlServer

              'CacheSEI' => array(
                  'Servidor' => 'sei-memcached',
	          'Porta' => '11211'),

              'JODConverter' => array('Servidor' => 'http://sei-jod:8080/converter/service'),

 	      'Edoc' => array('Servidor' => 'http://[Servidor .NET]'),
 	       
 	      'Solr' => array(
                  'Servidor' => 'http://sei-solr:8983/solr',
                  'CoreProtocolos' => 'sei-protocolos',
                  'TempoCommitProtocolos' => 300,
                  'CoreBasesConhecimento' => 'sei-bases-conhecimento',
                  'TempoCommitBasesConhecimento' => 60,
                  'CorePublicacoes' => 'sei-publicacoes',
                  'TempoCommitPublicacoes' => 60),

	      'HostWebService' => array(
	          'Edoc' => array('*'),
		  'Sip' => array('*'), 
		  'Publicacao' => array('*'), 
		  'Ouvidoria' => array('*'),),
 	       
 	      'InfraMail' => array(
	   	  'Tipo' => '2', 
		  'Servidor' => 'sei-smtp',
		  'Porta' => '25',
		  'Codificacao' => '8bit', 
		  'MaxDestinatarios' => 999, 
		  'MaxTamAnexosMb' => 999, 
		  'Seguranca' => '', 
		  'Autenticar' => false, 
		  'Usuario' => '',
		  'Senha' => '',
                  'MaxTamAnexosMb' => 20,
		  'Protegido' => ''),
		  
		  'MdEstatisticas' => array(
            'url' => getenv('MD_ESTATISTICAS_URL'),
            'sigla' => getenv('MD_ESTATISTICAS_SIGLA'),
            'chave' => getenv('MD_ESTATISTICAS_CHAVE')
          )

 	  );
 	}
}
?>
