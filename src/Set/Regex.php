<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};
use ReverseRegex\{
    Lexer,
    Random\GeneratorInterface,
    Random\SrandRandom,
    Random\SimpleRandom,
    Parser,
    Generator\Scope,
    Exception,
};

/**
 * @implements Set<string>
 */
final class Regex implements Set
{
    private Parser $parser;
    private int $size;
    private \Closure $predicate;

    private function __construct(string $expression)
    {
        $lexer = new Lexer($expression);
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
        $self->predicate = static function(string $value) use ($previous, $predicate): bool {
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
    public function values(Random $rand): \Generator
    {
        $iterations = 0;

        do {
            $value = '';
            /** @var Parser */
            $parser = $this->parser->parse();
            /** @var Scope */
            $scope = $parser->getResult();
            $scope->generate($value, $this->random($rand));

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

    /**
     * @return Dichotomy<string>|null
     */
    private function shrink(string $value): ?Dichotomy
    {
        if ($value === '') {
            return null;
        }

        $strategyA = $this->removeTrailingCharacter($value);
        $strategyB = $this->removeLeadingCharacter($value);

        if (\is_null($strategyA) && \is_null($strategyB)) {
            return null;
        }

        return new Dichotomy(
            $strategyA ?? $this->identity($value),
            $strategyB ?? $this->identity($value),
        );
    }

    /**
     * @return callable(): Value<string>
     */
    private function removeTrailingCharacter(string $value): ?callable
    {
        /** @var string */
        $shrinked = \mb_substr($value, 0, -1, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return null;
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * @return callable(): Value<string>
     */
    private function removeLeadingCharacter(string $value): ?callable
    {
        /** @var string */
        $shrinked = \mb_substr($value, 1, null, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return null;
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * Non shrinkable as it is alreay the minimum value accepted by the predicate
     *
     * @return callable(): Value<string>
     */
    private function identity(string $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }

    private function random(Random $rand): GeneratorInterface
    {
        $random = new SimpleRandom($rand(\PHP_INT_MIN, \PHP_INT_MAX));

        return new class($random) implements GeneratorInterface {
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
                // by default we try the default min/max strategy but in case it
                // fails due to the maxx being too high (cf SimpleRandom hardcoded
                // limit) we then try with a maximum of 128 so it doesn't take
                // too long to generate the data
                try {
                    return $this->random->generate($min, $max);
                } catch (Exception $e) {
                    return $this->random->generate(
                        $min,
                        $max ? \min($max, 128) : null,
                    );
                }
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
    }
}
