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

use function date;
use function sprintf;

abstract class PhiveVersion {
    public function getVersionString(): string {
        return sprintf(
            'Phive %s - Copyright (C) 2015-%d by Arne Blankerts, Sebastian Heuer and Contributors',
            $this->getVersion(),
            date('Y')
        );
    }

    abstract public function getVersion(): string;
}
