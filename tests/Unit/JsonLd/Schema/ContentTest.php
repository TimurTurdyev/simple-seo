<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\JsonLd\Schema\Schema;

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
