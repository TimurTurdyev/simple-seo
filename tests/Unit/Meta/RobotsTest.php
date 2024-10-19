<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\Meta\MaxImagePreview;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;

it('renders no robots tag without directives', function (): void {
    expect((new MetaBuilder())->render())->toBe('');
});

it('renders single robots directives', function (): void {
    expect((new MetaBuilder())->noindex()->render())
        ->toBe('<meta name="robots" content="noindex">');

    expect((new MetaBuilder())->nosnippet()->render())
        ->toBe('<meta name="robots" content="nosnippet">');

    expect((new MetaBuilder())->noarchive()->render())
        ->toBe('<meta name="robots" content="noarchive">');
});

it('combines robots directives in call order', function (): void {
    $meta = (new MetaBuilder())
        ->noindex()
        ->nofollow()
        ->maxSnippet(50)
        ->maxImagePreview(MaxImagePreview::Large);

    expect($meta->render())->toBe(
        '<meta name="robots" content="noindex, nofollow, max-snippet:50, max-image-preview:large">'
    );
});

it('keeps directives idempotent on repeated calls', function (): void {
    $meta = (new MetaBuilder())->noindex()->noindex()->nofollow();

    expect($meta->render())->toBe('<meta name="robots" content="noindex, nofollow">');
});

it('replaces the max-snippet value on repeated calls', function (): void {
    $meta = (new MetaBuilder())->maxSnippet(50)->maxSnippet(120);

    expect($meta->render())->toBe('<meta name="robots" content="max-snippet:120">');
});

it('clears robots directives on reset', function (): void {
    $meta = (new MetaBuilder())->noindex();
    $meta->reset();

    expect($meta->render())->toBe('');
});
