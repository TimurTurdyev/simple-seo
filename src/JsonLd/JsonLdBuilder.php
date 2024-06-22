<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools\JsonLd;

use Closure;
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

    /** @var list<array{type: string, overrides: array<string, mixed>}> */
    private array $pageEntities = [];

    /** @var (Closure(): array{title: ?string, description: ?string, images: list<string>, canonical: ?string})|null */
    private ?Closure $pageProvider = null;

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

    /**
     * Add an entity built from the page values at render time: name from the
     * title (without suffix), description, image from Open Graph images and
     * url from the canonical. Overrides win over page values; null overrides
     * are dropped, so conditional fields can be written as ternaries.
     *
     * @param  array<string, mixed>  $overrides
     */
    public function fromPage(string $type, array $overrides = []): self
    {
        $this->pageEntities[] = ['type' => $type, 'overrides' => $overrides];

        return $this;
    }

    /**
     * Wired by SeoManager; without it fromPage() renders only the type
     * and overrides.
     *
     * @param  Closure(): array{title: ?string, description: ?string, images: list<string>, canonical: ?string}  $provider
     */
    public function setPageProvider(Closure $provider): void
    {
        $this->pageProvider = $provider;
    }

    public function render(): string
    {
        $entities = [...$this->entities, ...array_map($this->resolvePageEntity(...), $this->pageEntities)];

        if ($entities === []) {
            return '';
        }

        $payload = count($entities) === 1
            ? ['@context' => self::CONTEXT] + $entities[0]
            : ['@context' => self::CONTEXT, '@graph' => $entities];

        $json = json_encode($payload, self::JSON_FLAGS);

        return '<script type="application/ld+json">' . $json . '</script>';
    }

    public function count(): int
    {
        return count($this->entities) + count($this->pageEntities);
    }

    public function reset(): void
    {
        $this->entities = [];
        $this->pageEntities = [];
    }

    /**
     * @param  array{type: string, overrides: array<string, mixed>}  $pageEntity
     * @return array<string, mixed>
     */
    private function resolvePageEntity(array $pageEntity): array
    {
        $entity = ['@type' => $pageEntity['type']];

        if ($this->pageProvider !== null) {
            $page = ($this->pageProvider)();

            if ($page['title'] !== null) {
                $entity['name'] = $page['title'];
            }

            if ($page['description'] !== null) {
                $entity['description'] = $page['description'];
            }

            if ($page['images'] !== []) {
                $entity['image'] = count($page['images']) === 1 ? $page['images'][0] : $page['images'];
            }

            if ($page['canonical'] !== null) {
                $entity['url'] = $page['canonical'];
            }
        }

        $overrides = array_filter($pageEntity['overrides'], static fn (mixed $value): bool => $value !== null);

        return array_merge($entity, $overrides);
    }
}
