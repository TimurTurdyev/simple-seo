<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\SeoManager;

it('resolves the seo manager from the container', function (): void {
    expect(app(SeoManager::class))->toBeInstanceOf(SeoManager::class);
});

it('passes config defaults into the builders', function (): void {
    config()->set('simple-seo.meta.title.default', 'Default Title');
    config()->set('simple-seo.meta.title.suffix', ' - Site');
    config()->set('simple-seo.open_graph.site_name', 'Example');
    config()->set('simple-seo.twitter.card', 'summary_large_image');
    config()->set('simple-seo.twitter.site', '@example');

    $html = app(SeoManager::class)->render();

    expect($html)->toContain('<title>Default Title - Site</title>')
        ->and($html)->toContain('<meta property="og:site_name" content="Example">')
        ->and($html)->toContain('<meta property="og:type" content="website">')
        ->and($html)->not->toContain('twitter:card');
});

it('resolves the same scoped instance within a request', function (): void {
    $first = app(SeoManager::class);
    $second = app(SeoManager::class);

    expect($first)->toBe($second);
});

it('starts clean after scoped instances are flushed like octane does between requests', function (): void {
    app(SeoManager::class)->title('Request One');

    app()->forgetScopedInstances();

    expect(app(SeoManager::class)->render())->not->toContain('Request One');
});
