<?php

namespace WeatherCrawler\Tests;

use Psr\Container\ContainerInterface;
use ScriptFUSION\Porter\Porter;
use WeatherCrawler\Connector\CrawlerConnector;
use WeatherCrawler\WeatherCrawler;
use ScriptFUSION\StaticClass;

final class FixtureFactory
{
    use StaticClass;

    public static function createPorter(): Porter
    {
        return new Porter(
            \Mockery::mock(ContainerInterface::class)
                ->shouldReceive('has')
                    ->with(WeatherCrawler::class)
                    ->andReturn(true)
                ->shouldReceive('get')
                    ->with(WeatherCrawler::class)
                    ->andReturn(new WeatherCrawler(new CrawlerConnector()))
                ->getMock()
        );
    }
}
