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
        $currentSubdomenAlias = explode('.', $_SERVER['HTTP_HOST'])[0];
        $siteName = explode(".", \Yii::$app->params['siteAddress'])[0];

        $subdomens = Yii::$app->params['activeSubdomenRecords'] = Subdomen::find()
            ->where(['active' => 1])
            ->orderBy(['name' => SORT_ASC])
            ->all();;

        if ($currentSubdomenAlias != $siteName) {
            $subdomen_model = current(array_filter($subdomens, function ($subdomen) use ($currentSubdomenAlias) {
                return $subdomen->alias == $currentSubdomenAlias;
            }));

            if (empty($subdomen_model)) {
                \Yii::$app->response->cookies->remove('subdomen');
                throw new NotFoundHttpException();
            }

            Yii::$app->params['subdomen'] = $currentSubdomenAlias;
            Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
            Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
            Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
            Yii::$app->params['subdomen_name'] = $subdomen_model->name;
            Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
            Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;

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
            $redirect = Yii::$app->params['siteProtocol'] . '://' . "$subdomen_cookie.$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            Yii::$app->response->redirect($redirect)->send();
        } else {
            $subdomen_model = current(array_filter($subdomens, function ($subdomen) use ($currentSubdomenAlias) {
                return $subdomen->alias == 'msk';
            }));
            if (empty($subdomen_model)) {
                \Yii::$app->response->cookies->remove('subdomen');
                throw new NotFoundHttpException();
            }
            Yii::$app->params['subdomen'] = $currentSubdomenAlias;
            Yii::$app->params['subdomen_id'] = $subdomen_model->city_id;
            Yii::$app->params['subdomen_alias'] = $subdomen_model->alias;
            Yii::$app->params['subdomen_baseid'] = $subdomen_model->id;
            Yii::$app->params['subdomen_name'] = $subdomen_model->name;
            Yii::$app->params['subdomen_dec'] = $subdomen_model->name_dec;
            Yii::$app->params['subdomen_rod'] = $subdomen_model->name_rod;
        }
    }
}
