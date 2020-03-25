<?php

namespace app\models\search;

use app\models\StopPoints;
use yii\base\Model;
use yii\db\Expression;

class StopPointsSearch extends Model
{
    /**
     * @var float
     */
    public $lat;

    /**
     * @var float
     */
    public $lon;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['lat', 'lon'], 'number'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'lat' => 'Широта',
            'lon' => 'Долгота',
        ];
    }

    /**
     * @return bool
     */
    public function getHasCoordsFilter()
    {
        return $this->lat !== null && $this->lon !== null;
    }

    /**
     * @return array
     */
    public function getGridViewColumns()
    {
        $columns = [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'num_auto',
            'device_id',
            'time:datetime',
            'lat',
            'lon',
        ];

        if ($this->getHasCoordsFilter()) {
            $columns[] = [
                'attribute' => 'distance',
                'label'     => 'distance',
                'value'     => function(StopPoints $item) {
                    return round($item->distance, 2);
                }
            ];
        }

        $columns[] = ['class' => 'yii\grid\ActionColumn'];

        return $columns;
    }

    /**
     * @return \yii\db\ActiveQuery;
     */
    public function getQuery()
    {
        $stopPointsTable = StopPoints::tableName();

        /* @var \yii\db\ActiveQuery $query */
        $query = StopPoints::find()->select("{$stopPointsTable}.*");

        if ($this->getHasCoordsFilter()) {
            $query
                ->addSelect(StopPointsSearch::getDistanceColumnExpression())
                ->addSelect(StopPointsSearch::getDistnceGroupIdColumnExpression())
                ->orderBy('distance ASC');
        }

        return $query;
    }

    public function getDistanceColumnExpression()
    {
        $stopPointsTable = StopPoints::tableName();

        return new Expression("(
            6371 * acos( 
                    cos( radians(:lat) ) 
                    * cos( radians( {$stopPointsTable}.lat ) ) 
                    * cos( radians( {$stopPointsTable}.lon ) - radians(:lon) ) 
                    + sin( radians( :lat) ) 
                    * sin( radians( {$stopPointsTable}.lat ) )
                )
            ) as distance",
            [
                ':lat' => floatval($this->lat),
                ':lon' => floatval($this->lon),
            ]
        );
    }

    public function getDistnceGroupIdColumnExpression()
    {
        $result ='';
        $stopPointsTable = StopPoints::tableName();
        foreach(StopPoints::getDistanceGroups() as $key => $value) {
            $result .= "if ({$stopPointsTable}.distance >= {$value['from']} and {$stopPointsTable}.distance <= {$value['to']}, {$value['id']}, '{$value['id']}') as distance_group_id";
        }
        return $result;
    }
}