<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Meta;

use TimurTurdyev\SimpleSeo\Contracts\Section;
use TimurTurdyev\SimpleSeo\Support\Tag;

final class MetaBuilder implements Section
{
    private ?string $title = null;

    private ?string $description = null;

    private bool $withoutDefaults = false;

    /** @var array<string, string|null> Robots directives in insertion order (value for parameterized ones). */
    private array $robots = [];

    private ?string $canonical = null;

    private ?string $prev = null;

    private ?string $next = null;

    /** @var array<string, string> hreflang => url, in insertion order. */
    private array $alternates = [];

    /** @var array<string, string> meta name => content, in insertion order. */
    private array $verifications = [];

    public function __construct(
        private readonly ?string $defaultTitle = null,
        private readonly ?string $titleSuffix = null,
        private readonly ?string $defaultDescription = null,
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

    /**
     * Disable config defaults for this page; explicit values still render.
     */
    public function withoutDefaults(): self
    {
        $this->withoutDefaults = true;

        return $this;
    }

    public function noindex(): self
    {
        $this->robots['noindex'] = null;

        return $this;
    }

    public function nofollow(): self
    {
        $this->robots['nofollow'] = null;

        return $this;
    }

    public function nosnippet(): self
    {
        $this->robots['nosnippet'] = null;

        return $this;
    }

    public function noarchive(): self
    {
        $this->robots['noarchive'] = null;

        return $this;
    }

    public function maxSnippet(int $chars): self
    {
        $this->robots['max-snippet'] = (string) $chars;

        return $this;
    }

    public function maxImagePreview(MaxImagePreview $size): self
    {
        $this->robots['max-image-preview'] = $size->value;

        return $this;
    }

    public function canonical(string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    public function prev(string $url): self
    {
        $this->prev = $url;

        return $this;
    }

    public function next(string $url): self
    {
        $this->next = $url;

        return $this;
    }

    public function alternate(string $hreflang, string $url): self
    {
        $this->alternates[$hreflang] = $url;

        return $this;
    }

    public function verification(string $name, string $content): self
    {
        $this->verifications[$name] = $content;

        return $this;
    }

    public function googleSiteVerification(string $content): self
    {
        return $this->verification('google-site-verification', $content);
    }

    public function yandexVerification(string $content): self
    {
        return $this->verification('yandex-verification', $content);
    }

    public function bingVerification(string $content): self
    {
        return $this->verification('msvalidate.01', $content);
    }

    public function render(): string
    {
        return Tag::join($this->tags());
    }

    /**
     * The section as data, in output order; render() prints the same tags.
     *
     * @return list<Tag>
     */
    public function tags(): array
    {
        $tags = [];

        $title = $this->resolvedTitle();
        if ($title !== null) {
            $tags[] = new Tag('title', text: $title);
        }

        $description = $this->resolvedDescription();
        if ($description !== null) {
            $tags[] = new Tag('meta', ['name' => 'description', 'content' => $description]);
        }

        $robots = $this->robotsDirective();
        if ($robots !== null) {
            $tags[] = new Tag('meta', ['name' => 'robots', 'content' => $robots]);
        }

        if ($this->isFilled($this->canonical)) {
            $tags[] = new Tag('link', ['rel' => 'canonical', 'href' => $this->canonical]);
        }

        if ($this->isFilled($this->prev)) {
            $tags[] = new Tag('link', ['rel' => 'prev', 'href' => $this->prev]);
        }

        if ($this->isFilled($this->next)) {
            $tags[] = new Tag('link', ['rel' => 'next', 'href' => $this->next]);
        }

        foreach ($this->alternates as $hreflang => $url) {
            $tags[] = new Tag('link', ['rel' => 'alternate', 'hreflang' => (string) $hreflang, 'href' => $url]);
        }

        foreach ($this->verifications as $name => $content) {
            $tags[] = new Tag('meta', ['name' => (string) $name, 'content' => $content]);
        }

        return $tags;
    }

    /** The robots directive line, or null when the page sets none. */
    public function robotsDirective(): ?string
    {
        return $this->robots === [] ? null : $this->robotsContent();
    }

    public function reset(): void
    {
        $this->title = null;
        $this->description = null;
        $this->withoutDefaults = false;
        $this->robots = [];
        $this->canonical = null;
        $this->prev = null;
        $this->next = null;
        $this->alternates = [];
        $this->verifications = [];
    }

    /**
     * Report where each rendered value came from: 'page' or 'default'.
     *
     * @return array<string, string>
     */
    public function sources(): array
    {
        $sources = [];

        if ($this->resolve($this->title, $this->defaultTitle) !== null) {
            $sources['title'] = $this->title !== null ? 'page' : 'default';
        }

        if ($this->resolve($this->description, $this->defaultDescription) !== null) {
            $sources['description'] = $this->description !== null ? 'page' : 'default';
        }

        return $sources;
    }

    /**
     * The title as it will render: page value or config default.
     */
    public function resolvedTitle(bool $withSuffix = true): ?string
    {
        $title = $this->resolve($this->title, $this->defaultTitle);

        if ($title === null) {
            return null;
        }

        return $withSuffix ? $title . ($this->titleSuffix ?? '') : $title;
    }

    /**
     * The description as it will render: page value or config default.
     */
    public function resolvedDescription(): ?string
    {
        return $this->resolve($this->description, $this->defaultDescription);
    }

    public function canonicalUrl(): ?string
    {
        return $this->isFilled($this->canonical) ? $this->canonical : null;
    }

    /**
     * Resolve a scalar against its config default at render time.
     *
     * An explicit empty string is kept as state (it suppresses the tag) and
     * does not fall back to the default; only a missing value does.
     */
    private function resolve(?string $value, ?string $default): ?string
    {
        $resolved = $value ?? ($this->withoutDefaults ? null : $default);

        return ($resolved === null || $resolved === '') ? null : $resolved;
    }

    private function robotsContent(): string
    {
        $directives = [];

        foreach ($this->robots as $name => $value) {
            $directives[] = $value === null ? $name : "{$name}:{$value}";
        }

        return implode(', ', $directives);
    }

    /**
     * @phpstan-assert-if-true string $value
     */
    private function isFilled(?string $value): bool
    {
        return $value !== null && $value !== '';
    }
}
