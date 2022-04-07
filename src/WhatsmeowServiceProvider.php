<?php

namespace Shadowbane\Whatsmeow;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\ServiceProvider;

/**
 * Class WhatsmeowServiceProvider.
 *
 * @package Shadowbane\Whatsmeow
 */
class WhatsmeowServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->offerPublishing();

//        $this->app->when(WhatsmeowChannel::class)
//            ->needs(Whatsmeow::class)
//            ->give(static function () {
//                return new LaravelWablas(
//                    config('whatsmeow.token'),
//                    app(HttpClient::class),
//                    config('whatsmeow.endpoint')
//                );
//            });
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/whatsmeow.php',
            'whatsmeow'
        );
    }

    protected function offerPublishing()
    {
        if (!function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../config/whatsmeow.php' => config_path('whatsmeow.php'),
        ], 'config');
    }
}
