<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Contracts;

use TimurTurdyev\SimpleSeo\SeoData;

interface HasSeo
{
    /**
     * Describe this object's SEO payload in one value.
     */
    public function toSeo(): SeoData;
}
