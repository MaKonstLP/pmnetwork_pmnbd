'use strict';
import mousewheel from 'jquery-mousewheel';
import scrollbar from 'malihu-custom-scrollbar-plugin';

export default class Main{
	constructor(){

		$('body').on('click', '[data-seo-control]', function(){
			$(this).closest('[data-seo-text]').addClass('_active');
		});

		$('body').on('click', '[data-open-popup-form]', function(){
			$('.popup_wrap').addClass('_active');
		});

		$('body').on('click', '[data-close-popup]', function(){
			$('.popup_wrap').removeClass('_active');
            $('.popup_wrap .popup_form').show();
            $('.popup_wrap .popup_img').hide();
		});

        $('body').on('click', '[data-gallery-img-view]', function(){
            $('.popup_wrap .popup_img img').attr('src',$(this).attr('src'));
            $('.popup_wrap .popup_form').hide();
            $('.popup_wrap .popup_img').show();
            $('.popup_wrap').addClass('_active');
        });


		$('body').on('click', '.header_city', function(){
			if ($('.city_list_wrap').hasClass('__visible')) {
				$('.city_list_wrap').removeClass('__visible');
				$(this).find('.choose').removeClass('_expand');
			} else {
				$('.city_list_wrap').addClass('__visible');
				$(this).find('.choose').addClass('_expand');
			}
		});

		$('body').on('click', '.city_list_close', function(e){
    		$('.city_list_wrap').removeClass('__visible');
    	});

        $(".main_search_city_list").mCustomScrollbar({
		    theme:"dark-thick"
		});
		$(".main_search_type_room_list").mCustomScrollbar({
		    theme:"dark-thick"
		});

        $('.fast_filters').each(function(){
            if($(window).width() <= 768){
                $(this).mCustomScrollbar({
                    theme:"dark-thick",
                    axis:"x",
                    advanced:{
                        autoExpandHorizontalScroll:true //optional (remove or set to false for non-dynamic/static elements)
                      }
                });
            }
        });

        $(window).on('resize', function(){
            if($(window).width() <= 768){
                $('.fast_filters').each(function(){
                    if (!$(this).hasClass('mCustomScrollbar')){
                        $(this).mCustomScrollbar({
                            theme:"dark-thick",
                            axis:"x",
                            advanced:{
                                autoExpandHorizontalScroll:true //optional (remove or set to false for non-dynamic/static elements)
                              }
                        });    
                    }
                });
            } else {
                $('.fast_filters').each(function(){
                    $(this).mCustomScrollbar('destroy');
                });
            }
        });


    	$('.main_search_city_list  p').on('click', function(){
    		$('.main_search_city_input').val($(this).html());
    		//$(".main_search_city_list").hide();
    	});
    	$('.main_search_city_input').on('focus',function(){
    		$(".main_search_city_list").slideToggle('Fast');
    	}).on('blur',function(){
    		setTimeout(function(){$(".main_search_city_list").slideToggle('Fast');},100);
    	});

    	$('.main_search_type_room_list  p').on('click', function(){
    		$('.type_room_choose').html($(this).html());
    		$('.type_room_choose').css('color','#000');
    	});
    	$('body').on('click', function(e){
    		var $el = $(e.target);
    		var $parent_scroll = $el.parents('#mCSB_2_scrollbar_vertical');
    		console.log('el',$el);
			console.log('$parent_scroll',$parent_scroll);
    		if ($el.is(".main_search_type_room") || $el.is("span.choose") || $el.is("span.type_room_choose")){
    			$(".main_search_type_room_list").slideToggle('Fast');
    			window.main_search_type_room = !window.main_search_type_room;
	    		if ($(".main_search_type_room .choose").hasClass('_expand')) {
	    			$(".main_search_type_room .choose").removeClass('_expand');
	    			//window.main_search_type_room = false;
	    		} else {
					$(".main_search_type_room .choose").addClass('_expand');
					//window.main_search_type_room = true;
	    		}
    		} else {
    			if (typeof window.main_search_type_room !== 'undefined' && window.main_search_type_room && $parent_scroll.length == 0) {
    				$(".main_search_type_room_list").slideToggle('Fast');
    				$(".main_search_type_room .choose").removeClass('_expand');
	    			window.main_search_type_room = !window.main_search_type_room;
    			}
    		}

    		if ($el.is('input[name="date"]')) {
    			$(".input_wrapper_date_wrapper").slideToggle('Fast');
	    		window.input_wrapper_date_wrapper = !window.input_wrapper_date_wrapper;
    		} else if ($el.is('div.date_wrapper_arrow._next') || $el.is('div.date_wrapper_arrow._prev')) {
    			window.input_wrapper_date_wrapper = true;
    		} else {
    			if (typeof window.input_wrapper_date_wrapper !== 'undefined' && window.input_wrapper_date_wrapper) {
    				$(".input_wrapper_date_wrapper").slideToggle('Fast');
	    			window.input_wrapper_date_wrapper = !window.input_wrapper_date_wrapper;
    			}
    		}
    	});

        $('body').on('click', '.map_close', function(){
            $(this).closest('.map_data_wrap').hide();
        });
	}
}