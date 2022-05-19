<?php 
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */

class PaymeCallback{
	private $BD = null;
	private $Payme = null;
	private $Security = null;
	public $Get = null;
	private $Header = null;
	function __construct($db_group){ 
		$DbConnect = false;
		if (extension_loaded('pdo') and !$DbConnect){$DbConnect=true; $this->BD = new MySql($db_group); }
		if (extension_loaded('mysqli') and !$DbConnect){$DbConnect=true; $this->BD = new DbMySqli($db_group);}
		if (extension_loaded('mysql') and !$DbConnect){$DbConnect=true; $this->BD = new MySql($db_group);}
		$this->Security = new Security();
		$this->Payme = new Payme($this->BD);

		$this->Get();
		$this->Authorization($this->Get);
	}
	private function Get(){
		$this->Get = $this->Security->_json(true);
		$this->Header = PHP_AUTH_USER.':'.PHP_AUTH_PW;
	}
	private function Authorization(){
		$this->Payme->MerchantKey(PHP_AUTH_PW);
		$this->Payme->FindMerchantKey($this->Get);
	}
	
	//выполнять
	public function Execute($Sql=null){
		
		$function_name = $this->Get['method'];
		
		if(method_exists($this->Payme, $function_name))
		{
			$rezult = $this->Payme->$function_name($this->Get, $Sql);
		}
		
		return $rezult;
	}
}
?>