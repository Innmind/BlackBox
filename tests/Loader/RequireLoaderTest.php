<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Loader;

use Innmind\BlackBox\{
    Loader\RequireLoader,
    Loader,
    Test,
    Exception\FileDoesntExist,
    Exception\NoTestGeneratorFound,
};
use Innmind\Url\Path;
use Innmind\Immutable\StreamInterface;
use PHPUnit\Framework\TestCase;

class RequireLoaderTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Loader::class,
            new RequireLoader
        );
    }

    public function testLoadSingleTest()
    {
        $load = new RequireLoader;

        $generators = $load(new Path('fixtures/test/single.php'));

        $this->assertInstanceOf(StreamInterface::class, $generators);
        $this->assertSame(\Generator::class, (string) $generators->type());
        $this->assertCount(1, $generators);
        $this->assertInstanceOf(
            Test::class,
            $generators->current()->current()
        );
    }

    public function testLoadMultipleTests()
    {
        $load = new RequireLoader;

        $generators = $load(new Path('fixtures/test/generator.php'));

        $this->assertInstanceOf(StreamInterface::class, $generators);
        $this->assertSame(\Generator::class, (string) $generators->type());
        $this->assertCount(1, $generators);
        $this->assertInstanceOf(
            Test::class,
            $generators->current()->current()
        );
        $generators->current()->next();
        $this->assertInstanceOf(
            Test::class,
            $generators->current()->current()
        );
    }

    public function testThrowWhenFileDoesntExist()
    {
        $load = new RequireLoader;

        $this->expectException(FileDoesntExist::class);
        $this->expectExceptionMessage('fixtures/foo.php');

        $load(new Path('fixtures/foo.php'));
    }

    public function testThrowWhenNoTestFound()
    {
        $load = new RequireLoader;

        $this->expectException(NoTestGeneratorFound::class);
        $this->expectExceptionMessage('fixtures/random_file.php');

        $load(new Path('fixtures/random_file.php'));
    }
}
