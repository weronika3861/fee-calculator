<?php

declare(strict_types=1);

namespace App\Data;

class FeeDataProviderFactory
{
    public function create(string $type): FeeDataProviderInterface
    {
        return match ($type) {
            'static' => new MockFeeDataProvider(),
            default => throw new \InvalidArgumentException("Unknown FeeDataProvider type: $type"),
        };
    }
}
