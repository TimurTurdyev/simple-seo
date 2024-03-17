<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\TwitterCard;

use TimurTurdyev\Seotools\Contracts\Section;
use TimurTurdyev\Seotools\Support\Escaper;

final class TwitterCardBuilder implements Section
{
    private ?TwitterCardType $card = null;

    private ?string $site = null;

    private ?string $creator = null;

    private ?string $title = null;

    private ?string $description = null;

    private ?string $image = null;

    private bool $withoutDefaults = false;

    public function __construct(
        private readonly ?TwitterCardType $defaultCard = null,
        private readonly ?string $defaultSite = null,
    ) {
    }

    public function card(TwitterCardType $card): self
    {
        $this->card = $card;

        return $this;
    }

    public function site(string $handle): self
    {
        $this->site = $handle;

        return $this;
    }

    public function creator(string $handle): self
    {
        $this->creator = $handle;

        return $this;
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

    public function image(string $url): self
    {
        $this->image = $url;

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
     * X falls back to Open Graph tags, so this section renders only when
     * there is explicit page state; config defaults alone produce nothing.
     */
    public function render(): string
    {
        if (! $this->hasExplicitState()) {
            return '';
        }

        $tags = [];

        $card = $this->card ?? ($this->withoutDefaults ? null : $this->defaultCard);
        $this->push($tags, 'twitter:card', $card?->value);
        $this->push($tags, 'twitter:site', $this->site ?? ($this->withoutDefaults ? null : $this->defaultSite));
        $this->push($tags, 'twitter:creator', $this->creator);
        $this->push($tags, 'twitter:title', $this->title);
        $this->push($tags, 'twitter:description', $this->description);
        $this->push($tags, 'twitter:image', $this->image);

        return implode("\n", $tags);
    }

    public function reset(): void
    {
        $this->card = null;
        $this->site = null;
        $this->creator = null;
        $this->title = null;
        $this->description = null;
        $this->image = null;
        $this->withoutDefaults = false;
    }

    private function hasExplicitState(): bool
    {
        return $this->card !== null
            || $this->site !== null
            || $this->creator !== null
            || $this->title !== null
            || $this->description !== null
            || $this->image !== null;
    }

    /**
     * @param  list<string>  $tags
     */
    private function push(array &$tags, string $name, ?string $content): void
    {
        if ($content === null || $content === '') {
            return;
        }

        $tags[] = '<meta name="' . $name . '" content="' . Escaper::html($content) . '">';
    }
}
