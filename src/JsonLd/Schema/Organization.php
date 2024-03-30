<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\JsonLd\Schema;

final class Organization extends AbstractType
{
    public function __construct()
    {
        parent::__construct('Organization');
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function logo(string $url): self
    {
        return $this->set('logo', $url);
    }

    public function sameAs(string $url): self
    {
        return $this->push('sameAs', $url);
    }
}
