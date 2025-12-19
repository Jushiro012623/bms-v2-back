<?php
namespace App\Services\JwtServices;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class NotBlackListed implements Constraint
{
    public function assert(Token $token): void
    {
        $jti = $token->claims()->get('jti');

        if (!$jti) {
            throw new ConstraintViolation('Token is missing required jti claim');
        }

        if (cache()->has("blacklisted_tokens:{$jti}")) {
            throw new ConstraintViolation('Token is blacklisted');
        }
    }
}
