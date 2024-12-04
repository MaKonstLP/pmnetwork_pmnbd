<?php

namespace frontend\modules\pmnbd\components;

use common\models\Filter;
use frontend\components\ParamsFromQuery;
use frontend\modules\pmnbd\models\RestaurantTypeSlice;
use RestaurantTypesMapping;
use Yii;
use yii\helpers\ArrayHelper;

class Breadcrumbs
{
    const FILTER_SOLO_PARAM_PRIORITY = ['mesto', 'vmestimost', 'dopolnitelno', 'chek']; //приоритет для соло крошки

    public static function get_breadcrumbs($level)
    {
        switch ($level) {
            case 1:
                $breadcrumbs = [
                    [
                        'type' => 'single',
                        'link' => '/',
                        'name' => 'Главная'
                    ]
                ];
                break;
            case 2:
                $breadcrumbs = [
                    [
                        'type' => 'single',
                        'link' => '/',
                        'name' => 'Главная'
                    ],
                    [
                        'type' => 'single',
                        'link' => '/catalog/',
                        'name' => 'Каталог'
                    ]
                ];
                break;
            default:
                $breadcrumbs = [];
                break;
        }
        return $breadcrumbs;
    }

    public static function get_rooom_crumbs($rest)
    {
        return array_merge(
            self::get_restaurant_crumbs($rest),
            [[
                'type' => 'single',
                'link' => "/catalog/$rest->restaurant_slug/",
                'name' => "«{$rest->restaurant_name}»"
            ]]
        );
    }

    public static function get_query_crumbs($params_filter, $filter_model, $slices_model)
    {
        if (count($params_filter) > 1) {
            // print_r($temp_params->params_filter);die;
            //если в фильтре есть один единичный параметр то делаем из него крошку согласну приоритета
            foreach (self::FILTER_SOLO_PARAM_PRIORITY as $filterName) {
                if ( //если в get query есть текущий параметр и его значение в одном экземпляре
                    ($filterItemIds = $params_filter[$filterName] ?? null)
                    && (count($filterItemIds) == 1)
                    && ($filterItemId = $filterItemIds[0])
                    //и если есть соответствующий Slice
                    && ($slice = ParamsFromQuery::getSlice([$filterName => $filterItemId], $slices_model))
                    && ($filterItem = $slice->getFilterItem($filter_model))
                ) {

                    if ($filterItem->text == 'Веранды/Террасы') $filterItem->text = 'Веранды';
                    if ($filterItem->text == 'Отели/Гостиницы') $filterItem->text = 'Отели';
                    if ($filterItem->text == 'Бани/сауны') $filterItem->text = 'Сауны';

                    return array_merge(
                        self::get_breadcrumbs(2),
                        [[
                            'type' => 'single',
                            'link' => "/catalog/{$slice->alias}/",
                            'name' => str_replace('/', ' / ', $filterItem->text)
                        ]]
                    );
                }
            }
        }
        return self::get_breadcrumbs(2);
    }

    public static function get_restaurant_crumbs($rest)
    {
        $filter_model = Yii::$app->params['filter_model'];
        $restTypesSlicesCrumbs = array_reduce($rest->restaurant_types, function ($acc, $restTypeMeta) use ($filter_model) {
            $restTypeId = $restTypeMeta['id'];
            //если ресторанный комплекс, добавляем крошку на Рестораны
            if ($restTypeId == 8)
                $restTypeId = 1;

            if (
                ($restTypeSlice = RestaurantTypeSlice::find()->with('slice')->with('restaurantType')->where(['restaurant_type_value' => $restTypeId])->one())
                && ($sliceObj = $restTypeSlice->slice)
                && ($filterItemObj = $sliceObj->getFilterItem($filter_model))
            ) {

                if ($filterItemObj->text == 'Веранды/Террасы') $filterItemObj->text = 'Веранды';
                if ($filterItemObj->text == 'Отели/Гостиницы') $filterItemObj->text = 'Отели';
                if ($filterItemObj->text == 'Бани/сауны') $filterItemObj->text = 'Сауны';

                $acc[str_replace('/', ' / ', $filterItemObj->text)] = [
                    'type' => 'multiple',
                    'link' => "/catalog/{$sliceObj->alias}/",
                    'name' => str_replace('/', ' / ', $filterItemObj->text)
                ];
            }

            return $acc;
        }, []);

        $rest_res = [];
        foreach ($restTypesSlicesCrumbs as $item)
            $rest_res[] = $item;

        return array_merge(
            self::get_breadcrumbs(2),
            $rest_res
        );
    }
}
