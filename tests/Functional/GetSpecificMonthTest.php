<?php
declare(strict_types=1);

namespace WeatherCrawler\Tests;

use PHPUnit\Framework\TestCase;
use WeatherCrawler\Resource\GetSpecificMonth;
use ScriptFUSION\Porter\Specification\ImportSpecification;

final class GetSpecificMonthTest extends TestCase
{
    public function testGetSpecificMonthRecords()
    {
        $response = FixtureFactory::createPorter()->importOne(new ImportSpecification(new GetSpecificMonth('viewMain', '466910', '466910_鞍部', '2017-06')));
        $response = $response[0];

        $this->assertContains('2017-06', $response);
        $this->assertContains('Temperature', $response);
        $this->assertContains('T Max', $response);
        $this->assertContains('T Min', $response);
        $this->assertContains('RH', $response);
        $this->assertContains('RHMin', $response);
    }
}
