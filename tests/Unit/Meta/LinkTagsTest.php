<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\Meta\MetaBuilder;

it('renders the canonical link', function (): void {
    expect((new MetaBuilder())->canonical('https://example.com/page')->render())
        ->toBe('<link rel="canonical" href="https://example.com/page">');
});

it('renders prev and next pagination links', function (): void {
    $meta = (new MetaBuilder())
        ->prev('https://example.com/page?p=1')
        ->next('https://example.com/page?p=3');

    expect($meta->render())->toBe(
        "<link rel=\"prev\" href=\"https://example.com/page?p=1\">\n"
        . '<link rel="next" href="https://example.com/page?p=3">'
    );
});

it('adds alternate hreflang links in insertion order', function (): void {
    $meta = (new MetaBuilder())
        ->alternate('ru', 'https://example.com/ru')
        ->alternate('en', 'https://example.com/en')
        ->alternate('x-default', 'https://example.com');

    expect($meta->render())->toBe(
        "<link rel=\"alternate\" hreflang=\"ru\" href=\"https://example.com/ru\">\n"
        . "<link rel=\"alternate\" hreflang=\"en\" href=\"https://example.com/en\">\n"
        . '<link rel="alternate" hreflang="x-default" href="https://example.com">'
    );
});

it('replaces an alternate url for the same hreflang key', function (): void {
    $meta = (new MetaBuilder())
        ->alternate('ru', 'https://example.com/old')
        ->alternate('ru', 'https://example.com/new');

    expect($meta->render())
        ->toBe('<link rel="alternate" hreflang="ru" href="https://example.com/new">');
});

it('renders verification meta tags via shortcuts', function (): void {
    $meta = (new MetaBuilder())
        ->googleSiteVerification('g-token')
        ->yandexVerification('y-token')
        ->bingVerification('b-token');

    expect($meta->render())->toBe(
        "<meta name=\"google-site-verification\" content=\"g-token\">\n"
        . "<meta name=\"yandex-verification\" content=\"y-token\">\n"
        . '<meta name="msvalidate.01" content="b-token">'
    );
});

it('escapes urls in link attributes', function (): void {
    $meta = (new MetaBuilder())->canonical('https://example.com/?a=1&b="x"');

    expect($meta->render())
        ->toBe('<link rel="canonical" href="https://example.com/?a=1&amp;b=&quot;x&quot;">');
});
