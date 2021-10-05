require(
    [
        'jquery',
        'jquery/jquery.cookie',
        'Magento_Ui/js/modal/modal'
    ],
    function (
        $,
        modal
    ) {
        const popup = {
            type: 'popup',
            modalClass: 'modal-popup city-popup',
            responsive: true,
            innerScroll: true,
            clickableOverlay: true,
            buttons: []
        };
        let popupModel = $('.call-popup').modal(popup);
        let website_code = $.cookie("website_code");
        if (website_code == null){
            popupModel.modal('openModal');
        }
        // $('.modal-footer').hide();
        $("div#switcher-store-trigger").click(function() {
            popupModel.modal('openModal');
        });
    }
);
