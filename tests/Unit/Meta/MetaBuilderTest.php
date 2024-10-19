<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;

it('renders an explicit title', function (): void {
    $meta = (new MetaBuilder())->title('Hello');

    expect($meta->render())->toBe('<title>Hello</title>');
});

it('falls back to the default title at render time', function (): void {
    $meta = new MetaBuilder(defaultTitle: 'Default Title');

    expect($meta->render())->toBe('<title>Default Title</title>');
});

it('appends the title suffix to explicit and default titles', function (): void {
    $meta = new MetaBuilder(defaultTitle: 'Default', titleSuffix: ' - Site');

    expect($meta->render())->toBe('<title>Default - Site</title>');

    $meta->title('Page');

    expect($meta->render())->toBe('<title>Page - Site</title>');
});

it('suppresses defaults with withoutDefaults', function (): void {
    $meta = (new MetaBuilder(defaultTitle: 'Default', defaultDescription: 'Desc'))
        ->withoutDefaults();

    expect($meta->render())->toBe('');
});

it('renders explicit values even with withoutDefaults', function (): void {
    $meta = (new MetaBuilder(defaultTitle: 'Default'))
        ->withoutDefaults()
        ->title('Page');

    expect($meta->render())->toBe('<title>Page</title>');
});

it('replaces the title on repeated calls', function (): void {
    $meta = (new MetaBuilder())->title('First')->title('Second');

    expect($meta->render())->toBe('<title>Second</title>');
});

it('does not render a tag for an explicit empty string and does not fall back', function (): void {
    $meta = (new MetaBuilder(defaultTitle: 'Default'))->title('');

    expect($meta->render())->toBe('');
});

it('renders the description meta tag with default fallback', function (): void {
    $meta = new MetaBuilder(defaultDescription: 'Default description');

    expect($meta->render())->toBe('<meta name="description" content="Default description">');

    $meta->description('Page description');

    expect($meta->render())->toBe('<meta name="description" content="Page description">');
});

it('escapes user content in title and description', function (): void {
    $meta = (new MetaBuilder())
        ->title('<script>alert(1)</script>')
        ->description('He said "hi" & left');

    expect($meta->render())->toBe(
        "<title>&lt;script&gt;alert(1)&lt;/script&gt;</title>\n"
        . '<meta name="description" content="He said &quot;hi&quot; &amp; left">'
    );
});

it('resets page state but keeps constructor defaults', function (): void {
    $meta = (new MetaBuilder(defaultTitle: 'Default'))
        ->withoutDefaults()
        ->title('Page')
        ->description('Desc');

    $meta->reset();

    expect($meta->render())->toBe('<title>Default</title>');
});
