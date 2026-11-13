<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Blade;

it('omits the debug comment in production', function (): void {
    config()->set('app.debug', false);
    seo()->title('Page');

    expect(Blade::render('@seo'))->not->toContain('<!-- simple-seo:');
});
