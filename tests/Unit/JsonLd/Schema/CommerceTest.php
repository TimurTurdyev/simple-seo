<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\Schema\Availability;
use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;

it('builds an offer with a seller, a dated validity and a minimum order', function (): void {
    $offer = Schema::offer()
        ->availability(Availability::BackOrder)
        ->price('12990.50')
        ->priceValidUntil(new DateTimeImmutable('2025-12-13T15:04:05+03:00'))
        ->seller('Example')
        ->eligibleQuantity(10);

    expect($offer->toArray())->toBe([
        '@type' => 'Offer',
        'availability' => 'https://schema.org/BackOrder',
        'price' => '12990.50',
        'priceValidUntil' => '2025-12-13',
        'seller' => ['@type' => 'Organization', 'name' => 'Example'],
        'eligibleQuantity' => ['@type' => 'QuantitativeValue', 'minValue' => 10, 'unitCode' => 'H87'],
    ]);
});

it('writes a rating with a decimal comma as a dot', function (): void {
    expect(Schema::aggregateRating()->ratingValue('4,5')->jsonSerialize()['ratingValue'])->toBe('4.5');
});

it('turns nested types into plain arrays', function (): void {
    $product = Schema::product()
        ->name('Chair')
        ->brand(Schema::brand()->name('Acme'))
        ->additionalProperty('Цвет', 'Белый')
        ->offers(Schema::offer()->price(100)->seller(Schema::organization()->name('Shop')));

    expect($product->toArray())->toBe([
        '@type' => 'Product',
        'name' => 'Chair',
        'brand' => ['@type' => 'Brand', 'name' => 'Acme'],
        'additionalProperty' => [['@type' => 'PropertyValue', 'name' => 'Цвет', 'value' => 'Белый']],
        'offers' => ['@type' => 'Offer', 'price' => 100, 'seller' => ['@type' => 'Organization', 'name' => 'Shop']],
    ]);
});

it('builds a product group with variesBy as schema.org urls without repeats', function (): void {
    $group = Schema::productGroup()
        ->productGroupID('argo')
        ->name('Стол Арго')
        ->variesBy('color', 'size')
        ->variesBy('https://schema.org/color', 'material')
        ->hasVariant(Schema::product()->url('https://example.com/argo-white')->color('Белый'))
        ->hasVariant(Schema::product()->url('https://example.com/argo-oak')->color('Дуб'));

    expect($group->toArray())->toBe([
        '@type' => 'ProductGroup',
        'productGroupID' => 'argo',
        'name' => 'Стол Арго',
        'variesBy' => ['https://schema.org/color', 'https://schema.org/size', 'https://schema.org/material'],
        'hasVariant' => [
            ['@type' => 'Product', 'url' => 'https://example.com/argo-white', 'color' => 'Белый'],
            ['@type' => 'Product', 'url' => 'https://example.com/argo-oak', 'color' => 'Дуб'],
        ],
    ]);
});
