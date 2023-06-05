'use strict';
import Swiper from 'swiper';
import 'slick-carousel';

export default class Item {
	constructor($item) {
		let self = this;
		var fired = false;

		window.addEventListener('click', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		window.addEventListener('scroll', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		window.addEventListener('mousemove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		window.addEventListener('touchmove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, {passive: true});

		function load_other() {
			console.log('load_other');
			setTimeout(function() {
				self.reviewInit();
			}, 100);
		}

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

		$('[data-show-phone]').on('click', function() {
			let blur_phone = $(this).find('.object_fake_phone');
			let real_phone = $(this).find('.object_real_phone');

			blur_phone.toggleClass('hidden');
			real_phone.toggleClass('hidden');
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



	reviewInit(){
		let self = this;
		let yaIframe = $('[data-src]');
		if(yaIframe.length > 0){
			yaIframe.attr('src', yaIframe.data('src') );
			// this.script(yaUrl).then((data) => {
			// 	yaWrapper.attr('src', yaUrl );
			// });
		}
	}


	script(url) {
		console.log('script');
		return new Promise(function (resolve, reject) {
			let r = false;
			let t = document.getElementsByTagName('script')[0];
			let s = document.createElement('script');

			s.type = 'text/javascript';
			s.src = url;
			s.async = true;
			s.onload = s.onreadystatechange = function () {
				if (!r && (!this.readyState || this.readyState === 'complete')) {
					r = true;
					resolve(this);
				}
			};
			s.onerror = s.onabort = reject;
			t.parentNode.insertBefore(s, t);
		});
	}


}