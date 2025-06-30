<?php

namespace frontend\modules\pmnbd\models;

use Yii;
use common\models\Restaurants;
use common\models\RestaurantsModule;
use common\models\RoomsModule;
use common\models\RestaurantsTypes;
use common\models\RestaurantsSpecial;
use common\models\RestaurantsExtra;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\ImagesModule;
use common\components\AsyncRenewImages;
use frontend\modules\pmnbd\models\SubdomenFilteritem;
use common\models\Subdomen;
use common\models\FilterItems;
use common\models\Rooms;
use common\models\elastic\FilterQueryConstructorElastic;
use common\widgets\ProgressWidget;
use common\models\RestaurantsYandex;
use common\models\RestaurantsPremium;
use common\models\MetroStations;
use common\models\MetroLines;
use common\models\RoomsSpec;
use common\models\Slices;
use common\models\premium\PremiumRest;
use common\components\MetroUpdate;
use backend\modules\pmnbd\models\Metros;
use frontend\modules\pmnbd\models\RestaurantRedirect;

class ElasticItems extends \yii\elasticsearch\ActiveRecord
{

    const MAIN_REST_TYPE_ORDER = [3, 1];

    public function attributes()
    {
        return [
            'id',
            'restaurant_id',
            'restaurant_gorko_id',
            'restaurant_min_capacity',
            'restaurant_max_capacity',
            'restaurant_district',
            'restaurant_parent_district',
            'restaurant_city_id',
            'restaurant_alcohol',
            'restaurant_firework',
            'restaurant_name',
            'restaurant_slug',
            'restaurant_address',
            'restaurant_cover_url',
            'restaurant_latitude',
            'restaurant_longitude',
            'restaurant_own_alcohol',
            'restaurant_cuisine',
            'restaurant_parking',
            'restaurant_extra',
            'restaurant_extra_services',
            'restaurant_payment',
            'restaurant_special',
            'restaurant_special_array',
            'restaurant_specials',
            'restaurant_phone',
            'restaurant_images',
            'restaurant_commission',
            'restaurant_types',
            'restaurant_main_type',
            'restaurant_location',
            'restaurant_rating',
            'rooms',
            'restaurant_rev_ya',
            'restaurant_metro_stations',
            'restaurant_min_check',
            'restaurant_max_check',
            'restaurant_avg_check',
            'restaurant_premium',
			'restaurant_premium_features',
            'rent_room_only_min',
            'rest_banquet_price',
            'banquet_price_person_min',
			'premium_url',
        ];
    }

    public static function index()
    {
        return 'pmn_bd_restaurants';
//         return 'pmn_bd_dev_restaurants';
    }

    public static function type()
    {
        return 'items';
    }

    /**
     * @return array This model's mapping
     */
    public static function mapping()
    {
        return [
            static::type() => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'restaurant_id' => ['type' => 'integer'],
                    'restaurant_gorko_id' => ['type' => 'integer'],
                    'restaurant_min_capacity' => ['type' => 'integer'],
                    'restaurant_max_capacity' => ['type' => 'integer'],
                    'restaurant_district' => ['type' => 'integer'],
                    'restaurant_parent_district' => ['type' => 'integer'],
                    'restaurant_city_id' => ['type' => 'integer'],
                    'restaurant_alcohol' => ['type' => 'integer'],
                    'restaurant_firework' => ['type' => 'integer'],
                    'restaurant_rating' => ['type' => 'integer'],
                    'restaurant_name' => ['type' => 'text'],
                    'restaurant_slug' => ['type' => 'keyword'],
                    'restaurant_address' => ['type' => 'text'],
                    'restaurant_cover_url' => ['type' => 'text'],
                    'restaurant_latitude' => ['type' => 'text'],
                    'restaurant_longitude' => ['type' => 'text'],
                    'restaurant_own_alcohol' => ['type' => 'text'],
                    'restaurant_cuisine' => ['type' => 'text'],
                    'restaurant_parking' => ['type' => 'text'],
                    'restaurant_extra' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                    ]],
                    'restaurant_extra_services' => ['type' => 'text'],
                    'restaurant_payment' => ['type' => 'text'],
                    'restaurant_special' => ['type' => 'text'],
                    'restaurant_special_array' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                        'name' => ['type' => 'text'],
                        'link' => ['type' => 'text'],
                    ]],
                    'restaurant_phone' => ['type' => 'text'],
                    'restaurant_main_type' => ['type' => 'text'],
                    'restaurant_commission' => ['type' => 'integer'],
                    'restaurant_premium'            => ['type' => 'integer'],
                    'restaurant_premium_features'      => ['type' => 'nested', 'properties' => [
						'field'      => ['type' => 'text'],
						'value'      => ['type' => 'object','dynamic' => true],
					]],
                    'restaurant_types' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                        'name' => ['type' => 'text'],
                        'link' => ['type' => 'text'],
                    ]],
                    'restaurant_location' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                    ]],
                    'restaurant_specials' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                    ]],
                    'restaurant_images' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                        'sort' => ['type' => 'integer'],
                        'realpath' => ['type' => 'text'],
                        'subpath' => ['type' => 'text'],
                        'waterpath' => ['type' => 'text'],
                        'timestamp' => ['type' => 'text'],
                    ]],
                    'rooms' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'integer'],
                        'gorko_id' => ['type' => 'integer'],
                        'restaurant_id' => ['type' => 'integer'],
                        'price' => ['type' => 'integer'],
                        'capacity_reception' => ['type' => 'integer'],
                        'capacity' => ['type' => 'integer'],
                        'capacity_min' => ['type' => 'integer'],
                        'type' => ['type' => 'integer'],
                        'rent_only' => ['type' => 'integer'],
                        'banquet_price' => ['type' => 'integer'],
                        'bright_room' => ['type' => 'integer'],
                        'separate_entrance' => ['type' => 'integer'],
                        'type_name' => ['type' => 'text'],
                        'name' => ['type' => 'text'],
                        'slug' => ['type' => 'text'],
                        'restaurant_slug' => ['type' => 'text'],
                        'restaurant_name' => ['type' => 'text'],
                        'features' => ['type' => 'text'],
                        'cover_url' => ['type' => 'text'],
                        'payment_model' => ['type' => 'text'],
                        'payment_model_id' => ['type' => 'integer'],
                        'banquet_price_person' => ['type' => 'integer'],
                        'rent_room_only' => ['type' => 'integer'],
                        'banquet_price_min' => ['type' => 'integer'],
                        'images' => ['type' => 'nested', 'properties' => [
                            'id' => ['type' => 'integer'],
                            'sort' => ['type' => 'integer'],
                            'realpath' => ['type' => 'text'],
                            'subpath' => ['type' => 'text'],
                            'waterpath' => ['type' => 'text'],
                            'timestamp' => ['type' => 'text'],
                        ]],
                    ]],
                    'restaurant_rev_ya' => ['type' => 'nested', 'properties' => [
                        'id' => ['type' => 'long'],
                        'rate' => ['type' => 'text'],
                        'count' => ['type' => 'text'],
                    ]],
                    'restaurant_metro_stations'     => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                        'alias'                         => ['type' => 'text'],
                        'latitude'                      => ['type' => 'text'],
                        'longitude'                     => ['type' => 'text'],
                    ]],
                    'restaurant_min_check' => ['type' => 'integer'],
                    'restaurant_max_check' => ['type' => 'integer'],
                    'restaurant_avg_check' => ['type' => 'integer'],
                    'rent_room_only_min' => ['type' => 'integer'],
                    'banquet_price_person_min' => ['type' => 'integer'],
                    'rest_banquet_price' => ['type' => 'integer'],
                    'premium_url'                             	=> ['type' => 'text'],
                ]
            ],
        ];
    }

    /**
     * Set (update) mappings for this model
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    /**
     * Create this model's index
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            'settings' => [
                'number_of_replicas' => 0,
                'number_of_shards' => 1,
            ],
            'mappings' => static::mapping(),
            //'warmers' => [ /* ... */ ],
            //'aliases' => [ /* ... */ ],
            //'creation_date' => '...'
        ]);
    }

    /**
     * Delete this model's index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index(), static::type());
    }

    public static function refreshIndex($params)
    {
		$res = self::deleteIndex();
		$res = self::updateMapping();
		$res = self::createIndex();
        $res = self::updateIndex($params);
    }

    public static function refreshItem($params)
	{
		$item = self::findOne($params['gorko_id']);

		if($item){
			$item->delete();
		}

		$res = self::updateIndex($params);
	}

    public static function updateIndex($params)
    {
        $connectionMain = new \yii\db\Connection($params['main_connection_config']);
        $connectionMain->open();
        Yii::$app->set('db', $connectionMain);

        $connection_core = new \yii\db\Connection($params['main_connection_config']);
        $connection_core->open();
        Yii::$app->set('db', $connection_core);

        $restaurants = Restaurants::find()
            ->with('rooms')
            ->with('imagesext')
            ->with('subdomen')
            ->with('yandexReview')
            // ->where(['active' => 1, 'commission' => 2])
            // ->where(['gorko_id' => 499903])
            ->limit(100000);

        if (!empty($params['gorko_id'])) {
			$restaurants->andWhere(['gorko_id' => $params['gorko_id']]);
		}

		$restaurants = $restaurants->all();

        $subdomens = Subdomen::find()
			->select(['alias', 'city_id'])
			->indexBy('city_id')
			->column();

        $metroStations = MetroStations::find()->limit(100000)->asArray()->all($connectionMain);
        $metroLines = MetroLines::find()->limit(100000)->asArray()->all($connectionMain);



        $all_res = '';
        $restaurants_types = RestaurantsTypes::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_types = ArrayHelper::index($restaurants_types, 'value');

        $restaurants_specs = RestaurantsSpecial::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_specs = ArrayHelper::index($restaurants_specs, 'value');

        $restaurants_extra = RestaurantsExtra::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_extra = ArrayHelper::index($restaurants_extra, 'value');

        $connection = new \yii\db\Connection($params['premium_connection_config']);
        $connection->open();
        Yii::$app->set('db', $connection);

        $now = time();
		$channel_id = 4;
		$restaurants_premium_base = ArrayHelper::map(
			PremiumRest::find()
				->where(['wait' => 0, 'channel' => $channel_id, 'active' => 1])
				// ->andWhere(['>', 'finish', $now])
				->with('premiumFeature')
				->orderBy(['gorko_id'=>SORT_ASC])
				->limit(100000)
				->all(),
			'gorko_id', 
			function($model){ return $model->toArray([], ['premiumFeature.elasticValue']); }
		);

        $connection_sat = new \yii\db\Connection($params['site_connection_config']);
        $connection_sat->open();
        Yii::$app->set('db', $connection_sat);

        $images_module = ImagesModule::find()
            ->limit(500000)
            ->asArray()
            ->all();
        $images_module = ArrayHelper::index($images_module, 'gorko_id');

        $restaurants_module = RestaurantsModule::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_module = ArrayHelper::index($restaurants_module, 'gorko_id');

        $metroStations = ArrayHelper::index($metroStations, 'table_id');
        $metroLines = ArrayHelper::index($metroLines, 'id');

        $allLocations = [
            'metroStations' => $metroStations,
            'metroLines' => $metroLines
        ];

        $metros_with_same_station = Metros::find()
            ->where(['LIKE', 'same_station_table_id', ','])
            ->all();

        $rooms_spec_module = RoomsSpec::find()
            ->limit(200000)
            ->asArray()
            ->all();

        $slices = Slices::find()->all();

        $connectionSite = new \yii\db\Connection($params['site_connection_config']);
        $connectionSite->open();
        Yii::$app->set('db', $connectionSite);

        //print_r($images_module[21256309]);
        //exit;

		$rest_count = count($restaurants);
		$rest_iter = 0;
		foreach ($restaurants as $restaurant) {
			$res = self::addRecord($restaurant, $restaurants_types, $restaurants_specs, $restaurants_extra, $images_module, $restaurants_module, $params, $allLocations, $connectionMain, $connectionSite, $subdomens, $metros_with_same_station, $rooms_spec_module, $restaurants_premium_base, $slices);
			$all_res .= $res . ' | ';
			echo ProgressWidget::widget(['done' => $rest_iter++, 'total' => $rest_count]);
		}

        self::subdomenCheck($connection_core);

        echo 'Обновление индекса ' . self::index() . ' ' . self::type() . ' завершено<br>';
        return true;
    }

    public static function getTransliterationForUrl($name)
    {
        $latin = array('-', "Sch", "sch", 'Yo', 'Zh', 'Kh', 'Ts', 'Ch', 'Sh', 'Yu', 'ya', 'yo', 'zh', 'kh', 'ts', 'ch', 'sh', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', '', 'Y', '', 'E', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', '', 'y', '', 'e');
        $cyrillic = array(' ', "Щ", "щ", 'Ё', 'Ж', 'Х', 'Ц', 'Ч', 'Ш', 'Ю', 'я', 'ё', 'ж', 'х', 'ц', 'ч', 'ш', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Ь', 'Ы', 'Ъ', 'Э', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'ь', 'ы', 'ъ', 'э');
        return trim(
            preg_replace(
                "/(.)\\1+/",
                "$1",
                strtolower(
                    preg_replace(
                        "/[^a-zA-Z0-9-]/",
                        '',
                        str_replace($cyrillic, $latin, $name)
                    )
                )
            ),
            '-'
        );
    }

    public static function addRecord($restaurant, $restaurants_types, $restaurants_specs, $restaurants_extra, $images_module, $restaurants_module, $params, $allLocations, $connectionMain, $connectionSite, $subdomens, $metros_with_same_station, $rooms_spec_module, $restaurants_premium_base, $slices)
    {
        $isExist = false;

        if ($restaurant->gorko_id === 484179) {//рест скрыт по просьбе Жени - https://liderpoiska.planfix.ru/task/133229/
            return true;
        }

        if ($restaurant->gorko_id === 474269) {//рест Кафе «Меама» в Москве
            return true;
        }


        //   $restaurant_spec_white_list = [9];
        //   $restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);
        //   if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
        //       return 'Неподходящий тип мероприятия';
        //   }

        //   if (!$restaurant->commission) {
        //       return 'Не платный';
        //   }

        $premium = isset($restaurants_premium_base[$restaurant->gorko_id]);

        if($restaurant->premium_active == 1 && !isset($restaurants_premium_base[$restaurant->gorko_id])){
            return true;
        }

        if (!$premium) {
            $restaurant_spec_white_list = [9];
            $restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);
            if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
                return 'Неподходящий тип мероприятия';
            }

            if (!$restaurant->active) {
                return 'Не активен';
            }

            if (!$restaurant->commission) {
                return 'Не платный';
            }
        }

        try {
            $record = self::get($restaurant->gorko_id);
            if (!$record) {
                $record = new self();
                $record->setPrimaryKey($restaurant->gorko_id);
            } else {
                $isExist = true;
            }
        } catch (\Exception $e) {
            $record = new self();
            $record->setPrimaryKey($restaurant->gorko_id);
        }


        //Св-ва ресторана
        $record->id = $restaurant->id;
        $record->restaurant_commission = $restaurant->commission;
        $record->restaurant_id = $restaurant->id;
        $record->restaurant_gorko_id = $restaurant->gorko_id;
        $record->restaurant_min_capacity = $restaurant->min_capacity;
        $record->restaurant_max_capacity = $restaurant->max_capacity;
        $record->restaurant_district = $restaurant->district;
        $record->restaurant_parent_district = $restaurant->parent_district;
        $record->restaurant_city_id = $restaurant->city_id;
        $record->restaurant_alcohol = $restaurant->alcohol;
        $record->restaurant_firework = $restaurant->firework;
        $record->restaurant_name = $restaurant->name;
        $record->restaurant_address = $restaurant->address;
        $record->restaurant_cover_url = $restaurant->cover_url;
        $record->restaurant_latitude = $restaurant->latitude;
        $record->restaurant_longitude = $restaurant->longitude;
        $record->restaurant_own_alcohol = $restaurant->own_alcohol;
        $record->restaurant_cuisine = $restaurant->cuisine;
        $record->restaurant_parking = $restaurant->parking;
        $record->restaurant_extra_services = $restaurant->extra_services;
        $record->restaurant_payment = $restaurant->payment;
        $record->restaurant_special = $restaurant->special;



        //Особенности реста
        //'value в restaurants_specials' => 'значение params в slices'
        $specs_arr = [
            '49' => '12', //Караоке
            '53' => '5', //Велком зона
            '8' => '6', //Вай фай
            '9' => '4', //Сцена
            '10' => '7', //Проектор
            '11' => '10', //ТВ экран
            '22' => '9', //Детское меню
        ];
        $restaurant_specs = [];
        $restaurant_specs_rest = explode(',', $restaurant->special_ids);
        foreach ($restaurant_specs_rest as $key => $value) {
            $restaurant_specs_arr = [];
            $restaurant_specs_arr['id'] = $value;
            $restaurant_specs_arr['name'] = isset($restaurants_specs[$value]['text']) ? $restaurants_specs[$value]['text'] : '';

            foreach ($slices as $slice) {
                $slice_params = json_decode($slice['params'], true);
                $rest_spec_id = isset($specs_arr[$value]) ? $specs_arr[$value] : '';

                if (count($slice_params) == 1 && isset($slice_params['dopolnitelno']) && $slice_params['dopolnitelno'] == $rest_spec_id) {
                    $restaurant_specs_arr['link'] = $slice['alias'];
                }
            }

            array_push($restaurant_specs, $restaurant_specs_arr);
        }
        $record->restaurant_special_array = $restaurant_specs;




        switch ($restaurant->gorko_id) {
            case 483343:
                $record->restaurant_phone = '+7 963 716-59-17';
                break;
            case 441099:
                $record->restaurant_phone = '+7 930 036-84-71';
                break;
            case 466745:
                $record->restaurant_phone = '8 (8452) 491549';
                break;
            case 488891:
                $record->restaurant_phone = '+7 980 098-47-55';
                break;
            case 474269:
                $record->restaurant_phone = '+7 963 627-16-41';
                break;
            default:
                $record->restaurant_phone = $restaurant->phone;
                break;
        }

        $premium_restaurant_price = 0;
		$rooms_name = [];
		$rooms_hide = [];
		$rooms_price = [];

        $record->restaurant_premium = 0;
        if ($premium){
            $record->restaurant_premium = 1;
        }

        if(!empty($restaurants_premium_base[$restaurant->gorko_id]['premiumFeature'])){
			$premium_features = [];
			foreach ($restaurants_premium_base[$restaurant->gorko_id]['premiumFeature'] as $premium_feature) {
				$premium_features[$premium_feature['feature_id']] = [
					'field' => $premium_feature['feature_id'],
					'value' => $premium_feature['elasticValue'],
				];
			}
			$record->restaurant_premium_features = $premium_features;

			/** begin - применение свойств из премиум базы */
			if(!empty($premium_features[15]['value'])) $rooms_hide = $premium_features[15]['value']; // скрытие зала
			if(!empty($premium_features[14]['value'])) $rooms_name = $premium_features[14]['value']; // название зала
			if(!empty($premium_features[18]['value'])) $premium_restaurant_price = $premium_features[18]['value'];
			if(!empty($premium_features[19]['value'])) $rooms_price = $premium_features[19]['value'];
			/** end */
		}

        if(!empty($restaurants_premium_base[$restaurant->gorko_id]['proxy_phone']) ){
            $proxy_phone = $restaurants_premium_base[$restaurant->gorko_id]['proxy_phone'];
            if(  preg_match( '/^\+\d(\d{3})(\d{3})(\d{2})(\d{2})$/', $proxy_phone,  $matches ) )
            {
                $proxy_phone_pretty = '+7 ('.$matches[1].') '.$matches[2].'-'. $matches[3].'-'. $matches[4];
            }
            $record->restaurant_phone = $proxy_phone_pretty;
        }

        $restaurant->rating ? $record->restaurant_rating = $restaurant->rating : $record->restaurant_rating = 90;


        // Добавление ближайших станций метро
        $finalStationString = '';

        if ($restaurant->metro_station_id === '' || $restaurant->metro_station_id === NULL) {
            $log = 'Новый ресторан ' . $restaurant->name . ' с id=' . $restaurant->id;
            file_put_contents('/var/www/pmnetwork/log/elasticMetroLog.txt', $log . PHP_EOL, FILE_APPEND);

            Yii::$app->set('db', $connectionMain);
            $finalStationString = MetroUpdate::updateRestaurantClosestMetroStation($restaurant->id, $params);
            Yii::$app->set('db', $connectionSite);

            $metroStations = MetroStations::find()->limit(100000)->asArray()->all($connectionMain);
            $metroStations = ArrayHelper::index($metroStations, 'table_id');
            $allLocations['metroStations'] = $metroStations;
        }

        if ($restaurant->metro_station_id !== '0' && $finalStationString !== '0') {
            $metroStations = [];
            $restaurantStationList = null;

            if ($finalStationString !== '') {
                $restaurantStationList = explode(',', $finalStationString);
            } else {
                $restaurantStationList = explode(',', $restaurant->metro_station_id);
            }

            $log = serialize($restaurantStationList);
            file_put_contents('/var/www/pmnetwork/log/elasticMetroLog.txt', $log . PHP_EOL, FILE_APPEND);

            foreach ($restaurantStationList as $currentStationId) {
                $metroStation = [];
                $metroStation['id'] = (int) $allLocations['metroStations'][$currentStationId]['table_id'];
                $metroStation['name'] = $allLocations['metroStations'][$currentStationId]['name'];
                $metroStation['link'] = $allLocations['metroStations'][$currentStationId]['alias'];
                $metroStation['latitude'] = $allLocations['metroStations'][$currentStationId]['latitude'];
                $metroStation['longitude'] = $allLocations['metroStations'][$currentStationId]['longitude'];

                //проверка, что нет станция метро с одинаковым названием
                $double_station = false;
                foreach ($metros_with_same_station as $metro_with_same_station) {
                    $same_table_ids = explode(',', $metro_with_same_station['same_station_table_id']);
                    if (in_array($metroStation['id'], $same_table_ids)) {
                        $metroStation['id'] = $same_table_ids[0];

                        foreach ($metroStations as $metro_station) {
                            if ($metro_station['id'] == $metroStation['id']) {
                                $double_station = true;
                            }
                        }
                    }
                }

                // array_push($metroStations, $metroStation);
                if (!$double_station) {
                    array_push($metroStations, $metroStation);
                }
            }
            $record->restaurant_metro_stations = $metroStations;

            $log = serialize($record->restaurant_metro_stations);
            file_put_contents('/var/www/pmnetwork/log/elasticMetroLog.txt', $log . PHP_EOL, FILE_APPEND);
        }




        //Отзывы с Яндекса из общей базы
        $reviews = [];
        if (isset($restaurant->yandexReview)) {
            $reviews['id'] = $restaurant->yandexReview['rev_ya_id'];
            $reviews['rate'] = $restaurant->yandexReview['rev_ya_rate'];
            $reviews['count'] = $restaurant->yandexReview['rev_ya_count'];
        }
        $record->restaurant_rev_ya = $reviews;

        //Картинки ресторана
        $images = [];

        $group = array();
        foreach ($restaurant->imagesext as $value) {
            $group[$value['room_id']][] = $value;
        }
        $images_sorted = array();
        $room_ids = array();
        foreach ($group as $room_id => $images_ext) {
            $room_ids[] = $room_id;
            foreach ($images_ext as $image) {
                $images_sorted[$room_id][$image['event_id']][] = $image;
                ArrayHelper::multisort($images_sorted[$room_id][$image['event_id']], ['sort'], [SORT_ASC]);
            }
        }
        $specs = [9, 0, 1];
        $image_flag = false;
        foreach ($specs as $spec) {
            for ($i = 0; $i < 20; $i++) {
                foreach ($room_ids as $room_id) {
                    if (isset($images_sorted[$room_id]) && isset($images_sorted[$room_id][$spec]) && isset($images_sorted[$room_id][$spec][$i])) {
                        $image = $images_sorted[$room_id][$spec][$i];
                        $image_arr = [];
                        $image_arr['id'] = $image['gorko_id'];
                        $image_arr['sort'] = $image['sort'];
                        $search = ['lh3.googleusercontent.com', 'nocdn.gorko.ru'];
                        // $image_arr['realpath'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $image['path']);
                        $image_arr['realpath'] = str_replace($search, 'img.birthday-place.ru', $image['path']);
                        if (isset($images_module[$image['gorko_id']])) {
                            //  $image_arr['subpath'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $images_module[$image['gorko_id']]['subpath']);
                            //  $image_arr['waterpath'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $images_module[$image['gorko_id']]['waterpath']);
                            //  $image_arr['timestamp'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $images_module[$image['gorko_id']]['timestamp']);
                            $image_arr['subpath'] = str_replace($search, 'img.birthday-place.ru', $images_module[$image['gorko_id']]['subpath']);
                            $image_arr['waterpath'] = str_replace($search, 'img.birthday-place.ru', $images_module[$image['gorko_id']]['waterpath']);
                            $image_arr['timestamp'] = str_replace($search, 'img.birthday-place.ru', $images_module[$image['gorko_id']]['timestamp']);
                        } else {
                            $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                                'gorko_id' => $image['gorko_id'],
                                'params' => $params,
                                'rest_flag' => true,
                                'rest_gorko_id' => $restaurant->gorko_id,
                                'room_gorko_id' => false,
                                'elastic_index' => static::index(),
                                'elastic_type' => 'rest',
                            ]));
                        }
                        array_push($images, $image_arr);
                    }
                    if (count($images) > 19) {
                        $image_flag = true;
                        break;
                    }
                }
                if ($image_flag) break;
            }
            if ($image_flag) break;
        }
        $record->restaurant_images = $images;

        //Тип помещения
        $type_arr = [
            '1' => '1', //Ресторан
            '30' => '8', //Веранда
            '14' => '11', //Шатер
            '17' => '10', //Летняя площадка
            '15' => '9', //Коттедж
            '3' => '2', //Кафе
            '4' => '7', //Бар
            '36' => '4', //Лофт
            '25' => '3', //Отель
            '13' => '5', //База отдыха
            '32' => '12', //Детские
        ];
        $restaurant_types = [];
        $restaurant_types_rest = explode(',', $restaurant->type);
        foreach ($restaurant_types_rest as $key => $value) {
            $restaurant_types_arr = [];
            $restaurant_types_arr['id'] = $value;
            $restaurant_types_arr['name'] = isset($restaurants_types[$value]['text']) ? $restaurants_types[$value]['text'] : '';

            foreach ($slices as $slice) {
                $slice_params = json_decode($slice['params'], true);
                $rest_type_id = isset($type_arr[$value]) ? $type_arr[$value] : '';

                if (count($slice_params) == 1 && isset($slice_params['mesto']) && $slice_params['mesto'] == $rest_type_id) {
                    $restaurant_types_arr['link'] = $slice['alias'];
                }
            }

            array_push($restaurant_types, $restaurant_types_arr);
        }
        $record->restaurant_types = $restaurant_types;

        $restMainTypeIdsOrder = array_combine(self::MAIN_REST_TYPE_ORDER, self::MAIN_REST_TYPE_ORDER);

        foreach ($record->restaurant_types as $key => $type) {
            if (in_array($type['id'], $restMainTypeIdsOrder)) {
                $restMainTypeIdsOrder[intval($type['id'])] = $type;
            } else {
                $restMainTypeIdsOrder[] = $type;
            }
        }

        $record->restaurant_main_type = array_reduce($restMainTypeIdsOrder, function ($acc, $type) {
            return (empty($acc) && isset($type['name']) ? $type['name'] : $acc);
        }, '') ?: 'Ресторан';

        //Тип локации
        $restaurant_location = [];
        $restaurant_location_rest = explode(',', $restaurant->location);
        foreach ($restaurant_location_rest as $key => $value) {
            $restaurant_location_arr = [];
            $restaurant_location_arr['id'] = $value;
            array_push($restaurant_location, $restaurant_location_arr);
        }
        $record->restaurant_location = $restaurant_location;

        //Specials
        $restaurant_specials = [];
        $restaurant_specials_rest = explode(',', $restaurant->special_ids);
        foreach ($restaurant_specials_rest as $key => $value) {
            $restaurant_specials_arr = [];
            $restaurant_specials_arr['id'] = $value;
            array_push($restaurant_specials, $restaurant_specials_arr);
        }
        $record->restaurant_specials = $restaurant_specials;

        //Extra
        //'value в restaurants_extra' => 'значение params в slices'
        $extra_arr = [
            '6' => '8', //Живая музыка
            '7' => '11', //Ведущий
        ];
        $restaurant_extra = [];
        $restaurant_extra_rest = explode(',', $restaurant->extra_services_ids);
        foreach ($restaurant_extra_rest as $key => $value) {
            $restaurant_extra_arr = [];
            $restaurant_extra_arr['id'] = $value;
            $restaurant_extra_arr['name'] = isset($restaurants_extra[$value]['text']) ? $restaurants_extra[$value]['text'] : '';

            foreach ($slices as $slice) {
                $slice_params = json_decode($slice['params'], true);
                $rest_extra_id = isset($extra_arr[$value]) ? $extra_arr[$value] : '';

                if (count($slice_params) == 1 && isset($slice_params['dopolnitelno']) && $slice_params['dopolnitelno'] == $rest_extra_id) {
                    $restaurant_extra_arr['link'] = $slice['alias'];
                }
            }

            array_push($restaurant_extra, $restaurant_extra_arr);
        }
        $record->restaurant_extra = $restaurant_extra;


        /* if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
            $record->restaurant_slug = $row['slug'];
        } else {
            $record->restaurant_slug = self::getTransliterationForUrl($restaurant->name);
            \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' => $record->restaurant_slug])->execute();
        } */
        //меняем урлы и добавляем редиректы со старых урлов.
        //Если у реста основной тип не "Ресторан" или "Кафе", то в слаг записываем имя реста, иначе добавляем к имени префикс: "restoran-"
        if ($record->restaurant_main_type != 'Ресторан' && $record->restaurant_main_type != 'Кафе') {
            if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
                $record->restaurant_slug = $row['slug'];
            } else {
                $record->restaurant_slug = self::getTransliterationForUrl($restaurant->name);
                \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' => $record->restaurant_slug])->execute();
            }
        } else {
            if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
                $record->restaurant_slug = $row['slug'];
            } else {
                $record->restaurant_slug = 'restoran-' . self::getTransliterationForUrl($restaurant->name);
                \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' => $record->restaurant_slug])->execute();
            }
        }

        if(isset($subdomens[$restaurant->city_id])) {
			$record->premium_url = sprintf(
				'https://%s%s/catalog/%s/', 
				($subdomens[$restaurant->city_id] == '' ? '' : $subdomens[$restaurant->city_id] . '.'), 
				'birthday-place.ru',
				$record->restaurant_slug
			);
		}


        //Св-ва залов
        $rooms = [];
        $min_price = 1000000;
        $max_price = 0;
        $sum_price = 0;
        $count_room = 0;
        $banquet_price_person_min = 1000000;
        $rent_room_only_min = 1000000;
        $banquet_price_min = 1000000;

        foreach ($restaurant->rooms as $idx => $room) {

            $room_spec_white_list = 9; //день рождения
            $not_suitable_room = true;

            foreach ($rooms_spec_module as $rooms_spec) {
                if ($room['gorko_id'] == $rooms_spec['gorko_id'] && $room_spec_white_list == $rooms_spec['spec_id']) {
                    $not_suitable_room = false;
                    break;
                }
            }

            if ($not_suitable_room) {
                continue;
            }

            if(is_array($rooms_hide) && !empty($rooms_hide) && in_array($room->gorko_id, $rooms_hide)){
				continue;
			}

            $name = $room->name;
			if(!empty($rooms_name[$room->gorko_id])){
				$name = $rooms_name[$room->gorko_id];
			}

            $room_payment_model = $room->payment_model;
			$room_price = $room->price;
			$room_banquet_price = $room->banquet_price;
			$room_banquet_price_person = $room->banquet_price_person;
			$room_banquet_price_min = $room->banquet_price_min;
			$room_rent_only = $room->rent_only;
			$room_rent_room_only = $room->rent_room_only;
			if(!empty($rooms_price[$room->gorko_id])){
				$rooms_price_item = $rooms_price[$room->gorko_id];

				if(isset($rooms_price_item['payment_model']) and $rooms_price_item['payment_model'] !== '') $room_payment_model = $rooms_price_item['payment_model'];
				if(isset($rooms_price_item['price']) and $rooms_price_item['price'] !== '') $room_price = $rooms_price_item['price'];
				if(isset($rooms_price_item['banquet_price']) and $rooms_price_item['banquet_price'] !== '') $room_banquet_price = $rooms_price_item['banquet_price'];
				if(isset($rooms_price_item['banquet_price_person']) and $rooms_price_item['banquet_price_person'] !== '') $room_banquet_price_person = $rooms_price_item['banquet_price_person'];
				if(isset($rooms_price_item['banquet_price_min']) and $rooms_price_item['banquet_price_min'] !== '') $room_banquet_price_min = $rooms_price_item['banquet_price_min'];
				if(isset($rooms_price_item['rent_only']) and $rooms_price_item['rent_only'] !== '') $room_rent_only = $rooms_price_item['rent_only'];
				if(isset($rooms_price_item['rent_room_only']) and $rooms_price_item['rent_room_only'] !== '') $room_rent_room_only = $rooms_price_item['rent_room_only'];
			}

            $room_arr = [];

            if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $room->gorko_id])->one()) {
                $room_arr['slug'] = $row['slug'];
            } else {
                $slug = self::getTransliterationForUrl($room->name);
                $isSameSlug = count(array_filter($rooms, function ($prevRoom) use ($slug) {
                        return $prevRoom['slug'] == $slug;
                    })) > 0;
                $slugPostFix = $isSameSlug ? "-$idx" : "";
                $slug .= $slugPostFix;
                $room_arr['slug'] = $slug;
                \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $room->gorko_id, 'slug' => $room_arr['slug']])->execute();
            }

            // dj не должен попадать в залы
            if ($room->id == '33324') continue;

            $room_arr['id'] = $room->id;
            $room_arr['gorko_id'] = $room->gorko_id;
            $room_arr['restaurant_id'] = $room->restaurant_id;
            $room_arr['price'] = $room_price;
            if($room->restaurant_id == 474269)
                

            $room_arr['capacity_reception'] = $room->capacity_reception;
            $room_arr['capacity'] = $room->capacity;
            $room_arr['capacity_min'] = $room->capacity_min;
            $room_arr['type'] = $room->type;
            $room_arr['rent_only'] = $room->rent_only;
            $room_arr['banquet_price'] = $room_banquet_price;
            $room_arr['bright_room'] = $room->bright_room;
            $room_arr['separate_entrance'] = $room->separate_entrance;
            $room_arr['type_name'] = $room->type_name;
            $room_arr['name'] = $name;
            $room_arr['restaurant_slug'] = $record->restaurant_slug;
            $room_arr['restaurant_name'] = $restaurant->name;
            $room_arr['restaurant_main_type'] = $record->restaurant_main_type;
            $room_arr['features'] = $room->features;
            $room_arr['cover_url'] = $room->cover_url;
            $room_arr['banquet_price_person'] = $room_banquet_price_person;
            $room_arr['rent_room_only'] = $room_rent_room_only;
            $room_arr['banquet_price_min'] = $room_banquet_price_min;
            $room_arr['payment_model_id'] = $room_payment_model;
            switch ($room_payment_model) {
                case 1:
                    $room_arr['payment_model'] = 'Только за еду и напитки';
                    break;
                case 2:
                    $room_arr['payment_model'] = 'За аренду зала + за еду и напитки';
                    break;
                case 3:
                    $room_arr['payment_model'] = 'Только аренда (без еды)';
                    break;
                default:
                    $room_arr['payment_model'] = '';
                    break;
            }

            if($room->restaurant_id == 474269){
                $room_arr['price'] = 4500;
                $room_arr['banquet_price_person'] = 4500;
            }

            //Картинки залов
            $images = [];
            $image_flag = false;
            foreach ($specs as $spec) {
                for ($i = 0; $i < 20; $i++) {
                    if (isset($images_sorted[$room->gorko_id]) && isset($images_sorted[$room->gorko_id][$spec]) && isset($images_sorted[$room->gorko_id][$spec][$i])) {
                        $image = $images_sorted[$room->gorko_id][$spec][$i];
                        $image_arr = [];
                        $image_arr['id'] = $image['gorko_id'];
                        $image_arr['sort'] = $image['sort'];
                        $search = ['lh3.googleusercontent.com', 'nocdn.gorko.ru'];
                        // $image_arr['realpath'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $image['path']);
                        $image_arr['realpath'] = str_replace($search, 'img.birthday-place.ru', $image['path']);
                        if (isset($images_module[$image['gorko_id']])) {
                            //  $image_arr['subpath'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $images_module[$image['gorko_id']]['subpath']);
                            //  $image_arr['waterpath'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $images_module[$image['gorko_id']]['waterpath']);
                            //  $image_arr['timestamp'] = str_replace('lh3.googleusercontent.com', 'img.birthday-place.ru', $images_module[$image['gorko_id']]['timestamp']);
                            $image_arr['subpath'] = str_replace($search, 'img.birthday-place.ru', $images_module[$image['gorko_id']]['subpath']);
                            $image_arr['waterpath'] = str_replace($search, 'img.birthday-place.ru', $images_module[$image['gorko_id']]['waterpath']);
                            $image_arr['timestamp'] = str_replace($search, 'img.birthday-place.ru', $images_module[$image['gorko_id']]['timestamp']);
                        } else {
                            $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                                'gorko_id' => $image['gorko_id'],
                                'params' => $params,
                                'rest_flag' => false,
                                'rest_gorko_id' => $restaurant->gorko_id,
                                'room_gorko_id' => $room->gorko_id,
                                'elastic_index' => static::index(),
                                'elastic_type' => 'rest',
                            ]));
                        }
                        array_push($images, $image_arr);
                    }
                    if (count($images) > 19) {
                        $image_flag = true;
                        break;
                    }
                }
                if ($image_flag) break;
            }
            $room_arr['images'] = $images;

            if (!empty($room_price)) {
                $min_price = $min_price > $room_price ? $room_price : $min_price;
                $max_price = $max_price < $room_price ? $room_price : $max_price;
                $sum_price += $room_price;
                $count_room++;
            }

            array_push($rooms, $room_arr);
        }
        if (empty($rooms)) return 'Нет активных залов';
        $record->rooms = $rooms;

        foreach ($record->rooms as $room) {

            if ($room['rent_room_only'] < $rent_room_only_min and $room['rent_room_only'] > 0) {
                $rent_room_only_min = $room['rent_room_only'];
            }

            if ($room['banquet_price_person'] < $banquet_price_person_min and $room['banquet_price_person'] > 0) {
                $banquet_price_person_min = $room['banquet_price_person'];
            }

            if ($room['banquet_price_min'] < $banquet_price_min and $room['banquet_price_min'] > 0) {
                $banquet_price_min = $room['banquet_price_min'];
            }
        }

        $record->restaurant_min_check = ($min_price < 1000000) ? $min_price : 0;
        $record->restaurant_max_check = $max_price;
        $record->rent_room_only_min = $rent_room_only_min == 1000000 ? 0 : $rent_room_only_min;
        $record->banquet_price_person_min = $banquet_price_person_min == 1000000 ? 0 : $banquet_price_person_min;
        $record->rest_banquet_price = $banquet_price_min == 1000000 ? 0 : $banquet_price_min;
        $record->restaurant_avg_check = (!empty($sum_price) and !empty($count_room)) ? floor($sum_price / $count_room) : 0;

        if($restaurant->gorko_id == 474269){
            $record->restaurant_min_check = 4500;
            $record->restaurant_max_check = 4500;
            $record->restaurant_avg_check = 4500;
        }



        try {
            if (!$isExist) {
                $result = $record->insert();
            } else {
                $result = $record->update();
            }
        } catch (\Exception $e) {
            $result = $e;
        }

        $model = RestaurantsModule::findOne(['id' => $restaurant->gorko_id]);

        if (!$model) {
            $model = new RestaurantsModule();
            $model->id = $restaurant->gorko_id;
            $model->save();
        }

        foreach ($record->rooms as $room) {
            $model = RoomsModule::findOne(['gorko_id' => $room['gorko_id']]);

            if (!$model) {
                $model = new RoomsModule();
                $model->id = $room['id'];
                $model->gorko_id = $room['gorko_id'];
                $model->name = $room['name'];
                $model->restaurant_id = $room['restaurant_id'];
                $model->save();
            }
        }

        return $result;
    }

    public static function subdomenCheck($connection_core)
    {
        SubdomenFilteritem::deactivate();
        $counterActive = 0;
        $counterInactive = 0;
        foreach (Subdomen::find()->all() as $key => $subdomen) {
            $rest_total = self::find()
                ->limit(0)
                ->query(
                    ['bool' => ['must' => ['match' => ['restaurant_city_id' => $subdomen->city_id]]]]
                )
                ->search();
            $isActive = $rest_total['hits']['total'] > 9;
            $subdomen->active = $isActive;
            $subdomen->save();
            if ($subdomen->active) {
                foreach (FilterItems::find()->all() as $filterItem) {
                    $hits = self::getFilterItemsHitsForCity($filterItem, $subdomen->city_id);
                    $where = ['subdomen_id' => $subdomen->id, 'filter_items_id' => $filterItem->id];
                    $subdomenFilterItem = SubdomenFilteritem::find()->where($where)->one() ?? new SubdomenFilteritem($where);
                    $subdomenFilterItem->hits = $hits;
                    $subdomenFilterItem->is_valid = 1;
                    $subdomenFilterItem->save();
                    $hits > 0 ? $counterActive++ : $counterInactive++;
                }
            }
        }
        echo "active=$counterActive; inactive=$counterInactive";

        return 1;
    }

    public static function getFilterItemsHitsForCity($filterItem, $city_id)
    {
        $filter_item_arr = json_decode($filterItem->api_arr, true);
        $main_table = 'restaurants';
        $simple_query = [];
        $nested_query = [];
        $type_query = [];
        $location_query = [];
        $specials_query = [];
        $extra_query = [];
        foreach ($filter_item_arr as $filter_data) {

            $filter_query = new FilterQueryConstructorElastic($filter_data, $main_table);

            if ($filter_query->nested) {
                if (!isset($nested_query[$filter_query->query_type])) {
                    $nested_query[$filter_query->query_type] = [];
                }
            } elseif ($filter_query->type) {
                if (!isset($type_query[$filter_query->query_type])) {
                    $type_query[$filter_query->query_type] = [];
                }
            } elseif ($filter_query->location) {
                if (!isset($location_query[$filter_query->query_type])) {
                    $location_query[$filter_query->query_type] = [];
                }
            } elseif ($filter_query->specials) {
                if (!isset($specials_query[$filter_query->query_type])) {
                    $specials_query[$filter_query->query_type] = [];
                }
            } elseif ($filter_query->extra) {
                if (!isset($extra_query[$filter_query->query_type])) {
                    $extra_query[$filter_query->query_type] = [];
                }
            } else {
                if (!isset($simple_query[$filter_query->query_type])) {
                    $simple_query[$filter_query->query_type] = [];
                }
            }

            foreach ($filter_query->query_arr as $filter_value) {
                if ($filter_query->nested) {
                    array_push($nested_query[$filter_query->query_type], $filter_value);
                } elseif ($filter_query->type) {
                    array_push($type_query[$filter_query->query_type], $filter_value);
                } elseif ($filter_query->location) {
                    array_push($location_query[$filter_query->query_type], $filter_value);
                } elseif ($filter_query->specials) {
                    array_push($specials_query[$filter_query->query_type], $filter_value);
                } elseif ($filter_query->extra) {
                    array_push($extra_query[$filter_query->query_type], $filter_value);
                } else {
                    array_push($simple_query[$filter_query->query_type], $filter_value);
                }
            }
        }
        $final_query = [
            'bool' => [
                'must' => [],
            ]
        ];
        array_push($final_query['bool']['must'], ['match' => ['restaurant_city_id' => $city_id]]);
        foreach ($simple_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
        }

        foreach ($nested_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if ($main_table == 'rooms') {
                array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
            } else {
                array_push($final_query['bool']['must'], ['nested' => ["path" => "rooms", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($type_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if ($main_table == 'rooms') {
                array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
            } else {
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_types", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($location_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if ($main_table == 'rooms') {
                array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
            } else {
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_location", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($specials_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if ($main_table == 'rooms') {
                array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
            } else {
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_specials", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }

        foreach ($extra_query as $type => $arr_filter) {
            $temp_type_arr = [];
            foreach ($arr_filter as $key => $value) {
                array_push($temp_type_arr, $value);
            }
            if ($main_table == 'rooms') {
                array_push($final_query['bool']['must'], ['bool' => ['should' => $temp_type_arr]]);
            } else {
                array_push($final_query['bool']['must'], ['nested' => ["path" => "restaurant_extra", "query" => ['bool' => ['must' => ['bool' => ['should' => $temp_type_arr]]]]]]);
            }
        }


        $final_query = [
            "function_score" => [
                "query" => $final_query,
                "functions" => [
                    [
                        "filter" => ["match" => ["restaurant_commission" => "2"]],
                        "random_score" => [],
                        "weight" => 100
                    ],
                ]
            ]
        ];

        $query = self::find()->query($final_query)->limit(0);

        return $query->search()['hits']['total'];
    }
}
