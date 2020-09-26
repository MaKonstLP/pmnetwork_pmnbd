<?php

namespace app\modules\pmnbd\controllers;

use common\models\blog\BlogPost;
use Yii;
use yii\web\Controller;
use common\models\Filter;
use common\models\Slices;
use frontend\modules\pmnbd\models\ElasticItems;
use common\models\elastic\ItemsFilterElastic;
use common\models\Pages;
use common\models\Restaurants;
use common\models\Seo;
use common\models\Subdomen;
use common\models\SubdomenPages;
use frontend\modules\pmnbd\models\MediaEnum;
use yii\web\Response;
use frontend\components\QueryFromSlice;
use frontend\components\ParamsFromQuery;
use yii\web\NotFoundHttpException;

class SiteController extends BaseFrontendController
{
    const MAIN_FILTERS = ['mesto']; //, 'dopolnitelno'];

    public $filter_model,
        $slices_model;

    public function beforeAction($action)
    {
        if(!parent::beforeAction($action)) {
			return false;
		}
        $this->filter_model = Yii::$app->params['filter_model'];
        $this->slices_model = Yii::$app->params['slices_model'];

        return true;
    }


    public function actionIndex()
    {
        $aggs = ElasticItems::find()->limit(0)->query(
            ['bool' => ['must' => ['match' => ['restaurant_city_id' => Yii::$app->params['subdomen_id']]]]]
        )
            ->addAggregate('types', [
                'nested' => [
                    'path' => 'restaurant_types',
                ],
                'aggs' => [
                    'ids' => [
                        'terms' => [
                            'field' => 'restaurant_types.id',
                            'size' => 10000,
                        ]
                    ]
                ]
            ])->search();

        $mainRestTypesCounts = array_reduce($aggs['aggregations']['types']['ids']['buckets'], function ($acc, $item) {
            $acc[$item['key']] = $item['doc_count'];
            return $acc;
        }, []);

        $items = new ItemsFilterElastic([], 30, 1, false, 'restaurants', new ElasticItems());
        $mainWidget = $this->renderPartial('//components/generic/listing.twig', [
            'items' => $items->items
        ]);

        $filtersItemsForSelect = array_filter($this->filter_model, function ($filter) {
            return in_array($filter->alias, self::MAIN_FILTERS);
        });
        #Фиксированные срезы на главной
        $mainSlices = [
            '1500-rub'    => ['name' => 'Недорогие рестораны', 'count' => 0],
            'veranda'       => ['name' => 'Веранды', 'count' => 0],
            'loft'          => ['name' => 'Лофты', 'count' => 0],
            'shater'          => ['name' => 'Шатры', 'count' => 0],
            'za-gorodom'     => ['name' => 'За городом', 'count' => 0],
            '20-25-chelovek'  => ['name' => 'Банкет на 20 человек', 'count' => 0],
            '30-chelovek'     => ['name' => 'Банкет на 30 человек', 'count' => 0],
            'svoy-alko'     => ['name' => 'Рестораны со своим алкоголем', 'count' => 0],
        ];
        foreach ($mainSlices as $alias => $sliceTexts) {
            $slice_obj = new QueryFromSlice($alias);
            $temp_params = new ParamsFromQuery($slice_obj->params, $this->filter_model, $this->slices_model);
            // $sliceItems = new ItemsFilterElastic($temp_params->params_filter, 1, 1, false, 'restaurants', new ElasticItems());
            $mainSlices[$alias]['count'] = $temp_params->query_hits;
        }
        $mainSlices = array_filter($mainSlices, function ($slice) {
            return $slice['count'] > 0;
        });

        $blogPosts = BlogPost::findWithMedia()
            ->limit(5)->where(['published' => 1])
            ->orderBy(['featured' => SORT_DESC, 'published_at' => SORT_DESC])->all();

        $totalRests = $items->total;

        $seo = $this->getSeo('index', 1,  $totalRests);
        $this->setSeo($seo);

        return $this->render('index.twig', [
            'filters' => $filtersItemsForSelect,
            'seo' => $seo,
            'slider' =>  $mainWidget,
            'count' => $totalRests,
            'mainRestTypesCounts' => $mainRestTypesCounts,
            'mainSlices' => $mainSlices,
            'blogPosts' => $blogPosts,
            'subdomenObjects' => Yii::$app->params['activeSubdomenRecords']
        ]);
    }

    public function actionPage($page)
    {
        if(!($pageObj = Pages::findOne(['type' => $page]))) {
            throw new NotFoundHttpException();
        }
        $seo = $this->getSeo($page);
        $this->setSeo($seo);
        
        return $this->render('page.twig',['html' => $pageObj->seoObject->text1]);
    }

    public function actionFilterSubmit()
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
            $subdomenPart = $subdomen->city_id == 4400 ? '' : $subdomen->alias . '.';
            $redirect = \Yii::$app->params['siteProtocol'] . '://'
                . $subdomenPart
                . \Yii::$app->params['siteAddress']
                . "/catalog/$resultSliceAlias/";
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['redirect' => $redirect];
        }
        return;

    }

    public function actionError()
    {
        return $this->render('error.twig');
    }

    public function actionRobots()
    {
        header('Content-type: text/plain');
        if (Yii::$app->params['subdomen_alias']) {
            $subdomen_alias = Yii::$app->params['subdomen_alias'] . '.';
        } else {
            $subdomen_alias = '';
        }
        // echo "User-agent: *\nSitemap: https://'.$subdomen_alias.'birthday-place.ru/sitemap/";
        echo "User-agent: *\nDisallow: /";
        exit;
    }

    private function getSeo($type, $page = 1, $count = 0)
    {
        $seo = (new Seo($type, $page, $count))->withMedia([MediaEnum::HEADER_IMAGE, MediaEnum::ADVANTAGES]);
        return $seo->seo;
    }

    private function setSeo($seo)
    {
        $this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
    }

}
