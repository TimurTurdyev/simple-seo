<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use TimurTurdyev\Seotools\SeoManager;

/**
 * @see \TimurTurdyev\Seotools\SeoManager
 *
 * @method static SeoManager title(string $title)
 * @method static SeoManager description(string $description)
 * @method static SeoManager image(string $url)
 * @method static SeoManager canonical(string $url)
 * @method static SeoManager noindex()
 * @method static SeoManager apply(\TimurTurdyev\Seotools\SeoData|\TimurTurdyev\Seotools\Contracts\HasSeo $source)
 * @method static \TimurTurdyev\Seotools\Meta\MetaBuilder meta()
 * @method static \TimurTurdyev\Seotools\OpenGraph\OpenGraphBuilder openGraph()
 * @method static \TimurTurdyev\Seotools\TwitterCard\TwitterCardBuilder twitterCard()
 * @method static \TimurTurdyev\Seotools\JsonLd\JsonLdBuilder jsonLd()
 * @method static string render()
 * @method static void reset()
 */
final class Seo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SeoManager::class;
    }
}
