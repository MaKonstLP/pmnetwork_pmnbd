'use strict';

export default class Filter{
	constructor($filter){
		let self = this;
		this.$filter = $filter;
		this.state = {};

		this.init(this.$filter);

		//КЛИК ПО БЛОКУ С СЕЛЕКТОМ
		this.$filter.find('[data-filter-select-current]').on('click', function(e){
			let $target = $(e.target);
			let $parent = $(this).closest('[data-filter-select-block]');
			if ($target.is('span.choose')) {
				if ($(this).siblings('.filter_select_list').is(':visible')){
					self.selectBlockClick($parent);
				}
				let $clear = $parent.find('[data-filter-select-item]._active').removeClass('_active');
				self.selectStateRefresh($parent);
			} else {
				self.selectBlockClick($parent);
			}
				
		});

		//КЛИК ПО СТРОКЕ В СЕЛЕКТЕ
		this.$filter.find('[data-filter-select-item]').on('click', function(){
			$(this).toggleClass('_active');
			//self
			self.selectStateRefresh($(this).closest('[data-filter-select-block]'));
		});

		//КЛИК ПО ЧЕКБОКСУ
		this.$filter.find('[data-filter-checkbox-item]').on('click', function(){
			$(this).toggleClass('_checked');
			self.checkboxStateRefresh($(this));
		});

		//КЛИК ВНЕ БЛОКА С СЕЛЕКТОМ
		$('body').click(function(e) {
		    if (!$(e.target).closest('.filter_select_block').length){
		    	self.selectBlockActiveClose();
		    }
		});

		//ОТКРЫТЬ ПОПАП ФИЛЬТРА НА МОБИЛКЕ
		this.$filter.find('[data-filter-mobile]').on('click', function(){
			$('body').find('.popup_filter_wrap').slideToggle('Fast');
		});

		//ЗАКРЫТЬ ПОПАП ФИЛЬТРА НА МОБИЛКЕ
		$('body').on('click', '.popup_filter_close', function(e) {
		    $(this).closest('.popup_filter_wrap').slideToggle('Fast');
		});
	}

	init(){
		let self = this;

		this.$filter.find('[data-filter-select-block]').each(function(){
			self.selectStateRefresh($(this));
		});

		this.$filter.find('[data-filter-checkbox-item]').each(function(){
			self.checkboxStateRefresh($(this));
		});
	}

	filterListingSubmit(page = 1){
		let self = this;
		self.state.page = page;

		let data = {
			'filter' : JSON.stringify(self.state)
		}
		console.log('filter111', data);
		this.promise = new Promise(function(resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
	    });		

		$.ajax({
            type: 'get',
            url: '/ajax/filter/',
            data: data,
            success: function(response) {
            	response = $.parseJSON(response);
                self.resolve(response);
            },
            error: function(response) {

            }
        });
	}

	filterMainSubmit(){
		let self = this;
		let data = {
			'filter' : JSON.stringify(self.state)
		}
		console.log('filter', data);
		this.promise = new Promise(function(resolve, reject) {
			self.reject = reject;
			self.resolve = resolve;
	    });

		$.ajax({
            type: 'get',
            url: '/ajax/filter-main/',
            data: data,
            success: function(response) {
            	if(response){
            		//console.log(response);
            		self.resolve('/catalog/'+response);
            	}
            	else{
            		//console.log(response);
            		self.resolve(self.filterListingHref());
            	}
            },
            error: function(response) {

            }
        });
	}

	selectBlockClick($block){
		if($block.hasClass('_active')){
			this.selectBlockClose($block);
		}
		else{
			this.selectBlockOpen($block);			
		}
	}

	selectBlockClose($block){
		$block.removeClass('_active');
	}

	selectBlockOpen($block){
		this.selectBlockActiveClose();
		$block.addClass('_active');
	}

	selectBlockActiveClose(){
		this.$filter.find('[data-filter-select-block]._active').each(function(){
			$(this).removeClass('_active');
		});
	}

	selectStateRefresh($block){
		let self = this;
		let blockType = $block.data('type');		
		let $items = $block.find('[data-filter-select-item]._active');
		let $filterLabel = $block.siblings('[data-filter-label]');
		let selectText = $filterLabel.html();
		let $countItems = $block.find('[data-filter-select-count]');
		
		if($items.length > 0){
			self.state[blockType] = '';
			$block.find('[data-filter-select-current]').addClass('choosen');
			selectText = $items[0];
			$countItems.html($items.length);
			$filterLabel.show();
			$items.each(function(){
				if(self.state[blockType] !== ''){
					self.state[blockType] += ','+$(this).data('value');
					//selectText = 'Выбрано ('+$items.length+')';
					$countItems.show();
				}
				else{
					self.state[blockType] = $(this).data('value');
					selectText = $(this).text();
					$countItems.hide();
				}
			});
		}
		else{
			delete self.state[blockType];
			$block.find('[data-filter-select-current]').removeClass('choosen');
			$countItems.hide();
			$filterLabel.hide();
		}
		$block.find('[data-filter-select-current] p').text(selectText);
	}

	checkboxStateRefresh($item){
		let blockType = $item.closest('[data-type]').data('type');
		if($item.hasClass('_checked')){
			this.state[blockType] = 1;
		}
		else{
			delete this.state[blockType];
		}
	}

	filterListingHref(){
		if(Object.keys(this.state).length > 0){
			var href = '/catalog/?';
			$.each(this.state, function(key, value){
				href += '&' + key + '=' + value;
			});
		}
		else{
			var href = '/catalog/';
		}			

		return href;
	}
}