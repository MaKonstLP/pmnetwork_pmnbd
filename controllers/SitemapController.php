<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use common\models\Slices;
use common\models\elastic\ItemsFilterElastic;
use common\models\Filter;
use frontend\components\ParamsFromQuery;
use frontend\components\QueryFromSlice;
use frontend\modules\pmnbd\models\ElasticItems;
use common\models\blog\BlogPost;

class SitemapController extends Controller
{

	public function actionIndex()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
		Yii::$app->response->headers->add('Content-Type', 'text/xml');

		$host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
		$subdomenId = Yii::$app->params['subdomen_baseid'];
		$filter_model = Filter::find()
			->with(['items' => function ($query) use ($subdomenId) {
				$query->leftJoin(
					'subdomen_filteritem',
					"subdomen_filteritem.filter_items_id = filter_items.id AND subdomen_filteritem.subdomen_id = $subdomenId"
				)
					->where("subdomen_filteritem.is_valid=0 OR (subdomen_filteritem.is_valid=1 AND subdomen_filteritem.hits>0)")
					->select('*');
			}])
			->where(['active' => 1])
			->orderBy(['sort' => SORT_ASC])
			->all();
		$slices_model = Slices::find('alias')->all();

		$slices = array_filter($slices_model, function ($slice) use ($filter_model, $slices_model) {
			$slice_obj = new QueryFromSlice($slice->alias);
			$temp_params = new ParamsFromQuery($slice_obj->params, $filter_model, $slices_model);
			return $temp_params->query_hits > 0;
		});

		$elastic_model = new ElasticItems;
		$rests = new ItemsFilterElastic([], 9999, 1, false, 'restaurants', $elastic_model);

		$blogPosts = [];
        if (\Yii::$app->params['subdomen_alias'] == 'msk') {
            $blogPosts = BlogPost::findWithMedia()
                ->where(['published' => 1])
                ->orderBy(['featured' => SORT_DESC, 'published_at' => SORT_DESC])->all();
        }

		return $this->renderPartial('sitemap.twig', [
			'isMain' => Yii::$app->params['subdomen_id'] == 4400,
			'host' => $host,
			'slices' => $slices,
			'rests' => $rests->items,
			'posts' => $blogPosts
		]);
	}
}
