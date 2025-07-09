<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;
use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\SimpleSeo\SeoManager;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;

function taggedManager(): SeoManager
{
    $seo = new SeoManager(new MetaBuilder(), new OpenGraphBuilder(), new TwitterCardBuilder(), new JsonLdBuilder());

    $seo->title('Стол & стул')
        ->description('Офисная мебель')
        ->canonical('https://example.test/stol')
        ->image('https://example.test/stol.webp')
        ->noindex();
    $seo->jsonLd()->add(['@type' => 'Product', 'name' => 'Стол </script>']);

    return $seo;
}

it('gives the same tags as data that render() prints, values unescaped', function (): void {
    $tags = taggedManager()->tags();

    expect($tags[0])->toBe(['tag' => 'title', 'attrs' => [], 'text' => 'Стол & стул'])
        ->and($tags[1])->toBe(['tag' => 'meta', 'attrs' => ['name' => 'description', 'content' => 'Офисная мебель']])
        ->and($tags[2])->toBe(['tag' => 'meta', 'attrs' => ['name' => 'robots', 'content' => 'noindex, nofollow']])
        ->and($tags[3])->toBe(['tag' => 'link', 'attrs' => ['rel' => 'canonical', 'href' => 'https://example.test/stol']])
        ->and(count($tags))->toBe(count(explode("\n", taggedManager()->render())));
});

it('keeps json-ld safe for a script element in the data form too', function (): void {
    $script = array_values(array_filter(taggedManager()->tags(), static fn (array $tag): bool => $tag['tag'] === 'script'))[0];

    expect($script['attrs'])->toBe(['type' => 'application/ld+json'])
        ->and($script['text'] ?? '')->not->toContain('</script>');
});

it('describes the head by meaning', function (): void {
    $head = taggedManager()->toArray();

    expect($head['title'])->toBe('Стол & стул')
        ->and($head['canonical'])->toBe('https://example.test/stol')
        ->and($head['robots'])->toBe('noindex, nofollow')
        ->and($head['openGraph'])->toContain(['property' => 'og:image', 'content' => 'https://example.test/stol.webp'])
        ->and($head['twitter'])->toContain(['name' => 'twitter:title', 'content' => 'Стол & стул'])
        ->and($head['jsonLd'])->toBe(['@context' => 'https://schema.org', '@type' => 'Product', 'name' => 'Стол </script>']);
});
