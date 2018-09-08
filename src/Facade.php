<?php
/**
 * Libur Facade
 * @author Rifqi Khoeruman Azam
 *
 * Pondok Programmer - Yogyakarta
 */

namespace PravoDev\HolidaysId;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pravodev.holidaysid.libur';
    }
}
