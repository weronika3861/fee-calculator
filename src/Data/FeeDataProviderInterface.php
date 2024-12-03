<?php

declare(strict_types=1);

namespace App\Data;

interface FeeDataProviderInterface
{
    /**
     * Retrieve fee breakpoints for a given term.
     *
     * @param int $term Loan term in months.
     * @return array<int, array<string, float>> Array of fee breakpoints.
     */
    public function getBreakpoints(int $term): array;
}
