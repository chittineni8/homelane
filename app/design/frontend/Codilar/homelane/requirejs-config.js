/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
	map: {
        '*': {
            'homelanetheme': 'js/homelane-theme',
            'slickslider': 'js/slick.min'
        }
    },
    deps: [
        'homelanetheme',
        'slickslider'
    ],
    shim: {
        'homelanetheme': ['jquery'],
        'slickslider': ['jquery']
    }
};