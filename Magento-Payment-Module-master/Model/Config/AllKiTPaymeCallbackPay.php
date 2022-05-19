<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace KiT\Payme\Model\Config;
//namespace Magento\Payment\Model\Config\Source;
//			Magento\Payment\Model\Config\Source\Allspecificcountries

class AllKiTPaymeCallbackPay implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            	['value' => 0, 'label' => __('Моментально')],
            	['value' => 15000, 'label' => __('15 секунд')],
        		['value' => 30000, 'label' => __('30 секунд')],
        		['value' => 60000, 'label' => __('60 секунд')]
        ];
    }
}
