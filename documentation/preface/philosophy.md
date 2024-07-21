# Philosophy

BlackBox is designed around these objectives:

- generating tests
- composition

_Generating tests_ is what Property Based Testing is all about. But BlackBox takes it a step further as not every part of a program can be tested with [properties](terminology.md#property). Sometimes different parts of a program should be tested with the same test.

That's why BlackBox uses `Generator`s to provide tests/proofs. You can generate new ones on the fly. It can handle any number.

_Composition_ is a building block to create any [data sets](terminology.md#set). But it also applies to every part of the framework. It doesn't use any global state.

This allows the framework to [test itself](https://github.com/Innmind/BlackBox/blob/develop/proofs/application.php). To be used in other frameworks such as [PHPUnit](../phpunit/index.md). Or even create functions to [generate tests](../use-cases/databases.md).
