<?php
namespace PharIo\Phive\IntegrationTests;

use PHPUnit\Framework\TestCase;

class IntegrationTestCase extends TestCase {

    public function getFactory() {
        return new IntegrationTestFactory();
    }

}
