<?php

declare(strict_types=1);

namespace Cardoso\StartupKit\Core\Tests\Unit\Primitives\Result;

use Cardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use Cardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use Cardoso\StartupKit\Core\Primitives\Result\Result;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ResultTest extends TestCase
{
    #[Test]
    public function ok_has_isOk_true_and_unwrap_returns_value(): void
    {
        $result = Result::ok(42);

        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isErr());
        $this->assertSame(42, $result->unwrap());
    }

    #[Test]
    public function err_has_isErr_true_and_unwrap_throws(): void
    {
        $error = NotFoundError::make('Test', 1);
        $result = Result::err($error);

        $this->assertFalse($result->isOk());
        $this->assertTrue($result->isErr());

        $this->expectException(\RuntimeException::class);
        $result->unwrap();
    }

    #[Test]
    public function unwrapOr_returns_value_on_ok(): void
    {
        $result = Result::ok(42);

        $this->assertSame(42, $result->unwrapOr(0));
    }

    #[Test]
    public function unwrapOr_returns_default_on_err(): void
    {
        $result = Result::err(NotFoundError::make('Test', 1));

        $this->assertSame(0, $result->unwrapOr(0));
    }

    #[Test]
    public function map_applies_only_on_ok(): void
    {
        $result = Result::ok(41)
            ->map(fn(int $v) => $v + 1);

        $this->assertTrue($result->isOk());
        $this->assertSame(42, $result->unwrap());
    }

    #[Test]
    public function map_skips_on_err(): void
    {
        $result = Result::err(NotFoundError::make('Test', 1))
            ->map(fn($v) => $v + 1);

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function mapErr_applies_only_on_err(): void
    {
        $result = Result::err(ValidationError::make('field', 'bad'))
            ->mapErr(fn($e) => NotFoundError::make('Other', 1));

        $this->assertTrue($result->isErr());
        $this->assertSame('not_found', $result->match(fn() => null, fn($e) => $e->code()));
    }

    #[Test]
    public function flatMap_chains_on_ok(): void
    {
        $result = Result::ok(5)
            ->flatMap(fn(int $v) => Result::ok($v * 2));

        $this->assertTrue($result->isOk());
        $this->assertSame(10, $result->unwrap());
    }

    #[Test]
    public function flatMap_short_circuits_on_err(): void
    {
        $result = Result::ok(5)
            ->flatMap(fn(int $v) => Result::err(ValidationError::make('value', 'too high')));

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function match_dispatches_ok_callback(): void
    {
        $result = Result::ok('success');

        $output = $result->match(
            ok: fn($v) => strtoupper($v),
            err: fn($e) => 'error',
        );

        $this->assertSame('SUCCESS', $output);
    }

    #[Test]
    public function match_dispatches_err_callback(): void
    {
        $result = Result::err(NotFoundError::make('Test', 1));

        $output = $result->match(
            ok: fn($v) => 'ok',
            err: fn($e) => $e->code(),
        );

        $this->assertSame('not_found', $output);
    }

    #[Test]
    public function result_is_immutable(): void
    {
        $original = Result::ok(1);
        $mapped = $original->map(fn($v) => $v + 1);

        $this->assertSame(1, $original->unwrap());
        $this->assertSame(2, $mapped->unwrap());
    }

    #[Test]
    public function json_serialize_ok(): void
    {
        $result = Result::ok(['id' => 1]);
        $json = json_encode($result);

        $this->assertJson($json);
        $this->assertStringContainsString('"ok":true', $json);
    }

    #[Test]
    public function json_serialize_err(): void
    {
        $result = Result::err(NotFoundError::make('User', 'abc'));
        $json = json_encode($result);

        $this->assertJson($json);
        $this->assertStringContainsString('"ok":false', $json);
        $this->assertStringContainsString('"code":"not_found"', $json);
    }
}
