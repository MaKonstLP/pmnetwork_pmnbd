<?php

namespace frontend\modules\pmnbd\models;

use Yii;

/**
 * This is the model class for table "restaurants_redirect".
 */
class RestaurantRedirect extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'restaurants_redirect';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['redirect_from', 'redirect_to'], 'required'],
            [['redirect_from', 'redirect_to'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
