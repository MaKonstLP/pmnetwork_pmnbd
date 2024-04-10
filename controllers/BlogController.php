<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use backend\modules\pmnbd\models\blog\BlogPost;
use backend\modules\pmnbd\models\blog\BlogTag;
// use common\models\blog\BlogPost;
// use common\models\blog\BlogTag;
use common\models\Seo;
use backend\modules\pmnbd\models\blog\BlogPostSubdomen;
use frontend\modules\pmnbd\components\Breadcrumbs;
use frontend\modules\pmnbd\models\ElasticItems;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;

class BlogController extends BaseFrontendController
{
	protected $per_page = 12;

	public $filter_model,
		$slices_model;


	public function actionIndex($page = '')
	{
		$query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true])->andWhere(['not', ['collection' => true]]);
		$topPosts = (clone $query)->where(['featured' => true])->andWhere(['not', ['collection' => true]])->limit(5)->all();
		$query = (clone $query)->andWhere(['NOT IN','id',$topPosts])->orderBy(['published_at'=>SORT_DESC]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 10,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
			'sort'=>[
                'defaultOrder'=>[
                     'published_at'=>SORT_DESC
                ],
            ],
		]);

		$dataProvider->getPagination()->page = !empty($page) ? $page - 1 : 0;

		$seo = (new Seo('blog', $dataProvider->getPagination()->page + 1))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(1);
		$this->setSeo($seo);
		
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
			'itemOptions'=> ['tag' => false]
		];
		
		return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
	}

	public function actionPost($alias)
	{
        $subdomen = Yii::$app->params['subdomen_id'];

		$post = BlogPost::findWithMedia()
			->with('blogPostTags')
			->where(['published' => true, 'alias' => $alias])
			->andWhere(['collection' => false])
			->one();
		if (empty($post)) {
			throw new NotFoundHttpException();
		}

		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);

//		$tag = $post->blogPostTags[0]->blogTag ?? BlogTag::find()->one();
        //$similarPosts = $tag->getBlogPosts()->where(['published' => true])->andWhere(['!=', 'id', $post->id])->orderBy(['published_at' => SORT_DESC])->limit(6)->all();
//        $similarPosts = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true])->andWhere(['!=', 'id', $post->id])->andWhere(['not', ['collection' => true]])->orderBy(['published_at' => SORT_DESC])->limit(3)->all();


        $tag = $post->blogPostTags[0]->blogTag ?? BlogTag::find()->one();
        $similarPostsPrev = BlogPost::findWithMedia()
            ->with('blogPostTags')
            ->where(['published' => true])
            ->andWhere(['!=', 'id', $post->id])
            ->andWhere(['<', 'published_at', $post->published_at])
            ->andWhere(['collection' => false])
            ->orderBy(['published_at' => SORT_DESC])
            ->limit(2)
            ->all();

        $similarPostsNext = BlogPost::findWithMedia()
            ->with('blogPostTags')
            ->where(['published' => true])
            ->andWhere(['!=', 'id', $post->id])
            ->andWhere(['>', 'published_at', $post->published_at])
            ->andWhere(['collection' => false])
            ->orderBy(['published_at' => SORT_ASC])
            ->limit(3)
            ->all();

        $similarPosts = array_merge($similarPostsPrev, $similarPostsNext);

		// ===== schemaOrg Product START =====
		$rest_gorko_ids = $post->getRestCardsId();
		if (!empty($rest_gorko_ids)) {
			$this->setSchema($rest_gorko_ids, $post, $seo);
		}
		// ===== schemaOrg Product END =====


		return $this->render('post.twig', compact('post', 'similarPosts'));
	}

	public function actionPreviewPost($id)
	{
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['id' => $id])->one();
		if (empty($post)) {
			throw new NotFoundHttpException();
		}

		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);

		$tag = $post->blogPostTags[0]->blogTag ?? BlogTag::find()->one();
        $similarPosts = $tag->getBlogPosts()->where(['published' => true])->andWhere(['!=', 'id', $post->id])->andWhere(['not', ['collection' => true]])->orderBy(['published_at' => SORT_DESC])->limit(3)->all();
        $similarCollections = $tag->getBlogPosts()->where(['published' => true])->andWhere(['!=', 'id', $post->id])->andWhere(['collection' => true])->orderBy(['published_at' => SORT_DESC])->limit(3)->all();
		$preview = true;

		if ($post['collection'] == true) {
			return $this->render('collection.twig', compact('post', 'preview', 'similarCollections'));
		} else {
			return $this->render('post.twig', compact('post', 'preview', 'similarPosts'));
		}
	}

	public function actionTag($alias)
	{
		$tag = BlogTag::findWithMedia()->with('blogPosts')->where(['alias' => $alias])->one();
		if (empty($tag)) {
			throw new NotFoundHttpException();
		}

		$seo = ArrayHelper::toArray($tag->seoObject);
		$this->setSeo($seo);

		return $this->render('tag.twig', compact('tag'));
	}

	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}

	private function setSchema($rest_gorko_ids, $post, $seo)
	{
		$restaurants = ElasticItems::find()->query([
			"bool" => [
				"must" => [
					["terms" => ["restaurant_gorko_id" => $rest_gorko_ids]]
				]
			]
		])->limit(100)->all();

		$min_price = 99999;
		$max_price = 0;
		$review_count = 0;
		$total_rating = 0;
		$best_rating = 0;
		$rest_with_rating = 0;
		$average_rating = 0;
		foreach ($restaurants as $item) {
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
				if ($best_rating < $item['restaurant_rev_ya']['rate']) {
					$best_rating = $item['restaurant_rev_ya']['rate'];
				}
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
			"name": "' . $post['name'] . '",
			"description": "' . $seo['description'] . '"';

		if ($max_price) {
			$json_str .= ',';
			$json_str .= '
			"offers": {
				"@type": "AggregateOffer",
				"offerCount": "' . count($restaurants) . '",
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
				"bestRating": "'.$best_rating.'",
				"reviewCount": "' . $review_count . '",
				"ratingValue": "' . $average_rating . '"
			}';
		}
		$json_str .= '}';

		Yii::$app->params['schema_product'] = $json_str;
	}
}
