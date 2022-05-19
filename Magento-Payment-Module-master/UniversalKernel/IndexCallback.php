<?php 
//if(is_file(__DIR_.'/Error.php'))
if(!defined('TABLE_PREFIX')) define('TABLE_PREFIX', '');

include_once __DIR__.'/Core/Error.php';
include_once __DIR__.'/Core/MySql.php';
include_once __DIR__.'/Core/MySqli.php';
include_once __DIR__.'/Core/Security.php';
include_once __DIR__.'/Core/Format.php';
include_once __DIR__.'/Core/Payme.php';
include_once __DIR__.'/Core/PaymeCallback.php';
class ExePaymeCallback {

	
	static function Construct($db_group){
		define('LANG', 'ru');
		
		if(isset($_SERVER['PHP_AUTH_USER']))
		{
			date_default_timezone_set('Asia/Tashkent');
			
			if(isset($_SERVER['PHP_AUTH_PW'])){
				define('PHP_AUTH_USER', $_SERVER['PHP_AUTH_USER']);
				define('PHP_AUTH_PW', $_SERVER['PHP_AUTH_PW']);
			}
			else
			{
				$a = html_entity_decode(base64_decode( substr($_SERVER["PHP_AUTH_USER"],6)));
				list($name, $password) = explode(':', $a);
				//exit($password);
				define('PHP_AUTH_USER', $name);
				define('PHP_AUTH_PW', $password);
			}
				
			$PaymeCallback = new PaymeCallback($db_group);
			
			$Sql['PerformTransaction'][] = array(
					'Sql' => 'Update {TABLE_PREFIX}sales_order Set status=:p_state Where increment_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => 'payme_paid'
					)
			);
			$Sql['PerformTransaction'][] = array(
					'Sql' => 'Update {TABLE_PREFIX}sales_order_grid Set status=:p_state Where increment_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => 'payme_paid'
					)
			);
			$Sql['PerformTransaction'][] = array(
					'Sql' => 'UPDATE {TABLE_PREFIX}sales_order_status_history AS t
								JOIN {TABLE_PREFIX}sales_order o 
								  On o.entity_id = t.parent_id
								 Set t.status=:p_state
							   Where o.increment_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => 'payme_paid'
					)
			);
			
			
			$Sql['CancelTransaction'][] = array(
					'Sql' => 'Update {TABLE_PREFIX}sales_order Set status=:p_state Where increment_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => 'canceled'
					)
			);
			$Sql['CancelTransaction'][] = array(
					'Sql' => 'Update {TABLE_PREFIX}sales_order_grid Set status=:p_state Where increment_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => 'canceled'
					)
			);
			$Sql['CancelTransaction'][] = array(
					'Sql' => 'UPDATE {TABLE_PREFIX}sales_order_status_history AS t
								JOIN {TABLE_PREFIX}sales_order o 
								  On o.entity_id = t.parent_id
								 Set t.status=:p_state
							   Where o.increment_id = :p_cms_order_id',
					'Param'=>array(
							':p_state' => 'canceled'
					)
			);
			$rezult = $PaymeCallback->Execute($Sql);
			exit(json_encode($rezult));
		}
		else
		{
			
			if(!ini_get('register_globals'))
			{
				if(function_exists('apache_response_headers')){
					$headers = apache_request_headers();
					try {
						$headers['Authorization'] = $_SERVER['PHP_AUTH_USER'];
					}
					catch  (Exception $e) {
						$headers['Authorization'] = null;
					}
				}
				$Param['error'] = array(
						'code'=>(int)'-32504',
						'message'=>"Скрипты требует включения параметр register_globals данной директивы. Для этого в папке скрипта или в папке домена создайте файл .htaccess и поместите в него следующую директиву: php_flag register_globals on.Если тоже не будут работат тогда добавит файл .htaccess в нужной папке уже существует, то просто добавьте эту строку в конец <IfModule mod_rewrite.c> RewriteEngine on RewriteRule .* - [E=PHP_AUTH_USER:%{HTTP:Authorization},L] </IfModule>",
						"data" => array(function_exists('apache_response_headers')?$headers:headers_list()),
						"time"=>Format::timestamp(true)
				);
				exit(json_encode($Param));
			}
			$Security = new Security();
			$Get = $Security->_json(true);
			if(isset($Get['id']))
			{
				$Param['id'] = $Get['id'];
				$Param['error'] = array(
						'code'=>(int)'-32504',
						'message'=>array("ru"=>'Недостаточно привилегий для выполнения метода.',"uz"=>'Недостаточно привилегий для выполнения метода.',"en"=>'Недостаточно привилегий для выполнения метода.'),
						"data" => __METHOD__,
						"time"=>Format::timestamp(true)
				);
			}
			else
			{
				$Param['id'] = $Get['id'];
				$Param['error'] = array(
						'code'=>(int)'-32504',
						'message'=>array("ru"=>'Недостаточно привилегий для выполнения метода.',"uz"=>'Недостаточно привилегий для выполнения метода.',"en"=>'Недостаточно привилегий для выполнения метода.'),
						"data" => __METHOD__,
						"time"=>Format::timestamp(true)
				);				
			}

			exit(json_encode($Param));
		}		
	}

}
//print_r($db_group);
if(isset($db_group))
	ExePaymeCallback::Construct($db_group);
?>