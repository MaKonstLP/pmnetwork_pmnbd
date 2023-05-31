<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\web\Controller;
use common\models\Seo;
// use frontend\modules\banketnye_zaly_moskva\components\Breadcrumbs;
use frontend\modules\pmnbd\components\Breadcrumbs;
use backend\modules\pmnbd\models\Metros;
use frontend\widgets\PaginationWidgetPrevNext;
use yii\data\ActiveDataProvider;
use yii\widgets\LinkPager;
use yii\data\ArrayDataProvider;
use common\models\Pages;

class StanciiMetroController extends Controller
{
	protected $per_page = 36;

	public function actionIndex($page = '')
	{
		$metro_stations = Metros::find()
			->joinWith('slicesMetro')
			->where(['city_id' => Yii::$app->params['subdomen_id']])
			->andWhere(['>', 'slices_metro_extra.restaurant_count', 0])
			->orderBy(['name' => SORT_ASC])
			->asArray()
			->all();


		$new_arr_metro = [];
		foreach ($metro_stations as $station) {

			$page_model = Pages::findWithMedia()->where(['name' => $station['name']])->one();

			if (isset($page_model->mediaTargets[0]->siteObjectMedia) && !empty($page_model->mediaTargets[0]->siteObjectMedia)) {
				$url_image = $page_model->mediaTargets[0]->siteObjectMedia[0]->media['file'];
				$station['image'] = $url_image;
			}

			$new_arr_metro[] = $station;
		}


		$dataProvider = new ArrayDataProvider([
			// 'allModels' => $metro_stations,
			'allModels' => $new_arr_metro,
			'pagination' => [
				'pageSize' => $this->per_page,
				'forcePageParam' => false,
				'totalCount' => count($metro_stations)
			],
			// 'sort' => [
			// 	 'defaultOrder' => [
			// 		  'published_at' => SORT_DESC
			// 	 ],
			// ],
		]);

		$dataProvider->getPagination()->page = !empty($page) ? $page - 1 : 0;

		$listConfig = [
			'dataProvider' => $dataProvider,
			'itemView' => '_list-item.twig',
			'layout' => "{items}\n<div class='pagination_wrapper items_pagination' data-pagination-wrapper>{pager}</div>",
			'pager' => [
				'class' => LinkPager::class,
				'disableCurrentPageButton' => true,
				'nextPageLabel' => 'Следующая →',
				'prevPageLabel' => '← Предыдущая',
				'maxButtonCount' => 4,
				'activePageCssClass' => '_active',
				'pageCssClass' => 'items_pagination_item',
			],
			'itemOptions' => ['tag' => false]
		];


		$seo = $this->getSeo('stancii-metro', $dataProvider->getPagination()->page + 1, 0);
		$this->setSeo($seo);


		return $this->render('index.twig', array(
			'items' => $metro_stations,
			'seo' => $seo,
			'listConfig' => $listConfig,
		));
	}

	private function getSeo($type, $page, $count = 0)
	{
		$seo = new Seo($type, $page, $count);

		return $seo->seo;
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}
