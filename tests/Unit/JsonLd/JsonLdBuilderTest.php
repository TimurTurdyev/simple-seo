<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;

it('renders nothing when empty', function (): void {
    expect((new JsonLdBuilder())->render())->toBe('');
});

it('renders a single entity as a direct object with context', function (): void {
    $jsonLd = (new JsonLdBuilder())->add([
        '@type' => 'Organization',
        'name' => 'Example',
    ]);

    expect($jsonLd->render())->toBe(
        '<script type="application/ld+json">'
        . '{"@context":"https://schema.org","@type":"Organization","name":"Example"}'
        . '</script>'
    );
});

it('renders multiple entities as a graph without overwriting any of them', function (): void {
    $jsonLd = (new JsonLdBuilder())
        ->add(['@type' => 'Product', 'name' => 'Chair'])
        ->add(['@type' => 'BreadcrumbList', 'name' => 'Crumbs']);

    expect($jsonLd->render())->toBe(
        '<script type="application/ld+json">'
        . '{"@context":"https://schema.org","@graph":['
        . '{"@type":"Product","name":"Chair"},'
        . '{"@type":"BreadcrumbList","name":"Crumbs"}'
        . ']}'
        . '</script>'
    );
});

it('keeps unicode content unescaped', function (): void {
    $jsonLd = (new JsonLdBuilder())->add(['@type' => 'Product', 'name' => 'Кресло']);

    expect($jsonLd->render())->toContain('"name":"Кресло"');
});

it('escapes script closing tags inside string values', function (): void {
    $jsonLd = (new JsonLdBuilder())->add([
        '@type' => 'Product',
        'description' => 'evil</script><script>alert(1)</script>',
    ]);

    $html = $jsonLd->render();

    expect($html)->not->toContain('evil</script>')
        ->and($html)->toContain('</script>');
});

it('accepts JsonSerializable entities', function (): void {
    $entity = new class () implements JsonSerializable {
        /** @return array<string, string> */
        public function jsonSerialize(): array
        {
            return ['@type' => 'Person', 'name' => 'Timur'];
        }
    };

    $jsonLd = (new JsonLdBuilder())->add($entity);

    expect($jsonLd->render())->toContain('"@type":"Person"');
});

it('clears entities on reset', function (): void {
    $jsonLd = (new JsonLdBuilder())->add(['@type' => 'Product']);
    $jsonLd->reset();

    expect($jsonLd->render())->toBe('');
});
