<?php
namespace PharIo\Phive;

class PhiveVersion {

    /**
     * @var string
     */
    private $fallbackVersion;

    /**
     * @var string
     */
    private $version;

    /**
     * @var Git
     */
    private $git;

    /**
     * @param Git $git
     * @param string $version
     */
    public function __construct(Git $git, $version = '0.1.0') {
        $this->git = $git;
        $this->fallbackVersion = $version;
    }

    /**
     * @return string
     */
    public function getVersionString() {
        return sprintf(
            'Phive %s - Copyright (C) 2015-%d by Arne Blankerts, Sebastian Heuer and Contributors',
            $this->getVersion(),
            date('Y')
        );
    }

    /**
     * @return string
     */
    public function getVersion() {
        if ($this->version !== null) {
            return $this->version;
        }

        $phiveRoot = new Directory(realpath(__DIR__ . '/../../'));

        if (!$this->git->isRepository($phiveRoot)) {
            $this->version = $this->fallbackVersion;
            return $this->version;
        }
        
        try {
            $this->version = $this->git->getMostRecentTag($phiveRoot);
        } catch (GitException $e) {
            $this->version = $this->fallbackVersion;
        }
        
        return $this->version;
    }

}
