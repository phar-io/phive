<?php
namespace PharIo\Phive;

class PhiveVersion {

    private $fallbackVersion;

    private $version;

    public function __construct($version = '0.1.0') {
        $this->fallbackVersion = $version;
    }

    public function getVersionString() {
        return sprintf(
            'Phive %s - Copyright (C) 2015-%d by Arne Blankerts, Sebastian Heuer and Contributors',
            $this->getVersion(),
            date('Y')
        );
    }

    public function getVersion() {
        if ($this->version !== null) {
            return $this->version;
        }

        $path = realpath(__DIR__ . '/../../');
        if (!is_dir($path . '/.git')) {
            $this->version = $this->fallbackVersion;
            return $this->version;
        }

        $devNull = strtolower(substr(PHP_OS, 0, 3)) == 'win' ? 'nul' : '/dev/null';
        $dir = getcwd();
        chdir($path);
        $version = @exec('git describe --tags --always --dirty 2>' . $devNull, $output, $rc);
        chdir($dir);

        if ($rc !== 0) {
            $this->version = $this->fallbackVersion;
            return $this->version;
        }

        $this->version = $version;
        return $version;
    }

}
