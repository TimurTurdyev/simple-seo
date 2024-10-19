<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;

it('renders nothing when empty', function (): void {
    expect((new OpenGraphBuilder())->render())->toBe('');
});

it('renders basic properties in fixed order', function (): void {
    $og = (new OpenGraphBuilder())
        ->title('Page')
        ->description('Desc')
        ->type('article')
        ->url('https://example.com/page')
        ->siteName('Example')
        ->locale('ru_RU');

    expect($og->render())->toBe(implode("\n", [
        '<meta property="og:title" content="Page">',
        '<meta property="og:description" content="Desc">',
        '<meta property="og:type" content="article">',
        '<meta property="og:url" content="https://example.com/page">',
        '<meta property="og:site_name" content="Example">',
        '<meta property="og:locale" content="ru_RU">',
    ]));
});

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

it('adds alternate locales in order', function (): void {
    $og = (new OpenGraphBuilder())
        ->alternateLocale('en_US')
        ->alternateLocale('de_DE');

    expect($og->render())->toBe(
        "<meta property=\"og:locale:alternate\" content=\"en_US\">\n"
        . '<meta property="og:locale:alternate" content="de_DE">'
    );
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

it('adds generic properties in order', function (): void {
    $og = (new OpenGraphBuilder())
        ->property('article:published_time', '2024-03-16T10:00:00+00:00')
        ->property('article:author', 'https://example.com/author');

    expect($og->render())->toBe(
        "<meta property=\"article:published_time\" content=\"2024-03-16T10:00:00+00:00\">\n"
        . '<meta property="article:author" content="https://example.com/author">'
    );
});

it('escapes content', function (): void {
    $og = (new OpenGraphBuilder())->title('A & B "quoted"');

    expect($og->render())
        ->toBe('<meta property="og:title" content="A &amp; B &quot;quoted&quot;">');
});

it('resets state but keeps defaults', function (): void {
    $og = (new OpenGraphBuilder(defaultSiteName: 'Example'))
        ->withoutDefaults()
        ->title('Page')
        ->image('https://example.com/a.jpg');

    $og->reset();

    expect($og->render())->toBe('<meta property="og:site_name" content="Example">');
});
