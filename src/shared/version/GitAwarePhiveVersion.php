<?php
namespace PharIo\Phive;

class GitAwarePhiveVersion extends PhiveVersion {

    const UNKNOWN_VERSION = 'unknown';

    /**
     * @var Git
     */
    private $git;

    /**
     * @var string
     */
    private $version;

    /**
     * @param Git $git
     */
    public function __construct(Git $git) {
        $this->git = $git;
    }

    /**
     * @return string
     */
    public function getVersion() {
        if ($this->version !== null) {
            return $this->version;
        }

        $phiveRoot = new Directory(realpath(__DIR__ . '/../../../'));

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
