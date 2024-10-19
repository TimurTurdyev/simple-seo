<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use TimurTurdyev\SimpleSeo\SeoManager;

/**
 * @see \TimurTurdyev\SimpleSeo\SeoManager
 *
 * @method static SeoManager title(string $title)
 * @method static SeoManager description(string $description)
 * @method static SeoManager image(string $url)
 * @method static SeoManager canonical(string $url)
 * @method static SeoManager noindex()
 * @method static SeoManager apply(\TimurTurdyev\SimpleSeo\SeoData|\TimurTurdyev\SimpleSeo\Contracts\HasSeo $source)
 * @method static \TimurTurdyev\SimpleSeo\Meta\MetaBuilder meta()
 * @method static \TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder openGraph()
 * @method static \TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder twitterCard()
 * @method static \TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder jsonLd()
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
