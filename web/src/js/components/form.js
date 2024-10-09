import Animation from './animation.js';
//import modal from './modal';
import {status, json} from './utilities';
import Inputmask from 'inputmask';
var animation = new Animation;

export default class Form {
	constructor(form) {
		this.$form = $(form);
		this.$formWrap = this.$form.parents('.form_main');
		this.$submitButton = this.$form.find('button[type="submit"]');
		this.$policy = this.$form.find('[name="policy"]');
		this.$policy_checkbox = this.$form.find('[data-action="form_checkbox"]');
		this.to = (this.$form.attr('action') == undefined || this.$form.attr('action') == '') ? this.to : this.$form.attr('action');
		this.$formModal = this.$form.parents('body').find('.popup_wrap').not('.popup_wrap_single-map');
		this.$formModalMain = this.$formModal.find('.form_main');
		this.$formSuccess = this.$formModal.find('.form_success');
		this.target = this.$form.data('form-target');
		
		let im_phone = new Inputmask('+7 (999) 999-99-99', {
			clearIncomplete: true,
	    });

	    im_phone.mask($(this.$form).find('[name="phone"]'));


		this.bind();
	}

	bind() {
		let self = this;

		this.calendarInit();

		this.$form.on('click', '.date_wrapper_arrow', (e) => {
			let $el = $(e.currentTarget);
			this.calendarInit($el.data('next_date'));
		})

		this.$form.on('click', 'input[name="date"]', (e) => {
			let $el = $(e.currentTarget);
			if ($el.html() == '') {
				this.calendarInit();
			} else {
				this.calendarInit($el.html());
			}

		})

		this.$form.on('click', '.date_wrapper_week p', (e) => {
			let $el = $(e.currentTarget);
			let $elParent = $el.parent();
			if (!$elParent.hasClass('week_title')) {
				$elParent.parents('.date_wrapper').find('input').val($el.data('cur_date'));
				$elParent.parents('.date_wrapper').find('input').trigger('blur')
			}
		})

		this.$form.find('[data-dynamic-placeholder]').each(function () {
			$(this).on('blur',function () {
				if ($(this).val() == '')
					$(this).removeClass('form_input_filled');
				else
					$(this).addClass('form_input_filled');
			})
		})

		this.$form.find('[data-required]').each((i, el) => {
				$(el).on('blur', (e) => {
					console.log('blur')
					this.checkField($(e.currentTarget));
					this.checkValid();
				});

			$(el).on('change', (e) => {
			  console.log('change');
				// this.checkField($(e.currentTarget));
				this.checkValid();
			  // this.checkValid();
			});
		});

		this.$form.on('submit', (e) => {
			this.sendIfValid(e);
		});

		this.$form.on('click', 'button.disabled', function(e) {
			e.preventDefault();
			return false;
		})

		this.$policy.on('click',(e) => {
			var $el = $(e.currentTarget);

			if ($el.prop('checked'))
			$el.removeClass('_invalid');
				else
			$el.addClass('_invalid');

			this.checkValid();
		})

		this.$policy_checkbox.on('click',(e) => {

			let $el = $(e.currentTarget);
			let $input = $el.children('input');
			if ($el.hasClass('_active')){
				$el.removeClass('_active');
			} else {
				$el.addClass('_active');
			}
			$input.prop("checked", !$input.prop("checked"));
		})

		// клик по селекту
		this.$form.find('[data-form-select]').on('click', function() {
			var $parent = $(this).closest('[data-form-select-block]');
			self.selectBlockClick($parent);
		})

		// клик по элементу селекта
		this.$form.find('[data-form-select-item]').on('click', function () {
			$(this).siblings('[data-form-select-item]._active').removeClass('_active');
			$(this).addClass('_active');

			let hasSelectedItem = false;
			$(this).each(function() {
				if ($(this).hasClass('_active'))
					hasSelectedItem = true;
			})

			//скрываем поле с датой если выбран Корпорат на НГ
			if ($(this).data('value') == 'Corporate_NY') {
				$(this).closest('.form_inputs').find('.input_wrapper.date_wrapper').addClass('hidden');
				$(this).closest('.form_inputs').find('.input_wrapper.date_wrapper input').removeAttr('data-required')
			} else {
				$(this).closest('.form_inputs').find('.input_wrapper.date_wrapper').removeClass('hidden');
				$(this).closest('.form_inputs').find('.input_wrapper.date_wrapper input').attr('data-required', 'true');
			}

			if (hasSelectedItem)
				$(this).closest('[data-form-select-block]').addClass('_selected')
			else
				$(this).closest('[data-form-select-block]').removeClass('_selected')

			self.selectStateRefresh($(this).closest('[data-form-select-block]'));
		});

		// клик вне выпадашки селекта
		$('body').click(function (e) {
			if (!$(e.target).closest('[data-form-select-block]').length) {
				self.selectBlockActiveClose();
			}

			if ($(e.target).closest('.options').length) {
				self.selectBlockActiveClose();
			}
		});
	}

	selectStateRefresh($block) {
		var self = this;
		var blockType = $block.data('type');
		var $item = $block.find('[data-form-select-item]._active');
		var selectText = '-';

		if ($item.length > 0) {
			$block.find('[data-selected-option]').prop('value', $($item[0]).data('value'));
			$block.find('[data-selected-option]').val($($item[0]).data('value'));
			selectText = $($item[0]).text();
		}

		$block.find('[data-form-select]').text(selectText);
	}

	selectBlockClick($block) {
		if ($block.hasClass('_active')) {
			$block.removeClass('_active');
		}
		else {
			this.selectBlockActiveClose();
			$block.addClass('_active');
		}
	}

	selectBlockActiveClose() {
		this.$form.find('[data-form-select-block]._active').each(function () {
			$(this).removeClass('_active');
		});
	}

	checkValid() {
		this.$submitButton.removeClass('disabled');
		if (this.$form.find('.form_input_invalid').length > 0) {
			this.$submitButton.addClass('disabled');
		}
	}

	checkField($field) {
			var valid = true;
			var name = $field.attr('name');
			var pattern_email = /^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i;
			// var time_cur = Date.parse($field.val());
			// var date_now = new Date();
			// var time_now = new Date(date_now.getFullYear(), date_now.getMonth(), date_now.getDate()).getTime();

			// console.log('field name', name)
			console.log('field value', $field.val())
			// console.log('time_cur', time_cur)
			// console.log('time_now', time_now)

			if ($field.val() == '') {
				valid = false;
			} else {
				if (name === 'phone' && $field.val().indexOf('_') >= 0) {
					valid = false;
					var custom_error = 'Неверный формат телефона';
				}

				if (name === 'email' && !(pattern_email.test($field.val()))) {
					valid = false;
					var custom_error = 'Неверный формат электронной почты';
				}

				// if (name === 'date' && time_cur < time_now) {
				// 	valid = false;
				// 	var custom_error = 'Вы указали прошедшую дату';
				// }

				if (name === 'guests' && (typeof +$field.val() !== 'number' || +$field.val() !== Math.floor(+$field.val()) || +$field.val() <= 0)) {
					valid = false;
					var custom_error = 'Введите целое положительное число';
				}

				if (name === 'policy' && $field.prop('checked')) {
					valid = true;
				}
			}
			if (valid) {
				$field.removeClass('_invalid').addClass('check_approove');

        		if ($field.parent().find('.input_error').length > 0)
					$field.parent().find('.input_error').html('');

			} else {
				$field.addClass('_invalid').removeClass('check_approove');
				var form_error = $field.data('error') || 'Заполните поле';
				var error_message = custom_error || form_error;

				if ($field.siblings('.input_error').length  == 0) {
					$field.parent('.input_wrapper').append('<div class="input_error">' + error_message + '</div>');
				} else {
					$field.siblings('.input_error').html(error_message);
				}
			}
	}

	checkFields() {
		var valid = true;

    	this.$form.find('[data-required]').each((i, el) => {
			this.checkField($(el));
			if ($(el).hasClass('_invalid'))
				valid = false;
		});

		if (valid) {
			this.$submitButton.removeClass('disabled');
		} else {
			this.$form.find('._invalid')[0].focus();
			this.$submitButton.addClass('disabled');
		}

		return valid;
	}

	reset() {
		this.$form[0].reset();
		this.$form.find('input').removeClass('form_input_valid form_input_filled check_approove');
	}

	beforeSend() {
		// this.$submitButton.addClass('button__pending');
	}

	success(data) {
		//modal.append(data);
		//modal.show();

		this.$formModalMain.hide();
		data.title && this.$formSuccess.find('[data-form-result-title]').text(data.title);
		data.body && this.$formSuccess.find('[data-form-result-body]').text(data.body);
		this.$formSuccess.show();

		// if (this.$formModal.not('._active').length == 0)
		// this.$formModal.hasClass('._active').removeClass('_active');
		// this.$formModal.not('.popup_wrap_single-map ').not('.popup_wrap_blog ').not('._active').addClass('_active');

		this.$formModal.not('._active').addClass('_active');

		this.reset();
		console.log('reachGoal', this.target);
		ym('67719148', 'reachGoal', this.target);
		gtag('event', $(this).data('target'), {'event_category': 'click'});

		if ($('[data-premium-rest]').length > 0) {
			let data_premium = new FormData();
			data_premium.append('gorko_id', $('[data-premium-rest]').data('premium-rest'));
			data_premium.append('channel', $('[data-channel-id]').data('channel-id'));
			data_premium.append('response', JSON.stringify(data.response));
			fetch('/premium/premium-callback/', {
				method: 'POST',
				body: data_premium,
			})
				.then((response) => response.json())
				.then((data) => {
					console.log(data);
				})
				.catch((error) => {
					console.error('Error:', error);
				});
		}
		// this.$submitButton.removeClass('button__pending');
	}

	error() {
		// this.$submitButton.removeClass('button__pending');
		//modal.showError();
	}

	sendIfValid(e) {
	    e.preventDefault();
	    if (!this.checkFields()) return;
	    if (this.disabled) return;

	    this.disabled = true;
	    this.beforeSend();

	    var formData = new FormData(this.$form[0]);
		formData.append($('[name="csrf-param"]').attr('content'), $('[name="csrf-token"]').attr('content'));
		var formUrl = window.location.href;
	    formData.append('url', formUrl);
	    formData.append('check', '1');
	    fetch(this.to,{
			method: 'POST',
			body: formData
	    })
	    .then(status)
	    .then(json)
	    .then(data => {
			this.success(data);
			// this.reset();
			this.disabled = false;
	    })
	    .catch(() => {
			this.error();
			this.disabled = false;
	    });
	}

	calendarInit($chooseDate = '') {
		if ($chooseDate == '') {
			var $curDate = new Date();
			var $now = true;
		} else {
			var $curDate = new Date($chooseDate);
			if ($curDate.getMonth() == new Date().getMonth() && $curDate.getFullYear() == new Date().getFullYear()) var $now = true; else var $now = false;
		}
		var mnths = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
		var $firstDayMonth = new Date($curDate.getFullYear(), $curDate.getMonth(),1);
		var $prevDate = new Date($firstDayMonth.getFullYear(), $firstDayMonth.getMonth(),0);
		var $countDaysMonth = 33 - new Date($curDate.getFullYear(), $curDate.getMonth(), 33).getDate();
		var $lastDayMonth = new Date($curDate.getFullYear(), $curDate.getMonth(),$countDaysMonth);
		var $nextDate = new Date($lastDayMonth.getFullYear(), $lastDayMonth.getMonth(),$lastDayMonth.getDate()+1);

		var $render = '<div class="date_wrapper_title">\
							<p class="date_wrapper_cur_month">'
								+ mnths[$curDate.getMonth()] + ' ' + $curDate.getFullYear() +
							'</p>\
							<div class="date_wrapper_arrows">'
								+ (!$now ? '<div class="date_wrapper_arrow _prev" data-next_date="' + $prevDate.getFullYear() + '-' + ((($prevDate.getMonth()+1) <10) ? ('0' + ($prevDate.getMonth()+1)) : ($prevDate.getMonth()+1)) + '-' + (($prevDate.getDate() < 10) ? ('0' + $prevDate.getDate()) : $prevDate.getDate()) +'"></div>' : '')+
								'<div class="date_wrapper_arrow _next" data-next_date="' + $nextDate.getFullYear() + '-' + ((($nextDate.getMonth()+1) <10) ? ('0' + ($nextDate.getMonth()+1)) : ($nextDate.getMonth()+1)) + '-' + (($nextDate.getDate() <10) ? ('0' + $nextDate.getDate()) : $nextDate.getDate()) +'"></div>\
							</div>\
						</div>\
						<div class="date_wrapper_weeks">\
							<div class="date_wrapper_week week_title">\
								<p>Пн</p>\
								<p>Вт</p>\
								<p>Ср</p>\
								<p>Чт</p>\
								<p>Пт</p>\
								<p>Сб</p>\
								<p>Вс</p>\
							</div>'
		+ this.renderWeek($firstDayMonth) +
		'</div>';

		this.$form.find('.input_wrapper_date_wrapper').html();
		this.$form.find('.input_wrapper_date_wrapper').html($render);

		if(this.$form.find('.input_wrapper_date_wrapper').find('[data-cur_date]._now').length > 0){
			this.$form.find('.input_wrapper_date_wrapper').find('[data-cur_date]').each(function(){
				if($(this).hasClass('_now'))
					return false;

				$(this).addClass('_disabled');
			})
		}

	}

	renderWeek($date, $renderWeek = '') {
		var self = this;
		var $curWeekDay = $date.getDay();
		$curWeekDay = ($curWeekDay == 0) ? 7 : $curWeekDay;

		var $firstDayWeek = new Date($date.getFullYear(), $date.getMonth(),$date.getDate() - $curWeekDay);

		$renderWeek += '<div class="date_wrapper_week">';
		for (var $i=1; $i<8; $i++){
			var $weekDay = new Date($firstDayWeek.getFullYear(), $firstDayWeek.getMonth(),$firstDayWeek.getDate()+$i);
			var curDate = (($weekDay.getDate() < 10) ? ('0' + $weekDay.getDate()) : $weekDay.getDate()) + '.' + ((($weekDay.getMonth()+1) <10) ? ('0' + ($weekDay.getMonth()+1)) : ($weekDay.getMonth()+1)) + '.' + $weekDay.getFullYear();
			var disabled_class = ($weekDay.getMonth() != $date.getMonth()) ? 'not_this_month' : '';
			var now_class = ($weekDay.getFullYear() == new Date().getFullYear() && $weekDay.getMonth() == new Date().getMonth() && $weekDay.getDate() == new Date().getDate()) ? '_now' : '';
			var active_class = curDate == self.$form.find('[name="date"]').val() ? '_active' : '';
			$renderWeek += '<p class="'+disabled_class+ ' '+ now_class + ' '+ active_class +'" data-cur_date="'+ curDate +'">'+$weekDay.getDate()+'</p>';
		}
		$renderWeek += '</div>';

		var $nextWeekDay = new Date($weekDay.getFullYear(), $weekDay.getMonth(),$weekDay.getDate()+1);

		if ($date.getMonth() < $nextWeekDay.getMonth() || $date.getFullYear() < $nextWeekDay.getFullYear()) return $renderWeek;

		return this.renderWeek($nextWeekDay, $renderWeek);
	}
}
