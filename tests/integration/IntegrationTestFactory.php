<?php
namespace PharIo\Phive\IntegrationTests;

use PharIo\Phive\Cli\Request;
use PharIo\Phive\Directory;
use PharIo\Phive\Factory;
use PharIo\Phive\GnuPG;

class IntegrationTestFactory extends Factory {

    public function __construct() {
        parent::__construct(new Request([]));
    }

    /**
     * @param Directory $gpgHome
     *
     * @return GnuPG
     * @throws \PharIo\Phive\NoGPGBinaryFoundException
     */
    public function getShellBasedGnupg(Directory $gpgHome) {
        return new GnuPG(
            $this->getConfig()->getGPGBinaryPath(),
            new Directory(__DIR__),
            $gpgHome
        );
    }

}
