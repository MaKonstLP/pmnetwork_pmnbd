<?php

namespace app\modules\pmnbd;

use Yii;
use common\models\Filter;
use common\models\Slices;
use common\models\Subdomen;
use frontend\components\ParamsFromQuery;
use frontend\components\QueryFromSlice;
use backend\modules\pmnbd\models\blog\BlogPost;
use backend\modules\pmnbd\models\blog\BlogPostSubdomen;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use backend\modules\pmnbd\models\Metros;

/**
 * svadbanaprirode module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\pmnbd\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $currentSubdomenAlias = explode('.', $_SERVER['HTTP_HOST'])[0];
        $siteName = explode(".", \Yii::$app->params['siteAddress'])[0];
        Yii::$app->params['domen'] = $_SERVER['HTTP_HOST'];
        $subdomens = Yii::$app->params['activeSubdomenRecords'] = Subdomen::find()
            ->where(['active' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();;

        if ($currentSubdomenAlias != $siteName) {
            $subdomen_model = current(array_filter($subdomens, function ($subdomen) use ($currentSubdomenAlias) {
                return $subdomen->alias == $currentSubdomenAlias;
            }));

            if (empty($subdomen_model) || $subdomen_model->city_id == 4400) {
                return $this->redirect(Yii::$app->params['siteProtocol'] . '://' . \Yii::$app->params['siteAddress'] . "$_SERVER[REQUEST_URI]");
            }

            Yii::$app->params['subdomen'] = $currentSubdomenAlias;
            Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
            Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
            Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
            Yii::$app->params['subdomen_name'] = $subdomen_model->name;
            Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
            Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;
            Yii::$app->params['subdomen_phone'] = $subdomen_model->phone;
            $subdomen_phone_pretty = null;
            if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{2})(\d{2})$/', $subdomen_model->phone,  $matches ) )
            {
                $subdomen_phone_pretty = '+7 ('.$matches[1].') '.$matches[2].'-'. $matches[3].'-'. $matches[4];
            }
            Yii::$app->params['subdomen_phone_pretty'] = $subdomen_phone_pretty;

            $subdomenCookie = \Yii::$app->request->cookies->get('subdomen');
            if (empty($subdomenCookie) || $subdomenCookie != $currentSubdomenAlias) {
                \Yii::$app->response->cookies->add(new Cookie([
                    'name' => 'subdomen',
                    'value' => $currentSubdomenAlias,
                    'expire' => time() + 86400 * 30,
                    'domain' => '.' . explode(":", \Yii::$app->params['siteAddress'])[0]
                ]));
            }
        } elseif (
            $currentSubdomenAlias != $siteName
            && $subdomen_cookie = \Yii::$app->request->cookies->get('subdomen')
        ) {
            return $this->redirect = Yii::$app->params['siteProtocol'] . '://' . $subdomen_cookie . \Yii::$app->params['siteAddress'] . "$_SERVER[REQUEST_URI]";
        } else {
            $subdomen_model = current(array_filter($subdomens, function ($subdomen) {
                return $subdomen->city_id == 4400;
            }));
            if (empty($subdomen_model)) {
                \Yii::$app->response->cookies->remove('subdomen');
                return $this->redirect(Yii::$app->params['siteProtocol'] . '://' . \Yii::$app->params['siteAddress'] . "$_SERVER[REQUEST_URI]");
            }
            Yii::$app->params['subdomen'] = $currentSubdomenAlias;
            Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
            Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
            Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
            Yii::$app->params['subdomen_name'] = $subdomen_model->name;
            Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
            Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;
            Yii::$app->params['subdomen_phone'] = $subdomen_model->phone;
            $subdomen_phone_pretty = null;
            if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{2})(\d{2})$/', $subdomen_model->phone,  $matches ) )
            {
                $subdomen_phone_pretty = '+7 ('.$matches[1].') '.$matches[2].'-'. $matches[3].'-'. $matches[4];
            }
            Yii::$app->params['subdomen_phone_pretty'] = $subdomen_phone_pretty;
        }

        $subdomenId = Yii::$app->params['subdomen_baseid'];
        Yii::$app->params['filter_model'] = Filter::find()
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
        Yii::$app->params['slices_model'] = Slices::find()->all();

		  $metro_stations = Metros::find()
			->joinWith('slicesMetro')
			->where(['city_id' => Yii::$app->params['subdomen_id']])
			->andWhere(['>', 'slices_metro_extra.restaurant_count', 0])
			->asArray()
			->all();

			$headerSlices = [
				'restoran' => ['name' => 'Рестораны', 'type' => 'kind','count' => 0],
				'kafe' => ['name' => 'Кафе', 'type' => 'kind','count' => 0],
				'loft' => ['name' => 'Лофты', 'type' => 'kind','count' => 0],
				'veranda' => ['name' => 'Веранды', 'type' => 'kind','count' => 0],
				// 'otel' => ['name' => 'Отели', 'type' => 'kind','count' => 0],
				'za-gorodom' => ['name' => 'За городом', 'type' => 'feature','count' => 0],
				'svoy-alko' => ['name' => 'Со своим алкоголем', 'type' => 'feature','count' => 0],
			];
			if (empty($metro_stations)) {
				$headerSlices['otel'] = ['name' => 'Отели', 'type' => 'kind','count' => 0];
			  } else {
				$headerSlices['stancii-metro'] = ['name' => 'Метро', 'type' => 'metro','count' => 999];
			  }
			foreach ($headerSlices as $alias => $sliceTexts) {
				if ($alias != 'stancii-metro') {
					$slice_obj = new QueryFromSlice($alias);
					$temp_params = new ParamsFromQuery($slice_obj->params, Yii::$app->params['filter_model'], Yii::$app->params['slices_model']);
					$headerSlices[$alias]['count'] = $temp_params->query_hits;
				}
			}
			Yii::$app->params['header_slices'] = array_filter($headerSlices, function ($slice) {
            return $slice['count'] > 0;
       	 });

        $footerSlices = [
            'restoran' => ['name' => 'Рестораны', 'type' => 'kind','count' => 0],
            'kafe' => ['name' => 'Кафе', 'type' => 'kind','count' => 0],
            'loft' => ['name' => 'Лофты', 'type' => 'kind','count' => 0],
            'veranda' => ['name' => 'Веранды', 'type' => 'kind','count' => 0],
            'otel' => ['name' => 'Отели', 'type' => 'kind','count' => 0],
            'za-gorodom' => ['name' => 'За городом', 'type' => 'feature','count' => 0],
            'svoy-alko' => ['name' => 'Со своим алкоголем', 'type' => 'feature','count' => 0],
            'shater' => ['name' => 'Шатры', 'type' => 'kind','count' => 0],
            'letnyaya-ploshadka' => ['name' => 'Летние площадки', 'type' => 'kind','count' => 0],
            'karaoke' => ['name' => 'Караоке', 'type' => 'feature','count' => 0],
            'scena' => ['name' => 'Сцена', 'type' => 'feature','count' => 0],
            'zhivaya-muzika' => ['name' => 'Живая музыка', 'type' => 'feature','count' => 0],
            '10-chelovek' => ['name' => '10 человек', 'type' => 'vmestimost','count' => 0],
            '20-25-chelovek' => ['name' => '20 человек', 'type' => 'vmestimost','count' => 0],
            '30-chelovek' => ['name' => '30 человек', 'type' => 'vmestimost','count' => 0],
            '50-chelovek' => ['name' => '50 человек', 'type' => 'vmestimost','count' => 0],
            '100-chelovek' => ['name' => '100 человек', 'type' => 'vmestimost','count' => 0],
        ];
		  
        foreach ($footerSlices as $alias => $sliceTexts) {
				$slice_obj = new QueryFromSlice($alias);
				$temp_params = new ParamsFromQuery($slice_obj->params, Yii::$app->params['filter_model'], Yii::$app->params['slices_model']);
				$footerSlices[$alias]['count'] = $temp_params->query_hits;
        }
        Yii::$app->params['footer_slices'] = array_filter($footerSlices, function ($slice) {
            return $slice['count'] > 0;
        });

		//   echo ('<pre>');
		//   print_r(Yii::$app->params['footer_slices']);
		//   exit;

        $noindex_global = false;
        foreach ($_GET as $key => $value) {
            if ($key != 'page' && $key != 'q') {
                $noindex_global = true;
            }
        }
        Yii::$app->params['noindex_global'] = $noindex_global;

        $collection = BlogPost::findWithMedia()
		->with('blogPostTags')
		->joinWith('blogPostSubdomens')
		->where(['published' => true,'collection' => true])
		->andWhere([BlogPostSubdomen::tableName() . '.subdomen_id' => Yii::$app->params['subdomen_id']])
		->count();
		$this->view->params['collectionCount'] = $collection;

    }

    private function redirect($redirect)
    {
        header("Location: $redirect");
        die();
    }
}
