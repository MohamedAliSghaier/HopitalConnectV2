<?php


// src/Validator/NoBadWords.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER)]
class NoBadWords extends Constraint
{
    public string $message = 'This text contains inappropriate language.';

    public function validatedBy()
    {
        return NoBadWordsValidator::class;  // Link to the validator class
    }
}
