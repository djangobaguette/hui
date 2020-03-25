<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stop_points".
 *
 * @property int $id
 * @property string $num_auto
 * @property string $device_id
 * @property int $time
 * @property float $lat
 * @property float $lon
 */
class StopPoints extends \yii\db\ActiveRecord
{

    /**
     * @var null|float
     */
    public $distance;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stop_points';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['num_auto', 'device_id', 'time', 'lat', 'lon'], 'required'],
            [['time'], 'integer'],
            [['lat', 'lon'], 'number'],
            [['num_auto', 'device_id'], 'string', 'max' => 255],
            [['distance'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'num_auto' => 'Num Auto',
            'device_id' => 'Device ID',
            'time' => 'Time',
            'lat' => 'Lat',
            'lon' => 'Lon',
        ];
    }

    /**
     * @return array
     */
    public static function getDistanceGroups()
    {
        return [
            ['from' => 0, 'to' => 10, 'id' => 1],
            ['from' => 10, 'to' => 20, 'id' => 2],
            ['from' => 20, 'to' => 50, 'id' => 3],
            ['from' => 50, 'to' => 70, 'id' => 4],
            ['from' => 70, 'to' => 100, 'id' => 5],
            ['from' => 100, 'to' => 990000, 'id' => 6],
        ];
    }
}
