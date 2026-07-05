<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\ValueObjects;

final class DateRange implements \JsonSerializable
{
    private function __construct(
        private readonly \DateTimeImmutable $start,
        private readonly \DateTimeImmutable $end,
    ) {
        if ($start > $end) {
            throw new \InvalidArgumentException('Start date must be before or equal to end date.');
        }
    }

    public static function fromDateTime(\DateTimeImmutable $start, \DateTimeImmutable $end): self
    {
        return new self($start, $end);
    }

    public static function fromStrings(string $start, string $end): self
    {
        return new self(
            new \DateTimeImmutable($start),
            new \DateTimeImmutable($end),
        );
    }

    public function start(): \DateTimeImmutable
    {
        return $this->start;
    }

    public function end(): \DateTimeImmutable
    {
        return $this->end;
    }

    public function contains(\DateTimeImmutable $date): bool
    {
        return $date >= $this->start && $date <= $this->end;
    }

    public function overlaps(self $other): bool
    {
        return $this->start <= $other->end && $this->end >= $other->start;
    }

    public function durationDays(): int
    {
        return (int) $this->start->diff($this->end)->days;
    }

    public function equals(self $other): bool
    {
        return $this->start == $other->start && $this->end == $other->end;
    }

    public function jsonSerialize(): array
    {
        return [
            'start' => $this->start->format(\DateTimeInterface::ATOM),
            'end' => $this->end->format(\DateTimeInterface::ATOM),
        ];
    }
}
