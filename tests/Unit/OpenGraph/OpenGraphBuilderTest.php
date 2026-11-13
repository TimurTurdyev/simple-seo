<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;

it('falls back to config defaults at render time', function (): void {
    $og = new OpenGraphBuilder(defaultSiteName: 'Example', defaultType: 'website', defaultLocale: 'ru_RU');

    expect($og->render())->toBe(implode("\n", [
        '<meta property="og:type" content="website">',
        '<meta property="og:site_name" content="Example">',
        '<meta property="og:locale" content="ru_RU">',
    ]));
});

it('suppresses defaults with withoutDefaults', function (): void {
    $og = (new OpenGraphBuilder(defaultSiteName: 'Example', defaultType: 'website'))
        ->withoutDefaults();

    expect($og->render())->toBe('');
});

it('adds multiple images with metadata', function (): void {
    $og = (new OpenGraphBuilder())
        ->image('https://example.com/a.jpg', width: 1200, height: 630, alt: 'Cover')
        ->image('https://example.com/b.jpg');

    expect($og->render())->toBe(implode("\n", [
        '<meta property="og:image" content="https://example.com/a.jpg">',
        '<meta property="og:image:width" content="1200">',
        '<meta property="og:image:height" content="630">',
        '<meta property="og:image:alt" content="Cover">',
        '<meta property="og:image" content="https://example.com/b.jpg">',
    ]));
});

it('resets state but keeps defaults', function (): void {
    $og = (new OpenGraphBuilder(defaultSiteName: 'Example'))
        ->withoutDefaults()
        ->title('Page')
        ->image('https://example.com/a.jpg');

    $og->reset();

    expect($og->render())->toBe('<meta property="og:site_name" content="Example">');
});
