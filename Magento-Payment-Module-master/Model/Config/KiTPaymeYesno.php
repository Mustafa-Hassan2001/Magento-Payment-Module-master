<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace KiT\Payme\Model\Config;
//namespace Magento\Payment\Model\Config\Source;
//			Magento\Payment\Model\Config\Source\Allspecificcountries

class KiTPaymeYesno implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            	['value' => 'N', 'label' => __('Нет')],
            	['value' => 'Y', 'label' => __('Да')]
        ];
    }
}
