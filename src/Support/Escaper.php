<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Support;

final class Escaper
{
    /**
     * Escape a value for safe output inside HTML element content or
     * double/single quoted attribute values.
     */
    public static function html(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}
