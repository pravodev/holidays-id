<?php

/**
 * Mengambil data hari Libur Nasional dari google calendar
 * @author Rifqi Khoeruman Azam
 *
 * Pondok Programmer - Yogyakarta
 */

use Illuminate\Http\Request;
use pQuery\IQuery\pQuery;
use \Symfony\Component\DomCrawler\Crawler;

namespace PravoDev\HolidaysId;

class Scrapper
{
    public $apiKey, $calendar;

    public $resource = [];

    public $year = '2018';

    private $path = '/json/hari-libur.json';

    public function __construct($year = null)
    {
        if($year){
            $this->year = $year;
        }
    }
    /**
     * Set year property
     *
     * @param int $year
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Set key property
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->apiKey = $key;
        return $this;
    }

    /**
     * Set google calendar id property
     *
     * @param string $calendar
     * @return $this
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;
        return $this;
    }

    /**
     * get key from property or config file
     *
     * @return string api_key
     */
    public function getKey()
    {
        return $this->apiKey ?? config('holidays_id.api_key');
    }

    /**
     * get calendar from property or config file
     *
     * @return string calendar_id
     */
    public function getCalendar()
    {
        return $this->calendar ?? config('holidays_id.calendar_id');
    }

    /**
    * get path json file
    *
    * @return string path_name
    */
    public function getPath()
    {
        return storage_path() . $this->path;
    }

    /**
     * get url google calendar api
     *
     */
    public function getUrl()
    {
        $url = 'https://www.googleapis.com/calendar/v3/calendars/'. $this->getCalendar() .'/events?key='. $this->getKey();
        $datetime = new \DateTime();
        $newDate = $datetime->createFromFormat('d/m/Y', '01/01/' . $this->year);
        if($this->year){
            $url .= '&timeMin=' . $newDate->format('Y-m-d') . 'T10:00:00Z&timeMax=' . $this->year . '-12-31T10:00:00Z';
        }

        return $url;
    }

    /**
     *  write response to json file
     *
     * @return $this
     */
    public function writeData()
    {
        $url = $this->getUrl();

        $currentData = $this->getFileContent();
        $data = [];

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', $this->getUrl());
        $response = $res->getBody()->getContents();
        if($response){
            $data = json_decode($response);
            $data = collect($data->items)->map(function($calendar){
                return [
                    'date' => $calendar->start->date,
                    'event' => $calendar->summary
                ];
            })->toArray();

        }
        $resource = [
            $this->year => $data
        ];
        if(!empty($currentData)){
            $resource = $resource + $currentData;
        }
        file_put_contents($this->getPath(), json_encode($resource));

        return $this;
    }

    /**
     * get current content from json file
     *
     * @return array []
     */
    public function getFileContent()
    {
        if(file_exists($this->getPath())){
            return json_decode(file_get_contents($this->getPath()), true);
        }
        return [];
    }

    /**
     * set resource property
     *
     * @param int $year
     */
    public function setResource()
    {
        $data = $this->getFileContent();
        $searhByYear = array_key_exists($this->year, $data);

        if(empty($data) || $searhByYear == false){
            $this->writeData();
            return $this->setResource($this->year);
        }

        if(count($data)){
            if($this->year){
                $this->resource = $data[$this->year];
            }else{
                $this->resource = collect($data);
            }
        }

        return $this;
    }

    /**
     * get data;
     */
    public function getData()
    {
        if(empty($this->resource)){
            $this->setResource();
        }
        return $this->resource;
    }

    /**
     * find by key
     */
    public function find($value, $key, $year)
    {
        $data = collect($this->getData())->filter(function($calendar) use ($value, $key){
            if($calendar[$key] == $value){
                return $calendar;
            }

            return false;
        })->first();

        return $data;
    }
}
