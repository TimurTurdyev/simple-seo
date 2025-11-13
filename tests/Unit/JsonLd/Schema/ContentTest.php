<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;
use TimurTurdyev\SimpleSeo\Support\Exceptions\SimpleSeoException;

it('builds an article with a person author and iso dates', function (): void {
    $article = Schema::article()
        ->headline('Как выбрать офисное кресло')
        ->url('https://example.com/blog/chairs')
        ->datePublished(new DateTimeImmutable('2024-03-12T09:00:00+03:00'))
        ->author(Schema::person()->name('Timur')->url('https://example.com/authors/timur'));

    $html = (new JsonLdBuilder())->add($article)->render();

    expect($html)->toContain('"@type":"Article"')
        ->and($html)->toContain('"datePublished":"2024-03-12T09:00:00+03:00"')
        ->and($html)->toContain('"author":{"@type":"Person","name":"Timur","url":"https://example.com/authors/timur"}');
});

it('builds a blog posting with the BlogPosting type', function (): void {
    expect(Schema::blogPosting()->headline('Post')->jsonSerialize())
        ->toBe(['@type' => 'BlogPosting', 'headline' => 'Post']);
});

it('numbers breadcrumb positions automatically and allows a final crumb without url', function (): void {
    $crumbs = Schema::breadcrumbs()
        ->item('Главная', 'https://example.com')
        ->item('Каталог', 'https://example.com/catalog')
        ->item('Столы');

    expect($crumbs->jsonSerialize())->toBe([
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => 'https://example.com'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Каталог', 'item' => 'https://example.com/catalog'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Столы'],
        ],
    ]);
});

it('collects person sameAs links', function (): void {
    $person = Schema::person()->name('Timur')->sameAs('https://x.com/timur')->sameAs('https://github.com/timur');

    expect($person->jsonSerialize()['sameAs'])
        ->toBe(['https://x.com/timur', 'https://github.com/timur']);
});

it('adds breadcrumbs from a list with name or title and url or href', function (): void {
    $crumbs = Schema::breadcrumbs()
        ->items([
            ['title' => 'Главная', 'href' => 'https://example.com'],
            ['name' => 'Каталог', 'url' => 'https://example.com/catalog'],
            ['name' => 'Без ссылки'],
        ])
        ->item('Столы');

    expect($crumbs->toArray()['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Главная', 'item' => 'https://example.com'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Каталог', 'item' => 'https://example.com/catalog'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Без ссылки'],
        ['@type' => 'ListItem', 'position' => 4, 'name' => 'Столы'],
    ]);
});

it('refuses a breadcrumb without a name', function (): void {
    Schema::breadcrumbs()->items([['url' => 'https://example.com']]);
})->throws(SimpleSeoException::class);

it('numbers an item list after the offset, skips repeated urls and counts the whole list', function (): void {
    $list = Schema::itemList()
        ->items([
            ['url' => 'https://example.com/a', 'name' => 'A'],
            ['url' => 'https://example.com/b', 'name' => 'B'],
            ['url' => 'https://example.com/a', 'name' => 'A again'],
        ])
        ->item('https://example.com/c')
        ->offset(20);

    expect($list->toArray())->toBe([
        '@type' => 'ItemList',
        'numberOfItems' => 3,
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 21, 'url' => 'https://example.com/a', 'name' => 'A'],
            ['@type' => 'ListItem', 'position' => 22, 'url' => 'https://example.com/b', 'name' => 'B'],
            ['@type' => 'ListItem', 'position' => 23, 'url' => 'https://example.com/c'],
        ],
    ])->and($list->numberOfItems(140)->toArray()['numberOfItems'])->toBe(140);
});

it('builds a video with duration in seconds and a single thumbnail as a string', function (): void {
    $video = Schema::videoObject()
        ->name('Обзор')
        ->thumbnailUrl('https://example.com/poster.jpg')
        ->uploadDate(new DateTimeImmutable('2025-10-01T12:30:00+03:00'))
        ->duration(47)
        ->publisher('Example');

    expect($video->toArray())->toBe([
        '@type' => 'VideoObject',
        'name' => 'Обзор',
        'thumbnailUrl' => 'https://example.com/poster.jpg',
        'uploadDate' => '2025-10-01T12:30:00+03:00',
        'duration' => 'PT47S',
        'publisher' => ['@type' => 'Organization', 'name' => 'Example'],
    ])
        ->and($video->thumbnailUrl('https://example.com/poster-2.jpg')->toArray()['thumbnailUrl'])
        ->toBe(['https://example.com/poster.jpg', 'https://example.com/poster-2.jpg'])
        ->and($video->duration('PT1M30S')->toArray()['duration'])->toBe('PT1M30S');
});
