<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class ProductGroup extends AbstractType
{
    /** @var list<string> */
    private array $variesBy = [];

    public function __construct()
    {
        parent::__construct('ProductGroup');
    }

    public function productGroupID(string $id): self
    {
        return $this->set('productGroupID', $id);
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function description(string $description): self
    {
        return $this->set('description', $description);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function brand(string|Organization|Brand $brand): self
    {
        return $this->set('brand', $brand);
    }

    /**
     * Properties the variants differ in: a schema.org name ('color',
     * 'size') or a full url. Calls accumulate, repeats are dropped.
     */
    public function variesBy(string ...$properties): self
    {
        foreach ($properties as $property) {
            $url = str_contains($property, '://') ? $property : 'https://schema.org/'.$property;

            if (! in_array($url, $this->variesBy, true)) {
                $this->variesBy[] = $url;
            }
        }

        return $this->set('variesBy', $this->variesBy === [] ? null : $this->variesBy);
    }

    public function hasVariant(Product $variant): self
    {
        return $this->push('hasVariant', $variant);
    }
}
