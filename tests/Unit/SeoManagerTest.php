<?php

declare(strict_types=1);

use TimurTurdyev\Seotools\Contracts\Section;
use TimurTurdyev\Seotools\SeoManager;
use TimurTurdyev\Seotools\Support\Exceptions\SeotoolsException;

function fakeSection(string $html): Section
{
    return new class ($html) implements Section {
        public function __construct(private string $html)
        {
        }

        public function render(): string
        {
            return $this->html;
        }

        public function reset(): void
        {
            $this->html = '';
        }
    };
}

it('renders sections in registration order', function (): void {
    $manager = new SeoManager([
        'meta' => fakeSection('<title>Hello</title>'),
        'og' => fakeSection('<meta property="og:title" content="Hello">'),
    ]);

    expect($manager->render())->toBe(
        "<title>Hello</title>\n<meta property=\"og:title\" content=\"Hello\">"
    );
});

it('skips sections that render nothing', function (): void {
    $manager = new SeoManager([
        'meta' => fakeSection(''),
        'og' => fakeSection('<meta property="og:type" content="website">'),
    ]);

    expect($manager->render())->toBe('<meta property="og:type" content="website">');
});

it('returns a registered section by name', function (): void {
    $meta = fakeSection('<title>X</title>');
    $manager = new SeoManager(['meta' => $meta]);

    expect($manager->section('meta'))->toBe($meta);
});

it('throws on an unknown section name', function (): void {
    $manager = new SeoManager([]);

    $manager->section('missing');
})->throws(SeotoolsException::class, 'Unknown SEO section [missing].');

it('resets every section', function (): void {
    $manager = new SeoManager([
        'meta' => fakeSection('<title>A</title>'),
        'og' => fakeSection('<meta property="og:type" content="website">'),
    ]);

    $manager->reset();

    expect($manager->render())->toBe('');
});
