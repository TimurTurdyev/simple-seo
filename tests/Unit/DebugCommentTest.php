<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;
use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\SimpleSeo\SeoManager;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardType;

it('reports empty state', function (): void {
    $seo = new SeoManager(
        new MetaBuilder(),
        new OpenGraphBuilder(),
        new TwitterCardBuilder(),
        new JsonLdBuilder(),
    );

    expect($seo->debugComment())->toBe('<!-- simple-seo: empty -->');
});

it('distinguishes page values from defaults without exposing content', function (): void {
    $seo = new SeoManager(
        new MetaBuilder(defaultTitle: 'Secret Default', defaultDescription: 'Secret Desc'),
        new OpenGraphBuilder(defaultSiteName: 'Example', defaultType: 'website'),
        new TwitterCardBuilder(defaultCard: TwitterCardType::Summary),
        new JsonLdBuilder(),
    );

    $seo->title('Page Title');
    $seo->twitterCard()->site('@page');
    $seo->jsonLd()->add(['@type' => 'WebSite'])->add(['@type' => 'Organization']);

    $comment = $seo->debugComment();

    expect($comment)->toBe(
        '<!-- simple-seo: meta.title=page, meta.description=default, '
        . 'og.title=page, og.type=default, og.site_name=default, '
        . 'twitter.card=default, twitter.site=page, twitter.title=page, '
        . 'jsonld.entities=2 -->'
    )->and($comment)->not->toContain('Secret');
});
