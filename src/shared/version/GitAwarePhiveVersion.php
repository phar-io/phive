<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PharIo\FileSystem\Directory;

class GitAwarePhiveVersion extends PhiveVersion {
    public const UNKNOWN_VERSION = 'unknown';

    /** @var Git */
    private $git;

    /** @var null|string */
    private $version;

    public function __construct(Git $git) {
        $this->git = $git;
    }

    public function getVersion(): string {
        if ($this->version !== null) {
            return $this->version;
        }

        $phiveRoot = new Directory(__DIR__ . '/../../../');

        if (!$this->git->isRepository($phiveRoot)) {
            $this->version = self::UNKNOWN_VERSION;

            return $this->version;
        }

        try {
            $this->version = $this->git->getMostRecentTag($phiveRoot);
        } catch (GitException $e) {
            $this->version = self::UNKNOWN_VERSION;
        }

        return $this->version;
    }
}
