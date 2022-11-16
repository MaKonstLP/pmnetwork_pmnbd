<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use backend\modules\pmnbd\models\blog\BlogPost;
use backend\modules\pmnbd\models\blog\BlogTag;
use backend\modules\pmnbd\models\blog\BlogPostSubdomen;
use common\models\Seo;
use frontend\modules\pmnbd\components\Breadcrumbs;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;

class CollectionController extends BaseFrontendController
{
	protected $per_page = 12;
	public $filter_model,
		   $slices_model;

	public function actionIndex()
	{
		$subdomen = Yii::$app->params['subdomen_id'];

		$query = BlogPost::findWithMedia()
			->with('blogPostTags')
			->joinWith('blogPostSubdomens')
			->where(['published' => true,'collection' => true])
			->andWhere([BlogPostSubdomen::tableName() . '.subdomen_id' => $subdomen]);

		if (!$query->count() > 0) {
			throw new NotFoundHttpException();
		}
	
		$topPosts = (clone $query)->andWhere(['featured' => true])->limit(5)->all();
		$query = (clone $query)->andWhere(['NOT IN','id',$topPosts])->orderBy(['published_at'=>SORT_DESC]);

		// echo '<pre>';
		// print_r($query->count());
		// echo '</pre>';

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

		$seo = (new Seo('collection', $dataProvider->getPagination()->page + 1))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(1);
		$this->setSeo($seo);

		if ($query->count() > 0) {
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
		} else {
			$listConfig = false;
		}

		return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
	}

	public function actionPost($alias)
	{
		$subdomen = Yii::$app->params['subdomen_id'];

		$post = BlogPost::findWithMedia()
			->with('blogPostTags')
			->where(['published' => true, 'alias' => $alias])
			->andWhere(['collection' => true])
			->one();
		if (empty($post)) {
			throw new NotFoundHttpException();
		}

		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);

		$tag = $post->blogPostTags[0]->blogTag ?? BlogTag::find()->one();
        $similarPosts = BlogPost::findWithMedia()
			->with('blogPostTags')
			->joinWith('blogPostSubdomens')
			->where(['published' => true])
			->andWhere(['!=', 'id', $post->id])
			->andWhere(['collection' => true])
			->andWhere([BlogPostSubdomen::tableName() . '.subdomen_id' => $subdomen])
			->orderBy(['published_at' => SORT_DESC])
			->limit(3)
			->all();

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
        $similarPosts = $tag->getBlogPosts()->where(['published' => true])->andWhere(['!=', 'id', $post->id])->orderBy(['published_at' => SORT_DESC])->limit(6)->all();
		$preview = true;

		return $this->render('post.twig', compact('post', 'preview', 'similarPosts'));
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
}