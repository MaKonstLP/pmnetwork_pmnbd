'use strict';

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
		});
	}
}