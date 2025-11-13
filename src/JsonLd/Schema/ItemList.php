<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class ItemList extends AbstractType
{
    /** @var list<array{url: string, name: ?string}> */
    private array $items = [];

    /** @var array<string, true> */
    private array $seen = [];

    private int $offset = 0;

    public function __construct()
    {
        parent::__construct('ItemList');
    }

    /**
     * Positions continue after this many items, as on the second and
     * further pages of a listing.
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * A url that is already in the list is skipped.
     */
    public function item(string $url, ?string $name = null): self
    {
        if (isset($this->seen[$url])) {
            return $this;
        }

        $this->seen[$url] = true;
        $this->items[] = ['url' => $url, 'name' => $name];

        return $this;
    }

    /**
     * @param  iterable<array{url: string, name?: ?string}>  $items
     */
    public function items(iterable $items): self
    {
        foreach ($items as $item) {
            $this->item($item['url'], $item['name'] ?? null);
        }

        return $this;
    }

    /**
     * Size of the whole list across all pages; without it the number
     * of items added here.
     */
    public function numberOfItems(int $count): self
    {
        return $this->set('numberOfItems', $count);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();

        if ($this->items === []) {
            return $data;
        }

        $elements = [];

        foreach ($this->items as $index => $item) {
            $element = ['@type' => 'ListItem', 'position' => $this->offset + $index + 1, 'url' => $item['url']];

            if ($item['name'] !== null) {
                $element['name'] = $item['name'];
            }

            $elements[] = $element;
        }

        return [
            '@type' => $data['@type'],
            'numberOfItems' => $data['numberOfItems'] ?? count($elements),
            'itemListElement' => $elements,
        ] + $data;
    }
}
