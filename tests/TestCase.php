<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TimurTurdyev\Seotools\Laravel\SeotoolsServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [SeotoolsServiceProvider::class];
    }
}
