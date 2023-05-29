<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Random\{
    Randomizer,
    Engine\Secure,
    Engine\Mt19937,
};

enum Random
{
    case default;
    case secure;
    case mersenneTwister;

    /**
     * This method should be __invoke but Psalm doesn't like it for some reason
     */
    public function between(int $min, int $max): int
    {
        // By default use a crypto secure engine in order to generate true
        // random data
        $engine = match ($this) {
            self::default, self::secure => new Secure,
            self::mersenneTwister => new Mt19937,
        };
        $random = new Randomizer($engine);

        return $random->getInt($min, $max);
    }
}
