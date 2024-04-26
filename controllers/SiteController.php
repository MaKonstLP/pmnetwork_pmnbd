<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use backend\modules\pmnbd\models\blog\BlogPost;
use yii\web\Controller;
use common\models\Filter;
use common\models\Slices;
use frontend\modules\pmnbd\models\ElasticItems;
use common\models\elastic\ItemsFilterElastic;
use common\models\Pages;
use common\models\Restaurants;
use common\models\RestaurantsTypes;
use common\models\Seo;
use common\models\Subdomen;
use common\models\SubdomenPages;
use frontend\components\Declension;
use frontend\components\PremiumMixer;
use frontend\modules\pmnbd\models\MediaEnum;
use yii\web\Response;
use frontend\components\QueryFromSlice;
use frontend\components\ParamsFromQuery;
use frontend\modules\pmnbd\models\RestaurantTypeSlice;
use yii\web\NotFoundHttpException;

class SiteController extends BaseFrontendController
{
	const MAIN_FILTERS = ['mesto']; //, 'dopolnitelno'];

	public $filter_model,
		$slices_model;

	public function beforeAction($action)
	{
		if (!parent::beforeAction($action)) {
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
			if (
				$item['doc_count'] > 3 && count($acc) < 5
				&& ($restTypeSlice = RestaurantTypeSlice::find()->with('slice')->with('restaurantType')->where(['restaurant_type_value' => intval($item['key'])])->one())
				&& ($sliceObj = $restTypeSlice->slice)
				&& ($typeObj = $restTypeSlice->restaurantType)
			) {
				$acc[] = [
					'alias' => $sliceObj->alias,
					'plural' => str_replace(" площадок", "", Declension::get_num_ending($item['doc_count'], array_map('mb_strtolower', [$typeObj->text, $typeObj->plural_2, $typeObj->plural_5]))),
					'count' => $item['doc_count']
				];
			}
			return $acc;
		}, []);


		$mainRestTypesCounts = count($mainRestTypesCounts) >= 3 ? $mainRestTypesCounts : [];

		// print_r($mainRestTypesCounts);die;

		// $items = new ItemsFilterElastic([], 36, 1, false, 'restaurants', new ElasticItems());
		$items = PremiumMixer::getItemsWithPremium([], 36, 1, false, 'restaurants', new ElasticItems(), false, false, false, false, false, true);
		$all_items = PremiumMixer::getItemsWithPremium([], 9999, 1, false, 'restaurants', new ElasticItems(), false, false, false, false, false, true);
		$mainWidget = $this->renderPartial('//components/generic/listing.twig', [
			'items' => $items->items
		]);

		// echo ('<pre>');
		// print_r($all_items);
		// exit;

		$filtersItemsForSelect = array_filter($this->filter_model, function ($filter) {
			return in_array($filter->alias, self::MAIN_FILTERS);
		});
		#Фиксированные срезы на главной
		$mainSlices = [
			// '1500-rub'    => ['name' => 'Недорогие рестораны', 'count' => 0],
			'restoran'    => ['name' => 'Рестораны', 'count' => 0],
			'kafe'    => ['name' => 'Кафе', 'count' => 0],
			'veranda'    => ['name' => 'Веранды', 'count' => 0],
			'shater'          => ['name' => 'Шатры', 'count' => 0],
			'za-gorodom'     => ['name' => 'За городом', 'count' => 0],
			'10-chelovek'  => ['name' => 'Банкет на 10 человек', 'count' => 0],
			'15-chelovek'  => ['name' => 'Банкет на 20 человек', 'count' => 0],
			'svoy-alko'     => ['name' => 'Рестораны со своим алкоголем', 'count' => 0],
		];

		$mainSliceMsk = [
			'1500-rub'    => ['name' => 'Недорогие рестораны', 'count' => 0],
		];
		$mainSliceOther = [
			'1000-rub'    => ['name' => 'Недорогие рестораны', 'count' => 0],
		];

		if (Yii::$app->params['subdomen_id'] == 4400) {
			$mainSlices = array_merge($mainSliceMsk, $mainSlices);
		} else {
			$mainSlices = array_merge($mainSliceOther, $mainSlices);
		}

		foreach ($mainSlices as $alias => $sliceTexts) {
			$slice_obj = new QueryFromSlice($alias);
			$temp_params = new ParamsFromQuery($slice_obj->params, $this->filter_model, $this->slices_model);
			// $sliceItems = new ItemsFilterElastic($temp_params->params_filter, 1, 1, false, 'restaurants', new ElasticItems());
			$mainSlices[$alias]['count'] = $temp_params->query_hits;
		}
		$mainSlices = array_filter($mainSlices, function ($slice) {
			return $slice['count'] > 0;
		});

		$blogPosts = [];
		if (\Yii::$app->params['subdomen_alias'] == 'msk') {
			$blogPosts = BlogPost::findWithMedia()
				->where(['published' => 1])
				->andWhere(['not', ['collection' => true]])
				->limit(5)
				->orderBy(['featured' => SORT_DESC, 'published_at' => SORT_DESC])
				->all();
		}

        $collectionPosts = BlogPost::findWithMedia()
            ->where(['published' => true])
            ->where(['collection' => true])
            ->limit(3)
            ->orderBy(['featured' => SORT_DESC, 'published_at' => SORT_DESC])
            ->all();

		$totalRests = $items->total;

		$seo = $this->getSeo('index', 1,  $totalRests);
		$this->setSeo($seo);

		// ===== schemaOrg Product START =====
		$min_price = 99999;
		$max_price = 0;
		$review_count = 0;
		$total_rating = 0;
		$rest_with_rating = 0;
		$average_rating = 0;
		foreach ($all_items->items as $item) {
			if (
				isset($item['restaurant_min_check'])
				&& !empty($item['restaurant_min_check'])
				&& isset($item['restaurant_max_check'])
				&& !empty($item['restaurant_max_check'])
			) {
				if ($item['restaurant_min_check'] < $min_price) {
					$min_price = $item['restaurant_min_check'];
				}
				if ($item['restaurant_max_check'] > $max_price) {
					$max_price = $item['restaurant_max_check'];
				}
			}

			if (isset($item['restaurant_rev_ya']['count']) && !empty($item['restaurant_rev_ya']['count'])) {
				$review_count += preg_replace('/[^0-9]/', '', $item['restaurant_rev_ya']['count']);
			}

			if (isset($item['restaurant_rev_ya']['rate']) && !empty($item['restaurant_rev_ya']['rate'])) {
				$total_rating += $item['restaurant_rev_ya']['rate'];
				$rest_with_rating += 1;
			}
		}
		if ($total_rating != 0) {
			$average_rating = round($total_rating / $rest_with_rating, 1);
		}

		$json_str = '';
		$json_str .= '{
				"@context": "https://schema.org",
				"@type": [
					"Product"
				],
				"name": "' . $seo['h1'] . '",
				"description": "' . $seo['description'] . '"';

		if ($max_price) {
			$json_str .= ',';
			$json_str .= '
				"offers": {
					"@type": "AggregateOffer",
					"offerCount": "' . $items->total . '",
					"priceCurrency": "RUB",
					"highPrice": "' . $max_price . '",
					"lowPrice": "' . $min_price . '"
				}';
		}

		if ($review_count && $average_rating) {
			$json_str .= ',';
			$json_str .= '
				"aggregateRating": {
					"@type": "AggregateRating",
					"bestRating": "5",
					"reviewCount": "' . $review_count . '",
					"ratingValue": "' . $average_rating . '"
				}';
		}
		$json_str .= '}';

		Yii::$app->params['schema_product'] = $json_str;
		// ===== schemaOrg Product END =====

		return $this->render('index.twig', [
			'filters' => $filtersItemsForSelect,
			'seo' => $seo,
			'slider' =>  $mainWidget,
			'count' => $totalRests,
			'mainRestTypesCounts' => $mainRestTypesCounts,
			'mainSlices' => $mainSlices,
			'blogPosts' => $blogPosts,
			'collectionPosts' => $collectionPosts,
			'subdomenObjects' => Yii::$app->params['activeSubdomenRecords'],
			'items' => $items->items,
		]);
	}

	public function actionPage($page)
	{
		if (!($pageObj = Pages::findOne(['type' => $page]))) {
			throw new NotFoundHttpException();
		}
		$seo = $this->getSeo($page);
		$this->setSeo($seo);

		return $this->render('page.twig', ['html' => $pageObj->seoObject->text1]);
	}

    public function actionAdvertising($page) {

        if (!($pageObj = Pages::findOne(['type' => $page]))) {
            throw new NotFoundHttpException();
        }
        $seo = $this->getSeo($page);
        $this->setSeo($seo);

        return $this->render('advertising.twig', [
            'h1' => $pageObj->seoObject->heading,
            'text1' => $pageObj->seoObject->text1
        ]);
    }

	public function actionFilterSubmit()
	{
		if (!Yii::$app->request->isAjax) throw new NotFoundHttpException();

		if (
			!($cityId = Yii::$app->request->get('city_id'))
			|| !($filter = Yii::$app->request->get('filter'))
		)  return ['error' => "invalid get query params"];

		//city_id 123213, filter "6,1" = Filter id,FilterItems value
		if (
			count($exploded = explode(',', $filter)) != 2
			|| !($filter = Filter::findOne($exploded[0]))
		) return ['error' => "unable to find filter"];

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

	public function actionFilterCity()
	{
		if (!Yii::$app->request->isAjax) throw new NotFoundHttpException();

		Yii::$app->response->format = Response::FORMAT_JSON;
		if (
			!($cityId = Yii::$app->request->get('cityId'))
			|| !($subdomen = Subdomen::findOne(['city_id' => $cityId]))
		) {
			return ['error' => "unable to find subdomen with city_id $cityId"];
		}

		$cityFilterModel = Filter::find()
			->with(['items' => function ($query) use ($subdomen) {
				$query->leftJoin(
					'subdomen_filteritem',
					"subdomen_filteritem.filter_items_id = filter_items.id AND subdomen_filteritem.subdomen_id = {$subdomen->id}"
				)
					->where("subdomen_filteritem.is_valid=0 OR (subdomen_filteritem.is_valid=1 AND subdomen_filteritem.hits>0)")
					->select('*');
			}])
			->where(['active' => 1])
			->orderBy(['sort' => SORT_ASC])
			->all();

		$filters = array_filter($cityFilterModel, function ($filter) {
			return in_array($filter->alias, self::MAIN_FILTERS);
		});

		return ['selectsHtml' => $this->renderPartial('//components/filter/homepage_rest-types_list.twig', [
			'filters' => $filters
		])];
	}

	public function actionError()
	{
		return $this->render('error.twig');
	}

	public function actionRobots()
	{
		header('Content-type: text/plain');

		$subdomen_alias = '';
		if (!empty(Yii::$app->params['subdomen_alias']) && Yii::$app->params['subdomen_id'] != 4400) {
			$subdomen_alias = Yii::$app->params['subdomen_alias'] . '.';
		}

		echo "User-agent: *\nDisallow: *utm*\nDisallow: *etext=*\nDisallow: *from=*\nDisallow: *baobab_event_id*\nDisallow: /blog/preview-post/\nDisallow: /blog/?*q=*\nDisallow: /blog/?*per-page=*\nSitemap: https://{$subdomen_alias}birthday-place.ru/sitemap/";
		// echo "User-agent: *\nDisallow: /";
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
