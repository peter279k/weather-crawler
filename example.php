<?php

set_time_limit(0);

require_once __DIR__ . '/vendor/autoload.php';

use Joomla\DI\Container;
use ScriptFUSION\Porter\Porter;
use WeatherCrawler\WeatherCrawler;
use WeatherCrawler\Connector\CrawlerConnector;
use WeatherCrawler\Resource\GetSpecificMonth;
use ScriptFUSION\Porter\Specification\ImportSpecification;
use Symfony\Component\DomCrawler\Crawler;
use EasyCSV\Writer;
use EasyCSV\Reader;

@mkdir('./weathers');

$container = new Container();
$container->set(WeatherCrawler::class, new WeatherCrawler(new CrawlerConnector()));
$porter = new Porter($container);

$years = ['2016', '2017', '2018'];
$months = range(1, 12);
$yearMonths = [];

foreach ($years as $year) {
    foreach ($months as $month) {
        $month = (string) $month;
        $month = strlen($month) === 1 ? '0' . $month : $month;
        $yearMonths[] = $year . '-' . $month;
    }
}

$weatherStations = [];
$weatherIndex = 0;
$reader = new Reader('./weather_station.csv');

while ($row = $reader->getRow()) {
    $stns = range(1, 4);
    foreach ($stns as $number) {
        if (empty($row['stn' . $number])) {
            continue;
        }

        if ($row['county'] !== '台中市') {
            continue;
        }

        $stnArray = explode(' ', $row['stn' . $number]);
        $station = str_replace(['(', ')'], '', $stnArray[count($stnArray) - 1]);
        $stnName = $stnArray[0] . '_' . $station;
        $weatherStations[$weatherIndex] = [$station, $stnName];
        $weatherIndex += 1;
    }
}

foreach ($yearMonths as $yearMonth) {
    foreach ($weatherStations as $weatherStation) {
        $specificMonth = new GetSpecificMonth('viewMain', $weatherStation[0], $weatherStation[1], $yearMonth);
        $response = $porter->importOne(new ImportSpecification($specificMonth));
        $crawler = new Crawler($response[0]);
        $csvString = [];
        $csvRecords = [];

        $nodeValues = $crawler->filter('table#MyTable tr')->each(function (Crawler $node, $index) {
            global $csvRecords;
            global $csvString;
            try {
                $tdObject = $node->filter('td')->each(function (Crawler $tdNode, $tdIndex) {
                    global $csvString;

                    $tdString = str_replace(['&nbsp;', ' '], '', $tdNode->text());
                    if (strlen($tdString) !== 0) {
                        if ($tdIndex === 7 || $tdIndex === 8 || $tdIndex === 10 || $tdIndex === 13 || $tdIndex === 14) {
                            $csvString[] = $tdString;
                        }
                    }
                });

                $csvRecords[] = implode(',', $csvString);
                $csvString = [];
            } catch (\Exception $e) {
            }
        });

        $csvRecords = array_slice($csvRecords, 2);
        array_unshift($csvRecords, 'Temperature,T_Max,T_Min,RH,RHMin');

        $writer = new Writer('./weathers/weather_' . $yearMonth . '_' . $weatherStation[1] . '.csv');
        $writer->writeFromArray($csvRecords);

        $sleepSec = random_int(5, 10);
        echo 'Sleep ' . $sleepSec . PHP_EOL;
        sleep($sleepSec);
    }
}
