<?php

namespace App\Infrastructure\Validator;

use App\Application\Validator\DataValidatorInterface;
use App\Domain\Exception\DataValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidator;
use Symfony\Component\Validator\ConstraintViolationInterface;

class SymfonyDataValidator implements DataValidatorInterface
{
    public function __construct(
        private SymfonyValidator $validator
    ) {}

    public function validate(object $dto): void
    {
        $violations = $this->validator->validate($dto);

        if (count($violations) === 0) {
            return;
        }

        $errors = [];

        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $field = $violation->getPropertyPath();
            $errors[$field] = $violation->getMessage();
        }

        throw new DataValidationException($errors);
    }
}
