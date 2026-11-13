<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;

it('replaces an alternate url for the same hreflang key', function (): void {
    $meta = (new MetaBuilder())
        ->alternate('ru', 'https://example.com/old')
        ->alternate('ru', 'https://example.com/new');

    expect($meta->render())
        ->toBe('<link rel="alternate" hreflang="ru" href="https://example.com/new">');
});
