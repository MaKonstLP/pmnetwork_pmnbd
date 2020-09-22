<?php

namespace app\modules\pmnbd\controllers;

use common\models\blog\BlogPost;
use common\models\blog\BlogTag;
use common\models\Seo;
use frontend\modules\pmnbd\components\Breadcrumbs;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;

class BlogController extends BaseFrontendController
{
	protected $per_page = 12;

	public $filter_model,
		$slices_model;


	public function actionIndex()
	{
		$query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 5,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
		]);
		$seo = (new Seo('blog', $dataProvider->getPagination()->page + 1))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(1);
		$this->setSeo($seo);

		$topPosts = (clone $query)->where(['featured' => true])->limit(5)->all();

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

		];
		return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
	}

	public function actionPost($alias)
	{
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();
		if(empty($post)) {
			return new NotFoundHttpException();
		}
		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);
		return $this->render('post.twig', compact('post'));
	}	

	public function actionTag($alias)
	{
		$tag = BlogTag::findWithMedia()->with('blogPosts')->where(['alias' => $alias])->one();
		if(empty($tag)) {
			return new NotFoundHttpException();
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
