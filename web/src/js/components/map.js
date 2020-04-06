'use strict';

export default class YaMap{
	constructor(){
		ymaps.ready(function () {
	    var myMap = new ymaps.Map('map', {
	        center: [
            	$('.map #map').data('mapdotx'),
            	$('.map #map').data('mapdoty'),
            ],
            zoom: 15
        }),

        myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
            hintContent: 'hintContent',
            balloonContent: 'balloonContent'
        }, {
            iconLayout: 'default#image',
        });

	    myMap.geoObjects
		        .add(myPlacemark); 
		});
	}
}