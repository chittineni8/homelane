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
    $.widget('webkul.regionlist', {
        options: {
            errorSelectRegion: $t("Please select valid entries."),
            errorSelectAction: $t("Please select Action.")
        },
        _create: function () {
            var self = this;
            $("#wk-zcv-select-action").on("change", function(){
                var wk_zcv_select_id = document.getElementById("wk-zcv-select-action");
                var wk_zcv_select_value = wk_zcv_select_id.options[wk_zcv_select_id.selectedIndex].value;
                if(wk_zcv_select_value == '') {
                    document.getElementById("submitButton").style.display='none';
                } else {
                    document.getElementById("submitButton").style.display='block';
                }
            });
            $("#wk-zcv-zipcode-select").on("change", function(){
                var wk_zcv_select_ids = document.getElementById("wk-zcv-zipcode-select");
                var wk_zcv_select_values = wk_zcv_select_ids.options[wk_zcv_select_ids.selectedIndex].value;
                if(wk_zcv_select_values == '') {
                    document.getElementById("wk-zcv-zipcode-button").style.display='none';
                } else {
                    document.getElementById("wk-zcv-zipcode-button").style.display='block';
                }
            });
            $("#filterForm").on("click", function(event) {
                event.preventDefault();
                var regionNameValue = document.getElementById("regionname").value;
                var changeRegionName = regionNameValue.replace(/[^a-zA-Z ]/g, "");
                document.getElementById("regionname").value = changeRegionName;
                document.getElementById("form-regionlist-filter").submit();
            });
            $("#fliter_zipcode_form").on("click", function(event) {
                event.preventDefault();
                var zipcodeId = document.getElementById("zipcodeid").value;
                var changezipcodeValue = zipcodeId.replace(/[^a-zA-Z0-9 ]/g, "");
                document.getElementById("zipcodeid").value = changezipcodeValue;
                var zipcodeValue = document.getElementById("zipcodevalue").value;
                var changezipcodeValue = zipcodeValue.replace(/[^a-zA-Z0-9 ]/g, "");
                document.getElementById("zipcodevalue").value = changezipcodeValue;
                document.getElementById("form-zipcodelist-filter").submit();
            });

            $("#special-from-date").datepicker({dateFormat: "yy-mm-dd"});
            $("#special-to-date").datepicker({dateFormat: "yy-mm-dd"});
            $(document).ready(function () {
                $(document).on('click', '#wk-zcv-delete-checkbox', function (event) {
                    if (!this.checked) {
                        $('.wk-zcv-delete-checkbox').each(function () {
                            $(this).removeAttr('checked','checked');
                        })
                    } else {
                        $('.wk-zcv-delete-checkbox').each(function () {
                            $(this).attr('checked','checked');
                        })
                    }
                });
                $(document).on('click', '.wk-ssp-mass-action-submit', function (event) {
                    var flag = 0;
                    $(".wk-zcv-delete-checkbox").each(function () {
                        if ($(this).is(':checked')) {
                            flag = 1;
                        }
                    });
                    if (flag == 0) {
                        $(this).val('');
                        alert({
                            content: self.options.errorSelectRegion
                        });
                        return false;
                    } else {
                        if (flag == 1) {
                            if (!$('.wk-ssp-mass-action').val()) {
                                alert({
                                    content: self.options.errorSelectAction
                                });
                                return false;
                            }
                        }
                    }
                });
            })
        }
    });
    return $.webkul.regionlist;
});


