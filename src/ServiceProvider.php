<?php
/**
 * Service Provider
 *
 * @author Rifqi Khoeruman Azam
 *
 * Pondok Programmer - Yogyakarta
 */
namespace PravoDev\HolidaysId;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/holidays_id.php', 'holidays_id'
        );
        $this->app->singleton('pravodev.holidaysid.libur', function () {
            return new Libur();
        });
        $this->app->alias('pravodev.holidaysid.libur', 'PravoDev\HolidaysId\Libur');
    }
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/holidays_id.php' => config_path('holidays_id.php')
        ]);
    }
}
