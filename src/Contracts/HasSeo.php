<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\Contracts;

use TimurTurdyev\Seotools\SeoData;

interface HasSeo
{
    /**
     * Describe this object's SEO payload in one value.
     */
    public function toSeo(): SeoData;
}
