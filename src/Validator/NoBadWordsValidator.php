<?php

// src/Validator/NoBadWordsValidator.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoBadWordsValidator extends ConstraintValidator
{
    private array $badWords = [
        'fuck',
        'test',
        'bitch',
        'shit',
        'putain',
        'merde',
        // Add as many as you want
    ];

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\NoBadWords */

        if (null === $value || '' === $value) {
            return;
        }

        foreach ($this->badWords as $badWord) {
            if (stripos($value, $badWord) !== false) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                return;
            }
        }
    }
}