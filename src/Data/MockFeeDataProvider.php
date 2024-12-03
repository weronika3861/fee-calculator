<?php

declare(strict_types=1);

namespace App\Data;

class MockFeeDataProvider implements FeeDataProviderInterface
{
    /**
     * @var array<int, array<int, array<string, float>>>
     */
    private array $feeBreakpoints;

    public function __construct()
    {
        $this->feeBreakpoints = [
            12 => $this->getTwelveMonthBreakpoints(),
            24 => $this->getTwentyFourMonthBreakpoints(),
        ];
    }

    /**
     * @return array<int, array<string, float>>
     */
    public function getBreakpoints(int $term): array
    {
        if (!isset($this->feeBreakpoints[$term])) {
            throw new \InvalidArgumentException("No breakpoints defined for term $term months.");
        }

        return $this->feeBreakpoints[$term];
    }

    /**
     * @return array<int, array<string, float>>
     */
    private function getTwelveMonthBreakpoints(): array
    {
        return [
            ['loan' => 1000.00, 'fee' => 50.00],
            ['loan' => 2000.00, 'fee' => 90.00],
            ['loan' => 3000.00, 'fee' => 90.00],
            ['loan' => 4000.00, 'fee' => 115.00],
            ['loan' => 5000.00, 'fee' => 100.00],
            ['loan' => 6000.00, 'fee' => 120.00],
            ['loan' => 7000.00, 'fee' => 140.00],
            ['loan' => 8000.00, 'fee' => 160.00],
            ['loan' => 9000.00, 'fee' => 180.00],
            ['loan' => 10000.00, 'fee' => 200.00],
            ['loan' => 11000.00, 'fee' => 220.00],
            ['loan' => 12000.00, 'fee' => 240.00],
            ['loan' => 13000.00, 'fee' => 260.00],
            ['loan' => 14000.00, 'fee' => 280.00],
            ['loan' => 15000.00, 'fee' => 300.00],
            ['loan' => 16000.00, 'fee' => 320.00],
            ['loan' => 17000.00, 'fee' => 340.00],
            ['loan' => 18000.00, 'fee' => 360.00],
            ['loan' => 19000.00, 'fee' => 380.00],
            ['loan' => 20000.00, 'fee' => 400.00],
        ];
    }

    /**
     * @return array<int, array<string, float>>
     */
    private function getTwentyFourMonthBreakpoints(): array
    {
        return [
            ['loan' => 1000.00, 'fee' => 70.00],
            ['loan' => 2000.00, 'fee' => 100.00],
            ['loan' => 3000.00, 'fee' => 120.00],
            ['loan' => 4000.00, 'fee' => 160.00],
            ['loan' => 5000.00, 'fee' => 200.00],
            ['loan' => 6000.00, 'fee' => 240.00],
            ['loan' => 7000.00, 'fee' => 280.00],
            ['loan' => 8000.00, 'fee' => 320.00],
            ['loan' => 9000.00, 'fee' => 360.00],
            ['loan' => 10000.00, 'fee' => 400.00],
            ['loan' => 11000.00, 'fee' => 440.00],
            ['loan' => 12000.00, 'fee' => 480.00],
            ['loan' => 13000.00, 'fee' => 520.00],
            ['loan' => 14000.00, 'fee' => 560.00],
            ['loan' => 15000.00, 'fee' => 600.00],
            ['loan' => 16000.00, 'fee' => 640.00],
            ['loan' => 17000.00, 'fee' => 680.00],
            ['loan' => 18000.00, 'fee' => 720.00],
            ['loan' => 19000.00, 'fee' => 760.00],
            ['loan' => 20000.00, 'fee' => 800.00],
        ];
    }
}
