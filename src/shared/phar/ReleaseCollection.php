<?php
namespace PharIo\Phive;

use PharIo\Version\VersionConstraint;

class ReleaseCollection {

    /**
     * @var Release[]
     */
    private $releases = [];

    /**
     * @param Release $release
     */
    public function add(Release $release) {
        $this->releases[] = $release;
    }

    /**
     * @param VersionConstraint $versionConstraint
     *
     * @return Release
     * @throws ReleaseException
     */
    public function getLatest(VersionConstraint $versionConstraint) {
        /** @var null|Release $latest */
        $latest = null;
        foreach ($this->releases as $release) {
            if (!$versionConstraint->complies($release->getVersion())) {
                continue;
            }
            if ($latest === null || $release->getVersion()->isGreaterThan($latest->getVersion())) {
                $latest = $release;
            }
        }
        if ($latest === null) {
            throw new ReleaseException('No matching release found');
        }
        return $latest;
    }

}
