<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('appends the debug comment when app.debug is enabled', function (): void {
    config()->set('app.debug', true);
    seo()->title('Page');

    expect(Blade::render('@seo'))->toContain('<!-- simple-seo: meta.title=page');
});

it('omits the debug comment in production', function (): void {
    config()->set('app.debug', false);
    seo()->title('Page');

    expect(Blade::render('@seo'))->not->toContain('<!-- simple-seo:');
});
