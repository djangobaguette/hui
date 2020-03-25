<?php

namespace Brezgalov\PortTransitCommon\Utils;

use Brezgalov\GmapsApiClient\GMapsApi;
use Brezgalov\IPoint\IPoint;
use Brezgalov\IPoint\Point;
use Brezgalov\KladrApiClient\KladrApi;

class GeoHelper
{
    /**
     * @var array
     */
    protected $decorators = [];

    /**
     * @var string
     */
    protected $kladrToken;

    /**
     * @var string
     */
    protected $googleToken;

    /**
     * @param KladrApi
     */
    protected $kladrClient;

    /**
     * @var GMapsApi
     */
    protected $googleClient;

    /**
     * @param array $tokens - ['kladr' => ..., 'google' => ....]; Tokens are optional
     */
    public static function getInstance(array $tokens)
    {
        $inst = new GeoHelper();
        $inst->kladrToken = @$tokens['kladr'];
        $inst->googleToken = @$tokens['google'];
        return $inst;
    }

    /**
     * @return KladrApi
     */
    protected function getKladrClient()
    {
        if (empty($this->kladrClient)) {
            $this->kladrClient = new KladrApi($this->kladrToken);
        }
        $this->kladrClient->setRequestDecorators($this->decorators);
        return $this->kladrClient;
    }

    /**
     * @return GMapsApi
     */
    protected function getGoogleClient()
    {
        if (empty($this->googleClient)) {
            $this->googleClient = new GMapsApi($this->googleToken);
        }
        $this->googleClient->setRequestDecorators($this->decorators);
        return $this->googleClient;
    }

    /**
     * @param $lon
     * @param $lat
     * @return string
     */
    public static function selectDistanceDb($lon, $lat)
    {
        return "(6371 * 2 * ASIN(SQRT(POWER(SIN((lat - ABS(" . str_replace(",", ".", $lat) . ")) * PI()/180 / 2), 2) + COS(lat * PI()/180) * COS(ABS(" . str_replace(",", ".", $lat) . ") * PI()/180) * POWER(SIN((lon - " . str_replace(",", ".", $lon) . ") * PI()/180 / 2), 2))))  as distanceDb";
    }

    /**
     * set decorators to use
     * @param array $decorators
     * @return $this
     */
    public function useDecorators(array $decorators)
    {
        $this->decorators = $decorators;
        return $this;
    }

    /**
     * @param $kladr
     * @return string
     * @throws \Exception
     */
    public function findAddress($kladr)
    {
        $response = $this->getKladrClient()->search([
            'cityId' => $kladr,
            'withParent' => 1,
            'contentType' => 'city',
        ]);
        if (!$response->isSuccessful() || !array_key_exists('result', $response->data)) {
            throw new \Exception('Не удается получить адресс по кладр ' . $kladr);
        }

        $result = $response->data['result'];
        $firstResult = array_shift($result);
        if (empty($firstResult)) {
            return '';
        }

        $return = '';
        if (array_key_exists('parents', $firstResult) && is_array($firstResult['parents'])) {
            foreach ($firstResult['parents'] as $v) {
                $return .= '' . $v['name'] . ' ' . $v['type'] . ', ';
            }
        }

        return $return . $firstResult['name'] . ', ' . $firstResult['type'];
    }

    /**
     * @param IPoint $from
     * @param IPoint $to
     * @return array
     * @throws \Exception
     */
    public function getDistance(IPoint $from, IPoint $to)
    {
        $resp = $this->getGoogleClient()->getDistance($from, $to);

        if ($resp->isSuccessful()) {
            return $resp->data;
        } else {
            throw new \Exception('Не удалось получить успешный ответ: ' . $resp->status . ': ' . $resp->getErrorsConcat(', '));
        }
    }

    /**
     * Get coord by address value
     *
     * @param $address
     * @return Point
     * @throws \Exception
     */
    public function getGoogleCoord($address)
    {
        $resp = $this->getGoogleClient()->getPointByAddress($address);
        if ($resp->isSuccessful()) {
            return new Point([
                'lat' => $resp->data->lat,
                'lon' => $resp->data->lon,
            ]);
        } else {
            throw new \Exception('Не удалось получить успешный ответ: ' . $resp->status . ': ' . $resp->getErrorsConcat(', '));
        }
    }

    /**
     * @param $query
     * @return array
     * @throws \Exception
     */
    public function findRegionName($query)
    {
        $response = $this->getKladrClient()->search([
            'query' => $query,
            'limit' => 1,
            'contentType' => 'region',
            'oneString' => 1,
            'withParent' => 1,
        ]);
        if (isset($response->data['result'][0]['parents'][0]['id'])) {
            return [
                'id' => $response->data['result'][0]['parents'][0]['id'],
                'name' => $response->data['result'][0]['parents'][0]['name'] . ', ' . $response->data['result'][0]['parents'][0]['typeShort'],
            ];
        }
        return [
            'id' => '',
            'name' => '',
        ];
    }

    /**
     * Return array of cities with regions by city name
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function findCitiesByName($name)
    {
        $response = $this->getKladrClient()->search([
            'query' => $name,
            'contentType' => 'city',
            'typeCode' => 1,
            'withParent' => 1,
        ]);
        return @$response->data['result'];
    }

    /**
     * @param $query
     * @param $regionId
     * @return array
     * @throws \Exception
     */
    public function findDistrictName($query, $regionId)
    {
        $response = $this->getKladrClient()->search([
            'query' => $query,
            'limit' => 1,
            'contentType' => 'district',
            'oneString' => 1,
            'withParent' => 1,
            'regionId' => $regionId,
        ]);
        if (isset($response->data['result'][0]['id']) && $response->data['result'][0]['parents'][1]['contentType'] == 'district') {
            return [
                'id' => $response->data['result'][0]['parents'][1]['id'],
                'name' => $response->data['result'][0]['parents'][1]['name'] . ', ' . $response->data['result'][0]['parents'][1]['typeShort'],
            ];
        }
        return [
            'id' => '',
            'name' => '',
        ];
    }

    /**
     * @param $query
     * @param $regionId
     * @param $districtId
     * @return array
     * @throws \Exception
     */
    public function findLocalityName($query, $regionId, $districtId)
    {
        $response = $this->getKladrClient()->search([
            'query' => $query,
            'limit' => 1,
            'contentType' => 'city',
            'withParent' => 1,
            'districtId' => $districtId,
            'regionId' => $regionId,
        ]);
        if (isset($response->data['result'][0]['id']) && $response->data['result'][0]['parents'][1]['contentType'] == 'district') {
            return [
                'id' => $response->data['result'][0]['id'],
                'name' => $response->data['result'][0]['name'] . ', ' . $response->data['result'][0]['typeShort'],
            ];
        }
        return [
            'id' => '',
            'name' => '',
        ];
    }

    /**
     * @param $result
     * @return array
     * @throws \Exception
     */
    public function getArrayAddress($result)
    {
        $region = $district = $city = [
            'id' => '',
            'name' => '',
        ];

        foreach ($result as $res) {
            $data = null;
            $arrayRef = null;
            switch ($res['types'][0]) {
                case 'administrative_area_level_1':
                    $data = $this->findRegionName($res['short_name']);
                    $arrayRef = &$region;
                    break;
                case 'administrative_area_level_2':
                    $data = $this->findDistrictName($res['short_name'], $region['id']);
                    $arrayRef = &$district;
                    break;
                case 'locality':
                    $data = $this->findLocalityName($res['short_name'], $region['id'], $district['id']);
                    $arrayRef = &$district;
                    break;
            }
            $arrayRef['id'] = @$data['id'] ?: '';
            $arrayRef['name'] = @$data['name'] ?: '';
        }

        return [
            'region' => $region,
            'district' => $district,
            'city' => $city
        ];
    }
}
