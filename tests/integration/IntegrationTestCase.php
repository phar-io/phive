<?php
namespace PharIo\Phive\IntegrationTests;

class IntegrationTestCase extends \PHPUnit_Framework_TestCase {

    public function getFactory() {
        return new IntegrationTestFactory();
    }

}
