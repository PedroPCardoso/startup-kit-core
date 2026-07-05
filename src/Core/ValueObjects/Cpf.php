<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\ValueObjects;

final class Cpf implements \JsonSerializable
{
    private function __construct(
        private readonly string $cpf,
    ) {}

    public static function fromString(string $cpf): self
    {
        $digits = preg_replace('/\D/', '', $cpf);

        if (strlen($digits) !== 11) {
            throw new \InvalidArgumentException('CPF must have exactly 11 digits.');
        }

        if (preg_match('/^(\d)\1{10}$/', $digits)) {
            throw new \InvalidArgumentException('CPF cannot have all identical digits.');
        }

        if (!self::validateChecksum($digits)) {
            throw new \InvalidArgumentException('Invalid CPF checksum.');
        }

        return new self($digits);
    }

    public function value(): string
    {
        return $this->cpf;
    }

    public function formatted(): string
    {
        return sprintf(
            '%s.%s.%s-%s',
            substr($this->cpf, 0, 3),
            substr($this->cpf, 3, 3),
            substr($this->cpf, 6, 3),
            substr($this->cpf, 9, 2),
        );
    }

    public function equals(self $other): bool
    {
        return $this->cpf === $other->cpf;
    }

    private static function validateChecksum(string $digits): bool
    {
        $sum1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum1 += (int) $digits[$i] * (10 - $i);
        }
        $digit1 = ($sum1 % 11 < 2) ? 0 : 11 - ($sum1 % 11);

        $sum2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum2 += (int) $digits[$i] * (11 - $i);
        }
        $digit2 = ($sum2 % 11 < 2) ? 0 : 11 - ($sum2 % 11);

        return (int) $digits[9] === $digit1 && (int) $digits[10] === $digit2;
    }

    public function jsonSerialize(): string
    {
        return $this->cpf;
    }
}
