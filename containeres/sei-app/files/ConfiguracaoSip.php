<?

class ConfiguracaoSip extends InfraConfiguracao  {

 	private static $instance = null;

 	public static function getInstance(){
 	  if (ConfiguracaoSip::$instance == null) {
 	    ConfiguracaoSip::$instance = new ConfiguracaoSip();
 	  }
 	  return ConfiguracaoSip::$instance;
 	}

 	public function getArrConfiguracoes(){
 	  return array(
 	      'Sip' => array(
 	          'URL' => 'https://'.getenv('SEI_HOST_URL').'/sip',
 	          'Producao' => true),
 	       
 	      'PaginaSip' => array('NomeSistema' => 'SIP'),

 	      'SessaoSip' => array(
 	          'SiglaOrgaoSistema' => getenv('SEI_ORGAO'),
 	          'SiglaSistema' => 'SIP',
 	          'PaginaLogin' => 'https://'.getenv('SEI_HOST_URL').'/sip/login.php',
 	          'SipWsdl' => 'https://'.getenv('SEI_HOST_URL').'/sip/controlador_ws.php?servico=wsdl',
 	          'https' => true),
 	       
 	      'BancoSip'  => array(
 	          'Servidor' => 'sei-bd',
 	          'Porta' => '3306',
 	          'Banco' => 'sip',
 	          'Usuario' => getenv('SEI_DB_USERNAME'),
 	          'Senha' => getenv('SEI_DB_PASSWORD'),
 	          'UsuarioScript' => getenv('SEI_DB_USERNAME'),
              'SenhaScript' => getenv('SEI_DB_PASSWORD'),
 	          'Tipo' => 'MySql'), //MySql, SqlServer ou Oracle

              'CacheSip' => array(
                  'Servidor' => 'sei-memcached',
                  'Porta' => '11211'),
 	       
 	      'HostWebService' => array(
 	          'Replicacao' => array(''), //endereo ou IP da mquina que implementa o servio de replicao de usurios
 	          'Pesquisa' => array('*'), //endereos/IPs das mquinas do SEI
 	          'Autenticacao' => array('*')), //endereos/IPs das mquinas do SEI
 	      
 	      'InfraMail' => array(
 	          'Tipo' => '2', //1 = sendmail (neste caso no  necessrio configurar os atributos abaixo), 2 = SMTP
 	          'Servidor' => 'sei-smtp',
 	          'Porta' => '25',
 	          'Codificacao' => '8bit', //8bit, 7bit, binary, base64, quoted-printable
 	          'Autenticar' => false, //se true ento informar Usuario e Senha
 	          'Usuario' => '',
 	          'Senha' => '',
                  'MaxTamAnexosMb' => 20,
 	          'Protegido' => ''),  //campo usado em desenvolvimento, se tiver um email preenchido entao todos os emails enviados terao o destinatario ignorado e substitudo por este valor (evita envio incorreto de email)

 	  );
 	  	
 	}
}
?>
