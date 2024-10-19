<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class WebSite extends AbstractType
{
    public function __construct()
    {
        parent::__construct('WebSite');
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }
}
