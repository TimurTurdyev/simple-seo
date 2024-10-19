<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\SeoManager;

if (! function_exists('seo')) {
    /**
     * Resolve the request-scoped SEO manager. Available inside a Laravel
     * application only; outside Laravel the function is declared but the
     * container call would fail.
     */
    function seo(): SeoManager
    {
        return app(SeoManager::class);
    }
}
