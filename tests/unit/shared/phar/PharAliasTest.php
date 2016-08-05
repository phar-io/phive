<?php
namespace PharIo\Phive;

/**
 * @covers PharIo\Phive\PharAlias
 */
class PharAliasTest extends \PHPUnit_Framework_TestCase {

    use ScalarTestDataProvider;

    /**
     * @return array
     */
    public static function versionConstraintProvider() {
        return [
            [new AnyVersionConstraint()],
            [new ExactVersionConstraint('1.0.0')]
        ];
    }

    /**
     * @dataProvider stringProvider
     *
     * @param string $value
     */
    public function testValueHandling($value) {
        $alias = new PharAlias($value, new AnyVersionConstraint(), new AnyVersionConstraint());
        $this->assertSame($value, (string)$alias);
    }

    /**
     * @dataProvider versionConstraintProvider
     *
     * @param VersionConstraint $constraint
     */
    public function testGetVersionConstraint(VersionConstraint $constraint) {
        $alias = new PharAlias('foo', $constraint, $constraint);
        $this->assertSame($constraint, $alias->getVersionConstraint());
    }

}



