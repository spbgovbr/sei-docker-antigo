<?
/**
 * @package infra_php
*/
abstract class InfraCache {
  
  private $objMemcache = null;
  
  public function __construct(){
    $this->objMemcache = new Memcache();
    $this->objMemcache->connect($this->getStrServidor(), $this->getNumPorta(), 10);
    //$this->objMemcache->connect('127.0.0.1', 11211);
    //$this->objMemcache->flush();
  }
  
	public abstract function getStrServidor();
	public abstract function getNumPorta();
  
  public function getNumTimeout(){
    //[RANCHER] - Mudança para contornar problema no Rancher em Produção: valor padrao 1
    return getenv('CACHE_TIMEOUT') + 0;
  }
  
  public abstract function getObjInfraSessao();
  
  public function setAtributo($strChave, $varValor, $numTempo){

    if (InfraDebug::isBolProcessar()) {
      InfraDebug::getInstance()->gravarInfra('[InfraCache->setAtributo] ' . $strChave);
    }

    $strChave = trim($strChave);
    
    if ($strChave==''){
      throw new InfraException('Chave do atributo não informada.');
    }
    
    if (strlen($strChave) > 250){
    	throw new InfraException('Chave do atributo não pode ter mais de 250 caracteres.');
    }
    
    if ($numTempo < 0 || $numTempo > 2592000){
      throw new InfraException('Tempo de armazenagem na cache inválido para o atributo '.$strChave .'.');
    }
    
    if( $this->objMemcache->replace($this->formatarChave($strChave), $varValor, 0, $numTempo) === false ){
      if ($this->objMemcache->set($this->formatarChave($strChave), $varValor, 0, $numTempo) === false){
        throw new InfraException('Erro configurando atributo '.$strChave.' na cache.');	
      }
    } 
    
    
    $arr = $this->objMemcache->get($this->getStrChaveSistema());
    
    $arr = ($arr === false) ? array() : unserialize($arr);
    
    $arr[$strChave] = array(time(),$numTempo);
    
    if( $this->objMemcache->replace($this->getStrChaveSistema(), serialize($arr), 0, 2592000) === false ){
      if ($this->objMemcache->set($this->getStrChaveSistema(), serialize($arr), 0, 2592000) === false){
        throw new InfraException('Erro adicionando chave ['.$strChave .'] no índice.');
    	}
    } 
  }
  
  public function getAtributo($strChave){

    if (InfraDebug::isBolProcessar()) {
      InfraDebug::getInstance()->gravarInfra('[InfraCache->getAtributo] ' . $strChave);
    }

    $strChave = trim($strChave);
     
    if ($strChave==''){
      throw new InfraException('Chave do atributo não informada.');
    }
    
    return $this->objMemcache->get($this->formatarChave($strChave));
  }
  
  public function removerAtributo($strChave){

    if (InfraDebug::isBolProcessar()) {
      InfraDebug::getInstance()->gravarInfra('[InfraCache->remover] ' . $strChave);
    }

    $strChave = trim($strChave);
     
    if ($strChave==''){
      throw new InfraException('Chave do atributo não informada.');
    }
    
    $ret = $this->objMemcache->delete($this->formatarChave($strChave),0);
    
    $arr = $this->objMemcache->get($this->getStrChaveSistema());
    
    $arr = ($arr === false) ? array() : unserialize($arr);
    
    if (array_key_exists($strChave, $arr)){
    	unset($arr[$strChave]);
      if ($this->objMemcache->replace($this->getStrChaveSistema(), serialize($arr), 0, 2592000) === false){
      	 throw new InfraException('Erro removendo chave ['.$strChave .'] do índice.');
      }
    }
    
    return $ret;  
    
  }

  public function listarAtributos(){

    if (InfraDebug::isBolProcessar()) {
      InfraDebug::getInstance()->gravarInfra('[InfraCache->listarAtributos]');
    }

    $arr = $this->objMemcache->get($this->getStrChaveSistema());
    
    $arr = ($arr === false) ? array() : unserialize($arr);
    
    $ret = array();
    
		foreach($arr as $strChave => $tempo){
			if ((time() - $tempo[0]) < $tempo[1]){
				$ret[] = $strChave;
			}
		}    
		
		return $ret;
  }

  private function getStrChaveSistema(){
  	return $this->getObjInfraSessao()->getStrSiglaOrgaoSistema().'.'.$this->getObjInfraSessao()->getStrSiglaSistema();
  }
  
  private function formatarChave($strChave){
  	return $this->getStrChaveSistema().'.'.$strChave;
  }
  
}
?>
