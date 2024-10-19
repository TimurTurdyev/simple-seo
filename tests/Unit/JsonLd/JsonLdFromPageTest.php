<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;
use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\SimpleSeo\SeoManager;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;

function fromPageManager(?string $defaultTitle = null, ?string $titleSuffix = null, ?string $defaultDescription = null): SeoManager
{
    return new SeoManager(
        new MetaBuilder($defaultTitle, $titleSuffix, $defaultDescription),
        new OpenGraphBuilder(),
        new TwitterCardBuilder(),
        new JsonLdBuilder(),
    );
}

it('builds the entity from page title, description, image and canonical', function (): void {
    $seo = fromPageManager()
        ->title('Chair EX-500')
        ->description('Ergonomic chair.')
        ->image('https://example.com/chair.jpg')
        ->canonical('https://example.com/chairs/ex-500');

    $seo->jsonLd()->fromPage('Product');

    expect($seo->jsonLd()->render())->toBe(
        '<script type="application/ld+json">'
        . '{"@context":"https://schema.org","@type":"Product",'
        . '"name":"Chair EX-500","description":"Ergonomic chair.",'
        . '"image":"https://example.com/chair.jpg","url":"https://example.com/chairs/ex-500"}'
        . '</script>'
    );
});

it('uses the title without the suffix', function (): void {
    $seo = fromPageManager(titleSuffix: ' - Site');
    $seo->title('Page');

    $seo->jsonLd()->fromPage('Article');

    expect($seo->meta()->render())->toContain('<title>Page - Site</title>')
        ->and($seo->jsonLd()->render())->toContain('"name":"Page"')
        ->and($seo->jsonLd()->render())->not->toContain('Page - Site');
});

it('falls back to config defaults for name and description', function (): void {
    $seo = fromPageManager(defaultTitle: 'Site', defaultDescription: 'Default description.');

    $seo->jsonLd()->fromPage('WebPage');

    expect($seo->jsonLd()->render())->toContain('"name":"Site"')
        ->and($seo->jsonLd()->render())->toContain('"description":"Default description."');
});

it('lets overrides win over page values', function (): void {
    $seo = fromPageManager()->title('Page title');

    $seo->jsonLd()->fromPage('Product', ['name' => 'Custom name']);

    expect($seo->jsonLd()->render())->toContain('"name":"Custom name"')
        ->and($seo->jsonLd()->render())->not->toContain('Page title');
});

it('drops null overrides so conditional fields need no if', function (): void {
    $seo = fromPageManager()->title('Page');

    $seo->jsonLd()->fromPage('Product', ['offers' => null]);

    expect($seo->jsonLd()->render())->not->toContain('offers');
});

it('serializes schema builder objects passed as overrides', function (): void {
    $seo = fromPageManager()->title('Chair');

    $seo->jsonLd()->fromPage('Product', [
        'offers' => Schema::offer()->price('49900.00')->priceCurrency('RUB')->inStock(),
    ]);

    expect($seo->jsonLd()->render())->toContain(
        '"offers":{"@type":"Offer","price":"49900.00","priceCurrency":"RUB",'
        . '"availability":"https://schema.org/InStock"}'
    );
});

it('renders a single image as a string and multiple as an array', function (): void {
    $single = fromPageManager()->image('https://example.com/a.jpg');
    $single->jsonLd()->fromPage('Product');

    expect($single->jsonLd()->render())->toContain('"image":"https://example.com/a.jpg"');

    $multiple = fromPageManager()
        ->image('https://example.com/a.jpg')
        ->image('https://example.com/b.jpg');
    $multiple->jsonLd()->fromPage('Product');

    expect($multiple->jsonLd()->render())
        ->toContain('"image":["https://example.com/a.jpg","https://example.com/b.jpg"]');
});

it('renders only the type when the page has no values', function (): void {
    $seo = fromPageManager();

    $seo->jsonLd()->fromPage('WebPage');

    expect($seo->jsonLd()->render())->toBe(
        '<script type="application/ld+json">'
        . '{"@context":"https://schema.org","@type":"WebPage"}'
        . '</script>'
    );
});

it('joins page entities and added entities into one graph', function (): void {
    $seo = fromPageManager()->title('Chair');

    $seo->jsonLd()
        ->fromPage('Product')
        ->add(['@type' => 'BreadcrumbList', 'name' => 'Crumbs']);

    $html = $seo->jsonLd()->render();

    expect($html)->toContain('"@graph":[')
        ->and($html)->toContain('"@type":"BreadcrumbList"')
        ->and($html)->toContain('"@type":"Product","name":"Chair"');
});

it('renders only the type and overrides without a page provider', function (): void {
    $jsonLd = (new JsonLdBuilder())->fromPage('Product', ['name' => 'Standalone']);

    expect($jsonLd->render())->toBe(
        '<script type="application/ld+json">'
        . '{"@context":"https://schema.org","@type":"Product","name":"Standalone"}'
        . '</script>'
    );
});

it('clears page entities on reset', function (): void {
    $seo = fromPageManager()->title('Chair');
    $seo->jsonLd()->fromPage('Product');

    expect($seo->jsonLd()->count())->toBe(1);

    $seo->reset();

    expect($seo->jsonLd()->count())->toBe(0)
        ->and($seo->jsonLd()->render())->toBe('');
});
