---
hide:
    - navigation
    - toc
---

# Welcome to BlackBox

BlackBox is a [Property Based Testing](https://en.wikipedia.org/wiki/Software_testing#Property_testing) framework.

It leverages randomness to prove the correctness of your code.

It's the main testing framework for the [Innmind ecosystem](https://innmind.github.io/documentation/).

It allows to:

<div class="annotate" markdown>
- write [tests](preface/terminology.md#test) (1)
- write [proofs](preface/terminology.md#proof)
- write [properties](preface/terminology.md#property)
- generate [random data](preface/terminology.md#set)
</div>

1. Like any other PHP testing framework

Its Functional[^1] design also allows you to use it for your own scenarii.

??? example "Sneak peek"
    ```php title="tests.php"
    use Innmind\BlackBox\{
        Application
        Set,
        Runner\Assert,
    };

    Application::new([])
        ->scenariiPerProof(1_000)
        ->tryToProve(static function() {
            yield proof(
                'Add is commutative',
                given(
                    Set\Integers::any(),
                    Set\Integers::any(),
                ),
                static function(Assert $assert, int $a, int $b) {
                    $assert->same(
                        add($a, $b),
                        add($b, $a),
                    );
                },
            );
        })
        ->exit();
    ```

[^1]: As in Functional Programming
