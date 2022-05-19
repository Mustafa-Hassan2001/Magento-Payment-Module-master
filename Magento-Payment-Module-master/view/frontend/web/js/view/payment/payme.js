/**
 * 
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'payme',
                component: 'KiT_Payme/js/view/payment/method-renderer/payme'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
