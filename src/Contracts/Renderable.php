<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Contracts;

interface Renderable
{
    /**
     * Render the HTML fragment for this piece of markup.
     *
     * Returns an empty string when there is nothing to render.
     */
    public function render(): string;
}
