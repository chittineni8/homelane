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
        // const callPopup = modal(popup, $('.call-popup'));
        $('.call-popup').modal(popup).modal('openModal');
        // $('.modal-footer').hide();
        $("div#switcher-store-trigger").click(function() {
            $('.call-popup').modal('openModal');
        });
    }
);
