<?php

namespace KiT\Payme\Model;

use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Framework\Config\ConfigOptionsListConstants;
class Payme extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_code = 'payme';
    protected $_isInitializeNeeded = true;

    /**
    * @var \Magento\Framework\Exception\LocalizedExceptionFactory
    */
    protected $_exception;

    /**
    * @var \Magento\Sales\Api\TransactionRepositoryInterface
    */
    protected $_transactionRepository;

    /**
    * @var Transaction\BuilderInterface
    */
    protected $_transactionBuilder;

    /**
    * @var \Magento\Framework\UrlInterface
    */
    protected $_urlBuilder;

    /**
    * @var \Magento\Sales\Model\OrderFactory
    */
    protected $_orderFactory;

    /**
    * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    /**
    * @param \Magento\Framework\UrlInterface $urlBuilder
    * @param \Magento\Framework\Exception\LocalizedExceptionFactory $exception
    * @param \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
    * @param Transaction\BuilderInterface $transactionBuilder
    * @param \Magento\Sales\Model\OrderFactory $orderFactory
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Framework\Model\Context $context
    * @param \Magento\Framework\Registry $registry
    * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
    * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
    * @param \Magento\Payment\Helper\Data $paymentData
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param \Magento\Payment\Model\Method\Logger $logger
    * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
    * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
    * @param array $data
    */
    public function __construct(
      \Magento\Framework\UrlInterface $urlBuilder,
      \Magento\Framework\Exception\LocalizedExceptionFactory $exception,
      \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
      \Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface $transactionBuilder,
      \Magento\Sales\Model\OrderFactory $orderFactory,
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Framework\Model\Context $context,
      \Magento\Framework\Registry $registry,
      \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
      \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
      \Magento\Payment\Helper\Data $paymentData,
      \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
      \Magento\Payment\Model\Method\Logger $logger,
      \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
      \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
      array $data = []
    ) {
      $this->_urlBuilder = $urlBuilder;
      $this->_exception = $exception;
      $this->_transactionRepository = $transactionRepository;
      $this->_transactionBuilder = $transactionBuilder;
      $this->_orderFactory = $orderFactory;
      $this->_storeManager = $storeManager;

      parent::__construct(
          $context,
          $registry,
          $extensionFactory,
          $customAttributeFactory,
          $paymentData,
          $scopeConfig,
          $logger,
          $resource,
          $resourceCollection,
          $data
      );
    }

    /**
     * Instantiate state and set it to state object.
     *
     * @param string                        $paymentAction
     * @param \Magento\Framework\DataObject $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $order->setCanSendNewEmailFlag(false);

        $stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

	public function getPostHTML($order, $storeId = null)
    {

    	$StatusTest = $this->getConfigData('status_test');
    	if($StatusTest == 'Y')
    		$merchantUrl = $this->getConfigData('checkout_url_test');
    	else 
    		$merchantUrl = $this->getConfigData('checkout_url');
    	
    	$merchantId 		= $this->getConfigData('merchant_id');
		$merchantKey 		= $this->getConfigData('merchant_key');
		$checkoutUrl 		= $this->getConfigData('checkout_url');
		$callback_timeout 	= $this->getConfigData('callback_pay');
		$Redirect		  	= $this->getConfigData('redirect');
		$Amount				= $order->getGrandTotal()*100;
		$OrderId 			= $order->getIncrementId();
		
		/*
		$paymeform = '<form id="payme_form" name="payme_form" method="POST"  action="'.$checkoutUrl.'">';

		$paymeform.= $this->addHiddenField(array('name'=>'account[order_id]', 'value'=>$OrderId));
		$paymeform.= $this->addHiddenField(array('name'=>'amount', 'value'=>$order->getGrandTotal()));
		$paymeform.= $this->addHiddenField(array('name'=>'merchant', 'value'=>$merchantId));
		$paymeform.= $this->addHiddenField(array('name'=>'lang', 'value'=>'ru'));
		$paymeform.= $this->addHiddenField(array('name'=>'description', 'value'=>'Payment for Order Id #'.$OrderId));
		
		$paymeform.= '</form>';
		
		$paymehtml = '<html><body>';
		$paymehtml.= $paymeform;
		//$paymehtml.= '<script type="text/javascript">document.getElementById("payme_form").submit();</script>';
		$paymehtml.= '</body></html>';
		return $paymehtml;
		
		$paymeform = '<div onload="Paycom.QR('#form-payme', '#qr-container')">
			<form id="form-payme" method="POST" action="https://checkout.paycom.uz/">
			    <input type="hidden" name="merchant" value="587f72c72cac0d162c722ae2">
			    <input type="hidden" name="account[order_id]" value="197">
			    <input type="hidden" name="amount" value="500">
			    <input type="hidden" name="lang" value="ru">
			    <input type="hidden" name="qr" data-width="250">
			    <div id="qr-container"></div>
			</form>
			<script src="https://cdn.paycom.uz/integration/js/checkout.min.js"></script>
		</div>';
		
		*/
		
		$Get = array(
				'Amount'=>$Amount,
				'OrderId'=>$OrderId,
				'CmsOrderId'=>$OrderId,
				'IsFlagTest'=>$StatusTest
		);
		
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

		$return = include './app/code/KiT/Payme/UniversalKernel/IndexInsertOrder.php';

		$Url = "{$merchantUrl}/".base64_encode("m={$merchantId};ac.order_id={$return['id']};a={$Amount};l=ru;c={$Redirect}?order_id={$return['id']};ct={$callback_timeout}");
		//$Url1 = "{$merchantUrl}/".("m={$return['id']};ac.order_id={$OrderId};a={$Amount};l=ru;c=http://magento.uz;ct={$callback_timeout}");
		//print_r($return);
		//exit($Url.'<br>'.$Url1);
		header('Location: '.$Url);
		exit();
		return $Url;
    }
    public function OrderReturn()
    {
    
    	$StatusTest = $this->getConfigData('status_test');
    	if($StatusTest == 'Y')
    		$merchantUrl = $this->getConfigData('checkout_url_test');
    	else
    		$merchantUrl = $this->getConfigData('checkout_url');
    	 
    	$merchantId 		= $this->getConfigData('merchant_id');
    	$merchantKey 		= $this->getConfigData('merchant_key');
    	$checkoutUrl 		= $this->getConfigData('checkout_url');
    	$callback_timeout 	= $this->getConfigData('callback_pay');
    	$Redirect		  	= $this->getConfigData('redirect');
    
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
    
    	$paymeform = include './app/code/KiT/Payme/UniversalKernel/IndexOrderReturn.php';

    	return $paymeform;
    }
    private function rm_db($key) {
    	$om = \Magento\Framework\App\ObjectManager::getInstance();
    	$config = $om->get('Magento\Framework\App\DeploymentConfig');
    	$result = $config->get(
    			ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
    			. '/' . $key
    	);
    	return $result;
    }
	/**
	 * 
	 * @param  $field
	 * @return string
	 */
    
	protected function addHiddenField($field)
	{
		$name = $field['name'];
		$value = $field['value'];	
		$input = "<input name='".$name."' type='hidden' value='".$value."' />";	
		
		return $input;
	}
	
    /**
     * Get return URL.
     *
     * @param int|null $storeId
     *
     * @return string
     */
	
    public function getSuccessUrl($storeId = null)
    {
        return $this->_getUrl('checkout/onepage/success', $storeId);
    }
    
    /**
     * Build URL for store.
     *
     * @param string    $path
     * @param int       $storeId
     * @param bool|null $secure
     *
     * @return string
     */

    protected function _getUrl($path, $storeId, $secure = null)
    {
        $store = $this->_storeManager->getStore($storeId);

        return $this->_urlBuilder->getUrl(
            $path,
            ['_store' => $store, '_secure' => $secure === null ? $store->isCurrentlySecure() : $secure]
        );
    }
}
