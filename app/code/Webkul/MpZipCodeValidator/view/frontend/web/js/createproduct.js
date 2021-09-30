/**
 * @category Webkul
 * @package  Webkul_MpZipCodeValidator
 * @author   Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
define([
"jquery",
'mage/translate',
'Magento_Ui/js/modal/alert',
"mage/mage",
], function ($, $t, alert) {
    'use strict';
    $.widget('webkul.createproduct', {
        options: {
        },
        _create: function () {
            var self = this;
            $("#wk-zcv-valid-action").on("change", function(){
                var wk_zcv_valid_id = document.getElementById("wk-zcv-valid-action");
                var wk_zcv_region_value = wk_zcv_valid_id.options[wk_zcv_valid_id.selectedIndex].value;
                if(wk_zcv_region_value != 0) {
                    document.getElementById("wk-zcv-region-field").style.display='none';
                } else {
                    document.getElementById("wk-zcv-region-field").style.display='block';
                }
            });
            $('document').ready(function() {
                var wk_zcv_valid_id = document.getElementById("wk-zcv-valid-action");
                var wk_zcv_region_value = wk_zcv_valid_id.options[wk_zcv_valid_id.selectedIndex].value;
                if(wk_zcv_region_value == 0) {
                    document.getElementById("wk-zcv-region-field").style.display='block';
                }
            });
        }
    });
    return $.webkul.createproduct;
});