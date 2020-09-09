'use strict';
import Swiper from 'swiper';

export default class Slider{
	constructor(){
		self = this;
		this.swiperArr = [];

		//if($(window).width() <= 1650){
		self.initSwiperItem($('[data-gallery-main-swiper]'), $('[data-gallery-thumb-swiper]'));	
		self.initSwiperSameItem($('[data-other-objects-swiper]'));	
		self.initSwiperTitleBlog($('[data-gallery-blog-swiper]'));	
		//self.initSwiper1($('[data-gallery-main-swiper] [data-gallery-list]'));
		
	//}
		$('body').on('click', '[data-gallery-img-view]', function () {
            let slider = $(this).closest('[data-gallery-main-swiper]');
            let active = $(this).closest('[data-swiper-slide-index]').attr('data-swiper-slide-index');
            let slider_popup = $('[data-gallery-img-swiper]');
            let sliders = slider.find('.swiper-slide').not('.swiper-slide-duplicate').each(function(){
            	slider_popup.find('[data-gallery-list]').append('<div class="object swiper-slide">'+$(this).find('.object_img').html()+'</div');
            });
            slider_popup.find('[data-gallery-img-view]').each(function(){$(this).removeAttr('data-gallery-img-view')});
            slider_popup.find('.swiper-slide').removeClass('swiper-slide-duplicate-active swiper-slide-active');
            slider_popup.find('[data-swiper-slide-index="'+active+'"]').addClass('swiper-slide-active');
            $('.popup_wrap .popup_form').hide();
            $('.popup_wrap .popup_img').show();
            $('.popup_wrap').addClass('_active');
            self.initSwiperPopup(slider_popup, active);
        });

		$(window).on('resize', function(){
			//if($(window).width() <= 1650){
				/*if(self.swiperArr.length == 0){
					$('[data-widget-wrapper]').each(function(){
						self.initSwiper($(this).find('[data-listing-wrapper]'));
					});
				}	*/				
			/*}
			else{
				$.each(self.swiperArr, function(){
					this.destroy(true, true);
				});
				self.swiperArr = [];
			}*/
		});
	}

	initSwiperItem($container_main, $container_thumb){
		let galleryThumbs = new Swiper($container_thumb, {
	      	spaceBetween: 0,
	      	slidesPerView: 15,
	      	watchSlidesVisibility: true,
     		watchSlidesProgress: true,
	      	centerInsufficientSlides: true,
	      	//centeredSlides: true,
	      	slideToClickedSlide: true,
	    });
		

		let galleryTop = new Swiper($container_main, {
	      spaceBetween: 0,
	      slidesPerView: 3.2,
	      centeredSlides: true,
	      loop: true,
	      init: false,
	      navigation: {
	        nextEl: '.listing_widget_arrow._next',
          	prevEl: '.listing_widget_arrow._prev',
	      },
	      thumbs: {
	        swiper: galleryThumbs
	      },
	      pagination: {
		              el: '.listing_widget_pagination',
		              type: 'bullets',
		            },

	      breakpoints: {
	        	1000:{
	        		slidesPerView: 1.2,
	        	},
	        	600:{
	        		slidesPerView: 1,
	        		pagination: {
		              el: '.listing_widget_pagination',
		              type: 'bullets',
		            },
	        	}
	        }
	    });

	    let setActive = function() {
      		let activeIndex = galleryTop.realIndex+1;
       		let slidesCount = $(galleryTop.el).find('.swiper-slide').not('.swiper-slide-duplicate').length;
			let activeSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + activeIndex + ')');
          	let nextSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child('+(activeIndex + 1)+')').length > 0 ? $('[data-gallery-thumb-swiper] .swiper-slide:nth-child('+(activeIndex + 1)+')') : $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(1)');
          	let prevSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child('+(activeIndex - 1)+')').length > 0 ? $('[data-gallery-thumb-swiper] .swiper-slide:nth-child('+(activeIndex - 1)+')') : $('[data-gallery-thumb-swiper] .swiper-slide:nth-child('+slidesCount+')');
          	$('[data-gallery-thumb-swiper] .swiper-slide').each(function(){$(this).removeClass('swiper-slide-virtual-active')});
          	nextSlide.addClass('swiper-slide-virtual-active');
          	prevSlide.addClass('swiper-slide-virtual-active');
      	};

		galleryTop.on('slideChange', function () {
          	setActive();
          	this.thumbs.swiper.slideTo(this.realIndex, false,false);
        });

		galleryTop.on('init', function () {
			setActive();
		});

		galleryTop.init();
	}

	initSwiperSameItem($container){
		let swiper = new Swiper($container, {
	        slidesPerView: 3,
	        //spaceBetween: 30,
	        loop: true,
	        navigation: {
              nextEl: '.listing_widget_arrow._next',
              prevEl: '.listing_widget_arrow._prev',
            },
            pagination: {
		              el: '.listing_widget_pagination',
		              type: 'bullets',
		            },
	        breakpoints: {
	        	/*1200:{
	        		slidesPerView: 3,
	        	},*/
	        	1000:{
	        		slidesPerView: 2.5,
					pagination: {
		              el: '.listing_widget_pagination',
		              type: 'bullets',
		            },
	        		navigation: false,
	        		loop: false,
	        		//centeredSlides: true,
	        	},
	        	767:{
	        		slidesPerView: 1.2,
	        	}
	        }
	    });

	    let swiper_var = $container.swiper;
		this.swiperArr.push(swiper);
	}

	initSwiperTitleBlog($container){
		let swiper = new Swiper($container, {
	        slidesPerView: 1,
	        spaceBetween: 0,
	        loop: false,
	        navigation: {
              nextEl: '.listing_widget_arrow._next',
              prevEl: '.listing_widget_arrow._prev',
            },
            pagination: {
              el: '.listing_widget_pagination',
              type: 'bullets',
            },
	    });
	}

	initSwiperPopup($container, $start){
		let swiper_popup = new Swiper($container, {
	        slidesPerView: 1,
	        spaceBetween: 0,
	        loop: false,
	        initialSlide: $start,
	        autoHeight: true,
	        navigation: {
              nextEl: '.slider_arrow._next',
              prevEl: '.slider_arrow._prev',
            },
	    });
	}
}