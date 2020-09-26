'use strict';
import Swiper from 'swiper';

export default class Widget{
	constructor(){
		self = this;
		this.swiperArr = [];

		//if($(window).width() <= 1650){
			$('[data-widget-wrapper]').each(function(){
				let listing_wrap = $(this).find('[data-listing-wrapper]');
				self.initSwiper(listing_wrap, listing_wrap.find('.swiper-slide').length);
			});
	//}

		$(window).on('resize', function(){
			//if($(window).width() <= 1650){
				if(self.swiperArr.length == 0){
					$('[data-widget-wrapper]').each(function(){
						self.initSwiper($(this).find('[data-listing-wrapper]'));
					});
				}					
			/*}
			else{
				$.each(self.swiperArr, function(){
					this.destroy(true, true);
				});
				self.swiperArr = [];
			}*/
		});
	}

	initSwiper($container, $items){
		console.log('items', $items);
		let swiper = new Swiper($container, {
	        slidesPerView: 4,
	        spaceBetween: 30,
	        loop:  ($items < 4 ? false : true),
	        navigation: {
              nextEl: '.listing_widget_arrow._next',
              prevEl: '.listing_widget_arrow._prev',
            },
            pagination: {
		              el: '.listing_widget_pagination',
		              type: 'bullets',
		            },
	        breakpoints: {
	        	1000:{
	        		slidesPerView: 4,
	        		loop:  ($items < 4 ? false : true),
	        		pagination: {
		              el: '.listing_widget_pagination',
		              type: 'bullets',
		            },
	        		navigation: false,
	        	},
	        	768:{
	        		slidesPerView:2.5,
					loop:  ($items < 3 ? false : true),
	        	},
	        	600:{
	        		slidesPerView: ($items < 2 ? $items : 1.2),
	        		loop:  ($items < 2 ? false : true),
	        	}
	        }
	    });

	    let swiper_var = $container.swiper;
		this.swiperArr.push(swiper);
	}
}