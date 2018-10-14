<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Then;

use Innmind\Immutable\{
    StreamInterface,
    Stream,
    Sequence,
    Str,
};

final class Failure
{
    private $message;
    private $stackTrace;

    public function __construct(string $message)
    {
        $this->message = Str::of($message);

        $e = new \Exception;
        $this->stackTrace = Sequence::of(...$e->getTrace())
            ->filter(static function(array $trace): bool {
                return array_key_exists('file', $trace) && array_key_exists('line', $trace);
            })
            ->reduce(
                Stream::of(Str::class),
                static function(StreamInterface $stack, array $trace): StreamInterface {
                    return $stack->add(
                        Str::of("{$trace['file']}:{$trace['line']}")
                    );
                }
            );
    }

    public function message(): Str
    {
        return $this->message;
    }

    /**
     * @return StreamInterface<Str>
     */
    public function stackTrace(): StreamInterface
    {
        return $this->stackTrace;
    }
}
