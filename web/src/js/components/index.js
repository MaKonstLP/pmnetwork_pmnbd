'use strict';
import Filter from './filter';

export default class Index{
	constructor($block){
		var self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));

		//КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
		$('[data-filter-button]').on('click', function(){
			console.log('click');
			if($(this).hasClass('filter_submit_button')) {
				console.log('filter_submit_button');
				$(this).closest('.popup_filter_wrap').slideToggle('Fast');
			}
			self.redirectToListing();
		});
	}

	redirectToListing(){
		this.filter.filterMainSubmit();
		this.filter.promise.then(
			response => {
				window.location.href = response;
			}
		);
	}
}