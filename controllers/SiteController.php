<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use common\widgets\FilterWidget;
use common\models\elastic\ItemsWidgetElastic;
use common\models\Pages;
use common\models\Filter;
use common\models\Slices;
use app\modules\pmnbd\models\ElasticItems;
use common\models\elastic\ItemsElastic;
use common\models\elastic\ItemsFilterElastic;
use common\models\Subdomen;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use common\components\QueryFromSlice;
use common\components\ParamsFromQuery;

class SiteController extends Controller
{
    //public function getViewPath()
    //{
    //    return Yii::getAlias('@app/modules/svadbanaprirode/views/site');
    //}

    const MAIN_FILTERS = ['mesto'];

    public function actionIndex()
    {
        if (
            Yii::$app->request->isAjax
            && ($cityId = Yii::$app->request->get('city_id'))
            && ($filter = Yii::$app->request->get('filter'))
        ) {
            //city_id 123213, filter "6,1" = Filter id,FilterItems value
            if (
                count($exploded = explode(',', $filter)) != 2
                || !($filter = Filter::findOne($exploded[0]))
            ) return;

            //params {"mesto":5}
            $slices = Slices::find()->where(['like', 'params', $filter->alias])->all();
            $resultSliceAlias = null;
            foreach ($slices as $slice) {
                $decoded = json_decode($slice->params, true);
                if ($decoded[$filter->alias] == $exploded[1]) {
                    $resultSliceAlias = $slice->alias;
                    break;
                }
            }

            if (!$resultSliceAlias || !($subdomen = Subdomen::findOne(['city_id' => $cityId]))) return;

            $redirect = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://")
                . $subdomen->alias . '.'
                . \Yii::$app->params['siteAddress']
                . "/catalog/$resultSliceAlias/";
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['redirect' => $redirect];
        }

        $filter_model = Filter::find()->with('items')->all();

        $items = new ItemsFilterElastic(['mesto' => ['2','3','5','7','8']], 10000, 1, false, 'restaurants', new ElasticItems());
        $mainWidget = $this->renderPartial('//components/generic/listing.twig', [
            'items' => array_slice($items->items, 0, 30)
        ]);

        $mainRestTypesCounts = array_reduce(
            $items->items,
            function ($acc, $rest) {
                foreach ($rest->restaurant_types as $type) {
                    if (!isset($acc[$type['id']])) {
                        $acc[$type['id']] = 1;
                    } else $acc[$type['id']] += 1;
                }
                return $acc;
            },
            [] //[ '1' => 200, '3' => 70 ...]
        );
       
        $filtersItemsForSelect = array_filter($filter_model, function ($filter) {
            return in_array($filter->alias, self::MAIN_FILTERS);
        });

        $seo = Pages::find()->where(['type' => 'index'])->one();
        $this->setSeo($seo);

        #Фиксированные срезы на главной
        $mainSlices = [
            'check-1500'    => ['name' => 'Недорогие рестораны'],
            'veranda'       => ['name' => 'Веранды'],
            'loft'          => ['name' => 'Лофты'],
            'tent'          => ['name' => 'Шатры'],
            '15-people'     => ['name' => 'Банкет на 15 человек'],
            '20-25-people'  => ['name' => 'Банкет на 20 человек'],
            '30-people'     => ['name' => 'Банкет на 30 человек'],
            'svoy-alko'     => ['name' => 'Рестораны со своим алкоголем'],
        ];
        foreach ($mainSlices as $alias => $sliceTexts) {
            $slice = Slices::find()->where(['alias' => $alias])->one();
            $filter_model = Filter::find()->where(['active' => true])->with('items')->orderBy('sort')->all();
            $slice_obj = new QueryFromSlice($slice);
            $temp_params = new ParamsFromQuery($slice_obj->params, $filter_model, $slice);
            $sliceItems = new ItemsFilterElastic($temp_params->params_filter, 10000, 1, false, 'restaurants', new ElasticItems());
            $mainSlices[$alias]['count'] = $sliceItems->total;
        }

        return $this->render('index.twig', [
            'appParams' => \Yii::$app->params,
            'filters' => $filtersItemsForSelect,
            'seo' => $seo,
            'slider' =>  $mainWidget,
            'count' => $items->total,
            'mainRestTypesCounts' => $mainRestTypesCounts,
            'mainSlices' => $mainSlices
        ]);
    }

    private function setSeo($seo)
    {
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
    }

    /*  public function actionError()
    {
        echo "Произошла ошибка на сайте. Обратитесь к администратору.";
        exit;
        return $this->render('error.twig');
    } */
}
