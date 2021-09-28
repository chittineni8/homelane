require(['jquery','slickslider'], function($) {
	$(document).ready( function() {
		$(window).scroll(function () {
            if ($(this).scrollTop() > 200) {
                $('body').addClass('stickyy');
            } else {
                $('body').removeClass('stickyy');
            }
            if ($(this).scrollTop() > 201) {
                $('body').addClass('slideDown');
            } else {
                $('body').removeClass('slideDown');
            }
        });
        $(window).resize(function () {
            if ($(window).width() < 1024) {
                if ($(this).scrollTop() > 50) {
                    $('body').addClass('stickyy');
                } else {
                    $('body').removeClass('stickyy');
                }

                if ($(this).scrollTop() > 51) {
                    $('body').addClass('slideDown');
                } else {
                    $('body').removeClass('slideDown');
                }
            }
        });
        if ($(window).width() < 768) {
           	var storeclone = $('div#switcher-store div#switcher-store-trigger').clone();
           	console.log(storeclone);
  			$('.mob-store-switcher').html(storeclone);
        }



		if($('ul.header.links').find('.customer-register-link').length <= 0) {
			$("body").addClass("logged-in");
		}

		$(function() {
			//cache a reference to the tabs
			var tabs = $('#moving-tabs  li');
			//on click to tab, turn it on, and turn previously-on tab off
			tabs.click(function() { 
				$(this).addClass('on').siblings('.on').removeClass('on');
			});
			setInterval(function() {
				//get currently-on tab
				var onTab = tabs.filter('.on');
				//click either next tab, if exists, else first one
				var nextTab = onTab.index() < tabs.length-1 ? onTab.next() : tabs.first();
				nextTab.addClass('on').siblings('.on').removeClass('on');;
			}, 3000);
		});

		var submitIcon = $('form#search_mini_form .field.search label');
		var inputBox = $('input#search');
		var searchBox = $('.block.block-search');
		var isOpen = false;
		submitIcon.click(function(){
			if(isOpen == false){
				searchBox.addClass('searchbox-open');
				inputBox.focus();
				isOpen = true;
			} else {
				searchBox.removeClass('searchbox-open');
				inputBox.focusout();
				isOpen = false;
			}
		});  
		submitIcon.mouseup(function(){
			return false;
		});
		searchBox.mouseup(function(){
			return false;
		});
		$(document).mouseup(function(){
			if(isOpen == true){
				$('form#search_mini_form .field.search label').css('display','block');
				submitIcon.click();
			}
		});
		$('li.link.authorization-link a').addClass('customer-login-link')
		function buttonUp(){
            var inputVal = $('input#search').val();
            inputVal = $.trim(inputVal).length;
            if( inputVal !== 0){
                $('form#search_mini_form .field.search label').css('display','none');
            } else {
                $('input#search').val('');
                $('form#search_mini_form .field.search label').css('display','block');
            }
        }
		if ($(window).width() < 768) {
			$(".hp-category-block .pagebuilder-column-group").not(".slick-initialized").slick({
		        dots: false,arrows: true,infinite: true,speed: 300,slidesToShow: 1,slidesToScroll: 1,
		        centerMode: true,centerPadding: '80px',autoplay: true,initialSlide: 1,speed: 1000,cssEase: 'linear',
		    });
		}
		$(".hp-customer-review-outer ul").not(".slick-initialized").slick({
		    dots: true,arrows: true,infinite: true,speed: 300,slidesToShow: 1,slidesToScroll: 1,
		    autoplay: true,initialSlide: 1,speed: 1000,cssEase: 'linear',
		});

		$("form.query-form .control input[type='text']").on('change keyup', function() {
		  if ($(this).val().length > 0) {
		    $(this).parent().addClass('input-has-value');
		  } else {
		    $(this).parent().removeClass('input-has-value');
		  }  
		});
		$("form.query-form .control input[type='email']").on('change keyup',function () {    
			var inputvalues = $(this).val();    
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;    
			if(regex.test(inputvalues)) {    
   				$(this).parent().addClass('input-has-value');
			} else {
				$(this).parent().removeClass('input-has-value');
			}
		});
		$("form.query-form .control.phoneno input[type='number']").on('change keyup',function () { 
			var mobNum = $(this).val();   
			if(mobNum.length == 10) {
				$(this).parent().addClass('input-has-value');
			} else {
				$(this).parent().removeClass('input-has-value');
			}
		});
		$("form.query-form .control.pincode input[type='number']").on('change keyup',function () { 
			var mobNum = $(this).val();   
			if(mobNum.length == 6) {
				$(this).parent().addClass('input-has-value');
			} else {
				$(this).parent().removeClass('input-has-value');
			}
		});


	});
});