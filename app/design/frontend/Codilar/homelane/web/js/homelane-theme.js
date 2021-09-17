require(['jquery'], function($) {
	$(document).ready( function() {
		console.log('comming...');
		if($('ul.header.links').find('.customer-register-link').length <= 0) {
			$("body").addClass("logged-in");
		}

		$(function() {
			//cache a reference to the tabs
			var tabs = $('#moving-tabs li');
			//on click to tab, turn it on, and turn previously-on tab off
			tabs.click(function() { 
				$(this).addClass('on').siblings('.on').removeClass('on');
			});
			setInterval(function() {
				//get currently-on tab
				var onTab = tabs.filter('.on');
				//click either next tab, if exists, else first one
				var nextTab = onTab.index() < tabs.length-1 ? onTab.next() : tabs.first();
				nextTab.click();
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
	});
});