<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\Meta\MaxImagePreview;
use TimurTurdyev\Seotools\Meta\MetaBuilder;

it('renders all tags in the fixed section order', function (): void {
    $meta = (new MetaBuilder(titleSuffix: ' - Shop'))
        ->title('Page')
        ->description('Description')
        ->noindex()
        ->maxImagePreview(MaxImagePreview::Large)
        ->canonical('https://example.com/page')
        ->prev('https://example.com/page?p=1')
        ->next('https://example.com/page?p=3')
        ->alternate('ru', 'https://example.com/ru')
        ->googleSiteVerification('token');

    expect($meta->render())->toBe(implode("\n", [
        '<title>Page - Shop</title>',
        '<meta name="description" content="Description">',
        '<meta name="robots" content="noindex, max-image-preview:large">',
        '<link rel="canonical" href="https://example.com/page">',
        '<link rel="prev" href="https://example.com/page?p=1">',
        '<link rel="next" href="https://example.com/page?p=3">',
        '<link rel="alternate" hreflang="ru" href="https://example.com/ru">',
        '<meta name="google-site-verification" content="token">',
    ]));
});
