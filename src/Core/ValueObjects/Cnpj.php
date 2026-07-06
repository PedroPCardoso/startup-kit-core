<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\ValueObjects;

final class Cnpj implements \JsonSerializable
{
    private function __construct(
        private readonly string $cnpj,
    ) {}

    public static function fromString(string $cnpj): self
    {
        $digits = preg_replace('/\D/', '', $cnpj);

        if (strlen($digits) !== 14) {
            throw new \InvalidArgumentException('CNPJ must have exactly 14 digits.');
        }

        if (preg_match('/^(\d)\1{13}$/', $digits)) {
            throw new \InvalidArgumentException('CNPJ cannot have all identical digits.');
        }

        if (!self::validateChecksum($digits)) {
            throw new \InvalidArgumentException('Invalid CNPJ checksum.');
        }

        return new self($digits);
    }

    public function value(): string
    {
        return $this->cnpj;
    }

    public function formatted(): string
    {
        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($this->cnpj, 0, 2),
            substr($this->cnpj, 2, 3),
            substr($this->cnpj, 5, 3),
            substr($this->cnpj, 8, 4),
            substr($this->cnpj, 12, 2),
        );
    }

    public function equals(self $other): bool
    {
        return $this->cnpj === $other->cnpj;
    }

    private static function validateChecksum(string $digits): bool
    {
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum1 += (int) $digits[$i] * $weights1[$i];
        }
        $digit1 = ($sum1 % 11 < 2) ? 0 : 11 - ($sum1 % 11);

        $sum2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum2 += (int) $digits[$i] * $weights2[$i];
        }
        $digit2 = ($sum2 % 11 < 2) ? 0 : 11 - ($sum2 % 11);

        return (int) $digits[12] === $digit1 && (int) $digits[13] === $digit2;
    }

    public function jsonSerialize(): string
    {
        return $this->cnpj;
    }
}
