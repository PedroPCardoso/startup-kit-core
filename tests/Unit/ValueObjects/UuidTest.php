<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Unit\ValueObjects;

use PedroPCardoso\StartupKit\Core\ValueObjects\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    #[Test]
    public function valid_uuid(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $uuid->value());
    }

    #[Test]
    public function generate_creates_valid_uuid(): void
    {
        $uuid = Uuid::generate();
        $value = $uuid->value();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $value);
    }

    #[Test]
    public function generated_uuid_has_version_4(): void
    {
        $uuid = Uuid::generate();
        $parts = explode('-', $uuid->value());

        $this->assertSame('4', $parts[2][0]);
    }

    #[Test]
    public function invalid_format_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Uuid::fromString('not-a-uuid');
    }

    #[Test]
    public function normalizes_to_lowercase(): void
    {
        $uuid = Uuid::fromString('550E8400-E29B-41D4-A716-446655440000');

        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $uuid->value());
    }

    #[Test]
    public function equals(): void
    {
        $a = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');
        $b = Uuid::fromString('550E8400-E29B-41D4-A716-446655440000');
        $c = Uuid::generate();

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    #[Test]
    public function json_serialize(): void
    {
        $uuid = Uuid::fromString('550e8400-e29b-41d4-a716-446655440000');

        $this->assertJson(json_encode($uuid));
    }
}
