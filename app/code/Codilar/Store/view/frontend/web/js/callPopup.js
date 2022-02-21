require(
    [
        'jquery',
        'jquery/jquery.cookie',
        'Magento_Ui/js/modal/modal'
    ],
    function ($,modal) {
        const popup = {
            type: 'popup',
            modalClass: 'modal-popup city-popup',
            responsive: true,
            innerScroll: true,
            clickableOverlay: true,
            buttons: []
        };
        var popupModel = $('.call-popup').modal(popup);
        var website_code = $.cookie("website_code");
        console.log(website_code);
        if (website_code == null){
                popupModel.modal('openModal');
            }
        $("ul#searchable-content li").on('click', function() {
            var current_store_url = $(this).find('.city-url').attr('data-storeurl');
            // alert(current_store_url)
            $.cookie('website_code', current_store_url);
        });
        $("div#switcher-store-trigger").on('click',function() {
            popupModel.modal('openModal');
        });

        $('.city-popup .action-close, .modals-overlay').click(function(){
            var current_store_url = $("ul#searchable-content li.current-website").find('.city-url').attr('data-storeurl');
            $.cookie('website_code', current_store_url);
        });
    });
