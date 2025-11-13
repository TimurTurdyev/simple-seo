<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class AggregateRating extends AbstractType
{
    public function __construct()
    {
        parent::__construct('AggregateRating');
    }

    /**
     * A decimal comma becomes a dot: search engines do not parse "4,5".
     */
    public function ratingValue(float|int|string $value): self
    {
        return $this->set('ratingValue', is_string($value) ? str_replace(',', '.', $value) : $value);
    }

    public function reviewCount(int $count): self
    {
        return $this->set('reviewCount', $count);
    }

    public function bestRating(float|int|string $value): self
    {
        return $this->set('bestRating', $value);
    }
}
