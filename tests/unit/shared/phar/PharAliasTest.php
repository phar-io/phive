<?php
namespace PharIo\Phive;

use PharIo\Version\AnyVersionConstraint;

/**
 * @covers \PharIo\Phive\PharAlias
 */
class PharAliasTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    /**
     * @dataProvider stringProvider
     *
     * @param string $value
     */
    public function testValueHandling($value) {
        $alias = new PharAlias($value, new AnyVersionConstraint(), new AnyVersionConstraint());
        $this->assertSame($value, $alias->asString());
    }

}



