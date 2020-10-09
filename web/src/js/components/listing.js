'use strict';
import Filter from './filter';

export default class Listing{
	constructor($block){
		self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));		

		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function(){
			if($(this).hasClass('filter_submit_button')) {
				$(this).closest('.popup_filter_wrap').slideToggle('Fast');
			}
			self.reloadListing();
		});

		//КЛИК ПО ПАГИНАЦИИ
		$('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function(){
			let $page_id = +$(this).siblings('[data-pagination-wrapper] [data-listing-pagitem]._active').data('page-id') + +$(this).data('page-increase');
			$page_id = (isNaN($page_id)) ?  $(this).data('page-id') : $page_id;
			self.reloadListing($page_id);
		});
		//console.log(this);

		//КЛИК ПО ПОКАЗАТЬ ЕЩЕ
		$('body').on('click', '[data-append-items]', function(){
			let $page_id = +$(this).siblings('[data-pagination-wrapper]').find('[data-listing-pagitem]._active').data('page-id') + +$(this).siblings('[data-pagination-wrapper]').find('[data-page-increase]').data('page-increase');
			if (!isNaN($page_id)) {
				self.filter.filterListingSubmit($page_id);
				self.filter.promise.then(
					response => {
						$('[data-listing-list]').append(response.listing.replace(/item swiper-slide/g,'item swiper-slide __hide'));
						$('[data-pagination-wrapper]').html(response.pagination);
						let visible_items = $(this).closest('[data-page-type="listing"]').find('[data-listing-list] .item:not(:visible)');
						if (visible_items.length > 0) {
							visible_items.each(function(){
								console.log($(this));
								$(this).slideToggle('Fast');
								if (!isNaN($page_id)) {
									self.appendInListing($page_id);
								}
							});
						} 
						if($('body').find('[data-page-increase="1"]').length == 0) {
							$(this).hide();
						}
					}
				);
			}
		});
	}

	reloadListing(page = 1){
		let self = this;
		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				//console.log(response);
				$('[data-listing-list]').html(response.listing);
				$('[data-listing-title]').html(response.title);
				$('[data-listing-text-top]').html(response.text_top);
				$('[data-listing-text-bottom]').html(response.text_bottom);
				$('[data-pagination-wrapper]').html(response.pagination);
				$('[data-listing-fast-filters]').html(response.fast_filters);
				self.block.removeClass('_loading');
				$('html,body').animate({scrollTop:0}, 400);
				history.pushState({}, '', '/catalog/'+response.url);
			}
		);
	}

	appendInListing(page = 1){
		let self = this;
		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				console.log('resp', response);
				let append_items =$('<div></div>').html(response.listing).find('.item').each(function(){
					$(this).css('display', 'none');
				});
				$('[data-listing-list]').append(response.listing);
				//$('[data-listing-title]').html(response.title);
				//$('[data-listing-text-top]').html(response.text_top);
				//$('[data-listing-text-bottom]').html(response.text_bottom);
				$('[data-pagination-wrapper]').html(response.pagination);
				self.block.removeClass('_loading');
				//$('html,body').animate({scrollTop:0}, 400);
				//history.pushState({}, '', '/catalog/'+response.url);
			}
		);
	}
}