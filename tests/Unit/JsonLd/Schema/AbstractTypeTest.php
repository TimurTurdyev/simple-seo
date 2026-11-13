<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\Schema\AbstractType;

function customType(): AbstractType
{
    return new class ('Thing') extends AbstractType {
    };
}

it('removes a property when set to null', function (): void {
    $type = customType()->property('name', 'Example')->property('name', null);

    expect($type->jsonSerialize())->toBe(['@type' => 'Thing']);
});

it('formats DateTimeInterface values as ISO 8601', function (): void {
    $date = new DateTimeImmutable('2024-03-16T10:00:00+00:00');
    $type = customType()->property('datePublished', $date);

    expect($type->jsonSerialize()['datePublished'])->toBe('2024-03-16T10:00:00+00:00');
});
