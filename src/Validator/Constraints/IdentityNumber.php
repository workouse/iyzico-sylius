<?php

namespace Eres\SyliusIyzicoPlugin\Validator\Constraints;

use Symfony\Component\Validator\Constraint;


class IdentityNumber extends Constraint
{
    public $message = 'This value is not a valid identity number.';
}
