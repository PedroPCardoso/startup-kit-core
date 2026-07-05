<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Unit\ValueObjects;

use Cardoso\StartupKit\Core\ValueObjects\Cnpj;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CnpjTest extends TestCase
{
    #[Test]
    public function valid_cnpj(): void
    {
        $cnpj = Cnpj::fromString('11.222.333/0001-81');
        $this->assertSame('11222333000181', $cnpj->value());
    }

    #[Test]
    public function valid_cnpj_without_format(): void
    {
        $cnpj = Cnpj::fromString('11222333000181');
        $this->assertSame('11222333000181', $cnpj->value());
    }

    #[Test]
    public function invalid_length_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cnpj::fromString('1234567890123');
    }

    #[Test]
    public function all_same_digits_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cnpj::fromString('11111111111111');
    }

    #[Test]
    public function invalid_checksum_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Cnpj::fromString('11222333000182');
    }

    #[Test]
    public function formatted(): void
    {
        $cnpj = Cnpj::fromString('11222333000181');
        $this->assertSame('11.222.333/0001-81', $cnpj->formatted());
    }

    #[Test]
    public function equals(): void
    {
        $a = Cnpj::fromString('11.222.333/0001-81');
        $b = Cnpj::fromString('11222333000181');

        $this->assertTrue($a->equals($b));
    }

    #[Test]
    public function json_serialize(): void
    {
        $cnpj = Cnpj::fromString('11222333000181');
        $this->assertSame('"11222333000181"', json_encode($cnpj));
    }
}
