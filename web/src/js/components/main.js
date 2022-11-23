'use strict';
import mousewheel from 'jquery-mousewheel';
import scrollbar from 'malihu-custom-scrollbar-plugin';
import Cookies from 'js-cookie';

export default class Main {
	constructor() {
		var self = this;


		$('body').on('click', '[data-seo-control]', function () {
			$(this).closest('[data-seo-text]').addClass('_active');
		});

		// $('body').on('click', '[data-open-popup-form]', function () {
		// 	$('.popup_wrap').not('.popup_phone_wrap').not('.popup_wrap_blog').not('.popup_wrap_single-map').addClass('_active');
		// 	$('body').addClass('_popup_overflow');

		// 	let dataFormTarget = $('.form_block').data('form-target');
		// 	if ($('[data-page-type="index"].post-blog').length && $(this).data('rest-name')) {
		// 		let restaurantType = $(this).data('rest-type').toLowerCase();
		// 		let restaurantName = $(this).data('rest-name');
		// 		$('.form_title_main').html('Забронировать ' + restaurantType + ' «' + $.trim(restaurantName) + '»');

		// 		$('.form_block').attr('data-form-target', 'bron_2');
		// 	} else {
		// 		$('.form_title_main').html('Помочь подобрать зал?');
		// 		$('.form_block').attr('data-form-target', dataFormTarget);
		// 	}
		// });


		$('body').on('click', '[data-open-popup-form-blog]', function () {
			$('.popup_wrap.popup_wrap_blog').addClass('_active');
			$('body').addClass('_popup_overflow');
			
			let dataFormTarget = $(this).data('form-target'); 
			if ($(this).data('rest-name')) {
				let restaurantType = $(this).data('rest-type').toLowerCase();
				let restaurantName = $(this).data('rest-name');
				$('.popup_wrap_blog .form_title_main').html('Забронировать ' + restaurantType + ' «' + $.trim(restaurantName) + '»');
				$('.form_block').attr('data-form-target', dataFormTarget);
			} else {
				$('.popup_wrap_blog .form_title_main').html('Помочь подобрать зал?');
			}
		});

		$('body').on('click', '[data-close-popup]', function () {
			$('.popup_wrap').removeClass('_active');
			$('.popup_wrap .popup_form').show();
			$('.popup_wrap .popup_img').hide();
			$('.popup_wrap .popup_form .form_main').show();
			$('.popup_wrap .popup_form .form_success').hide();
			$('body').removeClass('_popup_overflow');
		});

		$('[data-menu-burger]').on('click', function(){
			$(this).toggleClass('_active');
			$('[data-menu]').toggleClass('_active');
			// $('body').toggleClass('_popup_overflow');
		});

	
		/*$('body').on('click', '[data-gallery-img-view]', function () {
			 //$('.popup_wrap .popup_img img').attr('src', $(this).attr('src'));
			 $('.popup_wrap .popup_form').hide();
			 $('.popup_wrap .popup_img').show();
			 $('.popup_wrap').addClass('_active');
		});*/

		$('body').on('click', '[data-popup-phone]', function () {
			$('.popup_wrap .popup_phone').css({
				top: $(this).position().top,
				left: $(this).position().left,
			});
			$('.popup_phone_wrap').addClass('_active');

			// ==== Gorko-calltracking ====
			let phone = $(this).closest('.object_book_hidden').find('.popup_real_phone span').text();
			self.sendCalltracking(phone);
		});

		$('body').on('click', '[data-popup-phone-list]', function () {
			/* $(this).siblings('.popup_wrap').find('.popup_phone').css({
				top: $(this).position().top,
				left: $(this).position().left,
			}); */
			$(this).siblings('.popup_phone_wrap').addClass('_active');

			// ==== Gorko-calltracking ====
			let phone = $(this).closest('.item_info').find('.popup_real_phone span').text();
			self.sendCalltracking(phone);
		});

		/*$('body').on('click', '.header_city', function () {
			 if ($('.city_list_wrap').hasClass('__visible')) {
				  $('.city_list_wrap').removeClass('__visible');
				  $(this).find('.choose').removeClass('_expand');
			 } else {
				  $('.city_list_wrap').addClass('__visible');
				  $(this).find('.choose').addClass('_expand');
			 }
		});*/


		// if ($('[data-page-type="index"].post-blog').length) {
		// 	$('[data-open-popup-form]').on('click', function () {
		// 		let restaurantType = $(this).data('rest-type').toLowerCase();
		// 		let restaurantName = $(this).data('rest-name');
		// 		let formTitle = $('.form_title_main');
		// 		formTitle.html('Забронировать ' + restaurantType + ' «' + $.trim(restaurantName) + '»');
		// 	})
		// }




		// $('div[id^="icon"]').on('click', function(e) {
		// 	alert( $(this).data('id') );
		//  });








		$('body').on('click', '.city_list_close', function (e) {
			$('.city_list_wrap').removeClass('__visible');
		});

		$('.main_search_city_list').mCustomScrollbar({
			theme: 'dark-thick',
		});
		$('.main_search_type_room_list').mCustomScrollbar({
			theme: 'dark-thick',
		});

		$('.fast_filters').each(function () {
			if ($(window).width() <= 768) {
				$(this).mCustomScrollbar({
					theme: 'dark-thick',
					axis: 'x',
					documentTouchScroll: true,
					contentTouchScroll: 2,
					advanced: {
						autoExpandHorizontalScroll: true, //optional (remove or set to false for non-dynamic/static elements)
					},
				});
			}
		});

		$('[data-seo-text]').each(function () {
			if ($(this).height() > 200 && $(window).width() < 600) {
				$(this).addClass('_hidden');
			}
		});

		//НЕ ПОКАЗЫВАТЬ ЕЩЕ ЕСЛИ НЕТ РЕСТОРАНОВ
		if ($('body').find('[data-page-increase="1"]').length == 0) {
			$('[data-append-items]').hide();
		}

		$(window).on('resize', function () {
			$('[data-seo-text]').each(function () {
				if ($(this).height() > 200 && $(window).width() < 600) {
					$(this).addClass('_hidden');
				}
			});
			if ($(window).width() <= 768) {
				$('.fast_filters').each(function () {
					if (!$(this).hasClass('mCustomScrollbar')) {
						$(this).mCustomScrollbar({
							theme: 'dark-thick',
							axis: 'x',
							advanced: {
								autoExpandHorizontalScroll: true, //optional (remove or set to false for non-dynamic/static elements)
							},
						});
					}
				});
			} else {
				$('.fast_filters').each(function () {
					$(this).mCustomScrollbar('destroy');
				});
			}
		});

		let searchInCityList = function ($input) {
			let word = $input.val();
			let wrap = $input.closest('[data-search-wrap]');
			wrap.find('[data-search-city]').each(function () {
				let city_name = $(this)
					.html()
					.replace('<span>', '')
					.replace('</span>', '');
				let $substr = city_name
					.toLowerCase()
					.search(word.toLowerCase());
				let char_wrap = $(this).closest(
					'[data-search-city_in_char_wrap]'
				);
				let char_cities = char_wrap.find('[data-search-city_in_char]');
				if (word != '' && $substr == -1) {
					$(this).html(city_name);
					$(this).hide();
					if (char_cities.children(':visible').length < 1)
						char_wrap.hide();
				} else {
					let $str1 = city_name.substring(0, $substr);
					let $str2 = city_name.substring(
						$substr,
						$substr + word.length
					);
					let $str3 = city_name.substring($substr + word.length);
					$(this).html($str1 + '<span>' + $str2 + '</span>' + $str3);
					$(this).show();
					char_wrap.show();
				}
			});
		};

		$('[data-search-input]')
			.on('focus', function () {
				searchInCityList($(this));
			})
			.on('keyup', function () {
				searchInCityList($(this));
			});

		$('.main_search_city_input')
			.on('focus', function () {
				$('.main_search_city_list').slideToggle('Fast');
			})
			.on('blur', function () {
				setTimeout(function () {
					$('.main_search_city_list').slideToggle('Fast');
				}, 100);
			});

		let searchInCityList1 = function ($input) {
			let word = $input.val();
			$('.main_search_city_list p').each(function () {
				let city_name = $(this)
					.html()
					.replace('<span>', '')
					.replace('</span>', '');
				let $substr = city_name
					.toLowerCase()
					.search(word.toLowerCase());
				if (word != '' && $substr == -1) {
					$(this).html(city_name);
					$(this).hide();
				} else {
					let $str1 = city_name.substring(0, $substr);
					let $str2 = city_name.substring(
						$substr,
						$substr + word.length
					);
					let $str3 = city_name.substring($substr + word.length);
					$(this).html($str1 + '<span>' + $str2 + '</span>' + $str3);
					$(this).show();
				}
			});
		};

		$('.main_search_city_list').on('click', 'p', function () {
			const cityId = $(this).data('city-id');
			const onSuccess = ({ selectsHtml = null }) => {
				if (!selectsHtml) return;
				$('.main_search_city_input').val(
					$(this).html().replace('<span>', '').replace('</span>', '')
				);
				$('[data-selected-city-id]').data('selected-city-id', cityId);
				$('.main_search_type_room_list').html(selectsHtml);
				/*  $('.main_search_type_room_list').mCustomScrollbar({
					 theme: 'dark-thick',
				}); */
			};
			const onError = (err) => { };
			$.ajax({
				url: '/site/filter-city/',
				data: { cityId },
				cache: false,
				success: onSuccess,
				error: onError,
			});
		});

		$('.main_search_type_room_list').on('click', 'p', function () {
			$('.type_room_choose').html($(this).html());
			$('.type_room_choose').css('color', '#000');
			$('[data-selected-filter-item]').data(
				'selected-filter-item',
				$(this).data('filter-item')
			);
		});

		$('.main_search_submit').on('click', (e) => {
			$.ajax({
				url: '/site/filter-submit/',
				data: {
					city_id: $('[data-selected-city-id]').data(
						'selected-city-id'
					),
					filter: $('[data-selected-filter-item]').data(
						'selected-filter-item'
					),
				},
				cache: false,
				success: (res, text, xhr) => {
					if (!res.redirect) return;
					window.location.href = res.redirect;
				},
			});
		});

		$('body').on('click', function (e) {
			var $el = $(e.target);
			var $parent_scroll = $el.parents('#mCSB_2_scrollbar_vertical');
			var $e_city = $el.closest('.header_city');
			var $e_city_list = $el.closest('.city_list_wrap');

			if ($e_city.length > 0) {
				if ($('.city_list_wrap').hasClass('__visible')) {
					$('.city_list_wrap').removeClass('__visible');
					$(this).find('.choose').removeClass('_expand');
				} else {
					$('.city_list_wrap').addClass('__visible');
					$(this).find('.choose').addClass('_expand');
				}
			} else {
				if (
					$('.city_list_wrap').hasClass('__visible') &&
					$e_city_list.length < 1
				) {
					$('.city_list_wrap').removeClass('__visible');
					$(this).find('.choose').removeClass('_expand');
				}
			}
			if (
				$el.is('.main_search_type_room') ||
				$el.is('span.choose') ||
				$el.is('span.type_room_choose')
			) {
				$('.main_search_type_room_list').slideToggle('Fast');
				window.main_search_type_room = !window.main_search_type_room;
				if ($('.main_search_type_room .choose').hasClass('_expand')) {
					$('.main_search_type_room .choose').removeClass('_expand');
					//window.main_search_type_room = false;
				} else {
					$('.main_search_type_room .choose').addClass('_expand');
					//window.main_search_type_room = true;
				}
			} else {
				if (
					typeof window.main_search_type_room !== 'undefined' &&
					window.main_search_type_room &&
					$parent_scroll.length == 0
				) {
					$('.main_search_type_room_list').slideToggle('Fast');
					$('.main_search_type_room .choose').removeClass('_expand');
					window.main_search_type_room = !window.main_search_type_room;
				}
			}

			if ($el.is('input[name="date"]')) {
				$('.input_wrapper_date_wrapper').slideToggle('Fast');
				window.input_wrapper_date_wrapper = !window.input_wrapper_date_wrapper;
			} else if (
				$el.is('div.date_wrapper_arrow._next') ||
				$el.is('div.date_wrapper_arrow._prev')
			) {
				window.input_wrapper_date_wrapper = true;
			} else {
				if (
					typeof window.input_wrapper_date_wrapper !== 'undefined' &&
					window.input_wrapper_date_wrapper
				) {
					$('.input_wrapper_date_wrapper').slideToggle('Fast');
					window.input_wrapper_date_wrapper = !window.input_wrapper_date_wrapper;
				}
			}
		});

		$('body').on('click', '.map_close', function () {
			$(this).closest('.map_data_wrap').hide();
		});

		$('.main_rectangle_1').hover(
			function () {
				$(this).css('background-color', '#FFC24A');
				$(this)
					.siblings('.main_rectangle_2')
					.css('visibility', 'visible');
			},
			function () {
				$(this).css('background-color', '#FFAD0F');
				$(this)
					.siblings('.main_rectangle_2')
					.css('visibility', 'hidden');
			}
		);
		$('.main_rectangle a').hover(
			function () {
				$(this)
					.siblings('.main_rectangle_1')
					.css('background-color', '#FFC24A');
				$(this)
					.siblings('.main_rectangle_2')
					.css('visibility', 'visible');
			},
			function () {
				$(this)
					.siblings('.main_rectangle_1')
					.css('background-color', '#FFAD0F');
				$(this)
					.siblings('.main_rectangle_2')
					.css('visibility', 'hidden');
			}
		);

		$('body').on('click', '[data-target]', function () {
			const fire = () => {
				console.log('reachGoal', $(this).data('target'));
				ym('67719148', 'reachGoal', $(this).data('target'));
				gtag('event', $(this).data('target'), { 'event_category': 'click' });
			}
			if(typeof ym == 'undefined' || typeof gtag == 'undefined') {
				setTimeout(fire, 1000);
			}
			else {
				fire();
			}
		});

	}

	sendCalltracking(phone) {
		let clientId = '';
		ga.getAll().forEach((tracker) => {
			clientId = tracker.get('clientId');
		})

		let pageReferrer = '';
		if (document.referrer) {
			pageReferrer = document.referrer;
		}

		if (Cookies.get('a_ref_0')) {
			Cookies.set('a_ref_1', pageReferrer, { expires: 365 });
		} else {
			Cookies.set('a_ref_0', pageReferrer, { expires: 365 });
		}

		//запись в куки utm_details
		let currentUrl = '';
		if (window.location.href) {
			currentUrl = window.location.href;
		}
		let patternUtm = RegExp('utm_details=([^\&]*)', 'g');
		let utmExist = patternUtm.exec(currentUrl);
		let utm = {};
		if (utmExist) {
			let rows = utmExist[1].split('|');

			for (let i = 0; i < rows.length; i++) {
				let a = rows[i].split(':');
				utm[a[0]] = a[1];
			}
		}
		let utmJson = JSON.stringify(utm);

		if (Cookies.get('a_utm_0')) {
			Cookies.set('a_utm_1', utmJson, { expires: 365 });
		} else {
			Cookies.set('a_utm_0', utmJson, { expires: 365 });
		}


		const data = new FormData();

		data.append('phone', phone);
		data.append('clientId', clientId);

		$.ajax({
			type: 'post',
			url: '/ajax/send-calltracking/',
			data: data,
			processData: false,
			contentType: false,
			success: function (response) {
				// response = $.parseJSON(response);
				// response = JSON.parse(response);
				// self.resolve(response);
				console.log('calltracking sent');
			},
			error: function (response) {
				console.log('calltracking ERROR');
			}
		});
	}
}
