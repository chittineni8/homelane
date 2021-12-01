require(['jquery','slickslider'], function($) {
	$(document).ready( function() {
		$(window).scroll(function () {
            if ($(this).scrollTop() > 0) {
                $('body').addClass('stickyy');
            } else {
                $('body').removeClass('stickyy');
            }
            if ($(this).scrollTop() > 1) {
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
  			$('.mob-store-switcher').html(storeclone);
  			var sortbarclone = $('.toolbar-sorter.sorter').clone();
  			$('.filter-sortbar').html(sortbarclone);
  			var username = $('.customer-menu ul.top').clone();
  			$('.user-details-login').html(username);
  			var loginuserdetail = $('div.customer-menu').clone();
  			$('.login-user-details').html(loginuserdetail);

  			$("span.close-filter").click(function () {
		      $('div#layered-filter-block').toggleClass("active");
		      $('.block.filter .filter-title > strong').attr("aria-expanded","false");
		      $('.block.filter .filter-title > strong').attr("aria-selected","false");
		      $('body').toggleClass("filter-active");
		    });
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
			$(".hp-category-block .pagebuilder-column-group, .category-grid .image-box").not(".slick-initialized").slick({
		        dots: false,arrows: true,infinite: true,speed: 300,slidesToShow: 1,slidesToScroll: 1,
		        centerMode: true,centerPadding: '80px',autoplay: true,initialSlide: 1,speed: 1000,cssEase: 'linear',
		    });
		}
		$(".block.related .product-items").not(".slick-initialized").slick({
	        dots: false,arrows: true,infinite: true,speed: 300,slidesToShow: 4,slidesToScroll: 1,
	        centerMode: false,centerPadding: '80px',autoplay: true,initialSlide: 1,speed: 1000,cssEase: 'linear',
	        responsive: [
                {
                    breakpoint: 1023,
                    settings: {slidesToShow: 3,centerMode: true,centerPadding: '80px',}
                },
                {
                breakpoint: 767,
                    settings: {slidesToShow: 1,centerMode: true,centerPadding: '80px',}
                }
            ]
	    });
		$(".hp-customer-review-outer ul").not(".slick-initialized").slick({
		    dots: true,arrows: true,infinite: true,speed: 300,slidesToShow: 1,slidesToScroll: 1,
		    autoplay: true,initialSlide: 1,speed: 1000,cssEase: 'linear',
		});
		
		$('#layer-product-list').find('.pages a').click(function(){
			$("html, body").animate({ scrollTop: 0 }, "fast");

		});
		$("body").bind("ajaxComplete", function(e, xhr, settings) {
			$('#layer-product-list').find('.pages a').click(function(){
				$("html, body").animate({ scrollTop: 0 }, "fast");
			});
			$('a.mp-wishlist-delete').on('click', function(){
				$("html, body").animate({ scrollTop: 0 }, "fast");
			});
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
		if( !$('.customer-account-wrapper .control input').val() ) {
		    $('button#submit-query').prop('disabled', true);
		}
		$("form.query-form .control input").on('change keyup',function () {
			if ($('.control.input-has-value').length == 4) {
				$('button#submit-query').prop('disabled', false);
			} else {
				$('button#submit-query').prop('disabled', true);
			}
		});
		$("ol.filter-list").each(function () {
	      var liCount = $(this).children("li").length;
	      if (liCount > 5) {
	        $(this).next(".more").addClass("showMe");
	      }
	    });

	    $("label.more").click(function () {
	      $(this).prev("ol").find("li").toggleClass("showList");
	      $(this).text(this.innerHTML.includes("All") ? "See less..." : "See All");
	    });

	    /*sign up form*/
	    $(".customer-account-wrapper .control input[type='text']").on('change keyup', function() {
		  if ($(this).val().length > 3) {
		    $(this).parent().addClass('input-has-value');
		  } else {
		    $(this).parent().removeClass('input-has-value');
		  }  
		});
		$(".customer-account-wrapper .control #popup-email_address").on('change keyup',function () {    
			var inputvalues = $(this).val();    
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;    
			if(regex.test(inputvalues)) {    
   				$(this).parent().addClass('input-has-value');
			} else {
				$(this).parent().removeClass('input-has-value');
			}
		});
		$(".customer-account-wrapper .control #customer_mobile").on('change keyup blur',function () {
			$(this).parent().find('div.mage-error').hide(); 
			var mobNum = $(this).val(); 
			if(mobNum.length == 10) {
				$(this).parent().addClass('input-has-value');
				$('#mob-error-msg').hide();
			} else {
				$(this).parent().removeClass('input-has-value');

			}
		});
		$(".customer-account-wrapper .control #customer_mobile").on('change focusout',function () {
			var mobNum = $(this).val(); 
			if(mobNum.length == 10) {
				$('#mob-error-msg').hide();
			} else {
				$('#mob-error-msg').show();
			}
		});
		$(".customer-account-wrapper .control .validate-zip-international").on('change keyup',function () {
			$(this).parent().find('div.mage-error').hide(); 
			var mobNum = $(this).val();   
			if(mobNum.length == 6) {
				$(this).parent().addClass('input-has-value');
			} else {
				$(this).parent().removeClass('input-has-value');
			}
		});
		if( !$('.customer-account-wrapper .control input').val() ) {
		    $('button#submit').prop('disabled', true);
		}
		$(".customer-account-wrapper .control input").on('change keyup',function () {
			if ($('.control.input-has-value').length == 4) {
				$('button#submit').prop('disabled', false);
			} else {
				$('button#submit').prop('disabled', true);
			}
		});
		/*end sign up form*/

	});
});
