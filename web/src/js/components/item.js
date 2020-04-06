'use strict';
import Swiper from 'swiper';
import 'slick-carousel';

export default class Item{
	constructor($item){
		var self = this;
		
		$('[data-action="show_phone"]').on('click', function(){
			$('.object_book_hidden').addClass('_active');
		});			
	}
}