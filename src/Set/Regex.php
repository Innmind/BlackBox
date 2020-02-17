<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use ReverseRegex\{
    Lexer,
    Random\GeneratorInterface,
    Random\SrandRandom,
    Random\SimpleRandom,
    Parser,
    Generator\Scope,
};

/**
 * @implements Set<string>
 */
final class Regex implements Set
{
    private Parser $parser;
    private GeneratorInterface $random;
    private int $size;
    private \Closure $predicate;

    private function __construct(string $expression)
    {
        $lexer = new Lexer($expression);
        $random = new SimpleRandom(\random_int(\PHP_INT_MIN, \PHP_INT_MAX));
        $this->random = new class($random) implements GeneratorInterface {
            private GeneratorInterface $random;

            public function __construct(GeneratorInterface $random)
            {
                $this->random = $random;
            }

            /**
             * @psalm-suppress MissingReturnType
             * @param int $min
             * @param int|null $max
             */
            public function generate($min = 0,$max = null)
            {
                // we force the max to 128 here so it never exceeds the hardocded
                // max value in the SimpleRandom implementation and doesn't take
                // too long to generate unbounded strings lengths
                return $this->random->generate($min, 128);
            }

            /**
             * @psalm-suppress MissingReturnType
             * @param int $seed
             */
            public function seed($seed = null)
            {
                return $this->random->seed($seed);
            }

            /**
             * @param int|null $value
             *
             * @return float
             */
            public function max($value = null)
            {
                // even though the interface doesn't describe an expected argument
                // the SimpleRandom implementation has one
                /**
                 * @psalm-suppress TooManyArguments
                 * @var float
                 */
                return $this->random->max($value);
            }
        };
        $this->parser = new Parser($lexer, new Scope, new Scope);
        $this->size = 100;
        $this->predicate = static fn(string $str): bool => \preg_match("~^$expression$~", $str) === 1;
    }

    /**
     * @see https://github.com/icomefromthenet/ReverseRegex For the supported expressions
     */
    public static function for(string $expression): self
    {
        return new self($expression);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;
        $self = clone $this;
        /** @psalm-suppress MissingClosureParamType */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(): \Generator
    {
        $iterations = 0;

        do {
            $value = '';
            /** @var Parser */
            $parser = $this->parser->parse();
            /** @var Scope */
            $scope = $parser->getResult();
            $scope->generate($value, $this->random);

            if (!($this->predicate)($value)) {
                continue ;
            }

            /** @psalm-suppress MixedArgument because of the reference in the generate method */
            yield Value::immutable(
                $value,
                $this->shrink($value),
            );
            ++$iterations;
        } while ($iterations < $this->size);
    }

    private function shrink(string $value): ?Dichotomy
    {
        if ($value === '') {
            return null;
        }

        return new Dichotomy(
            $this->removeTrailingCharacter($value),
            $this->removeLeadingCharacter($value),
        );
    }

    private function removeTrailingCharacter(string $value): callable
    {
        $shrinked = \mb_substr($value, 0, -1, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    private function removeLeadingCharacter(string $value): callable
    {
        $shrinked = \mb_substr($value, 1, null, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * Non shrinkable as it is alreay the minimum value accepted by the predicate
     */
    private function identity(string $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
