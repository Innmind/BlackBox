<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox;

use function Innmind\BlackBox\Assert\{
    same,
    notSame,
    true,
    false,
    contains,
    notContains,
    count,
    notCount,
    instance,
    primitive,
    int,
    float,
    object,
    null,
    bool,
    string,
    iterable,
    resource,
    fn,
    regex,
    sequence,
    stream,
    set,
    map,
    that,
    exception,
};
use Innmind\BlackBox\Assertion;
use PHPUnit\Framework\TestCase;

class AssertionsTest extends TestCase
{
    public function testFunctions()
    {
        $this->assertInstanceOf(Assertion\Same::class, same(42));
        $this->assertInstanceOf(Assertion\NotSame::class, notSame(42));
        $this->assertInstanceOf(Assertion\Same::class, true());
        $this->assertInstanceOf(Assertion\Same::class, false());
        $this->assertInstanceOf(Assertion\Contains::class, contains(42));
        $this->assertInstanceOf(Assertion\NotContains::class, notContains(42));
        $this->assertInstanceOf(Assertion\Count::class, count(42));
        $this->assertInstanceOf(Assertion\NotCount::class, notCount(42));
        $this->assertInstanceOf(Assertion\Instance::class, instance('stdClass'));
        $this->assertInstanceOf(Assertion\Primitive::class, primitive('int'));
        $this->assertInstanceOf(Assertion\Primitive::class, int());
        $this->assertInstanceOf(Assertion\Primitive::class, float());
        $this->assertInstanceOf(Assertion\Primitive::class, object());
        $this->assertInstanceOf(Assertion\Primitive::class, null());
        $this->assertInstanceOf(Assertion\Primitive::class, bool());
        $this->assertInstanceOf(Assertion\Primitive::class, string());
        $this->assertInstanceOf(Assertion\Primitive::class, iterable());
        $this->assertInstanceOf(Assertion\Primitive::class, resource());
        $this->assertInstanceOf(Assertion\Primitive::class, fn());
        $this->assertInstanceOf(Assertion\Regex::class, regex('~pattern~'));
        $this->assertInstanceOf(Assertion\Instance::class, sequence());
        $this->assertInstanceOf(Assertion\Stream::class, stream('int'));
        $this->assertInstanceOf(Assertion\Set::class, set('int'));
        $this->assertInstanceOf(Assertion\Map::class, map('int', 'int'));
        $this->assertInstanceOf(Assertion\That::class, that(function(){}));
        $this->assertInstanceOf(Assertion\Exception::class, exception('foo'));
    }
}
