<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TimurTurdyev\SimpleSeo\Laravel\SimpleSeoServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [SimpleSeoServiceProvider::class];
    }
}
