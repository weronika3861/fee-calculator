<?php

declare(strict_types=1);

namespace App\Command;

use App\Calculator\FeeCalculatorInterface;
use App\Model\LoanProposalFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:calculate-fee',
    description: 'Calculates the loan fee based on the amount and term.'
)]
class CalculateFeeCommand extends Command
{
    public function __construct(
        private readonly LoanProposalFactory $loanProposalFactory,
        private readonly FeeCalculatorInterface $feeCalculator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('term', InputArgument::REQUIRED, 'The term of the loan in months (12 or 24)')
            ->addArgument('loanAmount', InputArgument::REQUIRED, 'The loan amount in PLN (e.g., 10240.00)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $loanAmountInput = (float)$input->getArgument('loanAmount');
        $termInput = (int)$input->getArgument('term');

        try {
            $loanProposal = $this->loanProposalFactory->create($termInput, $loanAmountInput);

            $fee = $this->feeCalculator->calculate($loanProposal);
            $io->success(sprintf(
                'The calculated fee for a loan of %.2f PLN with a term of %d months is: %.2f PLN.',
                $loanProposal->amount(),
                $loanProposal->term(),
                $fee
            ));

            return Command::SUCCESS;
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::INVALID;
        } catch (\Exception $e) {
            $io->error('An error occurred while calculating the fee: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
