<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\JsonLd\JsonLdBuilder;
use TimurTurdyev\Seotools\JsonLd\Schema\AbstractType;

function customType(): AbstractType
{
    return new class ('Thing') extends AbstractType {
    };
}

it('serializes with the type as the first key', function (): void {
    $type = customType()->property('name', 'Example');

    expect($type->jsonSerialize())->toBe(['@type' => 'Thing', 'name' => 'Example']);
});

it('removes a property when set to null', function (): void {
    $type = customType()->property('name', 'Example')->property('name', null);

    expect($type->jsonSerialize())->toBe(['@type' => 'Thing']);
});

it('formats DateTimeInterface values as ISO 8601', function (): void {
    $date = new DateTimeImmutable('2024-03-16T10:00:00+00:00');
    $type = customType()->property('datePublished', $date);

    expect($type->jsonSerialize()['datePublished'])->toBe('2024-03-16T10:00:00+00:00');
});

it('encodes nested serializable values through the json-ld builder', function (): void {
    $author = customType()->property('name', 'Timur');
    $entity = customType()->property('author', $author);

    $html = (new JsonLdBuilder())->add($entity)->render();

    expect($html)->toContain('"author":{"@type":"Thing","name":"Timur"}');
});
