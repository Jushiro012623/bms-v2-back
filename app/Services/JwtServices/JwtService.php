<?php

namespace App\Services\JwtServices;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use App\Services\JwtServices\NotBlackListed;
class JwtService
{
    private Configuration $config;
    private array $claims = [];
    public function __construct()
    {
        $this->config = Configuration::forSymmetricSigner(
            new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::base64Encoded(config('jwt.secret'))
        );
    }

    private function config(): Configuration
    {
        return $this->config;
    }

    public function getClaims(Token $token, string $key = ''): mixed
    {
        if($key === '') {
            return $token->claims()->all();
        }

        if (!$token->claims()->has($key)) {
            throw new AuthenticationException("Missing claim: {$key}");
        }
        return $token->claims()->get($key);
    }

    /**
     * Set a specific claim with a given value.
     *
     * @param string $claim The name of the claim to set.
     * @param mixed $value The value to assign to the specified claim.
     * @return self
     */
    public function setClaim(string $claim, mixed $value): self
    {
        if (in_array($claim, config('jwt.required_claims'), true)) {
            throw new \InvalidArgumentException("Cannot manually set reserved claim: {$claim}");
        }
        $this->claims[$claim] = $value;
        return $this;
    }

    /**
     * Set multiple claims to the current instance.
     *
     * @param array $claims An associative array of claims where the key is the claim name and the value is the claim value.
     * @return self
     */
    public function setClaims(array $claims): self
    {
        foreach ($claims as $claim => $value) {
            $this->setClaim($claim, $value);
        }
        return $this;
    }
    private function createToken(int|string $userID): string
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone(config('app.timezone')));

        $tokenBuilder = $this->config()->builder()
            ->issuedBy(config('app.url'))
            ->permittedFor(config('app.url'))
            ->identifiedBy(bin2hex(random_bytes(16)))
            ->relatedTo($userID)
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+' . config('jwt.ttl') . ' minutes'));

        foreach ($this->claims as $claim => $value) {
            $tokenBuilder->withClaim($claim, $value);
        }

        $token = $tokenBuilder->getToken($this->config()->signer(), $this->config()->signingKey());

        $this->claims = [];

        return $token->toString();
    }

    public function parseToken(?string $tokenString): Token
    {
        if (empty($tokenString)) {
            throw new AuthenticationException('Token string is empty');
        }

        try {
            return $this->config()->parser()->parse($tokenString);
        } catch (\Throwable $e) {
            throw new AuthenticationException('Failed to parse token: ' . $e->getMessage());
        }
    }

    public function issue(int|string $userID): string
    {
        return $this->createToken($userID);
    }

    public function refresh(string $tokenString): string
    {
        $token = $this->parseToken($tokenString);

        $previousCustomClaims = array_filter(
            $this->getClaims($token),
            fn($key) => !in_array($key, config('jwt.required_claims'), true),
            ARRAY_FILTER_USE_KEY
        );

        $this->setClaims($previousCustomClaims);

        $this->blacklistToken($this->getClaims($token, 'jti'));

        return $this->createToken($this->getClaims($token, 'sub'));
    }

    public function revoke(string $tokenString): void
    {
        $token = $this->parseToken($tokenString);

        $this->blacklistToken($this->getClaims($token, 'jti'));
    }

    public function validate(Token $token): void
    {
        try {
            $this->config()->validator()->assert($token, ...$this->buildConstraints());
        } catch (RequiredConstraintsViolated $e) {
            throw new AuthenticationException('Invalid or expired token');
        }
    }

    protected function blacklistToken(string $jti): void
    {
        cache()->put("blacklisted_tokens:{$jti}", true, now()->addMinutes(config('jwt.ttl')));
    }

    private function buildConstraints(): array
    {
        return [
            new IssuedBy(config('app.url')),
            new PermittedFor(config('app.url')),
            new ValidAt(
                new SystemClock(new \DateTimeZone(config('app.timezone'))),
                new \DateInterval('PT60S')
            ),
            new NotBlackListed(),
        ];
    }

    public function user(Token $token): User
    {
        $userID = $this->getClaims($token, 'sub');

        if (!$userID || !$user = User::find($userID)) {
            throw new AuthenticationException('Invalid user in token');
        }

        return $user;
    }
}
