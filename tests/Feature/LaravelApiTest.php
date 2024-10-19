<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;
use TimurTurdyev\SimpleSeo\Laravel\Facades\Seo;
use TimurTurdyev\SimpleSeo\SeoManager;

it('works through the facade', function (): void {
    Seo::title('Facade Page');

    expect(Seo::render())->toContain('<title>Facade Page</title>');
});

it('returns the same scoped instance through the helper', function (): void {
    expect(seo())->toBe(app(SeoManager::class));

    seo()->title('Helper Page');

    expect(seo()->render())->toContain('<title>Helper Page</title>');
});

it('renders markup through the blade directive', function (): void {
    seo()->title('Blade Page');

    $html = Blade::render('@seo');

    expect($html)->toContain('<title>Blade Page</title>')
        ->and($html)->toContain('og:title');
});
