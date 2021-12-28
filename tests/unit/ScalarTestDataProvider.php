<?php declare(strict_types=1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
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
