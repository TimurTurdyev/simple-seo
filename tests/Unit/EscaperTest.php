<?php

declare(strict_types=1);

use TimurTurdyev\SimpleSeo\Support\Escaper;

it('escapes html tags', function (): void {
    expect(Escaper::html('<script>alert(1)</script>'))
        ->toBe('&lt;script&gt;alert(1)&lt;/script&gt;');
});

it('escapes double and single quotes', function (): void {
    expect(Escaper::html('He said "hi" & \'bye\''))
        ->toBe('He said &quot;hi&quot; &amp; &apos;bye&apos;');
});

it('keeps utf-8 content intact', function (): void {
    expect(Escaper::html('Кириллица и эмодзи 🚀'))
        ->toBe('Кириллица и эмодзи 🚀');
});

it('escapes ampersands', function (): void {
    expect(Escaper::html('a & b &amp; c'))
        ->toBe('a &amp; b &amp;amp; c');
});
