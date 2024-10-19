<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

final class Breadcrumbs extends AbstractType
{
    private int $position = 0;

    public function __construct()
    {
        parent::__construct('BreadcrumbList');
    }

    /**
     * Add the next breadcrumb; positions are numbered automatically from 1.
     * The last crumb (current page) is usually added without a url.
     */
    public function item(string $name, ?string $url = null): self
    {
        $item = [
            '@type' => 'ListItem',
            'position' => ++$this->position,
            'name' => $name,
        ];

        if ($url !== null) {
            $item['item'] = $url;
        }

        return $this->push('itemListElement', $item);
    }
}
