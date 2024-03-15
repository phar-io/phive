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

use PharIo\FileSystem\File;
use PharIo\Version\Version;

class UsedPhar extends Phar {
    /** @var string[] */
    private $usages;

    public function __construct(
        string $name,
        Version $version,
        File $file,
        array $usages,
        ?string $signatureFingerprint = null
    ) {
        parent::__construct($name, $version, $file, $signatureFingerprint);
        $this->usages = $usages;
    }

    /**
     * @return string[]
     */
    public function getUsages(): array {
        return $this->usages;
    }
}
