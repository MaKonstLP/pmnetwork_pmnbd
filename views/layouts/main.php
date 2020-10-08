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

    <link rel="icon" type="image/png" href="/img/favicon_bd.ico">
    <link type="image/x-icon" rel="shortcut icon" href="/img/bd/favicon.ico">
    <link type="image/png" sizes="16x16" rel="icon" href="/img/bd/favicon-16x16.png">
    <link type="image/png" sizes="32x32" rel="icon" href="/img/bd/favicon-32x32.png">
    <link type="image/png" sizes="192x192" rel="icon" href="/img/bd/android-chrome-192x192.png">
    <link rel="apple-touch-icon" href="/img/bd/apple-touch-icon.png">
    <meta name="msapplication-square150x150logo" content="/img/bd/mstile-150x150.png">
    <meta name="msapplication-config" content="/img/bd/browserconfig.xml">
    <link rel="manifest" href="/img/bd/webmanifest.json">

    <title><?php echo $this->title ?></title>

    <?php $this->head() ?>
    <?php if (!empty($this->params['desc'])) echo "<meta name='description' content='" . $this->params['desc'] . "'>"; ?>
    <?php if (!empty($this->params['kw'])) echo "<meta name='keywords' content='" . $this->params['kw'] . "'>"; ?>
    <?= Html::csrfMetaTags() ?>
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m, e, t, r, i, k, a) {
            m[i] = m[i] || function() {
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
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/67719148" style="position:absolute; left:-9999px;" alt="" /></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-179040293-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-179040293-1');
    </script>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="main_wrap">

        <header>
            <div class="header_wrap<?php echo (Yii::$app->request->url == '/') ? ' home' : ''; ?>">
                <a href="/" class="header_logo">
                    <div class="header_logo_img"></div>
                </a>
                <a href="/" class="header_logo header_logo_2">
                    <div class="header_logo_text">МОЙ ДЕНЬ</div>
                </a>
                <div class="header_city <?php echo (Yii::$app->request->url == '/') ? ' home' : ''; ?>">
                    <img src="/img/geo_label.png" />
                    <span class="city"><?= Yii::$app->params['subdomen_name'] ?></span>
                    <span class="choose"></span>
                </div>
                <div class="city_list_wrap" data-search-wrap>
                    <div class="city_list_top">
                        <a href="/" class="header_logo">
                            <div class="header_logo_img"></div>
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
                            $link = "<a href='http://$alias$address' data-search-city>$subdomen->name</a>\n";
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
                <div class="header_phone">
                    <a href="tel:+79252380246" data-target="telefon_1">8 (925) 238-02-46</a>
                    <p data-open-popup-form class="_link" data-target="podbor_1">Подберите мне зал</p>
                </div>
            </div>
        </header>

        <div class="content_wrap index_first_page">
            <?= $content ?>
        </div>

        <footer>
            <div class="footer_wrap">
                <div class="footer_row">
                    <div class="footer_block _left">
                        <a href="/" class="footer_logo">
                            <div class="footer_logo_img"></div>
                        </a>
                        <a href="/" class="footer_logo footer_logo_2">
                            <div class="footer_logo_text">МОЙ ДЕНЬ</div>
                        </a>
                        <div class="footer_info">
                            <p class="footer_copy">© <?php echo date("Y"); ?> Мой день</p>
                            <a target="_blank" href="<?= Yii::$app->params['siteProtocol'] . '://' . Yii::$app->params['siteAddress'] ?>/politika/" class="footer_pc _link">Политика конфиденциальности</a>
                        </div>
                        <div class="footer_nav">
                            <ul class="footer_nav_wrap">
                                <li>
                                    <span>Типы заведения</span>
                                    <ul>
                                        <?php
                                        $kindArr = array_filter(Yii::$app->params['footer_slices'], function ($meta) {
                                            return $meta['type'] == 'kind';
                                        });
                                        foreach ($kindArr as $alias => $meta) { ?>
                                            <li><a href="/catalog/<?= $alias ?>/"><?= $meta['name'] ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <li>
                                    <span>Особенности</span>
                                    <ul>
                                        <?php
                                        $featureArr = array_filter(Yii::$app->params['footer_slices'], function ($meta) {
                                            return $meta['type'] == 'feature';
                                        });
                                        foreach ($featureArr as $type_alias => $meta) { ?>
                                            <li><a href="/catalog/<?= $type_alias ?>/"><?= $meta['name'] ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer_block _right">

                        <a class="footer_ph" href="tel:+79252380246" data-target="telefon_1">8 (925) 238-02-46</a>

                    </div>
                </div>
            </div>
        </footer>

    </div>

    <div class="popup_wrap">

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


    <?php $this->endBody() ?>
    <!--link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap&subset=cyrillic" rel="stylesheet"-->

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,800;1,400;1,600;1,800&display=swap&subset=cyrillic" rel="stylesheet" crossorigin="anonymous">
    <link href="//cdn.jsdelivr.net/jquery.mcustomscrollbar/3.0.6/jquery.mCustomScrollbar.min.css" rel="stylesheet" crossorigin="anonymous">
</body>

</html>
<?php $this->endPage() ?>