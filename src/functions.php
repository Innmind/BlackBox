<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\CLI\{
    Environment\GlobalEnvironment,
    Commands,
};
use Innmind\Stream\Writable;
use Innmind\StackTrace\{
    StackTrace,
    Throwable,
};
use Innmind\Url\Path;
use Innmind\Immutable\{
    SetInterface,
    Set,
    Str,
    Sequence,
};

function given(Given\InitialValue ...$initialValues): Given
{
    return new Given(...$initialValues);
}

function any(string $name, \Iterator $set): Given\InitialValue
{
    return new Given\Any(
        new Given\InitialValue\Name($name),
        $set
    );
}

function value(string $name, $value): Given\InitialValue
{
    return any($name, Set::of('mixed', $value));
}

function generate(string $name, callable $generate): Given\InitialValue
{
    return new Given\Generate(
        new Given\InitialValue\Name($name),
        $generate
    );
}

function when(callable $callable): When
{
    return new When($callable);
}

function then(Assertion ...$assertions): Then
{
    return new Then(...$assertions);
}

function test(string $name, Given $given, When $when, Then $then): Test
{
    return new Test\Test(new Test\Name($name), $given, $when, $then);
}

function run(string ...$suites): void
{
    $suites = Sequence::of(...$suites)->map(static function(string $suite): Path {
        return new Path($suite);
    });

    $run = new Commands(
        new CLI(
            new Suites(
                new Suite(
                    new Loader\RecursiveLoader(
                        new Loader\SilenceWhenNoGeneratorFound(
                            new Loader\RequireLoader
                        )
                    ),
                    new Runner\SameProcess
                )
            ),
            ...$suites
        )
    );
    $env = new GlobalEnvironment;

    try {
        $run($env);
    } catch (\Throwable $e) {
        $stack = new StackTrace($e);

        $print = static function(Writable $stream, Throwable $e): Writable {
            return $stream
                ->write(
                    Str::of('%s<%s>(%s)')->sprintf($e->class(), $e->code(), $e->message())
                )
                ->write($e->trace()->join("\n")->prepend("\n\n"));
        };

        $print($env->error(), $stack->throwable());
        $stack->previous()->reduce(
            $env->error(),
            static function(Writable $stream, Throwable $throwable) use ($print): Writable {
                return $print(
                    $stream->write(Str::of("\n\nCaused by:\n\n")),
                    $throwable
                );
            }
        );
    }

    exit($env->exitCode()->toInt());
}
