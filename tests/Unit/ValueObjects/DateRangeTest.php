<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Unit\ValueObjects;

use Cardoso\StartupKit\Core\ValueObjects\DateRange;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DateRangeTest extends TestCase
{
    #[Test]
    public function valid_range(): void
    {
        $range = DateRange::fromStrings('2026-01-01', '2026-12-31');

        $this->assertEquals(new \DateTimeImmutable('2026-01-01'), $range->start());
        $this->assertEquals(new \DateTimeImmutable('2026-12-31'), $range->end());
    }

    #[Test]
    public function start_before_end(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        DateRange::fromStrings('2026-12-31', '2026-01-01');
    }

    #[Test]
    public function same_date_is_valid(): void
    {
        $range = DateRange::fromStrings('2026-06-15', '2026-06-15');

        $this->assertSame(0, $range->durationDays());
    }

    #[Test]
    public function contains_date(): void
    {
        $range = DateRange::fromStrings('2026-01-01', '2026-12-31');

        $this->assertTrue($range->contains(new \DateTimeImmutable('2026-06-15')));
        $this->assertTrue($range->contains(new \DateTimeImmutable('2026-01-01')));

        $this->assertFalse($range->contains(new \DateTimeImmutable('2025-12-31')));
        $this->assertFalse($range->contains(new \DateTimeImmutable('2027-01-01')));
    }

    #[Test]
    public function overlaps(): void
    {
        $a = DateRange::fromStrings('2026-01-01', '2026-06-30');
        $b = DateRange::fromStrings('2026-05-01', '2026-12-31');
        $c = DateRange::fromStrings('2027-01-01', '2027-12-31');

        $this->assertTrue($a->overlaps($b));
        $this->assertFalse($a->overlaps($c));
    }

    #[Test]
    public function duration_days(): void
    {
        $range = DateRange::fromStrings('2026-01-01', '2026-01-10');

        $this->assertSame(9, $range->durationDays());
    }

    #[Test]
    public function equals(): void
    {
        $a = DateRange::fromStrings('2026-01-01', '2026-12-31');
        $b = DateRange::fromStrings('2026-01-01', '2026-12-31');
        $c = DateRange::fromStrings('2026-01-01', '2026-06-30');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    #[Test]
    public function json_serialize(): void
    {
        $range = DateRange::fromStrings('2026-01-01', '2026-12-31');
        $json = json_encode($range);

        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertArrayHasKey('start', $data);
        $this->assertArrayHasKey('end', $data);
    }
}
