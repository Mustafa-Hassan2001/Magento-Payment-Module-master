<?php 
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */
//namespace  KiT\Payme\UniversalKernel;
if(!defined('TABLE_PREFIX')) define('TABLE_PREFIX', '');
include_once __DIR__.'/Core/Error.php';
include_once __DIR__.'/Core/MySql.php';
include_once __DIR__.'/Core/MySqli.php';
include_once __DIR__.'/Core/Security.php';
include_once __DIR__.'/Core/Format.php';
include_once __DIR__.'/Core/Payme.php';
include_once __DIR__.'/Core/PaymeCallback.php';

class ConfigCreate {

	private $BD = null;
	static function Construct($db_group, $Get=null){
		date_default_timezone_set('Asia/Tashkent');
			//print_r($db_group);	
		$DbConnect = false;	
		if (extension_loaded('pdo') and !$DbConnect){$DbConnect=true; $Db = new MySql($db_group); }
		if (extension_loaded('mysqli') and !$DbConnect){$DbConnect=true; $Db = new DbMySqli($db_group);}
		if (extension_loaded('mysql') and !$DbConnect){$DbConnect=true; $Db = new MySql($db_group);}
		
		$Payme = new Payme($Db);
		if(is_null($Get)){
			$merchant_id 		= isset($_REQUEST['groups']['payme']['fields']['merchant_id'])?$_REQUEST['groups']['payme']['fields']['merchant_id']['value']:'';
			$merchant_key_test 	= isset($_REQUEST['groups']['payme']['fields']['merchant_key_test'])?$_REQUEST['groups']['payme']['fields']['merchant_key_test']['value']:'';
			$merchant_key 		= isset($_REQUEST['groups']['payme']['fields']['merchant_key'])?$_REQUEST['groups']['payme']['fields']['merchant_key']['value']:'';
			$checkout_url 		= isset($_REQUEST['groups']['payme']['fields']['checkout_url'])?$_REQUEST['groups']['payme']['fields']['checkout_url']['value']:'';
			$endpoint_url 		= isset($_REQUEST['groups']['payme']['fields']['endpoint_url'])?$_REQUEST['groups']['payme']['fields']['endpoint_url']['value']:'';
			$status_test 		= isset($_REQUEST['groups']['payme']['fields']['status_test'])?$_REQUEST['groups']['payme']['fields']['status_test']['value']:'';
			$status_tovar 		= isset($_REQUEST['groups']['payme']['fields']['status_tovar'])?$_REQUEST['groups']['payme']['fields']['status_tovar']['value']:'';
			$callback_pay 		= isset($_REQUEST['groups']['payme']['fields']['callback_pay'])?$_REQUEST['groups']['payme']['fields']['callback_pay']['value']:'';
			$redirect 			= isset($_REQUEST['groups']['payme']['fields']['redirect'])?$_REQUEST['groups']['payme']['fields']['redirect']['value']:'';

			$Get = array(
					'merchant_id' 		=> $merchant_id,
					'merchant_key_test' => $merchant_key_test,
					'merchant_key' 		=> $merchant_key,
					'checkout_url' 		=> $checkout_url,
					'endpoint_url' 		=> $endpoint_url,
					'status_test' 		=> $status_test,
					'status_tovar' 		=> $status_tovar,
					'callback_pay' 		=> $callback_pay,
					'redirect' 			=> $redirect
			);
		}
		$Payme->PaymeConfig($Get);
	}

}
//print_r($db_group);
if(isset($db_group))
	ConfigCreate::Construct($db_group, isset($Get)?$Get:null);
?>