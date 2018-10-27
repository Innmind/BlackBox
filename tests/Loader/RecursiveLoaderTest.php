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

        $generator = $load(new Path('fixtures'));

        $this->assertInstanceOf(\Generator::class, $generator);
        $this->assertInstanceOf(Test::class, $generator->current());
        $generator->next();
        $this->assertInstanceOf(Test::class, $generator->current());
    }
}
