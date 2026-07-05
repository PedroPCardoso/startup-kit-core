<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\ValueObjects;

final class Uuid implements \JsonSerializable
{
    private function __construct(
        private readonly string $uuid,
    ) {}

    public static function fromString(string $uuid): self
    {
        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $uuid)) {
            throw new \InvalidArgumentException(sprintf('Invalid UUID format: %s', $uuid));
        }

        return new self(strtolower($uuid));
    }

    public static function generate(): self
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return new self(vsprintf('%s-%s-%s-%s-%s', [
            bin2hex(substr($data, 0, 4)),
            bin2hex(substr($data, 4, 2)),
            bin2hex(substr($data, 6, 2)),
            bin2hex(substr($data, 8, 2)),
            bin2hex(substr($data, 10, 6)),
        ]));
    }

    public function value(): string
    {
        return $this->uuid;
    }

    public function equals(self $other): bool
    {
        return $this->uuid === $other->uuid;
    }

    public function jsonSerialize(): string
    {
        return $this->uuid;
    }
}
