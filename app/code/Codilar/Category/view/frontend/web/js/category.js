require(['jquery'], function ($) {
    $(function () {
        $('.category-filter').on('click', '.o-list .expand, .o-list .expanded', function () {
            var element = $(this).parent('li');
            if (element.hasClass('active')) {
                element.find('ul').slideUp();
                element.removeClass('active');
                element.removeClass('active-cat');
            } else {
                element.addClass('active');
                element.children('ul').slideDown();
            }
        });
        var currentli = $('li.current-category');
        if ($('ul.o-list li').hasClass('current-category')) {
            $(this).find('li.current-category').closest('ul').parentsUntil('.category-filter').addClass('active-cat').slideDown();
            $(this).find('li.current-category').closest('ul').parentsUntil('.category-filter').addClass('active');
            $(this).find('li.current-category').closest('ul').slideDown();
        }
    });
});
