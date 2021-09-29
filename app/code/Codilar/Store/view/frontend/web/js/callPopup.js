require(
    [
        'jquery',
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
        if ($('.call-popup').first().data('show-popup') == true){
            popupModel.modal('openModal');
        }
        // $('.modal-footer').hide();
        $("div#switcher-store-trigger").click(function() {
            popupModel.modal('openModal');
        });
    }
);
