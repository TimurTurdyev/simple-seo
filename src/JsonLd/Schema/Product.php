<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

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

    public function model(string $model): self
    {
        return $this->set('model', $model);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function brand(string|Organization|Brand $brand): self
    {
        return $this->set('brand', $brand);
    }

    public function category(string $category): self
    {
        return $this->set('category', $category);
    }

    public function color(string $color): self
    {
        return $this->set('color', $color);
    }

    public function material(string $material): self
    {
        return $this->set('material', $material);
    }

    public function size(string $size): self
    {
        return $this->set('size', $size);
    }

    public function image(string $url): self
    {
        return $this->push('image', $url);
    }

    public function additionalProperty(string $name, string|int|float $value): self
    {
        return $this->push('additionalProperty', [
            '@type' => 'PropertyValue',
            'name' => $name,
            'value' => $value,
        ]);
    }

    public function offers(Offer|AggregateOffer $offers): self
    {
        return $this->set('offers', $offers);
    }

    public function aggregateRating(AggregateRating $rating): self
    {
        return $this->set('aggregateRating', $rating);
    }

    /**
     * Ties a variant on its own page to its ProductGroup.
     */
    public function inProductGroupWithID(string $id): self
    {
        return $this->set('inProductGroupWithID', $id);
    }
}
