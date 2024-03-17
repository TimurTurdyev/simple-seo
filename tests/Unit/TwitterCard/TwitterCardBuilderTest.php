<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\TwitterCard\TwitterCardBuilder;
use TimurTurdyev\Seotools\TwitterCard\TwitterCardType;

it('renders nothing when empty', function (): void {
    expect((new TwitterCardBuilder())->render())->toBe('');
});

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

it('renders all fields in fixed order', function (): void {
    $twitter = (new TwitterCardBuilder())
        ->card(TwitterCardType::Summary)
        ->site('@site')
        ->creator('@author')
        ->title('Page')
        ->description('Desc')
        ->image('https://example.com/a.jpg');

    expect($twitter->render())->toBe(implode("\n", [
        '<meta name="twitter:card" content="summary">',
        '<meta name="twitter:site" content="@site">',
        '<meta name="twitter:creator" content="@author">',
        '<meta name="twitter:title" content="Page">',
        '<meta name="twitter:description" content="Desc">',
        '<meta name="twitter:image" content="https://example.com/a.jpg">',
    ]));
});

it('suppresses defaults with withoutDefaults', function (): void {
    $twitter = (new TwitterCardBuilder(
        defaultCard: TwitterCardType::Summary,
        defaultSite: '@example',
    ))->withoutDefaults()->title('Page');

    expect($twitter->render())->toBe('<meta name="twitter:title" content="Page">');
});

it('escapes content', function (): void {
    $twitter = (new TwitterCardBuilder())->title('A & "B"');

    expect($twitter->render())
        ->toBe('<meta name="twitter:title" content="A &amp; &quot;B&quot;">');
});

it('resets explicit state', function (): void {
    $twitter = (new TwitterCardBuilder())->card(TwitterCardType::Summary)->title('Page');
    $twitter->reset();

    expect($twitter->render())->toBe('');
});
