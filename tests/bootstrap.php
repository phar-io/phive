<?php declare(strict_types = 1);
/*
 * This file is part of Phive.
 *
 * Copyright (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
require __DIR__ . '/../src/autoload.php';

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/autoload.php';

if (!\extension_loaded('gnupg')) {
    \class_alias(\PharIo\GnuPG\GnuPG::class, '\Gnupg');
}
