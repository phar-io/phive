<?php declare(strict_types = 1);
namespace PharIo\Phive;

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
    public function select(ReleaseCollection $releases, VersionConstraint $versionConstraint, bool $acceptUnsigned): SupportedRelease {
        /** @var null|Release $latest */
        $latest = null;

        foreach ($releases as $release) {
            /** @var Release $release */
            if (!$versionConstraint->complies($release->getVersion())) {
                continue;
            }

            if ($latest === null || $release->getVersion()->isGreaterThan($latest->getVersion())) {
                if (!$release->isSupported()) {
                    /* @var UnsupportedRelease $release */
                    $this->output->writeWarning(
                        \sprintf(
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
                        \sprintf(
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
            throw new ReleaseException('No matching release found!');
        }

        return $latest;
    }
}
