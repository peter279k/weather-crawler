<?php
declare(strict_types=1);

namespace WeatherCrawler;

use ScriptFUSION\Porter\Connector\Connector;
use ScriptFUSION\Porter\Provider\Provider;

class WeatherCrawler implements Provider
{
    public const CWB_API_ENDPOINT = 'http://e-service.cwb.gov.tw/';

    private $connector;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public static function buildCwbApiUrl(string $url): string
    {
        return self::CWB_API_ENDPOINT . $url;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }
}
