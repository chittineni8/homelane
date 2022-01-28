/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
	map: {
        '*': {
            'homelanetheme': 'js/homelane-theme',
            'slickslider': 'js/slick.min',
            'intlTelInput': 'js/intlTelInput',
            'popupWindow':  'mage/popup-window'
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
    },
    config: {        
        mixins: {
        'mage/collapsible': {
            'js/mage/collapsible-mixin': true
            }
        }
    }
};