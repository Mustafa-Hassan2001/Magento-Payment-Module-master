<?php
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @copyright   Copyright KiT (http://skill.uz/)
 * @license      http://skill.uz/license-agreement.txt
 */
/*
namespace KiT\Payme\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallData implements InstallSchemaInterface
{
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		$installer->startSetup();
		
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/merchant_id',''));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/merchant_key',''));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/merchant_key_test',''));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/status_test','Y'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/status_tovar','Y'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/callback_pay','0'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/endpoint_url','http://magento.uz/payme/callback/start'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/checkout_url','https://checkout.paycom.uz'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/view_batten','Отправит'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/active','1'));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/callback_checkout_url',''));
		$installer->getConnection()->insertArray('core_config_data', array('config_id','scope','scope_id','path','value'), array(null,'default',	'0','payment/payme/checkout_url_test','https://test.paycom.uz'));
		
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
		include './app/code/KiT/Payme/UniversalKernel/IndexConfigCreate.php';
		
		$installer->endSetup();
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
}*/