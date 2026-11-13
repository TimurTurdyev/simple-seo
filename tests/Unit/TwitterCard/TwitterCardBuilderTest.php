<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardType;

it('renders nothing from config defaults alone because X falls back to og tags', function (): void {
    $twitter = new TwitterCardBuilder(
        defaultCard: TwitterCardType::Summary,
        defaultSite: '@example',
    );

    expect($twitter->render())->toBe('');
});

it('renders defaults once explicit state exists', function (): void {
    $twitter = (new TwitterCardBuilder(
        defaultCard: TwitterCardType::SummaryLargeImage,
        defaultSite: '@example',
    ))->title('Page');

    expect($twitter->render())->toBe(implode("\n", [
        '<meta name="twitter:card" content="summary_large_image">',
        '<meta name="twitter:site" content="@example">',
        '<meta name="twitter:title" content="Page">',
    ]));
});

it('suppresses defaults with withoutDefaults', function (): void {
    $twitter = (new TwitterCardBuilder(
        defaultCard: TwitterCardType::Summary,
        defaultSite: '@example',
    ))->withoutDefaults()->title('Page');

    expect($twitter->render())->toBe('<meta name="twitter:title" content="Page">');
});
