<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\JsonLd\Schema;

final class AggregateRating extends AbstractType
{
    public function __construct()
    {
        parent::__construct('AggregateRating');
    }

    public function ratingValue(float|int|string $value): self
    {
        return $this->set('ratingValue', $value);
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
