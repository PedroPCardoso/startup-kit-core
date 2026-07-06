<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Unit\ValueObjects;

use PedroPCardoso\StartupKit\Core\ValueObjects\Money;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    #[Test]
    public function from_int_creates_valid_money(): void
    {
        $money = Money::fromInt(1000, 'BRL');

        $this->assertSame(1000, $money->amount());
        $this->assertSame('BRL', $money->currency());
    }

    #[Test]
    public function from_float_creates_valid_money(): void
    {
        $money = Money::fromFloat(10.50, 'USD');

        $this->assertSame(1050, $money->amount());
        $this->assertSame('USD', $money->currency());
    }

    #[Test]
    public function zero_creates_zero_money(): void
    {
        $money = Money::zero('BRL');

        $this->assertSame(0, $money->amount());
    }

    #[Test]
    public function add_same_currency(): void
    {
        $a = Money::fromInt(100, 'BRL');
        $b = Money::fromInt(200, 'BRL');

        $result = $a->add($b);

        $this->assertSame(300, $result->amount());
        $this->assertSame('BRL', $result->currency());
    }

    #[Test]
    public function add_different_currency_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $a = Money::fromInt(100, 'BRL');
        $b = Money::fromInt(200, 'USD');

        $a->add($b);
    }

    #[Test]
    public function sub_same_currency(): void
    {
        $a = Money::fromInt(300, 'BRL');
        $b = Money::fromInt(100, 'BRL');

        $result = $a->sub($b);

        $this->assertSame(200, $result->amount());
    }

    #[Test]
    public function sub_negative_result_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $a = Money::fromInt(100, 'BRL');
        $b = Money::fromInt(200, 'BRL');

        $a->sub($b);
    }

    #[Test]
    public function mul(): void
    {
        $money = Money::fromInt(100, 'BRL');
        $result = $money->mul(3);

        $this->assertSame(300, $result->amount());
    }

    #[Test]
    public function mul_negative_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromInt(100, 'BRL')->mul(-1);
    }

    #[Test]
    public function div(): void
    {
        $money = Money::fromInt(100, 'BRL');
        $result = $money->div(3);

        $this->assertSame(33, $result->amount());
    }

    #[Test]
    public function div_by_zero_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Money::fromInt(100, 'BRL')->div(0);
    }

    #[Test]
    public function equals(): void
    {
        $a = Money::fromInt(100, 'BRL');
        $b = Money::fromInt(100, 'BRL');
        $c = Money::fromInt(200, 'BRL');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    #[Test]
    public function to_float(): void
    {
        $money = Money::fromInt(1050, 'BRL');

        $this->assertSame(10.50, $money->toFloat());
    }

    #[Test]
    public function negative_amount_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::fromInt(-1, 'BRL');
    }

    #[Test]
    public function invalid_currency_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Money::fromInt(100, 'XYZ');
    }

    #[Test]
    public function json_serialize(): void
    {
        $money = Money::fromInt(1000, 'BRL');
        $json = json_encode($money);

        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertSame(1000, $data['amount']);
        $this->assertSame('BRL', $data['currency']);
    }

    #[Test]
    public function immutability(): void
    {
        $original = Money::fromInt(100, 'BRL');
        $added = $original->add(Money::fromInt(50, 'BRL'));

        $this->assertSame(100, $original->amount());
        $this->assertSame(150, $added->amount());
    }
}
