<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use DateTimeInterface;

final class VideoObject extends AbstractType
{
    /** @var list<string> */
    private array $thumbnails = [];

    public function __construct()
    {
        parent::__construct('VideoObject');
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function description(string $description): self
    {
        return $this->set('description', $description);
    }

    public function contentUrl(string $url): self
    {
        return $this->set('contentUrl', $url);
    }

    public function embedUrl(string $url): self
    {
        return $this->set('embedUrl', $url);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    /**
     * Calls accumulate; a single thumbnail is written as a string.
     */
    public function thumbnailUrl(string $url): self
    {
        $this->thumbnails[] = $url;

        return $this->set('thumbnailUrl', count($this->thumbnails) === 1 ? $url : $this->thumbnails);
    }

    public function uploadDate(DateTimeInterface|string $date): self
    {
        return $this->set('uploadDate', $date);
    }

    /**
     * Seconds are written as ISO 8601 PT{n}S, a string goes as is.
     */
    public function duration(int|string $duration): self
    {
        return $this->set('duration', is_int($duration) ? 'PT'.$duration.'S' : $duration);
    }

    public function width(int $pixels): self
    {
        return $this->set('width', $pixels);
    }

    public function height(int $pixels): self
    {
        return $this->set('height', $pixels);
    }

    public function inLanguage(string $language): self
    {
        return $this->set('inLanguage', $language);
    }

    public function publisher(string|Organization $publisher): self
    {
        if (is_string($publisher)) {
            $publisher = (new Organization())->name($publisher);
        }

        return $this->set('publisher', $publisher);
    }
}
