<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\ValueObjects;

final class EmailAddress implements \JsonSerializable
{
    private function __construct(
        private readonly string $email,
    ) {}

    public static function fromString(string $email): self
    {
        $normalized = strtolower(trim($email));

        if (!filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf('Invalid email address: %s', $email));
        }

        return new self($normalized);
    }

    public function value(): string
    {
        return $this->email;
    }

    public function localPart(): string
    {
        return explode('@', $this->email)[0];
    }

    public function domain(): string
    {
        return explode('@', $this->email)[1];
    }

    public function equals(self $other): bool
    {
        return $this->email === $other->email;
    }

    public function jsonSerialize(): string
    {
        return $this->email;
    }
}
