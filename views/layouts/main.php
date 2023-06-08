<?php

/* @var $this \yii\web\View */

/* @var $content string */

use common\models\Subdomen;
use yii\helpers\Html;

//use frontend\modules\svadbanaprirode\assets\AppAsset;

frontend\modules\pmnbd\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/upload/img/bd/favicon.ico">
    <link type="image/x-icon" rel="shortcut icon" href="/upload/img/bd/favicon.ico">
    <link type="image/png" sizes="16x16" rel="icon" href="/upload/img/bd/favicon-16x16.png">
    <link type="image/png" sizes="32x32" rel="icon" href="/upload/img/bd/favicon-32x32.png">
    <link type="image/png" sizes="192x192" rel="icon" href="/upload/img/bd/android-chrome-192x192.png">
    <link rel="apple-touch-icon" href="/upload/img/bd/apple-touch-icon.png">
    <meta name="msapplication-square150x150logo" content="/upload/img/bd/mstile-150x150.png">
    <meta name="msapplication-config" content="/upload/img/bd/browserconfig.xml">
    <link rel="manifest" href="/upload/img/bd/webmanifest.json">

    <title><?php echo $this->title ?></title>

    <?php $this->head() ?>

    <?php if (Yii::$app->params['noindex_global'] === true) {
        echo '<meta name="robots" content="noindex" />';
    } ?>
    <?php if (!empty($this->params['desc'])) echo "<meta name='description' content='" . $this->params['desc'] . "'>"; ?>
    <?php if (!empty($this->params['kw'])) echo "<meta name='keywords' content='" . $this->params['kw'] . "'>"; ?>
    <?= Html::csrfMetaTags() ?>

	 <!-- schemaOrg START -->
	 <?php if (isset(Yii::$app->params['schema_product']) && !empty(Yii::$app->params['schema_product'])) {
		echo '<script type="application/ld+json">' . Yii::$app->params['schema_product'] . '</script>';
	 }
	 ?>
	 <!-- schemaOrg END -->

    <style>
        <?php
            if (file_exists(\Yii::getAlias('@app/modules/pmnbd/web/dist/app-main.min.css'))) {
                print_r(file_get_contents(\Yii::getAlias('@app/modules/pmnbd/web/dist/app-main.min.css')));
            }
        ?>
    </style>
</head>

<body>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    var fired = false;
    const REAL_USER_EVENT_TRIGGERS = [
        'click',
        'scroll',
        'keypress',
        'wheel',
        'mousemove',
        'touchmove',
        'touchstart',
    ];
    REAL_USER_EVENT_TRIGGERS.forEach(event => {
        window.addEventListener(event, () => {
            if (fired === false) {
                fired = true;
                load_other();
            }
        });
    }, {passive: true});

    function load_other() {
        setTimeout(() => {
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
            })
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(67719148, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true
            });

            var googletagmanager_js = document.createElement('script');
            googletagmanager_js.src = 'https://www.googletagmanager.com/gtag/js?id=UA-179040293-1';
            document.body.appendChild(googletagmanager_js);

        }, 500);
    }

    setTimeout(() => {
        fired = true;
        load_other();
        gtag('event', 'read', {'event_category': '15 seconds'});
    }, 15000);
</script>
<noscript>
    <div><img src="https://mc.yandex.ru/watch/67719148" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->
<!-- Global site tag (gtag.js) - Google Analytics -->
<?php /*<script async src="https://www.googletagmanager.com/gtag/js?id=UA-179040293-1"></script> */ ?>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-179040293-1');
</script>
<?php $this->beginBody() ?>

<!--    --><?php
//    echo '<pre>';
//    print_r(Yii::$app->params);
//    die();
//    ?>
<div class="main_wrap">

    <header class="<?= (Yii::$app->controller->action->id == 'post' || Yii::$app->request->url == '/') ? ' home' : ''; ?>">
        <div class="header_wrap">
            <div class="header_left">
                <a href="/" class="header_logo">
                    <img class="header_logo_img" src="/upload/img/logo.svg"
                         alt="День рождения в <?= Yii::$app->params['subdomen_dec'] ?>">
                </a>
                <a href="/" class="header_logo header_logo_2">
                    <div class="header_logo_text">МОЙ ДЕНЬ</div>
                </a>
                <div class="header_city">
                    <img src="/upload/img/geo_label.png"/>
                    <span class="city"><?= Yii::$app->params['subdomen_name'] ?></span>
                    <span class="choose"></span>
                </div>
            </div>
            <div class="city_list_wrap" data-search-wrap>
                <div class="city_list_top">
                    <a href="/" class="header_logo">
                        <img class="header_logo_img" src="/upload/img/logo.svg"
                             alt="День рождения в <?= Yii::$app->params['subdomen_dec'] ?>">
                    </a>
                    <a href="/" class="header_logo header_logo_2">
                        <div class="header_logo_text">МОЙ ДЕНЬ</div>
                    </a>
                    <div class="city_list_close"></div>
                </div>
                <span class="city_list_title">Выберите город</span>
                <input type="text" name="city" placeholder="Название города" data-search-input>
                <div class="city_list">
                    <?php
                    $address = \Yii::$app->params['siteAddress'];
                    $activeSubdomenRecords = \Yii::$app->params['activeSubdomenRecords'];
                    $reduced = array_reduce($activeSubdomenRecords, function ($acc, $subdomen) use ($address) {
                        $firstLetter = mb_substr($subdomen->name, 0, 1);
                        $alias = $subdomen->city_id == 4400 ? '' : $subdomen->alias . '.';
                        $link = "<a href='https://$alias$address' data-search-city>$subdomen->name</a>\n";
                        isset($acc[$firstLetter]) ? $acc[$firstLetter] .= $link : $acc[$firstLetter] = $link;
                        return $acc;
                    }, []);
                    foreach ($reduced as $letter => $links) : ?>
                        <div class='city_list_item' data-search-city_in_char_wrap>
                            <div class='char'><?= $letter ?></div>
                            <div class='city_in_char' data-search-city_in_char>
                                <?= $links ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="header_nav"
                 data-menu>
                <?php
                $kindArr = array_filter(Yii::$app->params['footer_slices'], function ($meta) {
                    return $meta['type'] == 'kind';
                }); ?>
                <?php foreach ($kindArr as $alias => $meta): ?>
                    <a href="/catalog/<?= $alias ?>/" class="header_nav-link"><?= $meta['name'] ?></a>
                <?php endforeach; ?>
                <?php
                $featureArr = array_filter(Yii::$app->params['footer_slices'], function ($meta) {
                    return $meta['type'] == 'feature';
                }); ?>
                <?php foreach ($featureArr as $type_alias => $meta): ?>
                    <a href="/catalog/<?= $type_alias ?>/" class="header_nav-link"><?= $meta['name'] ?></a>
                <?php endforeach; ?>
                <a href="/stancii-metro/" class="header_nav-link">Метро</a>
                <a href="/blog/" class="header_nav-link">Блог</a>
                <?php if ($this->params['collectionCount'] > 0): ?>
                    <a href="/collection/" class="header_nav-link">Подборки</a>
                <?php endif; ?>
            </div>
            <div class="header_nav-mobile"
                 data-menu-burger>
                <div class="header_nav-title">МЕНЮ</div>
                <div class="header_nav-burger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <!--                <div class="header_phone">-->
            <!--                    <a href="tel:-->
            <? //= Yii::$app->params['subdomen_phone'] ?><!--" data-target="telefon_1">-->
            <? //= Yii::$app->params['subdomen_phone_pretty'] ?><!--</a>-->
            <!--                    <p data-open-popup-form class="_link" data-target="podbor_1">Подберите мне зал</p>-->
            <!--                </div>-->
        </div>
    </header>

    <div class="content_wrap index_first_page">
        <?= $content ?>
    </div>

    <footer <?= Yii::$app->controller->action->id == 'post' ? ' class="__bgWhite"' : ''; ?>>
        <div class="footer_wrap">
            <div class="footer_row">
                <div class="footer_block _left">
                    <a href="/" class="footer_logo">
                        <img class="footer_logo_img" src="/upload/img/logo.svg"
                             alt="День рождения в <?= Yii::$app->params['subdomen_dec'] ?>">
                    </a>
                    <a href="/" class="footer_logo footer_logo_2">
                        <div class="footer_logo_text">МОЙ ДЕНЬ</div>
                    </a>
                    <div class="footer_info">
                        <p class="footer_copy">© <?php echo date("Y"); ?> Мой день</p>
                        <a target="_blank"
                           href="<?= Yii::$app->params['siteProtocol'] . '://' . Yii::$app->params['siteAddress'] ?>/politika/"
                           class="footer_pc _link">Политика конфиденциальности</a>
                    </div>
                    <div class="footer_nav">
                        <ul class="footer_nav_wrap">
                            <li class="footer_nav_wrap-el">
                                <span class="footer_nav_wrap-title">Типы заведения</span>
                                <ul class="footer_nav_wrap-sub _columns-2">
                                    <?php
                                    $kindArr = array_filter(Yii::$app->params['footer_slices'], function ($meta) {
                                        return $meta['type'] == 'kind';
                                    });
                                    foreach ($kindArr as $alias => $meta) { ?>
                                        <li class="footer_nav_wrap-sub_el"><a
                                                    href="/catalog/<?= $alias ?>/"><?= $meta['name'] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <li class="footer_nav_wrap-el">
                                <span class="footer_nav_wrap-title">Особенности</span>
                                <ul class="footer_nav_wrap-sub">
                                    <?php
                                    $featureArr = array_filter(Yii::$app->params['footer_slices'], function ($meta) {
                                        return $meta['type'] == 'feature';
                                    });
                                    foreach ($featureArr as $type_alias => $meta) { ?>
                                        <li class="footer_nav_wrap-sub_el"><a
                                                    href="/catalog/<?= $type_alias ?>/"><?= $meta['name'] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </li>
                            <li class="footer_nav_wrap-el">
                                <span class="footer_nav_wrap-title">Интересное</span>
                                <ul class="footer_nav_wrap-sub">
                                    <li class="footer_nav_wrap-sub_el"><a href="/blog/">Блог</a></li>
                                    <?php if ($this->params['collectionCount'] > 0): ?>
                                        <li class="footer_nav_wrap-sub_el"><a href="/collection/">Подборки</a></li>
                                    <?php endif; ?>
												<li class="footer_nav_wrap-sub_el"><a href="/stancii-metro/">Метро</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <!--                    <div class="footer_block _right">-->
                <!--                        <a class="footer_ph" href="tel:-->
                <? //= Yii::$app->params['subdomen_phone'] ?><!--"-->
                <!--                           data-target="telefon_1">-->
                <? //= Yii::$app->params['subdomen_phone_pretty'] ?><!--</a>-->
                <!--                    </div>-->
            </div>
        </div>
    </footer>

<!--    <p style="font-family: 'Lato', sans-serif; font-weight: 300">Lato 300</p>-->
<!--    <p style="font-family: 'Lato', sans-serif; font-weight: 300; font-style: italic">Lato 300 italic</p>-->
<!--    <p style="font-family: 'Lato', sans-serif; font-weight: 400">Lato 400</p>-->
<!--    <p style="font-family: 'Lato', sans-serif; font-weight: 400; font-style: italic">Lato 400 italic</p>-->
<!--    <p style="font-family: 'Lato', sans-serif; font-weight: 700">Lato 700</p>-->
<!--    <p style="font-family: 'Lato', sans-serif; font-weight: 700; font-style: italic">Lato 700 italic</p>-->
<!--    <p style="font-family: 'Proxima Nova Thin', sans-serif;">Proxima Nova Thin</p>-->
<!--    <p style="font-family: 'Proxima Nova Regular', sans-serif;">Proxima Nova Regular</p>-->

</div>

<div class="popup_wrap ">
    <div class="popup_layout" data-close-popup></div>
    <div class="popup_form">
        <?= $this->render('//components/generic/form.twig', ['title' => 'Помочь подобрать зал?', 'type' => 'header', 'target' => 'podbor_2']) ?>
    </div>
    <div class="popup_img">
        <div class="popup_img_close" data-close-popup></div>
        <div class="popup_img_slider_wrap">
            <div class="slider_arrow _prev"></div>
            <div class="slider_arrow _next"></div>
            <div class="object_gallery_container swiper-container" data-gallery-img-swiper>
                <div class="object_gallery_swiper swiper-wrapper" data-gallery-list></div>
            </div>
        </div>
    </div>

</div>

<!--    карта       -->
<div class="popup_wrap popup_wrap_single-map">
    <div class="popup_layout" data-close-popup></div>
    <div class="popup_form">
        <?= $this->render('//components/generic/popup_single_map.twig') ?>
    </div>
</div>

<?php $this->endBody() ?>
<!--link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap&subset=cyrillic" rel="stylesheet"-->

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,800;1,400;1,600;1,800&display=swap&subset=cyrillic"
      rel="stylesheet" crossorigin="anonymous">
<link href="//cdn.jsdelivr.net/jquery.mcustomscrollbar/3.0.6/jquery.mCustomScrollbar.min.css" rel="stylesheet"
      crossorigin="anonymous">
</body>

</html>
<?php $this->endPage() ?>

