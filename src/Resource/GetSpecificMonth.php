<?php
declare(strict_types=1);

namespace WeatherCrawler\Resource;

use WeatherCrawler\WeatherCrawler;
use ScriptFUSION\Porter\Connector\ImportConnector;
use ScriptFUSION\Porter\Provider\Patreon\Collection\PledgeRecords;
use ScriptFUSION\Porter\Provider\Patreon\PatreonProvider;
use ScriptFUSION\Porter\Provider\Resource\ProviderResource;

class GetSpecificMonth implements ProviderResource
{
    private $command;

    private $station;

    private $stName;

    private $datePicker;

    public function __construct(string $command = 'viewMain', string $station, string $stName, string $datePicker)
    {
        $this->command = $command;
        $this->station = $station;
        $this->stName = $stName;
        $this->datePicker = $datePicker;
    }

    public function getProviderClassName(): string
    {
        return WeatherCrawler::class;
    }

    public function fetch(ImportConnector $connector): \Iterator
    {
        $response[] = (string) $connector->fetch(
            WeatherCrawler::buildCwbApiUrl(
                sprintf("HistoryDataQuery/MonthDataController.do?command=%s&station=%s&stname=%s&datepicker=%s",
                    $this->command, $this->station, $this->stName, $this->datePicker)
            )
        );

        yield $response;
    }
}
