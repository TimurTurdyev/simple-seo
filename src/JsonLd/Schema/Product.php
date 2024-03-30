<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\JsonLd\Schema;

final class Product extends AbstractType
{
    public function __construct()
    {
        parent::__construct('Product');
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function description(string $description): self
    {
        return $this->set('description', $description);
    }

    public function sku(string $sku): self
    {
        return $this->set('sku', $sku);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function brand(string|Organization $brand): self
    {
        return $this->set('brand', $brand);
    }

    public function image(string $url): self
    {
        return $this->push('image', $url);
    }

    public function offers(Offer|AggregateOffer $offers): self
    {
        return $this->set('offers', $offers);
    }

    public function aggregateRating(AggregateRating $rating): self
    {
        return $this->set('aggregateRating', $rating);
    }
}
