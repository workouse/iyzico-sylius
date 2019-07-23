<?php

namespace Eres\SyliusIyzicoPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class IdentityNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {

        if (!$constraint instanceof IdentityNumber) {
            throw new UnexpectedTypeException($constraint, IdentityNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $identityNumberArr = str_split($value);
        $identityNumberCount = count($identityNumberArr);
        $identityNumberSum = null;
        $identityNumberCharacterType = false;
        foreach ($identityNumberArr as $item) {
            if (!is_numeric($item)) {
                $identityNumberCharacterType = true;
                break;
            }
            $identityNumberSum += $item;
        }

        if ($identityNumberCount !== 11 || $identityNumberCharacterType || str_split($identityNumberSum)[1] !== $identityNumberArr[$identityNumberCount - 1]) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
