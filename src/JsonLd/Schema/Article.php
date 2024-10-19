<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use DateTimeInterface;

final class Article extends AbstractType
{
    /**
     * @param  'Article'|'BlogPosting'  $type
     */
    public function __construct(string $type = 'Article')
    {
        parent::__construct($type);
    }

    public function headline(string $headline): self
    {
        return $this->set('headline', $headline);
    }

    public function description(string $description): self
    {
        return $this->set('description', $description);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function image(string $url): self
    {
        return $this->push('image', $url);
    }

    public function datePublished(DateTimeInterface|string $date): self
    {
        return $this->set('datePublished', $date);
    }

    public function dateModified(DateTimeInterface|string $date): self
    {
        return $this->set('dateModified', $date);
    }

    public function author(Person|string $author): self
    {
        return $this->set('author', $author);
    }
}
