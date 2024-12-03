<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class LoanProposalFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function create(int $term, float $amount): LoanProposal
    {
        $constraints = new Assert\Collection([
            'term' => new Assert\Choice([
                'choices' => [12, 24],
                'message' => 'Term must be either 12 or 24 months.',
            ]),
            'amount' => new Assert\Range([
                'min' => 1000,
                'max' => 20000,
                'notInRangeMessage' => 'Loan amount must be between {{ min }} and {{ max }} PLN.',
            ]),
        ]);

        $violations = $this->validator->validate(['term' => $term, 'amount' => $amount], $constraints);

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }

            throw new \InvalidArgumentException(implode("\n", $messages));
        }

        return new LoanProposal($term, $amount);
    }
}
