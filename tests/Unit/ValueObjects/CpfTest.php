<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Unit\ValueObjects;

use Cardoso\StartupKit\Core\ValueObjects\Cpf;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CpfTest extends TestCase
{
    #[Test]
    public function valid_cpf(): void
    {
        $cpf = Cpf::fromString('529.982.247-25');
        $this->assertSame('52998224725', $cpf->value());
    }

    #[Test]
    public function valid_cpf_without_format(): void
    {
        $cpf = Cpf::fromString('52998224725');
        $this->assertSame('52998224725', $cpf->value());
    }

    #[Test]
    public function invalid_length_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cpf::fromString('1234567890');
    }

    #[Test]
    public function all_same_digits_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cpf::fromString('11111111111');
    }

    #[Test]
    public function invalid_checksum_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cpf::fromString('52998224726');
    }

    #[Test]
    public function formatted(): void
    {
        $cpf = Cpf::fromString('52998224725');
        $this->assertSame('529.982.247-25', $cpf->formatted());
    }

    #[Test]
    public function equals(): void
    {
        $a = Cpf::fromString('529.982.247-25');
        $b = Cpf::fromString('52998224725');
        $c = Cpf::fromString('111.444.777-35');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    #[Test]
    public function json_serialize(): void
    {
        $cpf = Cpf::fromString('52998224725');
        $this->assertSame('"52998224725"', json_encode($cpf));
    }
}
