'use strict';
import Swiper from 'swiper';

export default class Slider {
	constructor() {
		self = this;
		this.swiperArr = [];
		self.mobileMode = self.getScrollWidth() < 768 ? true : false;

		//if($(window).width() <= 1650){
		if ($('[data-item-gallery]').length > 0) self.initSwiperListingGallery($('[data-item-gallery]'));

		if ($('[data-room-slider]').length > 0 && self.mobileMode) {
			self.initSwiperRoom('[data-room-slider]');
		}

		if ($('[data-gallery-main-swiper]').length > 0) self.initSwiperItem($('[data-gallery-main-swiper]'), $('[data-gallery-thumb-swiper]'));
		if ($('[data-other-objects-swiper]').length > 0) self.initSwiperSameItem($('[data-other-objects-swiper]'));
		if ($('[data-other-blogs-swiper]').length > 0) self.initSwiperSameBlogs($('[data-other-blogs-swiper]'));
		if ($('[data-gallery-blog-swiper]').length > 0) {
			self.initSwiperTitleBlog($('[data-gallery-blog-swiper]'));
		}
		if ($('[data-gallery-post-swiper]').length > 0) $('[data-gallery-post-swiper]').each(function () {
			self.initSwiperBlog($(this), $(this).siblings('[data-gallery-post-thumb-swiper]'))
		});
		//self.initSwiper1($('[data-gallery-main-swiper] [data-gallery-list]'));
		//}

		if (!self.mobileMode) {
			$('body').on('click', '[data-gallery-img-view]', function () {

				let slider = $(this).closest('[data-gallery-swiper]');
				let active = $(this).closest('[data-swiper-slide-index]').attr('data-swiper-slide-index');
				let slider_popup = $('[data-gallery-img-swiper]');
				let sliders = slider.find('.swiper-slide').not('.swiper-slide-duplicate').each(function () {
					slider_popup.find('[data-gallery-list]').append('<div class="object swiper-slide">' + $(this).find('.object_img').html() + '</div');
				});
				slider_popup.find('[data-gallery-img-view]').each(function () {
					$(this).removeAttr('data-gallery-img-view')
				});
				slider_popup.find('.swiper-slide').removeClass('swiper-slide-duplicate-active swiper-slide-active');
				slider_popup.find('[data-swiper-slide-index="' + active + '"]').addClass('swiper-slide-active');
				$('.popup_wrap .popup_form').hide();
				$('.popup_wrap .popup_img').show();
				$('.popup_wrap').not('.popup_phone_wrap').not('.popup_wrap_single-map').addClass('_active');
				self.initSwiperPopup(slider_popup, active);
				bodyOverflow();
			});
		}


		function bodyOverflow() {
			let body = $('body');
			let lockPaddingValue = window.innerWidth - document.querySelector('body').offsetWidth + 'px';
			body.toggleClass('_popup_overflow');
			if (body.hasClass('_popup_overflow') && window.innerWidth >= 570) {
				body.css('padding-right', lockPaddingValue);
				$('.popup_form').css('padding-right', lockPaddingValue);
			} else {
				body.css('padding-right', 0);
				$('.popup_form').css('padding-right', 0);
			}
		}

		// $(window).on('resize', function () {
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
		// });
	}

	initSwiperItem($container_main, $container_thumb) {
		let galleryThumbs = new Swiper($container_thumb, {
			spaceBetween: 0,
			slidesPerView: 15,
			// watchSlidesVisibility: true,
			watchSlidesProgress: true,
			centerInsufficientSlides: true,
			// centeredSlides: true,
			// slideToClickedSlide: true,

		});


		let galleryTop = new Swiper($container_main, {
			spaceBetween: 0,
			slidesPerView: 3.2,
			centeredSlides: true,
			loop: true,
			init: false,
			initialSlide: 0,
			navigation: {
				nextEl: '.listing_widget_arrow._next',
				prevEl: '.listing_widget_arrow._prev',
			},
			thumbs: {
				swiper: galleryThumbs
			},

			breakpoints: {
				1000: {
					spaceBetween: 0,
					slidesPerView: 1.2,
				},
				768: {
					spaceBetween: 0,
					slidesPerView: 1,
					pagination: {
						el: '.gallery-pagination',
						type: 'bullets',
					},
				}
				// 360: {
				//     slidesPerView: 1,
				//     pagination: {
				//         el: '.gallery-pagination',
				//         type: 'bullets',
				//         dynamicBullets: true,
				//         dynamicMainBullets: 1,
				//     },
				// }
			}
		});

		let setActive = function () {
			let activeIndex = galleryTop.realIndex + 2;
			let slidesCount = $(galleryTop.el).find('.swiper-slide').not('.swiper-slide-duplicate').length;
			let activeSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + activeIndex + ')');

			let nextSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex + 1) + ')').length > 0 ?
				$('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex + 1) + ')') :
				$('[data-gallery-thumb-swiper] .swiper-slide:nth-child(1)');

			let prevSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex - 1) + ')').length > 0 ?
				$('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex - 1) + ')') :
				$('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + slidesCount + ')');

			$('[data-gallery-thumb-swiper] .swiper-slide').each(function () {
				$(this).removeClass('swiper-slide-virtual-active')
				// $(this).removeClass('swiper-slide-next')
				// $(this).removeClass('swiper-slide-prev')
				// $(this).removeClass('swiper-slide-active')
			});

			activeSlide.addClass('swiper-slide-virtual-active');
			nextSlide.addClass('swiper-slide-virtual-active');
			prevSlide.addClass('swiper-slide-virtual-active');
		};

		galleryTop.on('slideChange', function () {
			setActive();
			this.thumbs.swiper.slideTo(this.realIndex, false, false);
		});

		galleryTop.on('init', function () {
			setActive();
		});

		galleryTop.init();
	}

	initSwiperBlog($container_main, $container_thumb) {
		let galleryThumbs = new Swiper($container_thumb, {
			spaceBetween: 4,
			slidesPerView: 8,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,
			centerInsufficientSlides: true,
			//centeredSlides: true,
			slideToClickedSlide: true,
			autoHeight: true,
			breakpoints: {
				768: {
					slidesPerView: 6,
				}
			}
		});


		let galleryTop = new Swiper($container_main, {
			spaceBetween: 0,
			slidesPerView: 1,
			centeredSlides: true,
			loop: true,
			init: false,
			autoHeight: true,
			/*navigation: {
			  nextEl: '.listing_widget_arrow._next',
				 prevEl: '.listing_widget_arrow._prev',
			},*/
			thumbs: {
				swiper: galleryThumbs
			},
			pagination: {
				el: '.listing_widget_pagination',
				type: 'bullets',
			},

			breakpoints: {
				600: {
					pagination: {
						el: '.listing_widget_pagination',
						type: 'bullets',
					},
				}
			}
		});

		let setActive = function () {
			let activeIndex = galleryTop.realIndex + 1;
			let slidesCount = $(galleryTop.el).find('.swiper-slide').not('.swiper-slide-duplicate').length;
			let activeSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + activeIndex + ')');
			let nextSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex + 1) + ')').length > 0 ? $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex + 1) + ')') : $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(1)');
			let prevSlide = $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex - 1) + ')').length > 0 ? $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + (activeIndex - 1) + ')') : $('[data-gallery-thumb-swiper] .swiper-slide:nth-child(' + slidesCount + ')');
			$('[data-gallery-thumb-swiper] .swiper-slide').each(function () {
				$(this).removeClass('swiper-slide-virtual-active')
			});
			nextSlide.addClass('swiper-slide-virtual-active');
			prevSlide.addClass('swiper-slide-virtual-active');
		};

		galleryTop.on('slideChange', function () {
			setActive();
			this.thumbs.swiper.slideTo(this.realIndex, false, false);
		});

		galleryTop.on('init', function () {
			setActive();
		});

		galleryTop.init();
	}

	initSwiperSameItem($container) {
		let swiper = new Swiper($container, {
			slidesPerView: 3,
			spaceBetween: 12,
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
				999: {
					slidesPerView: 2.5,
					pagination: {
						el: '.listing_widget_pagination',
						type: 'bullets',
					},
					navigation: false,
					loop: false,
					//centeredSlides: true,
				},
				768: {
					slidesPerView: 2,
				},
				600: {
					slidesPerView: 1.1,
				}
			}
		});

		let swiper_var = $container.swiper;
	}

	initSwiperSameBlogs($container) {
		let swiper = new Swiper($container, {
			slidesPerView: 3,
			spaceBetween: 30,
			//loop: true,
			navigation: {
				nextEl: '.listing_widget_arrow._next',
				prevEl: '.listing_widget_arrow._prev',
			},
			breakpoints: {
				/*1200:{
					 slidesPerView: 3,
				},*/
				1000: {
					slidesPerView: 2.5,
					pagination: {
						el: '.listing_widget_pagination',
						type: 'bullets',
					},
					navigation: false,
					loop: false,
					//centeredSlides: true,
					spaceBetween: 20,
				},
				767: {
					slidesPerView: 1,
					spaceBetween: 20,
				}
			}
		});

		let swiper_var = $container.swiper;
	}

	initSwiperTitleBlog($container) {
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

	initSwiperPopup($container, $start) {
		let swiper_popup = new Swiper($container, {
			slidesPerView: 1,
			spaceBetween: 50,
			loop: false,
			initialSlide: $start,
			autoHeight: true,
			navigation: {
				nextEl: '.slider_arrow._next',
				prevEl: '.slider_arrow._prev',
			},
			breakpoints: {
				768: {
					autoHeight: false,
				}
			},
		});
	}

	initSwiperListingGallery($container) {
		let swiper = new Swiper($container, {
			slidesPerView: 1,
			spaceBetween: 0,
			loop: true,
			pagination: {
				el: '.item-gallery-pagination',
				type: 'bullets',
				dynamicBullets: true,
				dynamicMainBullets: 1,
			},
		});
	}

	initSwiperRoom($container) {
		let swiper = new Swiper($container, {
			slidesPerView: 1,
			spaceBetween: 10,
			loop: false,
			pagination: {
				el: '.item-gallery-pagination',
				type: 'bullets',
				dynamicBullets: true,
				dynamicMainBullets: 1,
			},
		});
	}

	getScrollWidth() {
		return Math.max(
			document.body.scrollWidth, document.documentElement.scrollWidth,
			document.body.offsetWidth, document.documentElement.offsetWidth,
			document.body.clientWidth, document.documentElement.clientWidth
		);
	};
}