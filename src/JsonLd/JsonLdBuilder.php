<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\JsonLd;

use JsonSerializable;
use TimurTurdyev\Seotools\Contracts\Section;

final class JsonLdBuilder implements Section
{
    private const CONTEXT = 'https://schema.org';

    /**
     * JSON_HEX_TAG is mandatory: it encodes < and > so a string value
     * containing </script> cannot break out of the script element.
     */
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
        | JSON_HEX_TAG
        | JSON_THROW_ON_ERROR;

    /** @var list<array<mixed>> */
    private array $entities = [];

    /**
     * Add an independent schema.org entity to the graph.
     *
     * @param  array<mixed>|JsonSerializable  $entity
     */
    public function add(array|JsonSerializable $entity): self
    {
        $normalized = $entity instanceof JsonSerializable ? $entity->jsonSerialize() : $entity;

        if (! is_array($normalized)) {
            $normalized = [$normalized];
        }

        $this->entities[] = $normalized;

        return $this;
    }

    public function render(): string
    {
        if ($this->entities === []) {
            return '';
        }

        $payload = count($this->entities) === 1
            ? ['@context' => self::CONTEXT] + $this->entities[0]
            : ['@context' => self::CONTEXT, '@graph' => $this->entities];

        $json = json_encode($payload, self::JSON_FLAGS);

        return '<script type="application/ld+json">' . $json . '</script>';
    }

    public function reset(): void
    {
        $this->entities = [];
    }
}
