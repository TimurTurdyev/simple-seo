<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\OpenGraph;

use TimurTurdyev\SimpleSeo\Contracts\Section;
use TimurTurdyev\SimpleSeo\Support\Escaper;

final class OpenGraphBuilder implements Section
{
    private ?string $title = null;

    private ?string $description = null;

    private ?string $type = null;

    private ?string $url = null;

    private ?string $siteName = null;

    private ?string $locale = null;

    private bool $withoutDefaults = false;

    /** @var list<string> */
    private array $alternateLocales = [];

    /** @var list<array{url: string, width: ?int, height: ?int, alt: ?string}> */
    private array $images = [];

    /** @var list<array{property: string, content: string}> */
    private array $properties = [];

    public function __construct(
        private readonly ?string $defaultSiteName = null,
        private readonly ?string $defaultType = null,
        private readonly ?string $defaultLocale = null,
    ) {
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function siteName(string $siteName): self
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function locale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function alternateLocale(string $locale): self
    {
        $this->alternateLocales[] = $locale;

        return $this;
    }

    public function image(string $url, ?int $width = null, ?int $height = null, ?string $alt = null): self
    {
        $this->images[] = ['url' => $url, 'width' => $width, 'height' => $height, 'alt' => $alt];

        return $this;
    }

    /**
     * Add any raw Open Graph style property, e.g. article:published_time.
     */
    public function property(string $property, string $content): self
    {
        $this->properties[] = ['property' => $property, 'content' => $content];

        return $this;
    }

    /**
     * Disable config defaults for this page; explicit values still render.
     */
    public function withoutDefaults(): self
    {
        $this->withoutDefaults = true;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function imageUrls(): array
    {
        return array_map(static fn (array $image): string => $image['url'], $this->images);
    }

    public function render(): string
    {
        $tags = [];

        $this->push($tags, 'og:title', $this->title);
        $this->push($tags, 'og:description', $this->description);
        $this->push($tags, 'og:type', $this->resolve($this->type, $this->defaultType));
        $this->push($tags, 'og:url', $this->url);
        $this->push($tags, 'og:site_name', $this->resolve($this->siteName, $this->defaultSiteName));
        $this->push($tags, 'og:locale', $this->resolve($this->locale, $this->defaultLocale));

        foreach ($this->alternateLocales as $locale) {
            $this->push($tags, 'og:locale:alternate', $locale);
        }

        foreach ($this->images as $image) {
            $this->push($tags, 'og:image', $image['url']);
            $this->push($tags, 'og:image:width', $image['width'] === null ? null : (string) $image['width']);
            $this->push($tags, 'og:image:height', $image['height'] === null ? null : (string) $image['height']);
            $this->push($tags, 'og:image:alt', $image['alt']);
        }

        foreach ($this->properties as $property) {
            $this->push($tags, $property['property'], $property['content']);
        }

        return implode("\n", $tags);
    }

    public function reset(): void
    {
        $this->title = null;
        $this->description = null;
        $this->type = null;
        $this->url = null;
        $this->siteName = null;
        $this->locale = null;
        $this->withoutDefaults = false;
        $this->alternateLocales = [];
        $this->images = [];
        $this->properties = [];
    }

    /**
     * Report where each rendered scalar value came from: 'page' or 'default'.
     *
     * @return array<string, string>
     */
    public function sources(): array
    {
        $sources = [];

        foreach (['title' => $this->title, 'description' => $this->description, 'url' => $this->url] as $field => $value) {
            if ($value !== null && $value !== '') {
                $sources[$field] = 'page';
            }
        }

        $resolved = [
            'type' => [$this->type, $this->defaultType],
            'site_name' => [$this->siteName, $this->defaultSiteName],
            'locale' => [$this->locale, $this->defaultLocale],
        ];

        foreach ($resolved as $field => [$value, $default]) {
            $final = $this->resolve($value, $default);

            if ($final !== null && $final !== '') {
                $sources[$field] = $value !== null ? 'page' : 'default';
            }
        }

        return $sources;
    }

    /**
     * @param  list<string>  $tags
     */
    private function push(array &$tags, string $property, ?string $content): void
    {
        if ($content === null || $content === '') {
            return;
        }

        $tags[] = '<meta property="' . Escaper::html($property) . '" content="' . Escaper::html($content) . '">';
    }

    private function resolve(?string $value, ?string $default): ?string
    {
        return $value ?? ($this->withoutDefaults ? null : $default);
    }
}
