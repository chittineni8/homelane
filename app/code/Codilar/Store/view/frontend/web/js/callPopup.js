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
            modalClass: 'modal-popup',
            responsive: true,
            innerScroll: true,
            clickableOverlay: true,
            buttons: []
        };
        const callPopup = modal(popup, $('.call-popup'));
        $('.call-popup').modal('openModal');
    }
);
