<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use DateTimeInterface;

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

    public function availability(Availability|string $availability): self
    {
        return $this->set('availability', $availability instanceof Availability ? $availability->value : $availability);
    }

    public function inStock(): self
    {
        return $this->availability(Availability::InStock);
    }

    public function outOfStock(): self
    {
        return $this->availability(Availability::OutOfStock);
    }

    /**
     * schema.org types it as a Date, so a date object loses its time.
     */
    public function priceValidUntil(DateTimeInterface|string $date): self
    {
        return $this->set('priceValidUntil', $date instanceof DateTimeInterface ? $date->format('Y-m-d') : $date);
    }

    public function seller(string|Organization|Person $seller): self
    {
        if (is_string($seller)) {
            $seller = (new Organization())->name($seller);
        }

        return $this->set('seller', $seller);
    }

    /**
     * Minimum order quantity. The unit is a UN/CEFACT code, H87 is a piece.
     */
    public function eligibleQuantity(int $minValue, string $unitCode = 'H87'): self
    {
        return $this->set('eligibleQuantity', [
            '@type' => 'QuantitativeValue',
            'minValue' => $minValue,
            'unitCode' => $unitCode,
        ]);
    }
}
