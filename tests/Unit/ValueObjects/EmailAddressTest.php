<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Unit\ValueObjects;

use Cardoso\StartupKit\Core\ValueObjects\EmailAddress;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailAddressTest extends TestCase
{
    #[Test]
    public function valid_email(): void
    {
        $email = EmailAddress::fromString('test@example.com');

        $this->assertSame('test@example.com', $email->value());
    }

    #[Test]
    public function normalizes_to_lowercase(): void
    {
        $email = EmailAddress::fromString('Test@Example.COM');

        $this->assertSame('test@example.com', $email->value());
    }

    #[Test]
    public function trims_whitespace(): void
    {
        $email = EmailAddress::fromString('  user@example.com  ');

        $this->assertSame('user@example.com', $email->value());
    }

    #[Test]
    public function invalid_email_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EmailAddress::fromString('not-an-email');
    }

    #[Test]
    public function email_without_at_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        EmailAddress::fromString('userexample.com');
    }

    #[Test]
    public function local_part(): void
    {
        $email = EmailAddress::fromString('user@example.com');

        $this->assertSame('user', $email->localPart());
    }

    #[Test]
    public function domain(): void
    {
        $email = EmailAddress::fromString('user@example.com');

        $this->assertSame('example.com', $email->domain());
    }

    #[Test]
    public function equals(): void
    {
        $a = EmailAddress::fromString('user@example.com');
        $b = EmailAddress::fromString('User@Example.COM');
        $c = EmailAddress::fromString('other@example.com');

        $this->assertTrue($a->equals($b));
        $this->assertFalse($a->equals($c));
    }

    #[Test]
    public function json_serialize(): void
    {
        $email = EmailAddress::fromString('user@example.com');

        $this->assertSame('"user@example.com"', json_encode($email));
    }
}
