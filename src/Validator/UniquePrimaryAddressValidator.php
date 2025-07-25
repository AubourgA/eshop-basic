<?php

namespace App\Validator;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;


class UniquePrimaryAddressValidator extends ConstraintValidator
{

    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePrimaryAddress) {
            throw new UnexpectedTypeException($constraint, UniquePrimaryAddress::class);
        }
        
        if (!$value instanceof Address) {
            throw new UnexpectedValueException($value, Address::class);
        }
        
       
        if (!$value->isPrimary()) {
            return;
        }
        
        $existing = $this->addressRepository->findOneBy([
            'customer' => $value->getCustomer(),
            'type'     => $value->getType(),
            'isPrimary' => true
        ]);
        
       
        if ($existing && $existing->getId() !== $value->getId()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('isPrimary') 
                ->addViolation();
        }
    }

   
}