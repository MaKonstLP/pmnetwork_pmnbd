'use strict';

export default class YaMap {
    constructor() {
        let self = this;
        var fired = false;

        window.addEventListener('click', () => {
            if (fired === false) {
                fired = true;
                load_other();
            }
        }, {passive: true});

        window.addEventListener('scroll', () => {
            if (fired === false) {
                fired = true;
                load_other();
            }
        }, {passive: true});

        window.addEventListener('mousemove', () => {
            if (fired === false) {
                fired = true;
                load_other();
            }
        }, {passive: true});

        window.addEventListener('touchmove', () => {
            if (fired === false) {
                fired = true;
                load_other();
            }
        }, {passive: true});

        function load_other() {
            console.log('load_other');
            setTimeout(function() {
                self.mapInit();
            }, 100);
        }


        // if (typeof ymaps == 'undefined') {
        //     $(document).ready(() => {
        //         setTimeout(() => {
        //             $.getScript('https://api-maps.yandex.ru/2.1/?lang=ru_RU').done(() => {
        //                 this.mapInit();
        //             });
        //         }, 1500);
        //     });
        // } else {
        //     this.mapInit();
        // }

    }

    script(url) {
        if (Array.isArray(url)) {
            let self = this;
            let prom = [];
            url.forEach(function (item) {
                prom.push(self.script(item));
            });
            return Promise.all(prom);
        }

        return new Promise(function (resolve, reject) {
            let r = false;
            let t = document.getElementsByTagName('script')[0];
            let s = document.createElement('script');

            s.type = 'text/javascript';
            s.src = url;
            s.async = true;
            s.onload = s.onreadystatechange = function () {
                if (!r && (!this.readyState || this.readyState === 'complete')) {
                    r = true;
                    resolve(this);
                }
            };
            s.onerror = s.onabort = reject;
            t.parentNode.insertBefore(s, t);
        });
    }


    mapInit() {
        let self = this;
        this.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').then((data) => {
            const ymaps = global.ymaps;
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

        });
    }


    mapPopup() {

        ymaps.ready(function () {
            console.log('mapPopup');
            var parent = $('.map.map-popup');
            var myMap = new ymaps.Map('map-popup', {
                    center: [
                        parent.find('#map-popup').data('mapdotx'),
                        parent.find('#map-popup').data('mapdoty'),
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