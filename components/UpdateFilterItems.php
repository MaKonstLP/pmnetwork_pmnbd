<?php

namespace frontend\modules\pmnbd\components;

use Yii;
use yii\helpers\ArrayHelper;
// use common\models\Filter;
// use common\models\Slices;
// use common\models\MetroStations;
// use common\models\Okruga;
// use common\models\Rayoni;
// use common\models\elastic\ItemsFilterElastic;
use frontend\components\ParamsFromQuery;
// use frontend\modules\banketnye_zaly_moskva\models\ElasticItems;
// use frontend\modules\banketnye_zaly_moskva\models\RestaurantSpecFilterRel;

use Elasticsearch\ClientBuilder;


class UpdateFilterItems
{
  public static function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $slices_model);
		$return['params_filter'] = $temp_params->params_filter;

		return $return;
	}

  /* public static function getAggregateResult($filterState)
  {
    $filter_model = Filter::find()->with('items')->orderBy(['sort' => SORT_ASC])->all();
    $slices_model = Slices::find()->all();
    $params = UpdateFilterItems::parseGetQuery($filterState, $filter_model, $slices_model);
    $elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params['params_filter'], 1, 1, false, 'rooms', $elastic_model);

    $query = $items->query
      ->addAggregate('restaurant_metro_group', [
        'nested' => [
          'path' => 'restaurant_metro_stations'
        ],
        'aggs' => [
          'metro' => [
            'terms' => [
              'field' => 'restaurant_metro_stations.id',
              'size' => 10000,
            ]
          ]
        ]
      ])
      ->addAggregate('restaurant_spec_group', [
        'nested' => [
          'path' => 'restaurant_spec'
        ],
        'aggs' => [
          'prazdnik' => [
            'terms' => [
              'field' => 'restaurant_spec.id',
              'size' => 10000,
            ]
          ]
        ]
      ])
      ->addAggregate('capacity_group', [
        'nested' => [
          'path' => ''
        ],
        'aggs' => [
          'lyudey' => [
            'range' => [
              'field' => 'capacity',
              'ranges' => [
                ['to' => 15],
                ['from' => 15, 'to' => 20],
                ['from' => 20, 'to' => 30],
                ['from' => 30, 'to' => 40],
                ['from' => 40, 'to' => 50],
                ['from' => 50, 'to' => 60],
                ['from' => 60, 'to' => 80],
                ['from' => 80, 'to' => 100],
                ['from' => 100, 'to' => 150],
                ['from' => 150, 'to' => 200],
                ['from' => 200, 'to' => 300],
                ['from' => 300]
              ],
            ]
          ]
        ]
      ])
      ->addAggregate('alko', [
        'terms' => [
          'field' => 'restaurant_alcohol',
          'size' => 10000,
        ]
      ])
      ->addAggregate('parent_district_group', [
        'terms' => [
          'field' => 'restaurant_parent_district',
          'size' => 10000,
        ],
        'aggs' => [
          'district_group' => [
            'terms' => [
              'field' => 'restaurant_district',
              'size' => 10000,
            ]
          ]
        ]
      ]);

    return $query->search();
  } */

  /* public static function getFilter($filterState)
  {
    $enabledFilterItemsList = [];
    $aggregateResult = UpdateFilterItems::getAggregateResult($filterState);
    
    if ($aggregateResult['aggregations']['restaurant_metro_group']['doc_count'] > 0 && !isset($filterState['metro'])){

      $connectionMain = new \yii\db\Connection([
            'dsn'       => 'mysql:host=localhost;dbname=pmn',
            'username'  => 'root',
            'password'  => 'GxU25UseYmeVcsn5Xhzy',
            'charset'   => 'utf8mb4',
        ]);
      $connectionMain->open();

      $stationList = MetroStations::find()->all($connectionMain);
      $stationList = ArrayHelper::index($stationList, 'table_id');
      $tmp = '';
      foreach ($aggregateResult['aggregations']['restaurant_metro_group']['metro']['buckets'] as $key => $value){
        $tmp .= $stationList[$value['key']]['alias'] . ',';
      }
      $enabledFilterItemsList['metro'] = substr($tmp, 0, -1);
    }
    
    if ($aggregateResult['aggregations']['restaurant_spec_group']['doc_count'] > 0 && !isset($filterState['prazdnik'])){
      $specList = RestaurantSpecFilterRel::find()->all();
      $specList = ArrayHelper::index($specList, 'id');
      $tmp = '';
      foreach ($aggregateResult['aggregations']['restaurant_spec_group']['prazdnik']['buckets'] as $key => $value){
        if (array_key_exists($value['key'], $specList)){
          $tmp .= $specList[$value['key']]['alias'] . ',';
        }
      }
      $enabledFilterItemsList['prazdnik'] = substr($tmp, 0, -1);
    }

    foreach ($aggregateResult['aggregations']['parent_district_group']['buckets'] as $key => $districtAgg){

      if ($districtAgg['doc_count'] > 0 && $districtAgg['key'] === 0 && !isset($filterState['okrug'])){
        $okrugList = Okruga::find()->all();
        $okrugList = ArrayHelper::index($okrugList, 'id');
        $tmp = '';
        foreach ($districtAgg['district_group']['buckets'] as $key => $value){
          if (array_key_exists($value['key'], $okrugList)){
            $tmp .= $okrugList[$value['key']]['alias'] . ',';
          }
        }
        $enabledFilterItemsList['okrug'] = substr($tmp, 0, -1);
      }

      if ($districtAgg['doc_count'] > 0 && $districtAgg['key'] === 547 && !isset($filterState['rayon'])){
        $rayonList = Rayoni::find()->all();
        $rayonList = ArrayHelper::index($rayonList, 'id');
        $tmp = '';
        foreach ($districtAgg['district_group']['buckets'] as $key => $value){
          if (array_key_exists($value['key'], $rayonList)){
            $tmp .= $rayonList[$value['key']]['alias'] . ',';
          }
        }
        $enabledFilterItemsList['rayon'] = substr($tmp, 0, -1);
      }
    }

    if ($aggregateResult['aggregations']['capacity_group']['doc_count'] > 0 && !isset($filterState['lyudey'])){
      $map = [
        '*-15.0' => 'do-15',
        '15.0-20.0' => '15-19',
        '20.0-30.0' => '20-29',
        '30.0-40.0' => '30-39',
        '40.0-50.0' => '40-49',
        '50.0-60.0' => '50-59',
        '60.0-80.0' => '60-79',
        '80.0-100.0' => '80-99',
        '100.0-150.0' => '100-149',
        '150.0-200.0' => '150-199',
        '200.0-300.0' => '200-299',
        '300.0-*' => 'ot-300',
      ];
      $tmp = '';
      foreach ($aggregateResult['aggregations']['capacity_group']['lyudey']['buckets'] as $key => $value){
        if (array_key_exists($value['key'], $map) && $value['doc_count'] > 0){
          $tmp .= $map[$value['key']] . ',';
        }
      }
      $enabledFilterItemsList['lyudey'] = substr($tmp, 0, -1);
    }

    foreach ($aggregateResult['aggregations']['alko']['buckets'] as $key => $alkoItem){
      if ($alkoItem['key'] === 1 && $alkoItem['doc_count'] > 0 && !isset($filterState['alko'])){
        $enabledFilterItemsList['alko'] = 'da';
      }
    }
  
    return $enabledFilterItemsList;
  } */
}