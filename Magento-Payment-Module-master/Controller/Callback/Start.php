<?php
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */

namespace KiT\Payme\Controller\Callback;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\Config\ConfigOptionsListConstants;

//new Test();
class Start extends AppAction 
{
    public function __construct( 
        \Magento\Framework\App\Action\Context $context) 
    { 
  		return parent::__construct($context); 
    } 
    
    public function rm_db($key) {
    	$om = \Magento\Framework\App\ObjectManager::getInstance();
    	$config = $om->get('Magento\Framework\App\DeploymentConfig');
    	$result = $config->get(
    			ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
    			. '/' . $key
    	);
    	return $result;
    } 
    
    public function execute() 
    {
    	define('TABLE_PREFIX', $this->rm_db(ConfigOptionsListConstants::KEY_PREFIX));
    	$port = '3306';
    	$db_group = array(
    			'DB_HOST'=>$this->rm_db(ConfigOptionsListConstants::KEY_HOST),
    			'DB_PORT'=>$port,
    			'DB_NAME'=>$this->rm_db(ConfigOptionsListConstants::KEY_NAME),
    			'DB_USER'=>$this->rm_db(ConfigOptionsListConstants::KEY_USER),
    			'DB_PASS'=>$this->rm_db(ConfigOptionsListConstants::KEY_PASSWORD),
    			'CHARSET'=>'utf8',
    			'CHARSETCOLAT'=>'utf8_general_ci'
    	);
    	/*
    	 $db_group = array(
    	 'DB_HOST'=>'localhost',
    	 'DB_PORT'=>'3306',
    	 'DB_NAME'=>'irc_payme',
    	 'DB_USER'=>'irc_payme',
    	 'DB_PASS'=>'payme',
    	 'CHARSET'=>'utf8',
    	 'CHARSETCOLAT'=>'utf8_general_ci'
    	 );
    	 */
    	//print_r($Db);
    		
    	include_once './app/code/KiT/Payme/UniversalKernel/IndexCallback.php';
    	exit;
    }
}
