<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class LocalBusiness extends AbstractType
{
    public function __construct()
    {
        parent::__construct('LocalBusiness');
    }

    public function name(string $name): self
    {
        return $this->set('name', $name);
    }

    public function url(string $url): self
    {
        return $this->set('url', $url);
    }

    public function telephone(string $telephone): self
    {
        return $this->set('telephone', $telephone);
    }

    public function priceRange(string $priceRange): self
    {
        return $this->set('priceRange', $priceRange);
    }

    public function image(string $url): self
    {
        return $this->push('image', $url);
    }

    /**
     * Accepts a PostalAddress property map or a plain street address string.
     *
     * @param  array<string, string>|string  $address
     */
    public function address(array|string $address): self
    {
        if (is_string($address)) {
            $address = ['streetAddress' => $address];
        }

        return $this->set('address', ['@type' => 'PostalAddress'] + $address);
    }
}
