<?php

namespace frontend\modules\pmnbd\components;


class Breadcrumbs
{
    public static function get_breadcrumbs($level, $rest = null)
    {
        switch ($level) {
            case 1:
                $breadcrumbs = [
                    '/' => 'Главная',
                ];
                break;
            case 2:
                $breadcrumbs = [
                    '/' => 'Главная',
                    '/catalog/' => 'Каталог',
                ];
                break;
            case 3:
                $breadcrumbs = [
                    '/' => 'Главная',
                    '/catalog/' => 'Каталог',
                ];
                if ($rest) {
                    $breadcrumbs["/catalog/restoran-$rest->restaurant_slug/"] = $rest->restaurant_name;
                }
                break;
            default:
                $breadcrumbs = [];
                break;
        }
        return $breadcrumbs;
    }

    public static function get_query_crumbs($lastPart)
    {
        return array_merge(
            self::get_breadcrumbs(2),
            ['/catalog/' . array_key_first($lastPart) . '/' => $lastPart[array_key_first($lastPart)]]
        );
    }
}
