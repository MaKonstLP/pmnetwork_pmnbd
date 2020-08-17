<?php

/* @var $this \yii\web\View */
/* @var $content string */

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
    <?php if (!empty($this->params['desc'])) echo "<meta name='description' content='".$this->params['desc']."'>";?>
    <?php if (!empty($this->params['kw'])) echo "<meta name='keywords' content='".$this->params['kw']."'>";?>
    <?= Html::csrfMetaTags() ?>
</head>
<body>
<?php $this->beginBody() ?>

    <div class="main_wrap">
        
        <header>
            <div class="header_wrap<?php echo (Yii::$app->request->url == '/') ? ' home' : '';?>">
                <a href="/" class="header_logo">
                    <div class="header_logo_img"></div>
                </a>
                <a href="/" class="header_logo header_logo_2">
                    <div class="header_logo_text">МОЙ ДЕНЬ</div>
                </a>
                <div class="header_city <?php echo (Yii::$app->request->url == '/') ? ' home' : '';?>">
                    <img src="/img/geo_label.png"/>
                    <span class="city">Санкт-Петербург</span>
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
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div><div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div><div class="city_list_item">
                            <div class="char">К</div>
                            <div class="city_in_char">
                               <a href="#">Казань</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">М</div>
                            <div class="city_in_char">
                               <a href="#">Москва</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">Н</div>
                            <div class="city_in_char">
                               <a href="#">Нижний Новгород</a> 
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">С</div>
                            <div class="city_in_char">
                                <a href="#">Самара</a>
                                <a href="#">Санкт-Петербург</a>
                            </div>
                        </div>
                        <div class="city_list_item">
                            <div class="char">У</div>
                            <div class="city_in_char">
                                <a href="#">Уфа</a>
                                <a href="#">Ульяновск</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="header_phone">
                    <a href="tel:+78462057845">(846) 205-78-45</a>
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
                            <p class="footer_copy">© <?php echo date("Y");?> Мой день</p>
                            <a href="#" class="footer_pc _link">Политика конфиденциальности</a>
                        </div>    
                        <div class="footer_nav">
                            <ul class="footer_nav_wrap">
                                <li>
                                    <span>Типы заведения</span>
                                    <ul>
                                        <li><a href="#">Рестораны</a></li>
                                        <li><a href="#">Лофты</a></li>
                                        <li><a href="#">Кафе</a></li>
                                        <li><a href="#">Базы отдыха</a></li>
                                    </ul>
                                </li>
                                <li>
                                    <span>Особенности</span>
                                    <ul>
                                        <li><a href="#">За городом</a></li>
                                        <li><a href="#">Со своим алкоголем</a></li>
                                        <li><a href="#">У воды</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>                    
                    </div>
                    <div class="footer_block _right">
                        
                            <p>8 (846) 205-78-45</p>
                        
                    </div>
                </div>
            </div>
        </footer>

    </div>

    <div class="popup_wrap">

        <div class="popup_layout" data-close-popup></div>

        <div class="popup_form">
            <?=$this->render('//components/generic/form.twig',['title'=>'Помочь подобрать зал?'])?>
        </div>

        <div class="popup_img">
            <div class="popup_img_close" data-close-popup></div>
            <img src="">
        </div>

    </div>


<?php $this->endBody() ?>
<!--link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600&display=swap&subset=cyrillic" rel="stylesheet"-->
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap&subset=cyrillic" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,800;1,400;1,600;1,800&display=swap&subset=cyrillic" rel="stylesheet">
<link href="//cdn.jsdelivr.net/jquery.mcustomscrollbar/3.0.6/jquery.mCustomScrollbar.min.css" rel="stylesheet">
</body>
</html>
<?php $this->endPage() ?>
