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

class IndexInsertOrder {

	private $BD = null;
	static function Construct($db_group, $Get){
		date_default_timezone_set('Asia/Tashkent');
			//print_r($db_group);
		$DbConnect = false;
		if (extension_loaded('pdo') and !$DbConnect){$DbConnect=true; $Db = new MySql($db_group); }
		if (extension_loaded('mysqli') and !$DbConnect){$DbConnect=true; $Db = new DbMySqli($db_group);}
		if (extension_loaded('mysql') and !$DbConnect){$DbConnect=true; $Db = new MySql($db_group);}
		
		$Payme = new Payme($Db);
		$return = $Payme->InsertOredr($Get);
		return $return;
	}

}

//print_r($db_group);
if(isset($db_group))
	return IndexInsertOrder::Construct($db_group, $Get);
?>