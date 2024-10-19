<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class AggregateOffer extends AbstractType
{
    public function __construct()
    {
        parent::__construct('AggregateOffer');
    }

    public function lowPrice(float|int|string $price): self
    {
        return $this->set('lowPrice', $price);
    }

    public function highPrice(float|int|string $price): self
    {
        return $this->set('highPrice', $price);
    }

    public function priceCurrency(string $currency): self
    {
        return $this->set('priceCurrency', $currency);
    }

    public function offerCount(int $count): self
    {
        return $this->set('offerCount', $count);
    }
}
