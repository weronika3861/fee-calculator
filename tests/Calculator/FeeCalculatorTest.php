<?php

declare(strict_types=1);

namespace App\Tests\Calculator;

use App\Calculator\FeeCalculator;
use App\Data\FeeDataProviderFactory;
use App\Data\FeeDataProviderInterface;
use App\Model\LoanProposalFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FeeCalculatorTest extends TestCase
{
    private FeeCalculator $calculator;
    private LoanProposalFactory $loanProposalFactory;

    protected function setUp(): void
    {
        $breakpoints = [
            ['loan' => 1000.00, 'fee' => 50.00],
            ['loan' => 2000.00, 'fee' => 90.00],
            ['loan' => 3000.00, 'fee' => 135.00],
            ['loan' => 4000.00, 'fee' => 145.00],
        ];

        $mockDataProvider = $this->createMock(FeeDataProviderInterface::class);
        $mockDataProvider->method('getBreakpoints')->willReturn($breakpoints);

        $mockFactory = $this->createMock(FeeDataProviderFactory::class);
        $mockFactory->method('create')->willReturn($mockDataProvider);

        $this->loanProposalFactory = new LoanProposalFactory(
            $this->createMock(ValidatorInterface::class)
        );

        $this->calculator = new FeeCalculator($mockFactory, 'mock');
    }

    public function testCalculateReturnsCorrectFee(): void
    {
        $loanProposal = $this->loanProposalFactory->create(12, 1500.00);

        $fee = $this->calculator->calculate($loanProposal);

        $this->assertEquals(70.00, $fee);
    }

    public function testCalculateRoundsUpTotalToMultipleOfFive(): void
    {
        $loanProposal = $this->loanProposalFactory->create(12, 3001.01);

        $fee = $this->calculator->calculate($loanProposal);

        $this->assertEquals(138.99, $fee);
    }

    public function testCalculateThrowsExceptionForLoanGreaterThanMaxBoundary(): void
    {
        $loanProposal = $this->loanProposalFactory->create(12, 4000.01);

        $this->expectException(\InvalidArgumentException::class);

        $this->calculator->calculate($loanProposal);
    }

    public function testCalculateThrowsExceptionForLoanLessThanMinBoundary(): void
    {
        $loanProposal = $this->loanProposalFactory->create(12, 999.99);

        $this->expectException(\InvalidArgumentException::class);

        $this->calculator->calculate($loanProposal);
    }
}
