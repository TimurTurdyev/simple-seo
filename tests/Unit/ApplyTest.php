<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\Contracts\HasSeo;
use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;
use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\SimpleSeo\SeoData;
use TimurTurdyev\SimpleSeo\SeoManager;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;

function applyManager(): SeoManager
{
    return new SeoManager(
        new MetaBuilder(),
        new OpenGraphBuilder(),
        new TwitterCardBuilder(),
        new JsonLdBuilder(),
    );
}

it('applies a full seo data payload', function (): void {
    $seo = applyManager()->apply(new SeoData(
        title: 'Product page',
        description: 'Great product',
        image: 'https://example.com/cover.jpg',
        canonical: 'https://example.com/product',
        ogType: 'product',
        jsonLd: [Schema::product()->name('Product')],
    ));

    $html = $seo->render();

    expect($html)->toContain('<title>Product page</title>')
        ->and($html)->toContain('<meta property="og:type" content="product">')
        ->and($html)->toContain('<link rel="canonical" href="https://example.com/product">')
        ->and($html)->toContain('"@type":"Product"');
});

it('applies a HasSeo source like a model describing its own markup', function (): void {
    $product = new class () implements HasSeo {
        public function toSeo(): SeoData
        {
            return new SeoData(
                title: 'Кресло руководителя',
                image: 'https://example.com/chair.jpg',
            );
        }
    };

    $seo = applyManager()->apply($product);

    expect($seo->render())->toContain('<title>Кресло руководителя</title>')
        ->and($seo->render())->toContain('twitter:image');
});

it('skips null fields without touching state', function (): void {
    $seo = applyManager()->title('Existing');

    $seo->apply(new SeoData(description: 'Only description'));

    expect($seo->render())->toContain('<title>Existing</title>')
        ->and($seo->render())->toContain('<meta name="description" content="Only description">');
});

it('replaces scalars and accumulates json-ld on repeated apply', function (): void {
    $seo = applyManager()
        ->apply(new SeoData(title: 'First', jsonLd: [['@type' => 'WebSite']]))
        ->apply(new SeoData(title: 'Second', jsonLd: [['@type' => 'Organization']]));

    $html = $seo->render();

    expect($html)->toContain('<title>Second</title>')
        ->and($html)->not->toContain('First')
        ->and($html)->toContain('"@graph":[{"@type":"WebSite"},{"@type":"Organization"}]');
});
