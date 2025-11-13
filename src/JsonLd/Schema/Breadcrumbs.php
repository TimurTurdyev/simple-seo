<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\JsonLd\Schema;

use TimurTurdyev\SimpleSeo\Support\Exceptions\SimpleSeoException;

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

    /**
     * Add crumbs from a list as view components keep them: name or title,
     * url or href.
     *
     * @param  iterable<array{name?: string, title?: string, url?: ?string, href?: ?string}>  $crumbs
     *
     * @throws SimpleSeoException when a crumb has neither name nor title
     */
    public function items(iterable $crumbs): self
    {
        foreach ($crumbs as $crumb) {
            $name = $crumb['name'] ?? $crumb['title'] ?? throw new SimpleSeoException('A breadcrumb needs a name or a title.');

            $this->item($name, $crumb['url'] ?? $crumb['href'] ?? null);
        }

        return $this;
    }
}
