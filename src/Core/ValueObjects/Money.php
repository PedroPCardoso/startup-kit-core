<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\ValueObjects;

final class Money implements \JsonSerializable
{
    private const ISO_CURRENCIES = [
        'BRL', 'USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'CNY', 'INR',
        'MXN', 'ARS', 'CLP', 'COP', 'PEN', 'UYU', 'PYG', 'BOB',
    ];

    private const DECIMAL_SCALE = 2;

    private function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {}

    public static function fromInt(int $amount, string $currency): self
    {
        self::validateCurrency($currency);

        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }

        return new self($amount, strtoupper($currency));
    }

    public static function fromFloat(float $amount, string $currency): self
    {
        self::validateCurrency($currency);

        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative.');
        }

        return new self(
            (int) round($amount * (10 ** self::DECIMAL_SCALE)),
            strtoupper($currency),
        );
    }

    public static function zero(string $currency): self
    {
        return new self(0, strtoupper($currency));
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function sub(self $other): self
    {
        $this->assertSameCurrency($other);

        if ($this->amount < $other->amount) {
            throw new \InvalidArgumentException('Result would be negative.');
        }

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function mul(int $multiplier): self
    {
        if ($multiplier < 0) {
            throw new \InvalidArgumentException('Multiplier cannot be negative.');
        }

        return new self($this->amount * $multiplier, $this->currency);
    }

    public function div(int $divisor): self
    {
        if ($divisor <= 0) {
            throw new \InvalidArgumentException('Divisor must be positive.');
        }

        return new self(intdiv($this->amount, $divisor), $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function toFloat(): float
    {
        return $this->amount / (10 ** self::DECIMAL_SCALE);
    }

    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException(
                sprintf('Currency mismatch: %s vs %s', $this->currency, $other->currency)
            );
        }
    }

    private static function validateCurrency(string $currency): void
    {
        $upper = strtoupper($currency);
        if (!in_array($upper, self::ISO_CURRENCIES, true)) {
            throw new \InvalidArgumentException(sprintf('Unknown or unsupported currency: %s', $currency));
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
    }
}
