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
	      	slideToClickedSlide: true
	    });

		let galleryTop = new Swiper($container_main, {
	      spaceBetween: 0,
	      slidesPerView: 3.2,
	      centeredSlides: true,
	      loop: true,
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
	      on: {
	      	slideChange: function () {
	          	/*let activeIndex = this.activeIndex + 1;
	       
				let activeSlide = document.querySelector(`[data-gallery-thumb-swiper] .swiper-slide:nth-child(${activeIndex})`);
	          	let nextSlide = document.querySelector(`[data-gallery-thumb-swiper] .swiper-slide:nth-child(${activeIndex + 1})`);
	          	let prevSlide = document.querySelector(`[data-gallery-thumb-swiper] .swiper-slide:nth-child(${activeIndex - 1})`);

	          	console.log('activeSlide',activeSlide);
	          	console.log('nextSlide',nextSlide);
	          	console.log('prevSlide',prevSlide);*/
	          this.thumbs.swiper.slideTo(this.realIndex, false,false);
	          	/*if (nextSlide && !nextSlide.classList.contains('swiper-slide-visible')) {
	              	this.thumbs.swiper.slideNext()	
	          	} else if (prevSlide && !prevSlide.classList.contains('swiper-slide-visible')) {
	              	this.thumbs.swiper.slidePrev()	
	          	}*/
	    
	        }
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

	}

	initSwiperSameItem($container){
		let swiper = new Swiper($container, {
	        slidesPerView: 3,
	        spaceBetween: 30,
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
	        spaceBetween: 30,
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
}