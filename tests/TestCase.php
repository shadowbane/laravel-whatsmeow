<?php

namespace Shadowbane\Whatsmeow\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            \Shadowbane\Whatsmeow\WhatsmeowServiceProvider::class,
        ];
    }
}
