<?php
namespace app\modules\pmnbd\controllers;

use yii\web\Controller;
use frontend\widgets\FilterWidget;
use frontend\widgets\PaginationWidgetPrevNext;
use frontend\components\ParamsFromQuery;
use frontend\components\Breadcrumbs;
use backend\models\Pages;
use common\models\elastic\ItemsFilterElastic;
use frontend\modules\pmnbd\models\ElasticItems;

class BlogController extends Controller
{
	protected $per_page = 12;

	public $filter_model,
		   $slices_model;


	public function actionIndex()
	{	
		$page =	(isset($_GET['pages']) ? $_GET['pages'] : 1);
		$seo = Pages::find()->where(['type' => 'blog'])->one();
		$this->setSeo($seo);
		$seo->breadcrumbs = Breadcrumbs::get_breadcrumbs($page);
		return $this->actionListing(
			$page,
			$per_page		=	$this->per_page,
			$params_filter	= 	[],
			$seo 			=	$seo
		);	
	}

	public function actionListing($page, $per_page, $params_filter, $seo)
	{
		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'restaurants', $elastic_model);
		$title_item = array_shift($items->items);

		$filter = FilterWidget::widget([
			'filter_active' => $params_filter,
			'filter_model' => $this->filter_model
		]);

		$pagination = PaginationWidgetPrevNext::widget([
			'total' => $items->pages,
			'current' => $page,
		]);

		return $this->render('index.twig', array(
			'title_item' => $title_item,
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'count' => $items->total
		));	
	}

	public function actionAjaxFilter(){
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

		$items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'rooms');

		$pagination = PaginationWidgetPrevNext::widget([
			'total' => $items->pages,
			'current' => $params['page'],
		]);

		substr($params['listing_url'], 0, 1) == '?' ? $breadcrumbs = Breadcrumbs::get_breadcrumbs(1) : $breadcrumbs = Breadcrumbs::get_breadcrumbs(2);
		$params['seo']['breadcrumbs'] = $breadcrumbs;

		$title = $this->renderPartial('//components/generic/title.twig', array(
			'seo' => $params['seo'],
			'count' => $items->total
		));

		$text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $params['seo']['text_top']));
		$text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $params['seo']['text_bottom']));		

		return  json_encode([
			'listing' => $this->renderPartial('//components/generic/listing.twig', array(
				'items' => $items->items,
				'img_alt' => $params['seo']['img_alt'],
			)),
			'pagination' => $pagination,
			'url' => $params['listing_url'],
			'title' => $title,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
		]);
	}

	public function actionAjaxFilterSlice(){
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
	}

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if(isset($getQuery['page'])){
			$return['page'] = $getQuery['page'];
		}
		else{
			$return['page'] = 1;
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);

		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['seo'] = $temp_params->seo;
		return $return;
	}

	private function setSeo($seo){
		$this->view->title = $seo['title'];
        $this->view->params['desc'] = $seo['description'];
        $this->view->params['kw'] = $seo['keywords'];
	}

}