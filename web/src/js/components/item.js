'use strict';
import Swiper from 'swiper';
import 'slick-carousel';

export default class Item {
	constructor($item) {
		var self = this;

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
	}
}