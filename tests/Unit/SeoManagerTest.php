<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\JsonLd\JsonLdBuilder;
use TimurTurdyev\Seotools\Meta\MetaBuilder;
use TimurTurdyev\Seotools\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\Seotools\SeoManager;
use TimurTurdyev\Seotools\TwitterCard\TwitterCardBuilder;

function manager(): SeoManager
{
    return new SeoManager(
        new MetaBuilder(),
        new OpenGraphBuilder(),
        new TwitterCardBuilder(),
        new JsonLdBuilder(),
    );
}

it('spreads the title across meta, og and twitter sections', function (): void {
    $seo = manager()->title('Page');

    expect($seo->render())->toBe(implode("\n", [
        '<title>Page</title>',
        '<meta property="og:title" content="Page">',
        '<meta name="twitter:title" content="Page">',
    ]));
});

it('spreads the description across sections', function (): void {
    $seo = manager()->description('Desc');

    expect($seo->render())->toBe(implode("\n", [
        '<meta name="description" content="Desc">',
        '<meta property="og:description" content="Desc">',
        '<meta name="twitter:description" content="Desc">',
    ]));
});

it('mirrors canonical into og:url and image into og and twitter', function (): void {
    $seo = manager()
        ->canonical('https://example.com/page')
        ->image('https://example.com/a.jpg');

    expect($seo->render())->toBe(implode("\n", [
        '<link rel="canonical" href="https://example.com/page">',
        '<meta property="og:url" content="https://example.com/page">',
        '<meta property="og:image" content="https://example.com/a.jpg">',
        '<meta name="twitter:image" content="https://example.com/a.jpg">',
    ]));
});

it('lets a section-level call override the aggregate value', function (): void {
    $seo = manager()->title('Common');
    $seo->openGraph()->title('OG specific');

    expect($seo->render())->toContain('<title>Common</title>')
        ->and($seo->render())->toContain('<meta property="og:title" content="OG specific">');
});

it('takes the page out of the index with noindex', function (): void {
    $seo = manager()->noindex();

    expect($seo->render())->toBe('<meta name="robots" content="noindex, nofollow">');
});

it('renders sections in the fixed order meta, og, twitter, json-ld', function (): void {
    $seo = manager()->title('Page');
    $seo->jsonLd()->add(['@type' => 'WebSite', 'name' => 'Example']);

    $html = $seo->render();

    expect(strpos($html, '<title>'))
        ->toBeLessThan((int) strpos($html, 'og:title'))
        ->and(strpos($html, 'og:title'))->toBeLessThan((int) strpos($html, 'twitter:title'))
        ->and(strpos($html, 'twitter:title'))->toBeLessThan((int) strpos($html, 'application/ld+json'));
});

it('resets every section', function (): void {
    $seo = manager()->title('Page')->description('Desc')->image('https://example.com/a.jpg');
    $seo->jsonLd()->add(['@type' => 'WebSite']);

    $seo->reset();

    expect($seo->render())->toBe('');
});
