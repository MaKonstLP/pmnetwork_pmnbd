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
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <title><?php echo $this->title ?></title>
    <?php $this->head() ?>
    <?php if (!empty($this->params['desc'])) echo "<meta name='description' content='" . $this->params['desc'] . "'>"; ?>
    <?php if (!empty($this->params['kw'])) echo "<meta name='keywords' content='" . $this->params['kw'] . "'>"; ?>
    <?= Html::csrfMetaTags() ?>
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
                <div class="city_list_wrap">
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
                    <input type="text" name="city" placeholder="Название города">
                    <div class="city_list">
                        <?php
                        $address = \Yii::$app->params['siteAddress'];
                        $activeSubdomenRecords = \Yii::$app->params['activeSubdomenRecords'];
                        $reduced = array_reduce($activeSubdomenRecords, function ($acc, $subdomen) use ($address) {
                            $firstLetter = mb_substr($subdomen->name, 0, 1);
                            $alias = $subdomen->alias == 'msk' ? '' : $subdomen->alias.'.';
                            $acc[$firstLetter] .= "<a href='http://$alias$address'>$subdomen->name</a>\n";
                            return $acc;
                        }, []);
                        foreach ($reduced as $letter => $links) : ?>
                            <div class='city_list_item'>
                                <div class='char'><?= $letter ?></div>
                                <div class='city_in_char'>
                                    <?= $links ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="header_phone">
                    <a href="tel:+78462057845">8 (846) 205-78-45</a>
                    <p data-open-popup-form class="_link">Подберите мне зал</p>
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
                            <a href="#" class="footer_pc _link">Политика конфиденциальности</a>
                        </div>
                        <div class="footer_nav">
                            <ul class="footer_nav_wrap">
                                <li>
                                    <span>Типы заведения</span>
                                    <ul>
                                        <?php $slices_arr = ['restoran','kafe','loft','veranda','otel']; ?>
                                        <?php foreach (\Yii::$app->params['filtersRestTypes'] as $type_alias => $type_name) { ?>
                                            <?php if (in_array($type_alias, $slices_arr)) { ?>
                                                <li><a href="/catalog/<?php echo $type_alias; ?>/"><?php echo $type_name; ?></a></li>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <li>
                                    <span>Особенности</span>
                                    <ul>
                                        <?php foreach (\Yii::$app->params['filtersRestDop'] as $type_alias => $type_name) { ?>
                                                <li><a href="/catalog/<?php echo $type_alias; ?>/"><?php echo $type_name; ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="footer_block _right">

                        <a class="footer_ph" href="tel:+78462057845">8 (846) 205-78-45</a>

                    </div>
                </div>
            </div>
        </footer>

    </div>

    <div class="popup_wrap">

        <div class="popup_layout" data-close-popup></div>

        <div class="popup_form">
            <?= $this->render('//components/generic/form.twig', ['title' => 'Помочь подобрать зал?']) ?>
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