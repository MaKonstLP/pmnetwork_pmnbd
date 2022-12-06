<?php

namespace app\modules\pmnbd\controllers;

use Yii;
use backend\modules\pmnbd\models\blog\BlogPost;
use backend\modules\pmnbd\models\blog\BlogTag;
use common\models\Seo;
use backend\modules\pmnbd\models\blog\BlogPostSubdomen;
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

		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();
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
}
