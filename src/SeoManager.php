<?php

declare(strict_types=1);

namespace TimurTurdyev\Seotools;

use TimurTurdyev\Seotools\Contracts\Section;
use TimurTurdyev\Seotools\Support\Exceptions\SeotoolsException;

final class SeoManager
{
    /**
     * @param  array<string, Section>  $sections  Section map in render order.
     */
    public function __construct(
        private readonly array $sections,
    ) {
    }

    /**
     * @throws SeotoolsException When no section is registered under the name.
     */
    public function section(string $name): Section
    {
        return $this->sections[$name]
            ?? throw new SeotoolsException("Unknown SEO section [{$name}].");
    }

    /**
     * Render all registered sections in registration order, skipping the
     * ones that produce no output.
     */
    public function render(): string
    {
        $fragments = [];

        foreach ($this->sections as $section) {
            $html = $section->render();

            if ($html !== '') {
                $fragments[] = $html;
            }
        }

        return implode("\n", $fragments);
    }

    public function reset(): void
    {
        foreach ($this->sections as $section) {
            $section->reset();
        }
    }
}
