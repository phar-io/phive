<?php
namespace PharIo\Phive;

class Phar {

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var Version
     */
    private $version;

    /**
     * @var File
     */
    private $file;

    /**
     * @param string  $name
     * @param Version $version
     * @param File    $file
     */
    public function __construct($name, Version $version, File $file) {
        $this->name = $name;
        $this->file = $file;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return Version
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @return File
     */
    public function getFile() {
        return $this->file;
    }

}
