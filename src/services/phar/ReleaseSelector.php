<?php
namespace PharIo\Phive;

use PharIo\Phive\Cli\Output;
use PharIo\Version\VersionConstraint;

class ReleaseSelector {

    /** @var Output */
    private $output;

    /**
     * ReleaseSelector constructor.
     *
     * @param Output $output
     */
    public function __construct(Output $output) {
        $this->output = $output;
    }

    /**
     * @param VersionConstraint $versionConstraint
     *
     * @return SupportedRelease
     * @throws ReleaseException
     */
    public function select(ReleaseCollection $releases, VersionConstraint $versionConstraint) {
        /** @var null|Release $latest */
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
                $latest = $release;
            }
        }
        if ($latest === null) {
            throw new ReleaseException('No matching release found!');
        }

        return $latest;
    }

}
