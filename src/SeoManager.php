<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo;

use TimurTurdyev\SimpleSeo\Contracts\HasSeo;
use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;
use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;

final class SeoManager
{
    public function __construct(
        private readonly MetaBuilder $meta,
        private readonly OpenGraphBuilder $openGraph,
        private readonly TwitterCardBuilder $twitterCard,
        private readonly JsonLdBuilder $jsonLd,
    ) {
        $this->jsonLd->setPageProvider(fn (): array => [
            'title' => $this->meta->resolvedTitle(withSuffix: false),
            'description' => $this->meta->resolvedDescription(),
            'images' => $this->openGraph->imageUrls(),
            'canonical' => $this->meta->canonicalUrl(),
        ]);
    }

    public function meta(): MetaBuilder
    {
        return $this->meta;
    }

    public function openGraph(): OpenGraphBuilder
    {
        return $this->openGraph;
    }

    public function twitterCard(): TwitterCardBuilder
    {
        return $this->twitterCard;
    }

    public function jsonLd(): JsonLdBuilder
    {
        return $this->jsonLd;
    }

    /**
     * Set the page title across meta, Open Graph and Twitter sections.
     */
    public function title(string $title): self
    {
        $this->meta->title($title);
        $this->openGraph->title($title);
        $this->twitterCard->title($title);

        return $this;
    }

    /**
     * Set the page description across meta, Open Graph and Twitter sections.
     */
    public function description(string $description): self
    {
        $this->meta->description($description);
        $this->openGraph->description($description);
        $this->twitterCard->description($description);

        return $this;
    }

    /**
     * Add the page image to Open Graph and set it for the Twitter card.
     */
    public function image(string $url): self
    {
        $this->openGraph->image($url);
        $this->twitterCard->image($url);

        return $this;
    }

    /**
     * Set the canonical url and mirror it into og:url.
     */
    public function canonical(string $url): self
    {
        $this->meta->canonical($url);
        $this->openGraph->url($url);

        return $this;
    }

    /**
     * Take the whole page out of the index.
     */
    public function noindex(): self
    {
        $this->meta->noindex()->nofollow();

        return $this;
    }

    /**
     * Apply a page-level SEO payload from a DTO or any HasSeo source.
     */
    public function apply(SeoData|HasSeo $source): self
    {
        $data = $source instanceof HasSeo ? $source->toSeo() : $source;

        if ($data->title !== null) {
            $this->title($data->title);
        }

        if ($data->description !== null) {
            $this->description($data->description);
        }

        if ($data->image !== null) {
            $this->image($data->image);
        }

        if ($data->canonical !== null) {
            $this->canonical($data->canonical);
        }

        if ($data->ogType !== null) {
            $this->openGraph->type($data->ogType);
        }

        foreach ($data->jsonLd as $entity) {
            $this->jsonLd->add($entity);
        }

        return $this;
    }

    public function render(): string
    {
        $fragments = [];

        foreach ([$this->meta, $this->openGraph, $this->twitterCard, $this->jsonLd] as $section) {
            $html = $section->render();

            if ($html !== '') {
                $fragments[] = $html;
            }
        }

        return implode("\n", $fragments);
    }

    /**
     * Development helper: an HTML comment describing where every rendered
     * value came from (page or default) without exposing the values.
     */
    public function debugComment(): string
    {
        $parts = [];

        foreach ($this->meta->sources() as $field => $source) {
            $parts[] = "meta.{$field}={$source}";
        }

        foreach ($this->openGraph->sources() as $field => $source) {
            $parts[] = "og.{$field}={$source}";
        }

        foreach ($this->twitterCard->sources() as $field => $source) {
            $parts[] = "twitter.{$field}={$source}";
        }

        if ($this->jsonLd->count() > 0) {
            $parts[] = 'jsonld.entities=' . $this->jsonLd->count();
        }

        return '<!-- simple-seo: ' . ($parts === [] ? 'empty' : implode(', ', $parts)) . ' -->';
    }

    public function reset(): void
    {
        $this->meta->reset();
        $this->openGraph->reset();
        $this->twitterCard->reset();
        $this->jsonLd->reset();
    }
}
