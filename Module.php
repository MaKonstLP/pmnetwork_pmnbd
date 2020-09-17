<?php

namespace app\modules\pmnbd;

use common\models\Subdomen;
use Yii;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use common\models\elastic\ItemsFilterElastic;
use frontend\modules\pmnbd\models\ElasticItems;
use common\models\Filter;
use common\models\Slices;
use common\components\QueryFromSlice;
use common\components\ParamsFromQuery;

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

        $subdomen = explode('.', $_SERVER['HTTP_HOST'])[0];
        $siteName = explode(".", \Yii::$app->params['siteAddress'])[0];
        if ($subdomen != $siteName) {
            $subdomen_model = Subdomen::find()
                ->where(['alias' => $subdomen])
                ->one();

            if ($subdomen_model) {
                Yii::$app->params['subdomen'] = $subdomen;
                Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
                Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
                Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
                Yii::$app->params['subdomen_name'] = $subdomen_model->name;
                Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
                // Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;

                if (!\Yii::$app->request->cookies->get('subdomen')) {
                    \Yii::$app->response->cookies->add(new Cookie([
                        'name' => 'subdomen',
                        'value' => $subdomen,
                        'expire' => time() + 86400 * 30,
                        'domain' => '.' . explode(":", \Yii::$app->params['siteAddress'])[0]
                    ]));
                }
            } else {
                throw new NotFoundHttpException();
            }
        } elseif ($subdomen != $siteName && $subdomen_cookie = \Yii::$app->request->cookies->get('subdomen')) {
            $redirect = Yii::$app->params['siteProtocol'] . "$subdomen_cookie.$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            Yii::$app->response->redirect($redirect)->send();
        } else {
            $subdomen_model = Subdomen::find()
                ->where(['alias' => 'msk'])
                ->one();

            if ($subdomen_model) {
                Yii::$app->params['subdomen'] = $subdomen;
                Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
                Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
                Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
                Yii::$app->params['subdomen_name'] = $subdomen_model->name;
                Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
                // Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;
            } else {
                throw new NotFoundHttpException();
            }
        }
        Yii::$app->params['activeSubdomenRecords'] = Subdomen::find()
            ->where(['active' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();;

        $slices_model = Slices::find()->where(['like', 'params', '%mesto%', false])->all();

        foreach ($slices_model as $key => $slice) {
            $params = json_decode($slice->params, true);
            foreach ($params as $key => $value) {
                $params[$key] = [$value];
            }
            $items = new ItemsFilterElastic($params, 10000, 1, false, 'restaurants', new ElasticItems());
            if ($items->total > 0) {
                $filtersRestTypes = array_reduce(
                    $items->items,
                    function ($acc, $rest) {
                        foreach ($rest->restaurant_types as $type) {
                            if (!isset($acc[$type['name']])) {
                                $acc[$type['name']] = 1;
                            } else $acc[$type['name']] += 1;
                        }
                        return $acc;
                    },
                    []
                );

                foreach ($filtersRestTypes as $type_name => $type) {
                    if ($type == $items->total) Yii::$app->params['filtersRestTypes'][$slice->alias] = $type_name;
                }
            }
        }

        $slices_model = Slices::find()->where(['like', 'params', '%dopolnitelno%', false])->all();

        foreach ($slices_model as $key => $slice) {
            $params = json_decode($slice->params, true);
            foreach ($params as $key => $value) {
                $params[$key] = [$value];
            }
            $items = new ItemsFilterElastic($params, 10000, 1, false, 'restaurants', new ElasticItems());
            if ($items->total > 0) {
                Yii::$app->params['filtersRestDop'][$slice->alias] = $slice->h1;
            }
        }
    }
}
