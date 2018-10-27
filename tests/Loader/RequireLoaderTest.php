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

        $generator = $load(new Path('fixtures/test/single.php'));

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertInstanceOf(
            Test::class,
            $generator->current()
        );
    }

    public function testLoadMultipleTests()
    {
        $load = new RequireLoader;

        $generator = $load(new Path('fixtures/test/generator.php'));

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertInstanceOf(
            Test::class,
            $generator->current()
        );
        $generator->next();
        $this->assertInstanceOf(
            Test::class,
            $generator->current()
        );
    }

    public function testThrowWhenFileDoesntExist()
    {
        $load = new RequireLoader;

        $this->expectException(FileDoesntExist::class);
        $this->expectExceptionMessage('fixtures/foo.php');

        $generator = $load(new Path('fixtures/foo.php'));
        $generator->next();
    }

    public function testThrowWhenNoTestFound()
    {
        $load = new RequireLoader;

        $this->expectException(NoTestGeneratorFound::class);
        $this->expectExceptionMessage('fixtures/random_file.php');

        $generator = $load(new Path('fixtures/random_file.php'));
        $generator->next();
    }
}
