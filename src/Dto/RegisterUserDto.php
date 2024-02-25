<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class RegisterUserDto
{
    public function __construct(
        #[NotBlank()]
        #[Type('string')]
        public readonly string $email,

        #[NotBlank]
        #[Type('string')]
        public readonly string $password,

        #[NotBlank]
        #[Type('string')]
        public readonly string $name,
    ) {
    }
}
