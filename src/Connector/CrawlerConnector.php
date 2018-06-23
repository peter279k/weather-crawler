<?php

declare(strict_types=1);

namespace WeatherCrawler\Connector;

use ScriptFUSION\Porter\Net\Http\HttpConnector;
use ScriptFUSION\Porter\Net\Http\HttpOptions;

class CrawlerConnector extends HttpConnector
{
    private $options;

    public function __construct()
    {
        parent::__construct($this->options = (new HttpOptions));
    }
}
