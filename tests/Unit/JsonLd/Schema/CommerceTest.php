<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\JsonLd\JsonLdBuilder;
use TimurTurdyev\Seotools\JsonLd\Schema\Schema;

it('builds a catalog product with an aggregate offer like the prvoffice scenario', function (): void {
    $product = Schema::product()
        ->name('Офисные столы')
        ->image('https://example.com/cover.jpg')
        ->offers(
            Schema::aggregateOffer()
                ->lowPrice(4990)
                ->highPrice(129000)
                ->priceCurrency('RUB')
                ->offerCount(120)
        );

    $html = (new JsonLdBuilder())->add($product)->render();

    expect($html)->toContain('"@type":"Product"')
        ->and($html)->toContain('"name":"Офисные столы"')
        ->and($html)->toContain(
            '"offers":{"@type":"AggregateOffer","lowPrice":4990,"highPrice":129000,"priceCurrency":"RUB","offerCount":120}'
        );
});

it('builds an offer with the in-stock shortcut', function (): void {
    $offer = Schema::offer()->price('4990.00')->priceCurrency('RUB')->inStock();

    expect($offer->jsonSerialize())->toBe([
        '@type' => 'Offer',
        'price' => '4990.00',
        'priceCurrency' => 'RUB',
        'availability' => 'https://schema.org/InStock',
    ]);
});

it('nests offer and rating inside a product', function (): void {
    $product = Schema::product()
        ->name('Chair')
        ->sku('CH-1')
        ->offers(Schema::offer()->price(100)->priceCurrency('USD')->outOfStock())
        ->aggregateRating(Schema::aggregateRating()->ratingValue(4.8)->reviewCount(31));

    $html = (new JsonLdBuilder())->add($product)->render();

    expect($html)->toContain('"availability":"https://schema.org/OutOfStock"')
        ->and($html)->toContain('"aggregateRating":{"@type":"AggregateRating","ratingValue":4.8,"reviewCount":31}');
});
