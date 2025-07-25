<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_CLASS)]
class UniquePrimaryAddress extends Constraint
{
    public string $message = 'Une seule adresse principale est autorisée pour ce type.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}