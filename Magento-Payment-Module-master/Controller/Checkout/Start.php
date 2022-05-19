<?php
/**
 * @category    KiT
 * @package     KiT_Payme
 * @author      Игамбердиев Бахтиёр Хабибулаевич
 * @license      http://skill.uz/license-agreement.txt
 */

namespace KiT\Payme\Controller\Checkout;

class Start extends \Magento\Framework\App\Action\Action
{
    /**
    * @var \Magento\Checkout\Model\Session
    */
    protected $_checkoutSession;

   
    protected $_payme;

	protected $_resultPageFactory;
	
    /**
     * 
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \KiT\Payme\Model\Payme $payme
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
	
    public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Checkout\Model\Session $checkoutSession,
    \KiT\Payme\Model\Payme $payme,
	\Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_payme = $payme;
        $this->_checkoutSession = $checkoutSession;
		$this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
    * Start form Submission here
    */
    public function execute()
    {
    	$html = $this->_payme->getPostHTML($this->getOrder());
        echo $html;
    }

    /**
    * Get order object.
    *
    * @return \Magento\Sales\Model\Order
    */
    protected function getOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}
