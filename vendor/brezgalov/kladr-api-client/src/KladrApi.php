<?php

namespace Brezgalov\KladrApiClient;

use Brezgalov\ApiWrapper\Client;

class KladrApi extends Client
{
    /**
     * {@inheritdoc}
     */
    public function getBasePath()
    {
        return 'http://kladr-api.com/api.php';
    }

    /**
     * serch info in kladr
     * @param array $params
     * @return \brezgalov\ApiWrapper\Response
     * @throws \Exception
     */
    public function search(array $params)
    {
        $params['token'] = $this->token;
        return $this->prepareRequest('')->setQueryParams($params)->execJson();
    }
}