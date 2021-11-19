/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
	map: {
        '*': {
            'homelanetheme': 'js/homelane-theme',
            'slickslider': 'js/slick.min',
            'intlTelInput': 'js/intlTelInput'
        }
    },
    deps: [
        'homelanetheme',
        'slickslider',
        'intlTelInput'
    ],
    shim: {
        'homelanetheme': ['jquery'],
        'slickslider': ['jquery'],
        'intlTelInput': ['jquery']
    }
};