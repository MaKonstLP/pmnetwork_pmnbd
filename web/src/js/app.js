import $ from 'jquery';

import Listing from './components/listing';
import Item from './components/item';
import Main from './components/main';
import Index from './components/index';
import Widget from './components/widget';
import Form from './components/form';
import YaMap from './components/map';
import Slider from './components/slider';

window.$ = $;
window.jQuery = $;

(function($) {
  	$(function() {

  		if ($('[data-page-type="listing"]').length > 0) {
	    	var listing = new Listing($('[data-page-type="listing"]'));
	    }

	    if ($('[data-page-type="item"]').length > 0) {
	    	var item = new Item($('[data-page-type="item"]'));
	    }

	    if ($('[data-page-type="index"]').length > 0) {
	    	var index = new Index($('[data-page-type="index"]'));
	    }

	    if ($('[data-widget-wrapper]').length > 0) {
	    	var widget = new Widget();
	    }

	    if ($('[data-gallery-main-swiper]').length > 0 || $('[data-gallery-blog-swiper]').length > 0 || $('[data-other-objects-swiper]').length > 0 || $('[data-other-blogs-swiper]').length > 0 || $('[data-gallery-post-swiper]').length > 0 ) {
	    	var slider = new Slider();
	    }

	    if ($('.map').length > 0) {
	    	var yaMap = new YaMap();
	    }

	    var main = new Main();
	    var form = [];
	    var see_more = [];

	    $('form').each(function(){
	    	form.push(new Form($(this)))
	    });

  	});
})($);