<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;

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

it('escapes script closing tags inside string values', function (): void {
    $jsonLd = (new JsonLdBuilder())->add([
        '@type' => 'Product',
        'description' => 'evil</script><script>alert(1)</script>',
    ]);

    $html = $jsonLd->render();

    expect($html)->not->toContain('evil</script>')
        ->and($html)->toContain('</script>');
});
