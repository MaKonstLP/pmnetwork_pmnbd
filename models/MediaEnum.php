<?php

namespace frontend\modules\pmnbd\models;

use common\models\Pages;
use backend\modules\pmnbd\models\siteobject\BaseMediaEnum;
use common\models\SubdomenPages;

class MediaEnum extends BaseMediaEnum
{
    const HEADER_IMAGE = 'header-image';
    const ADVANTAGES = 'advantages';

    const LABEL_MAP = [
        self::HEADER_IMAGE => 'Изображения шапки',
        self::ADVANTAGES => 'Изображения преимуществ',
    ];

    public static function getMediaTypes()
    {
        return [
            SubdomenPages::class => [self::HEADER_IMAGE, self::ADVANTAGES],
            Pages::class => [self::HEADER_IMAGE, self::ADVANTAGES],
        ];
    }
}
