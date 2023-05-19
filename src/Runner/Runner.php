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
    /** @var \Generator<Proof> */
    private \Generator $proofs;
    private WithShrinking|WithoutShrinking $run;

    /**
     * @param \Generator<Proof> $proofs
     */
    private function __construct(
        Random $random,
        \Generator $proofs,
        WithShrinking|WithoutShrinking $run,
    ) {
        $this->random = $random;
        $this->proofs = $proofs;
        $this->run = $run;
    }

    public function __invoke(Assert $assert): void
    {
        foreach ($this->proofs as $proof) {
            // TODO print start of proof
            foreach ($proof->scenarii()->values($this->random) as $scenario) {
                try {
                    // TODO also inject the printer to show the shrinking process
                    ($this->run)($assert, $scenario);
                } catch (Failure $e) {
                    // TODO print the failure
                    break;
                } catch (EmptySet $e) {
                    // TODO print that the set is too restrictive
                    break;
                }
            }
            // TODO print end of proof
        }

        // TODO print end of proofs
    }
}
