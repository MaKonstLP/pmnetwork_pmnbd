<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\web\Controller;
use frontend\widgets\FilterWidget;
use frontend\widgets\PaginationWidgetPrevNext;
use frontend\components\ParamsFromQuery;
use frontend\components\QueryFromSlice;
use frontend\modules\pmnbd\components\Breadcrumbs;
use common\models\elastic\ItemsFilterElastic;
use common\models\Filter;
use common\models\Seo;
use common\models\Slices;
use frontend\modules\pmnbd\models\ElasticItems;
use yii\helpers\ArrayHelper;

class ListingController extends BaseFrontendController
{
	protected $per_page = 36;

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

	public function actionSlice($slice)
	{
		$slice_obj = new QueryFromSlice($slice);
		if ($slice_obj->flag) {
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, $this->filter_model, $this->slices_model);
			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2),
				$canonical 		= 	$canonical,
				$type 			=	$slice
			);
		} else {
			return $this->goHome();
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
				$canonical 		= 	$canonical
			);
		} else {
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			return $this->actionListing(
				$page 			=	1,
				$per_page		=	$this->per_page,
				$params_filter	= 	[],
				$breadcrumbs 	= 	[],
				$canonical 		= 	$canonical
			);
		}
	}

	public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false)
	{
		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'restaurants', $elastic_model);

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

		$main_flag = ($seo_type == 'listing' and count($params_filter) == 0);
		return $this->render('index.twig', array(
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'count' => $items->total,
			'menu' => $type,
			'main_flag' => $main_flag
		));
	}

	public function actionAjaxFilter()
	{
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'restaurants', $elastic_model);

		$pagination = PaginationWidgetPrevNext::widget([
			'total' => $items->pages,
			'current' => $params['page'],
		]);

		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true), $this->slices_model);
		$seo_type = $slice_url ? $slice_url : 'listing';

		$seo = $this->getSeo($seo_type, $params['page'], $items->total);
		
		$seo['breadcrumbs'] = [];
		if(!empty($params['params_filter'])) {
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
			'pagination' => $pagination,
			'url' => $params['listing_url'],
			'title' => $title,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
			'seo_title' => $seo['title']
		]);
	}

	public function actionAjaxFilterSlice()
	{
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
	}

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if (isset($getQuery['page'])) {
			$return['page'] = $getQuery['page'];
		} else {
			$return['page'] = 1;
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['canonical'] = $temp_params->canonical;

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
