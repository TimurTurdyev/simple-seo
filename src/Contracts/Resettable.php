<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Contracts;

interface Resettable
{
    /**
     * Reset all mutable state back to its initial value.
     *
     * Required for long-running runtimes (Octane and similar) where the same
     * object instance may serve multiple requests.
     */
    public function reset(): void;
}
