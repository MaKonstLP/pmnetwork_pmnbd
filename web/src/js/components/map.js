'use strict';

export default class YaMap {
    constructor() {
        if (typeof ymaps == 'undefined') {
            $(document).ready(() => {
                setTimeout(() => {
                    $.getScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU').done(() => {
                        this.mapInit();
                    });
                }, 1500);
            });
        } else {
            this.mapInit();
        }

    }

    mapInit() {

        ymaps.ready(function () {
            console.log('mapInit');
            var parent = $('.map');
            var myMap = new ymaps.Map('map', {
                    center: [
                        parent.find('#map').data('mapdotx'),
                        parent.find('#map').data('mapdoty'),
                    ],
                    zoom: 15,
                    behaviors: ["drag", "dblClickZoom", "rightMouseButtonMagnifier", "multiTouch"]
                }),

                myPlacemark = new ymaps.Placemark(myMap.getCenter(), {
                    hintContent: 'hintContent',
                    balloonContent: '\
                            <div class="balloon_content_wrap">\
                                <div class="balloon_content_img">\
                                    <img src="' + parent.find('.map_img').attr('src') + '">\
                                </div>\
                                <div class="ballon_content_text">\
                                    <a class="ballon_content_name" href="' + parent.find('.map_rest_name').attr('href') + '">' + parent.find('.map_rest_name').html() + '</a>\
                                    <p class="ballon_content_address">' + parent.find('.map_rest_address').html() + '</p>\
                                </div>\
                            </div>\
                    ',
                }, {
                    //iconLayout: 'default#image',
                    preset: 'islands#darkGreenIcon',
                    hideIconOnBalloonOpen: false,
                    balloonOffset: [0, -37],
                });


            if (window.matchMedia('(max-width: 768px)').matches) {
                myMap.behaviors.disable('scrollZoom');
                myMap.behaviors.disable('drag');
                myMap.events.add('click', () => {
                    myMap.behaviors.enable('scrollZoom');
                    myMap.behaviors.enable('drag');
                });
                const zoom = myMap.controls.get('zoomControl');
                zoom.events.add('click', () => {
                    myMap.behaviors.enable('scrollZoom');
                    myMap.behaviors.enable('drag');
                });
            }

            myMap.geoObjects.add(myPlacemark);

            if ($(window).width() > 600) {
                myPlacemark.balloon.open();
            }
        });

    }


}