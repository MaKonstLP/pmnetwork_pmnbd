<?php

namespace frontend\modules\pmnbd\models;

use Yii;
use common\models\Restaurants;
use common\models\RestaurantsModule;
use common\models\RestaurantsTypes;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\ImagesModule;
use common\components\AsyncRenewImages;
use frontend\modules\pmnbd\models\SubdomenFilteritem;
use common\models\Subdomen;
use common\models\FilterItems;
use common\models\Rooms;
use common\models\elastic\FilterQueryConstructorElastic;

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
            'restaurant_extra_services',
            'restaurant_payment',
            'restaurant_special',
            'restaurant_phone',
            'restaurant_images',
            'restaurant_commission',
            'restaurant_types',
            'restaurant_main_type',
            'restaurant_location',
            'rooms',
        ];
    }

    public static function index()
    {
        return 'pmn_bd_restaurants';
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
                    'id'                            => ['type' => 'integer'],
                    'restaurant_id'                 => ['type' => 'integer'],
                    'restaurant_gorko_id'           => ['type' => 'integer'],
                    'restaurant_min_capacity'       => ['type' => 'integer'],
                    'restaurant_max_capacity'       => ['type' => 'integer'],
                    'restaurant_district'           => ['type' => 'integer'],
                    'restaurant_parent_district'    => ['type' => 'integer'],
                    'restaurant_city_id'            => ['type' => 'integer'],
                    'restaurant_alcohol'            => ['type' => 'integer'],
                    'restaurant_firework'           => ['type' => 'integer'],
                    'restaurant_name'               => ['type' => 'text'],
                    'restaurant_slug'               => ['type' => 'keyword'],
                    'restaurant_address'            => ['type' => 'text'],
                    'restaurant_cover_url'          => ['type' => 'text'],
                    'restaurant_latitude'           => ['type' => 'text'],
                    'restaurant_longitude'          => ['type' => 'text'],
                    'restaurant_own_alcohol'        => ['type' => 'text'],
                    'restaurant_cuisine'            => ['type' => 'text'],
                    'restaurant_parking'            => ['type' => 'text'],
                    'restaurant_extra_services'     => ['type' => 'text'],
                    'restaurant_payment'            => ['type' => 'text'],
                    'restaurant_special'            => ['type' => 'text'],
                    'restaurant_phone'              => ['type' => 'text'],
                    'restaurant_main_type'          => ['type' => 'text'],
                    'restaurant_commission'         => ['type' => 'integer'],
                    'restaurant_types'              => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'integer'],
                        'name'                          => ['type' => 'text'],
                    ]],
                    'restaurant_location'              => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'integer'],
                    ]],
                    'restaurant_images'             => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'integer'],
                        'sort'                          => ['type' => 'integer'],
                        'realpath'                      => ['type' => 'text'],
                        'subpath'                       => ['type' => 'text'],
                        'waterpath'                     => ['type' => 'text'],
                        'timestamp'                     => ['type' => 'text'],
                    ]],
                    'rooms'                             => ['type' => 'nested', 'properties' => [
                        'id'                            => ['type' => 'integer'],
                        'gorko_id'                      => ['type' => 'integer'],
                        'restaurant_id'                 => ['type' => 'integer'],
                        'price'                         => ['type' => 'integer'],
                        'capacity_reception'            => ['type' => 'integer'],
                        'capacity'                      => ['type' => 'integer'],
                        'type'                          => ['type' => 'integer'],
                        'rent_only'                     => ['type' => 'integer'],
                        'banquet_price'                 => ['type' => 'integer'],
                        'bright_room'                   => ['type' => 'integer'],
                        'separate_entrance'             => ['type' => 'integer'],
                        'type_name'                     => ['type' => 'text'],
                        'name'                          => ['type' => 'text'],
                        'slug'                          => ['type' => 'text'],
                        'restaurant_slug'               => ['type' => 'text'],
                        'restaurant_name'               => ['type' => 'text'],
                        'features'                      => ['type' => 'text'],
                        'cover_url'                     => ['type' => 'text'],
                        'images'                        => ['type' => 'nested', 'properties' => [
                            'id'                            => ['type' => 'integer'],
                            'sort'                          => ['type' => 'integer'],
                            'realpath'                      => ['type' => 'text'],
                            'subpath'                       => ['type' => 'text'],
                            'waterpath'                     => ['type' => 'text'],
                            'timestamp'                     => ['type' => 'text'],
                        ]],
                    ]]
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

    public static function refreshIndex($params) {
        $res = self::deleteIndex();
        $res = self::updateMapping();
        $res = self::createIndex();
        $res = self::updateIndex($params);
    }

    public static function updateIndex($params) {
        $connection_core = new \yii\db\Connection($params['main_connection_config']);
        $connection_core->open();
        Yii::$app->set('db', $connection_core);

        $restaurants = Restaurants::find()
            ->with('rooms')
            ->with('rooms.images')
            ->with('images')
            ->with('subdomen')
            ->where(['active' => 1])
            ->limit(100000)
            ->all();

        $all_res = '';
        $restaurants_types = RestaurantsTypes::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_types = ArrayHelper::index($restaurants_types, 'value');

        $connection_sat = new \yii\db\Connection($params['site_connection_config']);
        $connection_sat->open();
        Yii::$app->set('db', $connection_sat);

        $images_module = ImagesModule::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $images_module = ArrayHelper::index($images_module, 'gorko_id');

        $restaurants_module = RestaurantsModule::find()
            ->limit(100000)
            ->asArray()
            ->all();
        $restaurants_module = ArrayHelper::index($restaurants_module, 'gorko_id');

        //print_r($images_module[21256309]);
        //exit;

        foreach ($restaurants as $restaurant) {
            $res = self::addRecord($restaurant, $restaurants_types, $images_module, $restaurants_module, $params);
            $all_res .= $res . ' | ';
        }

        self::subdomenCheck($connection_core);

        echo 'Обновление индекса ' . self::index() . ' ' . self::type() . ' завершено<br>' . $all_res;
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

    public static function addRecord($restaurant, $restaurants_types, $images_module, $restaurants_module, $params)
    {
        $isExist = false;

        $restaurant_spec_white_list = [9];
        $restaurant_spec_rest = explode(',', $restaurant->restaurants_spec);
        if (count(array_intersect($restaurant_spec_white_list, $restaurant_spec_rest)) === 0) {
            return 'Неподходящий тип мероприятия';
        }

        if(!$restaurant->commission){
            return 'Не платный';
        }

        try {
            $record = self::get($restaurant->id);
            if (!$record) {
                $record = new self();
                $record->setPrimaryKey($restaurant->id);
            } else {
                $isExist = true;
            }
        } catch (\Exception $e) {
            $record = new self();
            $record->setPrimaryKey($restaurant->id);
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
        $record->restaurant_phone = $restaurant->phone;

        //Картинки ресторана
        $images = [];
        foreach ($restaurant->images as $key => $image) {
            $image_arr = [];
            $image_arr['id'] = $image->gorko_id;
            $image_arr['sort'] = $image->sort;
            $image_arr['realpath'] = $image->realpath;
            if(isset($images_module[$image->gorko_id])){
                $image_arr['subpath']   = $images_module[$image->gorko_id]['subpath'];
                $image_arr['waterpath'] = $images_module[$image->gorko_id]['waterpath'];
                $image_arr['timestamp'] = $images_module[$image->gorko_id]['timestamp'];
            }
            else{
                $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                    'gorko_id'      => $image->gorko_id,
                    'params'        => $params,
                    'rest_flag'     => true,
                    'rest_gorko_id' => $restaurant->gorko_id,
                    'room_gorko_id' => false,
                    'elastic_index' => static::index(),
                    'elastic_type'  => 'rest',
                ]));
            }                
            array_push($images, $image_arr);
        }
        $record->restaurant_images = $images;

        //Тип помещения
        $restaurant_types = [];
        $restaurant_types_rest = explode(',', $restaurant->type);
        foreach ($restaurant_types_rest as $key => $value) {
            $restaurant_types_arr = [];
            $restaurant_types_arr['id'] = $value;
            $restaurant_types_arr['name'] = isset($restaurants_types[$value]['text']) ? $restaurants_types[$value]['text'] : '';
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

        if ($row = (new \yii\db\Query())->select('slug')->from('restaurant_slug')->where(['gorko_id' => $restaurant->gorko_id])->one()) {
            $record->restaurant_slug = $row['slug'];
        } else {
            $record->restaurant_slug = self::getTransliterationForUrl($restaurant->name);
            \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $restaurant->gorko_id, 'slug' =>  $record->restaurant_slug])->execute();
        }

        //Св-ва залов
        $rooms = [];
        
        foreach ($restaurant->rooms as $idx => $room) {
            
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
                \Yii::$app->db->createCommand()->insert('restaurant_slug', ['gorko_id' => $room->gorko_id, 'slug' =>  $room_arr['slug']])->execute();
            }

            $room_arr['id'] = $room->id;
            $room_arr['gorko_id'] = $room->gorko_id;
            $room_arr['restaurant_id'] = $room->restaurant_id;
            $room_arr['price'] = $room->price;
            $room_arr['capacity_reception'] = $room->capacity_reception;
            $room_arr['capacity'] = $room->capacity;
            $room_arr['type'] = $room->type;
            $room_arr['rent_only'] = $room->rent_only;
            $room_arr['banquet_price'] = $room->banquet_price;
            $room_arr['bright_room'] = $room->bright_room;
            $room_arr['separate_entrance'] = $room->separate_entrance;
            $room_arr['type_name'] = $room->type_name;
            $room_arr['name'] = $room->name;
            $room_arr['restaurant_slug'] = $record->restaurant_slug;
            $room_arr['restaurant_name'] = $restaurant->name;
            $room_arr['restaurant_main_type'] = $record->restaurant_main_type;
            $room_arr['features'] = $room->features;
            $room_arr['cover_url'] = $room->cover_url;

            //Картинки залов
            $images = [];
            foreach ($room->images as $key => $image) {
                $image_arr = [];
                $image_arr['id'] = $image->gorko_id;
                $image_arr['sort'] = $image->sort;
                $image_arr['realpath'] = $image->realpath;
                if(isset($images_module[$image->gorko_id])){
                    $image_arr['subpath']   = $images_module[$image->gorko_id]['subpath'];
                    $image_arr['waterpath'] = $images_module[$image->gorko_id]['waterpath'];
                    $image_arr['timestamp'] = $images_module[$image->gorko_id]['timestamp'];
                }
                else{
                    $queue_id = Yii::$app->queue->push(new AsyncRenewImages([
                        'gorko_id'      => $image->gorko_id,
                        'params'        => $params,
                        'rest_flag'     => false,
                        'rest_gorko_id' => $restaurant->gorko_id,
                        'room_gorko_id' => $room->gorko_id,
                        'elastic_index' => static::index(),
                        'elastic_type'  => 'rest',
                    ]));
                }                
                array_push($images, $image_arr);
            }
            $room_arr['images'] = $images;

            array_push($rooms, $room_arr);
        }
        $record->rooms = $rooms;


        try {
            if (!$isExist) {
                $result = $record->insert();
            } else {
                $result = $record->update();
            }
        } catch (\Exception $e) {
            $result = $e;
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
