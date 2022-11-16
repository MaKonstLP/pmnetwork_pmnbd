'use strict';
import Swiper from 'swiper';
import 'slick-carousel';

export default class Item {
	constructor($item) {
		var self = this;

		$('[data-action="roll_to_map"]').on('click', function () {
			let map_offset_top = $('.map').offset().top;
			let map_height = $('.map').height();
			let header_height = $('header').height();
			let window_height = $(window).height();
			let scroll_length = map_offset_top - header_height - ((window_height - header_height) / 2) + map_height / 1.5;

			setTimeout(function () {
				$('html,body').animate({scrollTop: scroll_length}, 400);
			}, 100);

		});

		$('[data-action="show_phone"]').on('click', function () {
			$('.object_book_hidden').addClass('_active');
		});

		$('[data-order]').on('click', function () {
			let map_offset_top = $('.form_wrapper').offset().top;
			let map_height = $('.form_wrapper').height();
			// let header_height = $('header').height();
			let window_height = $(window).height();
			// let scroll_length = map_offset_top - header_height - ((window_height - header_height) / 2) + map_height / 2;
			let scroll_length = map_offset_top - ((window_height) / 2) + map_height / 2;

			$('html,body').animate({ scrollTop: scroll_length }, 400);
		})


		var block_show = null;
		function scrollTracking(){
			var wt = $(window).scrollTop();
			var wh = $(window).height();
			var ww = $(window).width();
			var et = $('.object_meta._mobile').offset().top;
			var eh = $('.object_meta._mobile').outerHeight();

			if (ww > 570 && $('.object_meta._mobile').length > 0)
				return;

			if (wt + wh >= et && wt + wh - eh * 2 <= et + (wh - eh)){
				if (block_show == null || block_show == false) {
					$('.display_bottom').removeClass('_active');
					$('.footer_wrap').css('margin-bottom','0');
				}
				block_show = true;
			} else {
				if (block_show == null || block_show == true) {
					$('.display_bottom').addClass('_active');
					$('.footer_wrap').css('margin-bottom','72px');
				}
				block_show = false;
			}
		}

		$(window).scroll(function(){
			scrollTracking();
		});

		$(document).ready(function(){
			scrollTracking();
		});

	}
}