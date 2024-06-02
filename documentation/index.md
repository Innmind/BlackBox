---
hide:
    - toc
---

# Getting started

Property Based Testing leverages randomness to prove the correctness of our code. Each time you run the tests it will generated a new set of data, the result is that the more you run your tests the more confident in the correctness of your code.

When it finds a scenario that fails it will print all the input data that lead to the failure so you can write a non regression test.

## Installation

```sh
composer require --dev innmind/black-box
```
