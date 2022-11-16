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

(function ($) {
    $(function () {

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

        if (($('[data-gallery-main-swiper]').length > 0 ||
            $('[data-gallery-blog-swiper]').length > 0 ||
            $('[data-other-objects-swiper]').length > 0 ||
            $('[data-other-blogs-swiper]').length > 0 ||
            $('[data-item-gallery]').length > 0 ||
            $('[data-gallery-post-swiper]').length > 0) &&
            $('[data-page-type="listing"]').length == 0 ) {
            
            var slider = new Slider();
        }

        if ($('.map').length > 0) {
            var yaMap = new YaMap();
        }

        var main = new Main();
        var form = [];
        var see_more = [];

        $('form').each(function () {
            form.push(new Form($(this)))
        });

        $('body').on('click', '[data-single-map]', function () {
            console.log('data-single-map');

            let popup = $('.popup_wrap.popup_wrap_single-map');
            popup.find('.map_img').attr('src', $(this).data('map_img'));
            popup.find('.map_rest_name').attr('href', $(this).data('map_rest_href'));
            popup.find('.map_rest_name').text($(this).data('map_rest_name'));
            popup.find('.map_rest_address').text($(this).data('map_rest_address'));

            popup.find('[data-mapdotx]').data('mapdotx', $(this).data('map_dot_x'));
            popup.find('[data-mapdotx]').data('mapdoty', $(this).data('map_dot_y'));

            // data-single-map="" 
            // data-map_img="https://img.birthday-place.ru/hM7TDFS78-Qme3zpRVKflaLS5SEqs2eJuWr88xwmH-XMYtmMe6svnLCxL8FEeI7WLztdcPO-i-DD11-J_RnbybB30supq_QQ_gbFdA=w710-h472-n-l95" 
            // data-map_rest_name="Фонда" 
            // data-map_rest_href="/catalog/restoran-fonda/" 
            // data-map_rest_address="Москва, ЮВАО, ул. Угрешская, 2, стр. 90" 
            // data-map_dot_x="55.714275360107" 
            // data-map_dot_y="37.69261932373"

            yaMap.mapInit();
            popup.addClass('_active');
        });

 

    });
})($);