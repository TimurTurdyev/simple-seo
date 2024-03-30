<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\JsonLd\JsonLdBuilder;
use TimurTurdyev\Seotools\JsonLd\Schema\Schema;

it('builds an organization with sameAs links', function (): void {
    $org = Schema::organization()
        ->name('Example LLC')
        ->url('https://example.com')
        ->logo('https://example.com/logo.png')
        ->sameAs('https://x.com/example')
        ->sameAs('https://github.com/example');

    expect($org->jsonSerialize())->toBe([
        '@type' => 'Organization',
        'name' => 'Example LLC',
        'url' => 'https://example.com',
        'logo' => 'https://example.com/logo.png',
        'sameAs' => ['https://x.com/example', 'https://github.com/example'],
    ]);
});

it('builds a web site entity through the json-ld builder', function (): void {
    $html = (new JsonLdBuilder())
        ->add(Schema::webSite()->name('Example')->url('https://example.com'))
        ->render();

    expect($html)->toContain('"@type":"WebSite","name":"Example","url":"https://example.com"');
});

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
