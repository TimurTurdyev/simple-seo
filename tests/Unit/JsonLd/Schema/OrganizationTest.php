<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;

it('wraps a string address into a postal address', function (): void {
    $business = Schema::localBusiness()->name('Shop')->address('Тверская 1, Москва');

    expect($business->jsonSerialize()['address'])->toBe([
        '@type' => 'PostalAddress',
        'streetAddress' => 'Тверская 1, Москва',
    ]);
});

it('accepts a postal address property map', function (): void {
    $business = Schema::localBusiness()->name('Shop')->address([
        'streetAddress' => 'Тверская 1',
        'addressLocality' => 'Москва',
        'postalCode' => '125009',
    ]);

    expect($business->jsonSerialize()['address'])->toBe([
        '@type' => 'PostalAddress',
        'streetAddress' => 'Тверская 1',
        'addressLocality' => 'Москва',
        'postalCode' => '125009',
    ]);
});
