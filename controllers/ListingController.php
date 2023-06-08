<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\web\Controller;
use frontend\widgets\FilterWidget;
use frontend\widgets\PaginationWidgetPrevNext;
use frontend\components\ParamsFromQuery;
use frontend\components\QueryFromSlice;
use frontend\components\PremiumMixer;
use frontend\modules\pmnbd\components\Breadcrumbs;
use common\models\elastic\ItemsFilterElastic;
use common\models\Filter;
use common\models\RestaurantsTypes;
use common\models\Seo;
use common\models\Slices;
use frontend\modules\pmnbd\models\ElasticItems;
use frontend\modules\pmnbd\models\RestaurantTypeSlice;
use backend\modules\pmnbd\models\blog\BlogPost;
use backend\modules\pmnbd\models\blog\BlogPostSlice;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class ListingController extends BaseFrontendController
{
	//порядок и количество вывода для блока тэгов. Filter->alias => количество кнопок
	const FAST_FILTERS = [
		//для одиночного среза, по типу его фильтра
		'mesto' => ['mesto' => 15, 'vmestimost' => 2, 'dopolnitelno' => 1, 'chek' => 1],
		'vmestimost' => ['vmestimost' => 5, 'mesto' => 3, 'dopolnitelno' => 1, 'chek' => 1],
		'dopolnitelno' => ['dopolnitelno' => 10, 'chek' => 2, 'mesto' => 3, 'vmestimost' => 2],
		'chek' => ['chek' => 10, 'mesto' => 3, 'dopolnitelno' => 2, 'vmestimost' => 2],
		'metro' => ['mesto' => 4, 'metro' => 4],
		//для множественных
		'any' => ['dopolnitelno' => 2, 'mesto' => 3, 'vmestimost' => 2, 'chek' => 2]
	];

	protected $per_page = 36;

	public $filter_model,
		$slices_model;

	public function beforeAction($action)
	{
		if (!parent::beforeAction($action)) {
			return false;
		}
		if (strpos($action->controller->request->pathInfo, 'listing') !== false) {
			throw new \yii\web\NotFoundHttpException();
		}
		$this->filter_model = Yii::$app->params['filter_model'];
		$this->slices_model = Yii::$app->params['slices_model'];

		return true;
	}

	public function actionSlice($slice)
	{
		$is_metro = false;
		if (strpos($slice, 'metro-') !== false) {
			$is_metro = true;
		}
		
		$slice_obj = new QueryFromSlice($slice, $is_metro);

		if ($slice_obj->flag) {
			if ($slice_obj->slice_model['type'] == 'metro' &&  $slice_obj->slice_model['description'] != Yii::$app->params['subdomen_id']) {
				\Yii::$app->response->redirect('/', 301);
			}
			
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, $this->filter_model, $this->slices_model);

			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			if (isset($_GET['zzz'])) {
				print_r(count($params['params_filter']['mesto'] ?? []) == 1);
				die;
			}

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2),
				$canonical 		= 	$canonical,
				$type 			=	$slice,
				$fastFilters	=	$params['fast_filters'],
				$itemTypeName   =	(count($params['params_filter']['mesto'] ?? []) == 1
					? (RestaurantTypeSlice::find()->with('restaurantType')->where(['slice_id' => $slice_obj->slice_model])->one()->restaurantType->text ?? "")
					: ""),
				$slice_id = $slice_obj->slice_model['id'],
			);
		} else {
			// return $this->goHome();
			\Yii::$app->response->redirect('/', 301);
		}
	}

	public function actionIndex()
	{
		$getQuery = $_GET;
		unset($getQuery['q']);
		if (count($getQuery) > 0) {
			$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			// print_r($params);die;

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_query_crumbs($params['params_filter'], $this->filter_model, $this->slices_model),
				$canonical 		= 	$canonical,
				$type = false,
				$fastFilters	=	$params['fast_filters']
			);
		} else {
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
			return $this->actionListing(
				$page 			=	1,
				$per_page		=	$this->per_page,
				$params_filter	= 	[],
				$breadcrumbs 	= 	[],
				$canonical 		= 	$canonical,
				$type = false,
				$fastFilters	=	$params['fast_filters']
			);
		}
	}

	public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false, $fastFilters = [], $itemTypeName = "", $slice_id = false)
	{
		$elastic_model = new ElasticItems;
		// $items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'restaurants', $elastic_model);
		$items = PremiumMixer::getItemsWithPremium($params_filter, $per_page, $page, false, 'restaurants', $elastic_model, false, false, false, false, false, true);
		$all_items = PremiumMixer::getItemsWithPremium($params_filter, 9999, 1, false, 'restaurants', $elastic_model, false, false, false, false, false, true);

		$filter = FilterWidget::widget([
			'filter_active' => $params_filter,
			'filter_model' => $this->filter_model
		]);

		$pagination = PaginationWidgetPrevNext::widget([
			'total' => $items->pages,
			'current' => $page,
		]);


		$seo_type = $type ? $type : 'listing';
		$seo = $this->getSeo($seo_type, $page, $items->total);
		$seo['breadcrumbs'] = $breadcrumbs;
		$this->setSeo($seo, $page, $canonical, $items->total, $params_filter);

		if ($seo_type == 'listing' and count($params_filter) > 0) {
			$seo['text_top'] = '';
			$seo['text_bottom'] = '';
		}

		// ===== вывод на срезах "Подборок ресторанов" START =====
		$collection_posts = '';
		if ($type) {
			$collection_posts = BlogPost::findWithMedia()
				->with('blogPostTags')
				->joinWith('blogPostSlices')
				->where(['published' => true])
				->andWhere(['collection' => true])
				->andWhere([BlogPostSlice::tableName() . '.slice_id' => $slice_id])
				->andWhere([BlogPostSlice::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
				->all();
		}
		// ===== вывод на срезах "Подборок ресторанов" END =====

		// ===== schemaOrg Product START =====
		$min_price = 99999;
		$max_price = 0;
		$review_count = 0;
		$total_rating = 0;
		$rest_with_rating = 0;
		$average_rating = 0;
		if ($type) {
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
		}
		// ===== schemaOrg Product END =====


		//  echo "<pre>";
		//  print_r($items->items);
		//  exit;

		$main_flag = ($seo_type == 'listing' and count($params_filter) == 0);
		return $this->render('index.twig', array(
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'count' => $items->total,
			'menu' => $type,
			'main_flag' => $main_flag,
			'fastFilters' => $fastFilters,
			'itemTypeName' => $itemTypeName,
			'collection_posts' => $collection_posts,
		));
	}

	public function actionAjaxFilter()
	{
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

		$elastic_model = new ElasticItems;
		// $items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'restaurants', $elastic_model,'','','','','','','', $check_sort = $params['sort']);
		$items = PremiumMixer::getItemsWithPremium($params['params_filter'], $this->per_page, $params['page'], false, 'restaurants', $elastic_model, false, false, false, false, false, true, $params['sort']);

		//		if (!empty($params['sort']))
		//            if ($params['sort'] == '-check')
		//                ArrayHelper::multisort($items->items, 'restaurant_min_check', SORT_DESC);
		//            else
		//                ArrayHelper::multisort($items->items, 'restaurant_min_check', SORT_ASC);


		$pagination = PaginationWidgetPrevNext::widget([
			'total' => $items->pages,
			'current' => $params['page'],
		]);

		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true), $this->slices_model);
		$seo_type = $slice_url ? $slice_url : 'listing';

		// ===== вывод на срезах "Подборок ресторанов" START =====
		$collection_posts = '';
		if ($slice_url) {
			$slice_obj = new QueryFromSlice($slice_url);
			$slice_id = $slice_obj->slice_model['id'];

			$collection_posts = BlogPost::findWithMedia()
				->with('blogPostTags')
				->joinWith('blogPostSlices')
				->where(['published' => true])
				->andWhere(['collection' => true])
				->andWhere([BlogPostSlice::tableName() . '.slice_id' => $slice_id])
				->andWhere([BlogPostSlice::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
				->all();
		}
		// ===== вывод на срезах "Подборок ресторанов" END =====

		$seo = $this->getSeo($seo_type, $params['page'], $items->total);

		$seo['breadcrumbs'] = [];
		if (!empty($params['params_filter'])) {
			$seo['breadcrumbs'] = Breadcrumbs::get_query_crumbs($params['params_filter'], $this->filter_model, $this->slices_model);
		}

		$title = $this->renderPartial('//components/generic/title.twig', array(
			'seo' => $seo,
			'count' => $items->total
		));

		if ($params['page'] == 1) {
			$text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_top']));
			$text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_bottom']));
		} else {
			$text_top = '';
			$text_bottom = '';
		}

		if ($seo_type == 'listing' and count($params['params_filter']) > 0) {
			$text_top = '';
			$text_bottom = '';
		}

		return  json_encode([
			'listing' => $this->renderPartial('//components/generic/listing.twig', array(
				'items' => $items->items,
				'img_alt' => $seo['img_alt'],
			)),
			'fast_filters' => $this->renderPartial('//components/generic/listing_tags.twig', array(
				'fastFilters' => $params['fast_filters'],
			)),
			'collection_posts' => $this->renderPartial('//components/generic/listing_collections.twig', array(
				'collection_posts' => $collection_posts,
			)),
			'pagination' => $pagination,
			'url' => $params['listing_url'],
			'title' => $title,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
			'seo_title' => $seo['title'],
		]);
	}

	public function actionAjaxFilterSlice()
	{
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
	}

	//	private function sortListing($items, $sort){
	//	    if ($sort == '-check'){
	//            foreach ($items->items as $item){
	//                ArrayHelper::multisort($item['rooms'], 'price', SORT_ASC);
	//                $item['check'] = $item['rooms'][0]['price'];
	//            }
	//            ArrayHelper::multisort($items->items , 'check', SORT_ASC);
	//        }else{
	//            foreach ($items->items as $item){
	//                ArrayHelper::multisort($item['rooms'], 'price', SORT_DESC);
	//                $item['check'] = $item['rooms'][0]['price'];
	//            }
	//            ArrayHelper::multisort($items->items , 'check', SORT_DESC);
	//        }
	//
	//	    return $items;
	//    }

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if (isset($getQuery['page'])) {
			$return['page'] = $getQuery['page'];
		} else {
			$return['page'] = 1;
		}

		if (isset($getQuery['sort'])) {
			$return['sort'] = $getQuery['sort'];
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['canonical'] = $temp_params->canonical;

		// echo ('<pre>');
		// print_r($temp_params->listing_url . Yii::$app->params['subdomen_id']);
		// exit;
		//получаем ссылки для блока тэгов
		$return['fast_filters'] = \Yii::$app->cache->getOrSet(
			$temp_params->listing_url . Yii::$app->params['subdomen_id'],
			function () use ($temp_params, $filter_model, $slices_model, $return) {
				//если единичный срез, берем тип его фильтра
				$filterName = $temp_params->slice_alias ? array_key_first($return['params_filter']) : 'any';
				if (empty($return['params_filter'])) $filterName = 'mesto'; //для /catalog/
				//получаем массив по названию этого фильтра
				$fastFilters = self::FAST_FILTERS[$filterName] ?? [];
				$collectedSlices = array_reduce($slices_model, function ($acc, $slice) use ($fastFilters, $filter_model, $temp_params) {
					if ($slice->alias == $temp_params->slice_alias) return $acc;
					$sliceFilterParams = $slice->getFilterParams();
					$temp_params = new ParamsFromQuery($sliceFilterParams, $this->filter_model, $this->slices_model);
					//если в срезе есть ресты
					if ($temp_params->query_hits) {
						//и если его основной тип фильтра есть в массиве $fastFilters
						$filterAlias = array_key_first($sliceFilterParams);
						if (!empty($fastFilters[$filterAlias])) {
							if ($sliceFilterItem = $slice->getFilterItem($filter_model)) {
								switch ($slice->alias) {
									case '1000-rub':
										$slice_name = 'Недорогие';
										break;
									case '3000-rub':
										$slice_name = 'Дорогие';
										break;
									default:
										$slice_name = str_replace('/', ' / ', $sliceFilterItem->text);
										break;
								}
								//добавляем его в результирующий массив к другим позициям этого же типа фильтра
								$acc[$filterAlias][] = [
									'name' => $slice_name,
									'alias' => $slice->alias,
									'count' => $temp_params->query_hits
								];
							}
						}
					}
					return $acc;
				}, array_fill_keys(array_keys($fastFilters), []));

				return array_reduce(array_keys($collectedSlices), function ($acc, $filterName) use ($collectedSlices, $fastFilters) {
					$slicesToAdd = $collectedSlices[$filterName];
					//если в результирующем массиве для типа фильтра больше позиций чем предопределено в $fastFilters,
					//то рандомим и обрезаем до нужного кол-ва
					if (count($slicesToAdd) > $fastFilters[$filterName]) {
						shuffle($slicesToAdd);
						$slicesToAdd = array_slice($slicesToAdd, 0, $fastFilters[$filterName]);
					}
					//выпрямляем массив
					return array_merge($acc, $slicesToAdd);
				}, []);
			},
			365 * 24 * 60 * 60
		);
		$return['fast_filters'] = array_map("unserialize", array_unique(array_map("serialize", $return['fast_filters'])));

		return $return;
	}

	private function getSeo($type, $page, $count = 0)
	{
		$seo = new Seo($type, $page, $count);

		return $seo->seo;
	}

	private function setSeo($seo, $page, $canonical, $count, $params_filter)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$isAnyFilterParamMultiple = count(array_filter($params_filter, function ($params) {
			return count($params) > 1;
		})) > 0;
		if ($page != 1 || $isAnyFilterParamMultiple || count($params_filter) > 1) {
			$this->view->registerLinkTag(['rel' => 'canonical', 'href' => $canonical], 'canonical');
		}
		if ($count < 1 || $isAnyFilterParamMultiple || count($params_filter) > 1) {
			$this->view->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow'], 'robots');
		}
		$this->view->params['kw'] = $seo['keywords'];
	}
}
