/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_SMSProfile
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author    Magedelight <info@magedelight.com>
 */
define([
        'jquery',
        'Magento_Checkout/js/model/quote',
        'mage/url',
        'ko',
        'mage/translate',
        'mage/mage',
        'jquery/validate'
    ],
    function($, quote, url, ko) {
        var configValues = window.checkoutConfig;

        $(document).ready(function() {

            /* code for otp for cod start here */

            $(document).on("click", "#shipping-method-buttons-container .button", function() {
                var address = quote.shippingAddress();
                var billingAddress = quote.billingAddress();
                if(billingAddress != null) {
                    if($(document).find(".resendlink").length > 0 && $(document).find(".field-name-otp").length > 0 && address.telephone != billingAddress.telephone) {
                            $(document).find(".resendlink").remove();
                            $(document).find(".field-name-otp").remove();
                            $(document).find(".send_otp_cod").show();
                            $(document).find('#cashondelivery_codotp').val(0);
                            $(document).find('.otp_text').remove();
                            $(document).find('.cod').addClass('disabled');
    
                    }
                }
             }); 
               
             $(document).on("click", ".send_otp_cod", function() {
                var sendotpurl = url.build('smsprofile/otp/send');
                var address = quote.shippingAddress();                
                var mobile = address.telephone;
                var isResend = 0;
                $.ajax({

                    showLoader: true,
                    url: sendotpurl,
                    method: "POST",
                    data: {
                        mobile: mobile,
                        eventType: 'cod_otp',
                        resend: isResend,
                    },
                    dataType: "json"
                }).done(function(response) {
                    if (response.Success === 'success') {
                        $(document).find(".otp_cod_generatenote").html('OTP is send successfully.');
                        $(document).find('.otp_cod_generatenote').delay(5000).fadeOut(800);
                        if($(".resendlink").length == 0 && $(".field-name-otp").length == 0) {
                            $(".otp_cod_generatenote").after('<div class="resendlink"><a href="javascript:void(0);" class="resendotpCod" >Resend OTP</a></div><div class="field field-name-otp required"><label class="label" for="otp"><span>Please enter verification code here</span></label>  <div class="control"> <input id="otp_cod" name="otp" value="" title="otp" class="input-text required-entry" data-validate="{required:true}" autocomplete="off" aria-required="true" type="text" novalidate="novalidate" style="width: 200px;"> <button style="display: block;margin-top: 10px;" class="verif_otp_cod action primary" type="button">Verify OTP</button> </div></div><span class="otp_text"></span> ');
                        }     

                        
                    } else {
                        $(document).find(".otp_cod_generatenote").html('Not able to send SMS without respective OTP');
                        $(document).find('.otp_cod_generatenote').delay(5000).fadeOut(800);
                    }

                });
                $('.send_otp_cod').unbind("click"); /*stop click event after html added */ 
            });

             $(document).on("click", ".verif_otp_cod", function() {
                var sendotpurl = url.build('smsprofile/otp/verify');
                if ($('#otp_cod').val() != '') {
                    $('.otp_text').html('');
                    var address = quote.shippingAddress();
                    var mobile = address.telephone;
                    
                    $.ajax({
                        showLoader: true,
                        url: sendotpurl,
                        method: "POST",
                        data: {
                            otp: $("#otp_cod").val(),
                            mobile: mobile
                        },
                        dataType: "json"
                    }).done(function(response) {
                        if (response) {
                            $(document).find(".otp_cod_generatenote").html('');
                            $(document).find('.otp_cod_generatenote').fadeIn();
                            $('.otp_text').html(response.message);
                            if (response.message == 'Verified') {
                                 $('.otp_text').css('color','green');
                                $('.send_otp_cod').hide();
                                $('.resendlink').hide();
                                $('.field-name-otp').hide();
                                $('#cashondelivery_codotp').val(1);
                                $('.checkout-index-index .actions-toolbar .cod').removeClass('disabled');
                                
                            } else {
                                 $('.otp_text').css('color','red');
                                $('#cashondelivery_codotp').val(0);
                            }
                        }
                    });
                }  else {
                    alert('Please enter OTP');
                }
            });

            /* code for otp for cod ends here */

            /* code for resend otp start here*/ 

            $(document).on("click",".resendotpCod", function(){
                var sendotpurl = url.build('smsprofile/otp/send');
                var event = 'cod_otp';
                var address = quote.shippingAddress();
                var mobile = address.telephone;
                var isResend =1; 
                if($(document).find('.checkout-index-index .actions-toolbar .cod').hasClass("disabled")) {
                    $.ajax({
                        showLoader: true,
                        url: sendotpurl,
                        method: "POST",
                        data: {
                            mobile : mobile,
                            eventType : event,
                            resend : isResend,
                        },
                        dataType: "json"
                      }).done(function( response ){
                          if(response.Success === 'success' ){
                               $(document).find(".otp_cod_generatenote").fadeIn();
                               $(document).find(".otp_cod_generatenote").html('OTP is send successfully.');
                               $(document).find('.otp_cod_generatenote').delay(5000).fadeOut(800);
                          }
                          else {
                               $(document).find(".otp_cod_generatenote").fadeIn();
                               $(document).find(".otp_cod_generatenote").html('Not able to send SMS without respective OTP');
                               $(document).find('.otp_cod_generatenote').delay(5000).fadeOut(800);
                          }
                    });
                }      
            });
        });
    }
);