<?php
/**
 * Base Class Libur
 * @author Rifqi Khoeruman Azam
 *
 * Pondok Programmer - Yogyakarta
 */

 namespace PravoDev\HolidaysId;

 class Libur extends Scrapper
 {

    public function getDataByYear(int $year)
    {
        return $this->getDay();
    }

    public function today()
    {
        $this->date = date('Y-m-d');
        return $this;
    }

    public function isHoliday($date = null)
    {
        $date = $date ?? $this->date;
        $dateObject = \DateTime::createFromFormat('Y-m-d', $date);;
        $data = $this->find($date, 'date', $dateObject->format('Y'));

        if(empty($data)){
            return false;
        }

        return $data;

    }

    public function getYears()
    {

    }
 }
