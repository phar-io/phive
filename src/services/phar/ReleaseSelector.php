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

use function sprintf;
use PharIo\Phive\Cli\Output;
use PharIo\Version\VersionConstraint;

class ReleaseSelector {
    /** @var Output */
    private $output;

    /**
     * ReleaseSelector constructor.
     */
    public function __construct(Output $output) {
        $this->output = $output;
    }

    /**
     * @throws ReleaseException
     */
    public function select(
        PharIdentifier $identifier,
        ReleaseCollection $releases,
        VersionConstraint $versionConstraint,
        bool $acceptUnsigned
    ): SupportedRelease {
        /** @var null|SupportedRelease $latest */
        $latest = null;

        foreach ($releases as $release) {
            /** @var Release $release */
            if (!$versionConstraint->complies($release->getVersion())) {
                continue;
            }

            if ($latest === null || $release->getVersion()->isGreaterThan($latest->getVersion())) {
                if (!$release->isSupported()) {
                    /** @var UnsupportedRelease $release */
                    $this->output->writeWarning(
                        sprintf(
                            '%s %s: %s',
                            $release->getName(),
                            $release->getVersion()->getVersionString(),
                            $release->getReason()
                        )
                    );

                    continue;
                }

                /** @var SupportedRelease $release */
                if (!$acceptUnsigned && !$release->hasSignatureUrl()) {
                    $this->output->writeWarning(
                        sprintf(
                            '%s %s: %s',
                            $release->getName(),
                            $release->getVersion()->getVersionString(),
                            'No GPG Signature'
                        )
                    );

                    continue;
                }
                $latest = $release;
            }
        }

        if ($latest === null) {
            throw new ReleaseException(
                sprintf('No matching release found for %s!', $identifier->asString())
            );
        }

        return $latest;
    }
}
