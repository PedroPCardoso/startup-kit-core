<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Primitives\Tracing;

final class TraceContext implements \JsonSerializable
{
    private const VERSION = '00';
    private const FLAGS_SAMPLED = '01';

    private function __construct(
        private readonly string $traceId,
        private readonly string $spanId,
        private readonly string $flags,
    ) {}

    public static function fromW3c(string $traceparent): ?self
    {
        if (!preg_match('/^([0-9a-f]{2})-([0-9a-f]{32})-([0-9a-f]{16})-([0-9a-f]{2})$/i', $traceparent, $m)) {
            return null;
        }

        $version = $m[1];
        if ($version !== self::VERSION && $version !== 'ff') {
            return null;
        }

        return new self(
            traceId: strtolower($m[2]),
            spanId: strtolower($m[3]),
            flags: strtolower($m[4]),
        );
    }

    public static function generate(): self
    {
        return new self(
            traceId: self::generateHex(32),
            spanId: self::generateHex(16),
            flags: self::FLAGS_SAMPLED,
        );
    }

    public static function fromComponents(string $traceId, string $spanId, string $flags = '01'): self
    {
        return new self($traceId, $spanId, $flags);
    }

    public function traceId(): string
    {
        return $this->traceId;
    }

    public function spanId(): string
    {
        return $this->spanId;
    }

    public function flags(): string
    {
        return $this->flags;
    }

    public function isSampled(): bool
    {
        return ($this->flags[1] ?? '0') === '1';
    }

    public function withSpanId(string $spanId): self
    {
        return new self($this->traceId, $spanId, $this->flags);
    }

    public function toW3cTraceparent(): string
    {
        return sprintf('%s-%s-%s-%s', self::VERSION, $this->traceId, $this->spanId, $this->flags);
    }

    public function jsonSerialize(): array
    {
        return [
            'trace_id' => $this->traceId,
            'span_id' => $this->spanId,
            'flags' => $this->flags,
        ];
    }

    private static function generateHex(int $length): string
    {
        $bytes = random_bytes(intdiv($length, 2));
        return substr(bin2hex($bytes), 0, $length);
    }
}
