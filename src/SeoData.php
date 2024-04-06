<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools;

use JsonSerializable;

final readonly class SeoData
{
    /**
     * @param  list<array<mixed>|JsonSerializable>  $jsonLd
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?string $canonical = null,
        public ?string $ogType = null,
        public array $jsonLd = [],
    ) {
    }
}
