'use strict';
import Filter from './filter';
import Swiper from 'swiper';

export default class Listing {
    constructor($block) {
        self = this;
        this.block = $block;
        this.filter = new Filter($('[data-filter-wrapper]'));
        self.initSwiperListingGallery();


        $(document).ready(function () {
            self.constructor.setHeightBlockPrice();
        })

        //КЛИК ПО КНОПКЕ "ПОДОБРАТЬ"
        $('[data-filter-button]').on('click', function () {
            if ($(this).hasClass('filter_submit_button')) {
                $(this).closest('.popup_filter_wrap').slideToggle('Fast');
            }
            self.reloadListing();
        });

        //КЛИК ПО КНОПКЕ СОРТИРОВКИ
        $('[data-listing-sort]').on('click', function () {
            $('[data-listing-sort]._active').removeClass('_active');
            $(this).addClass('_active');
            self.reloadListing(1);
        });

        //КЛИК ПО ПАГИНАЦИИ
        $('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function () {
            let $page_id = +$(this).siblings('[data-pagination-wrapper] [data-listing-pagitem]._active').data('page-id') + +$(this).data('page-increase');
            $page_id = (isNaN($page_id)) ? $(this).data('page-id') : $page_id;
            console.log('[data-pagination-wrapper]');
            self.reloadListing($page_id);
        });
        //console.log(this);

        //КЛИК ПО ПОКАЗАТЬ ЕЩЕ
        $('body').on('click', '[data-append-items]', function () {
            let $page_id = +$(this).siblings('[data-pagination-wrapper]').find('[data-listing-pagitem]._active').data('page-id') + +$(this).siblings('[data-pagination-wrapper]').find('[data-page-increase]').data('page-increase');
            if (!isNaN($page_id)) {
                self.filter.filterListingSubmit($page_id);
                self.filter.promise.then(
                    response => {
                        $('[data-listing-list]').append(response.listing.replace(/item swiper-slide/g, 'item swiper-slide __hide'));
                        $('[data-pagination-wrapper]').html(response.pagination);
                        let visible_items = $(this).closest('[data-page-type="listing"]').find('[data-listing-list] .item:not(:visible)');
                        if (visible_items.length > 0) {
                            visible_items.each(function () {
                                console.log($(this));
                                $(this).slideToggle('Fast');
                                if (!isNaN($page_id)) {
                                    self.appendInListing($page_id);
                                }
                            });
                        }
                        if ($('body').find('[data-page-increase="1"]').length == 0) {
                            $(this).hide();
                        }
                    }
                );
            }
        });

        if ($('[data-other-blogs-swiper]').length > 0) {
            self.initSwiperSameBlogs($('[data-other-blogs-swiper]'));
        }
    }

    initSwiperListingGallery() {
        let swiper = new Swiper('[data-item-gallery]', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            pagination: {
                el: '.item-gallery-pagination',
                type: 'bullets',
                dynamicBullets: true,
                dynamicMainBullets: 1,
            },
        });
    }

    static setHeightBlockPrice() {
        let wrap = $('[data-listing-list]');
        let items = wrap.find('.item');
        let fixedHeight = 0;
        let counter = 1;
        let width = $(window).width();
        let max_counter = width > 1000 ? 3 : 2;

        if (width > 767) {
            items.each(function (index, element) {
                console.log(counter)
                let height = $(this).find('.item_bot').outerHeight();
                fixedHeight = height > fixedHeight ? height : fixedHeight;
                counter++;
                if (counter > max_counter) {
                    if (max_counter == 3)
                        $(items[index - 2]).find('.item_bot').css('height', fixedHeight)
                    $(items[index - 1]).find('.item_bot').css('height', fixedHeight)
                    $(items[index]).find('.item_bot').css('height', fixedHeight)
                    counter = 1;
                    fixedHeight = 0;
                }
            })
        }
    }

    initSwiperSameBlogs($container) {
        let swiper = new Swiper($container, {
            slidesPerView: 3,
            spaceBetween: 30,
            //loop: true,
            navigation: {
                nextEl: '.listing_widget_arrow._next',
                prevEl: '.listing_widget_arrow._prev',
            },
            pagination: {
                el: '.listing_widget_pagination',
                type: 'bullets',
            },
            breakpoints: {
                1000: {
                    slidesPerView: 2.5,
                    navigation: false,
                    loop: false,
                    spaceBetween: 20,
                },
                767: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                }
            }
        });

        let swiper_var = $container.swiper;
    }


    reloadListing(page = 1) {
        let self = this;
        let sort = $('[data-listing-sort]._active').length > 0 ? $('[data-listing-sort]._active').data('value') : '';
        console.log('sort', sort);
        console.log('[data-listing-sort]._active', $('[data-listing-sort]._active'));
        self.block.addClass('_loading');
        self.filter.filterListingSubmit(page, sort);
        self.filter.promise.then(
            response => {
                //console.log(response);
                $('[data-listing-list]').html(response.listing);
                $('[data-listing-title]').html(response.title);
                $('[data-listing-text-top]').html(response.text_top);
                $('[data-listing-text-bottom]').html(response.text_bottom);
                $('[data-pagination-wrapper]').html(response.pagination);
                $('[data-listing-fast-filters]').html(response.fast_filters);
                console.log('response.collection_posts: ', response.collection_posts);
                $('[data-listing-collections]').html(response.collection_posts);
                if ($('[data-other-blogs-swiper]').length > 0) {
                    self.initSwiperSameBlogs($('[data-other-blogs-swiper]'));
                }
                self.constructor.setHeightBlockPrice();
                self.initSwiperListingGallery();
                self.block.removeClass('_loading');
                $('html,body').animate({scrollTop: 0}, 400);
                history.pushState({}, '', '/catalog/' + response.url);
            }
        );
    }

    appendInListing(page = 1) {
        let self = this;
        self.block.addClass('_loading');
        self.filter.filterListingSubmit(page);
        self.filter.promise.then(
            response => {
                console.log('resp', response);
                let append_items = $('<div></div>').html(response.listing).find('.item').each(function () {
                    $(this).css('display', 'none');
                });
                $('[data-listing-list]').append(response.listing);
                //$('[data-listing-title]').html(response.title);
                //$('[data-listing-text-top]').html(response.text_top);
                //$('[data-listing-text-bottom]').html(response.text_bottom);
                $('[data-pagination-wrapper]').html(response.pagination);
                self.constructor.setHeightBlockPrice();
                self.block.removeClass('_loading');
                //$('html,body').animate({scrollTop:0}, 400);
                //history.pushState({}, '', '/catalog/'+response.url);
            }
        );
    }
}