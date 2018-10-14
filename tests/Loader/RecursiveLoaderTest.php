<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader\RecursiveLoader,
    Loader\SilenceWhenNoGeneratorFound,
    Loader\RequireLoader,
    Loader,
    Test,
};
use Innmind\Url\Path;
use Innmind\Immutable\StreamInterface;
use PHPUnit\Framework\TestCase;

class RecursiveLoaderTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Loader::class,
            new RecursiveLoader($this->createMock(Loader::class))
        );
    }

    public function testInvokation()
    {
        $load = new RecursiveLoader(
            new SilenceWhenNoGeneratorFound(
                new RequireLoader
            )
        );

        $generators = $load(new Path('fixtures'));

        $this->assertInstanceOf(StreamInterface::class, $generators);
        $this->assertSame(\Generator::class, (string) $generators->type());
        $this->assertCount(2, $generators);
        $this->assertInstanceOf(Test::class, $generators->current()->current());
        $generators->next();
        $this->assertInstanceOf(Test::class, $generators->current()->current());
    }
}
