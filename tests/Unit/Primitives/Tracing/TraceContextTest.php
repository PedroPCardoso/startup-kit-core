<?php

declare(strict_types=1);

namespace PedroPCardoso\StartupKit\Core\Tests\Unit\Primitives\Tracing;

use PedroPCardoso\StartupKit\Core\Primitives\Tracing\TraceContext;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TraceContextTest extends TestCase
{
    #[Test]
    public function parse_valid_w3c_traceparent(): void
    {
        $context = TraceContext::fromW3c(
            '00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01'
        );

        $this->assertNotNull($context);
        $this->assertSame('0af7651916cd43dd8448eb211c80319c', $context->traceId());
        $this->assertSame('b7ad6b7169203331', $context->spanId());
        $this->assertSame('01', $context->flags());
        $this->assertTrue($context->isSampled());
    }

    #[Test]
    public function parse_invalid_traceparent_returns_null(): void
    {
        $this->assertNull(TraceContext::fromW3c('invalid'));
        $this->assertNull(TraceContext::fromW3c(''));
        $this->assertNull(TraceContext::fromW3c('00-too-short'));
    }

    #[Test]
    public function generate_creates_valid_context(): void
    {
        $context = TraceContext::generate();

        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $context->traceId());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{16}$/', $context->spanId());
        $this->assertSame('01', $context->flags());
        $this->assertTrue($context->isSampled());
    }

    #[Test]
    public function to_w3c_traceparent(): void
    {
        $context = TraceContext::fromW3c(
            '00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01'
        );

        $this->assertSame(
            '00-0af7651916cd43dd8448eb211c80319c-b7ad6b7169203331-01',
            $context->toW3cTraceparent()
        );
    }

    #[Test]
    public function with_span_id_returns_new_instance(): void
    {
        $context = TraceContext::generate();
        $child = $context->withSpanId('aaaaaaaaaaaaaaaa');

        $this->assertSame('aaaaaaaaaaaaaaaa', $child->spanId());
        $this->assertNotSame($child->spanId(), $context->spanId());
    }

    #[Test]
    public function from_components(): void
    {
        $context = TraceContext::fromComponents(
            traceId: '0af7651916cd43dd8448eb211c80319c',
            spanId: 'b7ad6b7169203331',
            flags: '01',
        );

        $this->assertSame('0af7651916cd43dd8448eb211c80319c', $context->traceId());
        $this->assertSame('b7ad6b7169203331', $context->spanId());
    }

    #[Test]
    public function json_serialize(): void
    {
        $context = TraceContext::generate();
        $json = json_encode($context);

        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertArrayHasKey('trace_id', $data);
        $this->assertArrayHasKey('span_id', $data);
    }
}
