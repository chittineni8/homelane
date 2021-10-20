require(
    [
        'jquery',
    ],
    function(
        $
    ) {
        $(function() {

            const PARAMS = 'jlnarvaez_whatsappshare_params';
            const ENABLED = `${PARAMS}_enabled`;
            const PRODUCT_NAME = `${PARAMS}_show_product_name`;
            const PRODUCT_DESC = `${PARAMS}_show_product_desc`;
            const PRODUCT_PRICE = `${PARAMS}_show_product_price`;
            const ROW_PARAMS = `row_${PARAMS}`;
            const URL_SAMPLE = window.location.origin + "/productname";

            let defaults = {};
            defaults[ENABLED] = '';
            defaults[PRODUCT_NAME] = '<strong>Product Title Example</strong>';
            defaults[PRODUCT_DESC] = 'This is a sample product description. This product is great and is very very cheap. Have a lot of colors to choose.';
            defaults[PRODUCT_PRICE] = '25.99 €';


            createElemPreview();
            let selects = $(`#${PARAMS} .value select`);
            initLoad(selects);

            selects.on('change', function() {
                initLoad($(this))
            });


            function initLoad(currentElem)
            {
                let previewField = $(`#${ROW_PARAMS}_previewfield .preview-field-container`);
                previewField.html('');
                if ((currentElem.attr('id') === ENABLED) && (currentElem.val() === '0')) {
                    $(`[id^="${ROW_PARAMS}"]`).slice(1).hide();
                } else {
                    $(`[id^="${ROW_PARAMS}"]`).slice(1).show();
                    $(`#${PARAMS} .value select option[value=1]:selected`).each(function() {
                        let curField = $(this);
                        let attrId = curField.closest('select').attr('id');
                        if (attrId !== ENABLED) {
                            previewField.append(defaults[attrId] + '<br/><br/>');
                        }
                    });

                    previewField.append(`<a href="#">${URL_SAMPLE}</a>`);
                }
            }

            /**
             * Will create a container to show preview text
             */
            function createElemPreview()
            {
                let newElem = `<tr
                        id="${ROW_PARAMS}_previewfield"
                        style="display: none;">
                            <td class="label">
                                <label for="${ROW_PARAMS}_preview">
                                    <span>Preview</span>
                                </label>
                            </td>
                            <td class="value">
                                <div class="preview-field-container" style="
                                    background-color: #e5ffd6;
                                    padding: 0 10px 10px 10px;">
                                 </div>
                            </td>
                        </tr>`;
                $(`#${PARAMS} table`).append(newElem);
            }

        });
    });