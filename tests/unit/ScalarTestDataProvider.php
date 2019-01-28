<?php declare(strict_types = 1);
namespace PharIo\Phive;

trait ScalarTestDataProvider {
    public function stringProvider(): array {
        return [
            ['foo'],
            ['bar'],
            ['äüß'],
            ['Ӵ', 'Ӹ', 'ה']
        ];
    }

    public function intProvider(): array {
        return [
            [0],
            [1],
            [46],
            [2311341],
            [-5]
        ];
    }

    public function boolProvider(): array {
        return [
            [true],
            [false]
        ];
    }
}
