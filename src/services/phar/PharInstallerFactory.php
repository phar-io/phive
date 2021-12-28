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

use function file_get_contents;

class PharInstallerFactory {
    /** @var Factory */
    private $factory;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
    }

    public function getWindowsPharInstaller(): WindowsPharInstaller {
        return new WindowsPharInstaller($this->factory->getOutput(), file_get_contents(__DIR__ . '/../../../conf/pharBat.template'));
    }

    public function getUnixoidPharInstaller(): UnixoidPharInstaller {
        return new UnixoidPharInstaller($this->factory->getOutput());
    }
}
