<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Unit\Primitives\Result;

use PedroPCardoso\StartupKit\Core\Primitives\Errors\ConflictError;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\DomainError;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\ForbiddenError;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\NotFoundError;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\TimeoutError;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\UnauthorizedError;
use PedroPCardoso\StartupKit\Core\Primitives\Errors\ValidationError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ErrorsTest extends TestCase
{
    #[Test]
    public function not_found_error(): void
    {
        $error = NotFoundError::make('User', 'abc-123');

        $this->assertSame('not_found', $error->code());
        $this->assertStringContainsString('User', $error->message());
        $this->assertStringContainsString('abc-123', $error->message());
        $this->assertSame(['entity' => 'User', 'id' => 'abc-123'], $error->context());
    }

    #[Test]
    public function validation_error(): void
    {
        $error = ValidationError::make('email', 'invalid format');

        $this->assertSame('validation.email', $error->code());
        $this->assertStringContainsString('email', $error->message());
        $this->assertSame(['field' => 'email', 'reason' => 'invalid format'], $error->context());
    }

    #[Test]
    public function conflict_error(): void
    {
        $error = ConflictError::make('Order', 'duplicate number');

        $this->assertSame('conflict', $error->code());
        $this->assertStringContainsString('duplicate number', $error->message());
    }

    #[Test]
    public function unauthorized_error(): void
    {
        $error = UnauthorizedError::make();

        $this->assertSame('unauthorized', $error->code());
    }

    #[Test]
    public function forbidden_error(): void
    {
        $error = ForbiddenError::make('Custom message');

        $this->assertSame('forbidden', $error->code());
        $this->assertSame('Custom message', $error->message());
    }

    #[Test]
    public function timeout_error(): void
    {
        $error = TimeoutError::make('db.query');

        $this->assertSame('timeout', $error->code());
        $this->assertStringContainsString('db.query', $error->message());
    }

    #[Test]
    public function domain_error_is_instanceof_domain_error(): void
    {
        $error = NotFoundError::make('Test', 1);

        $this->assertInstanceOf(DomainError::class, $error);
    }

    #[Test]
    public function error_json_serialization(): void
    {
        $error = NotFoundError::make('Product', 'p-1');
        $json = json_encode($error);

        $this->assertJson($json);
        $data = json_decode($json, true);

        $this->assertSame('not_found', $data['code']);
        $this->assertSame('Product', $data['context']['entity']);
    }
}
