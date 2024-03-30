<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\JsonLd\Schema;

final class Offer extends AbstractType
{
    public function __construct()
    {
        parent::__construct('Offer');
    }

    public function price(float|int|string $price): self
    {
        return $this->set('price', $price);
    }

    public function priceCurrency(string $currency): self
    {
        return $this->set('priceCurrency', $currency);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function availability(string $availability): self
    {
        return $this->set('availability', $availability);
    }

    public function inStock(): self
    {
        return $this->availability('https://schema.org/InStock');
    }

    public function outOfStock(): self
    {
        return $this->availability('https://schema.org/OutOfStock');
    }
}
