<?php

declare(strict_types=1);

namespace App\Calculator;

use App\Data\FeeDataProviderFactory;
use App\Data\FeeDataProviderInterface;
use App\Model\LoanProposal;

class FeeCalculator implements FeeCalculatorInterface
{
    private FeeDataProviderInterface $dataProvider;

    public function __construct(FeeDataProviderFactory $factory, string $providerType)
    {
        $this->dataProvider = $factory->create($providerType);
    }

    public function calculate(LoanProposal $application): float
    {
        $amount = $application->amount();
        $breakpoints = $this->dataProvider->getBreakpoints($application->term());
        $calculatedFee = $this->calculateFee($breakpoints, $application->amount());

        return $this->roundFeeUpToEnsureTotalIsMultipleOfFive($calculatedFee, $amount);
    }

    /**
     * @param array<int, array<string, float>> $breakpoints
     */
    private function calculateFee(array $breakpoints, float $amount): float
    {
        $this->sortBreakpoints($breakpoints);

        $lastBreakpoint = $breakpoints[count($breakpoints) - 1];
        if ($amount === $lastBreakpoint['loan']) {
            return $lastBreakpoint['fee'];
        }

        return $this->interpolateFeeBetweenBreakpoints($breakpoints, $amount);
    }

    private function roundFeeUpToEnsureTotalIsMultipleOfFive(float $fee, float $amount): float
    {
        $total = $fee + $amount;
        $roundedTotal = ceil($total / 5) * 5;

        return round($roundedTotal - $amount, 2);
    }

    /**
     * @param array<int, array<string, float>> $breakpoints
     */
    private function sortBreakpoints(array &$breakpoints): void
    {
        usort($breakpoints, function ($a, $b) {
            return $a['loan'] <=> $b['loan'];
        });
    }

    /**
     * @param array<int, array<string, float>> $breakpoints
     */
    private function interpolateFeeBetweenBreakpoints(array $breakpoints, float $amount): float
    {
        $lowerBound = null;
        $upperBound = null;

        foreach ($breakpoints as $entry) {
            if ($entry['loan'] <= $amount) {
                $lowerBound = $entry;
            }
            if ($entry['loan'] > $amount && $upperBound === null) {
                $upperBound = $entry;
            }
        }

        if ($lowerBound === null || $upperBound === null) {
            throw new \InvalidArgumentException('Loan amount is out of bounds.');
        }

        $lowerLoan = $lowerBound['loan'];
        $upperLoan = $upperBound['loan'];
        $lowerFee = $lowerBound['fee'];
        $upperFee = $upperBound['fee'];

        $ratio = ($amount - $lowerLoan) / ($upperLoan - $lowerLoan);

        return $lowerFee + $ratio * ($upperFee - $lowerFee);
    }
}
