/**
 * Webkul Software
 * @category Webkul
 * @package  Webkul_MpZipCodeValidator
 * @author   Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
define([
    'jquery'
], function ($) {
    'use strict';
    $.widget('webkul.multishipping', {
        options: {
        },
        _create: function () {
            var self = this;
            $('document').ready(function() {
                var data = self.options.data;
                var productId = [];
                var zipcode = [];
                var i;
                for (i in data) {
                    if (data[i]['productId'] != null) {
                        productId.push(parseInt(data[i]['productId']));
                    }
                    if (data[i]['zipcode'] != null) {
                        zipcode.push(parseInt(data[i]['zipcode']))
                    }
                }
                setCustomMessage(productId, zipcode);
            });
            function setCustomMessage(productId, zipcode) {  
                var self = this;
                var url = window.location.href;
                var pathname = window.location.pathname.split('/multishipping/checkout/shipping/');
                var baseurl = pathname[0];
                
                var messageAjax = $.ajax({
                    url : baseurl +"/mpzipcodevalidator/zipcode/multishippingresult",
                    data : {
                        zipcode :zipcode,
                        productId : productId
                    },
                    type : "GET",
                    success : function (response) {
                        if (response != '') {
                            $('.actions-toolbar').hide();
                            $(".multi_shipping_error_message").html(response.message);
                        } else {
                            $('.actions-toolbar').show();
                        }
                    }
                });
            }
        },
    });
    return $.webkul.multishipping;
});