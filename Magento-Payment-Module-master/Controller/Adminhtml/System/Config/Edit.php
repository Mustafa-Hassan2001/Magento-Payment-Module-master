<?php
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */

namespace Magento\Config\Controller\Adminhtml\System\Config;
use Magento\Framework\Config\ConfigOptionsListConstants;
class Edit extends AbstractScopeConfig
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker
     * @param \Magento\Config\Model\Config $backendConfig
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker $sectionChecker,
        \Magento\Config\Model\Config $backendConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $configStructure, $sectionChecker, $backendConfig);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit configuration section
     *
     * @return \Magento\Framework\App\ResponseInterface|void
     */
    public function execute()
    {
    	$this->InsertConfig();
        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store = $this->getRequest()->getParam('store');

        /** @var $section \Magento\Config\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($current);
        if ($current && !$section->isVisible($website, $store)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $redirectResult */
            $redirectResult = $this->resultRedirectFactory->create();
            return $redirectResult->setPath('adminhtml/*/', ['website' => $website, 'store' => $store]);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Config::system_config');
        $resultPage->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo([$current]);
        $resultPage->addBreadcrumb(__('System'), __('System'), $this->getUrl('*\/system'));
        $resultPage->getConfig()->getTitle()->prepend(__('Configuration'));
        return $resultPage;
    }
    
    public function InsertConfig(){
    	$port = '3306';
    	define('TABLE_PREFIX', $this->rm_db(ConfigOptionsListConstants::KEY_PREFIX));
    	$db_group = array(
    			'DB_HOST'=>$this->rm_db(ConfigOptionsListConstants::KEY_HOST),
    			'DB_PORT'=>$port,
    			'DB_NAME'=>$this->rm_db(ConfigOptionsListConstants::KEY_NAME),
    			'DB_USER'=>$this->rm_db(ConfigOptionsListConstants::KEY_USER),
    			'DB_PASS'=>$this->rm_db(ConfigOptionsListConstants::KEY_PASSWORD),
    			'CHARSET'=>'utf8',
    			'CHARSETCOLAT'=>'utf8_general_ci'
    	);
    	if(isset($_SERVER['HTTP_HOST']))
    		$Http_Host = $_SERVER['HTTP_HOST'];
    	else
    		$Http_Host = '';
    	$Sql[] = "INSERT INTO {TABLE_PREFIX}core_config_data(config_id, scope, scope_id, path, value)
					SELECT t.*
					  from (select null, 'default', 0, 'payment/payme/active', '1' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/active' )
					        union
					        select null, 'default', 0, 'payment/payme/checkout_url', 'https://checkout.paycom.uz' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/checkout_url' )
					        union
					        select null, 'default', 0, 'payment/payme/checkout_url_test', 'https://test.paycom.uz' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/checkout_url_test' )
					        union
					        select null, 'default', 0, 'payment/payme/merchant_id', NULL from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/merchant_id' )
					        union
					        select null, 'default', 0, 'payment/payme/merchant_key', NULL from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/merchant_key' )
					        union
					        select null, 'default', 0, 'payment/payme/merchant_key_test', NULL from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/merchant_key_test' )
					        union
					        select null, 'default', 0, 'payment/payme/status_test', 'Y' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/status_test' )
					        union
					        select null, 'default', 0, 'payment/payme/status_tovar', 'N' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/status_tovar' )
					        union
					        select null, 'default', 0, 'payment/payme/callback_pay', '0' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/callback_pay' )
					        union
					        select null, 'default', 0, 'payment/payme/endpoint_url', 'http://{$Http_Host}/payme/callback/start' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/endpoint_url' )
					        union
					        select null, 'default', 0, 'payment/payme/callback_checkout_url', 'http://{$Http_Host}/?payme=pay' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/callback_checkout_url' )
					        union
					        select null, 'default', 0, 'payment/payme/redirect', 'http://{$Http_Host}/order_return/Checkout/OrderReturn' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/redirects' )
					        union
					        select null, 'default', 0, 'payment/payme/view_batten', 'Отправить' from dual WHERE NOT EXISTS (SELECT 1 FROM {TABLE_PREFIX}core_config_data tt WHERE tt.path = 'payment/payme/view_batten' )) t";
    	
    	$Sql[] = " INSERT INTO `{TABLE_PREFIX}sales_order_status` (`status`, `label`) 
    			   Select 'payme_paid', 'Оплачена'
    			     From dual
    			    Where NOT EXISTS (SELECT 1 FROM `sales_order_status` t WHERE t.status = 'payme_paid')";
    	
    	include './app/code/KiT/Payme/UniversalKernel/IndexInsert.php';
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
}
