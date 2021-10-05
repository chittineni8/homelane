require(['jquery'], function ($) {
    $(function () {
        $('.category-heading').on('click', function () {
            $('.category-filter').toggle();
        });
        $('.category-filter').on('click', '.o-list .expand, .o-list .expanded', function () {
            var element = $(this).parent('li');
            if (element.hasClass('active')) {
                element.find('ul:first').slideUp();
                element.removeClass('active');
            } else {
                element.addClass('active');
                element.children('ul').slideDown();
                //element.parent('ul').find('i').addClass('fa-plus');
                //element.find('> span i').addClass('fa-minus');
            }
        });
    });
});
