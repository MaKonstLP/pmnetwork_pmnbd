'use strict';

export default class YaMap{
	constructor(){
		ymaps.ready(function () {
	    var myMap = new ymaps.Map('map', {
	        center: [
            	$('.map #map').data('mapdotx'),
            	$('.map #map').data('mapdoty'),
            ],
            zoom: 15,
            behaviors: ["drag", "dblClickZoom", "rightMouseButtonMagnifier", "multiTouch"]
        }),

        myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
            hintContent: 'hintContent',
            balloonContent: '\
                            <div class="balloon_content_wrap">\
                                <div class="balloon_content_img">\
                                    <img src="'+ $('.map_img').attr('src') +'">\
                                </div>\
                                <div class="ballon_content_text">\
                                    <a class="ballon_content_name" href="#">'+ $('.map_rest_name').html() +'</a>\
                                    <p class="ballon_content_address">'+ $('.map_rest_address').html() +'</p>\
                                </div>\
                            </div>\
            ',
        }, {
            //iconLayout: 'default#image',
            preset: 'islands#darkGreenIcon',
            hideIconOnBalloonOpen: false,
            balloonOffset: [0,-37],
        });

	    myMap.geoObjects.add(myPlacemark); 
        
        if($(window).width() > 600){
            myPlacemark.balloon.open();
        }
		});
	}
}