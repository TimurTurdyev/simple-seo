<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use DateTimeInterface;
use JsonSerializable;

abstract class AbstractType implements JsonSerializable
{
    /** @var array<string, mixed> */
    private array $properties = [];

    public function __construct(
        private readonly string $type,
    ) {
    }

    /**
     * Set any raw schema.org property; escape hatch for fields without a
     * dedicated typed method.
     */
    public function property(string $name, mixed $value): static
    {
        return $this->set($name, $value);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return ['@type' => $this->type] + $this->properties;
    }

    protected function set(string $name, mixed $value): static
    {
        if ($value === null) {
            unset($this->properties[$name]);

            return $this;
        }

        $this->properties[$name] = $this->normalize($value);

        return $this;
    }

    /**
     * Append a value to a list property (creating the list on first use).
     */
    protected function push(string $name, mixed $value): static
    {
        $current = $this->properties[$name] ?? [];

        if (! is_array($current) || ! array_is_list($current)) {
            $current = [$current];
        }

        $current[] = $this->normalize($value);
        $this->properties[$name] = $current;

        return $this;
    }

    private function normalize(mixed $value): mixed
    {
        return $value instanceof DateTimeInterface ? $value->format('c') : $value;
    }
}
