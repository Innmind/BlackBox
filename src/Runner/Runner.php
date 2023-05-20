<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Random,
    Runner\Runner\WithShrinking,
    Runner\Runner\WithoutShrinking,
    Exception\EmptySet,
};

final class Runner
{
    private Random $random;
    private Printer $print;
    private WithShrinking|WithoutShrinking $run;
    /** @var \Generator<Proof> */
    private \Generator $proofs;

    /**
     * @param \Generator<Proof> $proofs
     */
    private function __construct(
        Random $random,
        Printer $print,
        WithShrinking|WithoutShrinking $run,
        \Generator $proofs,
    ) {
        $this->random = $random;
        $this->print = $print;
        $this->run = $run;
        $this->proofs = $proofs;
    }

    public function __invoke(Assert $assert): void
    {
        $this->print->start();

        foreach ($this->proofs as $proof) {
            $print = $this->print->proof($proof->name());

            try {
                foreach ($proof->scenarii()->values($this->random) as $scenario) {
                    try {
                        ($this->run)($print, $assert, $scenario);
                    } catch (Failure $e) {
                        $print->failed($e);

                        break;
                    }
                }
            } catch (EmptySet $e) {
                $print->emptySet();

                break;
            }

            $print->end();
        }

        $this->print->end();
    }
}
