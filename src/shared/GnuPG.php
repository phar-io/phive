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

use function putenv;
use PharIo\FileSystem\Directory;

class GnuPG {
    /** @var \gnupg */
    private $gnupg;

    /** @var Directory */
    private $homeDir;

    public function __construct(\gnupg $gnupg, Directory $homeDir) {
        $this->gnupg   = $gnupg;
        $this->homeDir = $homeDir;
    }

    /**
     * @return array|false
     */
    public function import(string $key) {
        putenv('GNUPGHOME=' . $this->homeDir->asString());

        return $this->gnupg->import($key);
    }

    public function keyinfo(string $search): array {
        putenv('GNUPGHOME=' . $this->homeDir->asString());

        return $this->gnupg->keyinfo($search);
    }

    /**
     * @return array|false
     */
    public function verify(string $message, string $signature) {
        putenv('GNUPGHOME=' . $this->homeDir->asString());

        return $this->gnupg->verify($message, $signature);
    }

    /**
     * @return false|string
     */
    public function geterror() {
        return $this->gnupg->geterror();
    }
}
