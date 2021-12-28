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

use Exception;

class MigrationsFailedException extends Exception {
    /** @var array */
    private $failed;

    public function __construct(array $failed) {
        $this->failed = $failed;
        parent::__construct();
    }

    public function getFailed(): array {
        return $this->failed;
    }
}
