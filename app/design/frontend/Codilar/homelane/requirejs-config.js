/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
	map: {
        '*': {
            'homelanetheme': 'js/homelane-theme'
        }
    },
    deps: [
        'homelanetheme'
    ],
    shim: {
        'homelanetheme': ['jquery']
    }
};