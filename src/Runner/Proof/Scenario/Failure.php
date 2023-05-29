<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof\Scenario;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof\Scenario,
    Set\Value,
};

final class Failure extends \Exception
{
    private Assert\Failure $failure;
    /** @var Value<Scenario> */
    private Value $scenario;

    /**
     * @param Value<Scenario> $scenario
     */
    private function __construct(
        Assert\Failure $failure,
        Value $scenario,
    ) {
        $this->failure = $failure;
        $this->scenario = $scenario;
    }

    /**
     * @internal
     *
     * @param Value<Scenario> $scenario
     */
    public static function of(
        Assert\Failure $failure,
        Value $scenario,
    ): self {
        return new self($failure, $scenario);
    }

    /**
     * @return Value<Scenario>
     */
    public function scenario(): Value
    {
        return $this->scenario;
    }

    public function assertion(): Assert\Failure
    {
        return $this->failure;
    }
}
