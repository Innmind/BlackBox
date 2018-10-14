<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Then;

use Innmind\BlackBox\Then\Failure;
use Innmind\Immutable\{
    Str,
    StreamInterface,
};
use PHPUnit\Framework\TestCase;

class FailureTest extends TestCase
{
    public function testInterface()
    {
        $failure = new Failure('foo');

        $this->assertInstanceOf(Str::class, $failure->message());
        $this->assertSame('foo', (string) $failure->message());
        $this->assertInstanceOf(StreamInterface::class, $failure->stackTrace());
        $this->assertSame(Str::class, (string) $failure->stackTrace()->type());
        $this->assertCount(11, $failure->stackTrace());
        $this->assertTrue($failure->stackTrace()->get(0)->contains('tests/Then/FailureTest.php:17'));
    }
}
