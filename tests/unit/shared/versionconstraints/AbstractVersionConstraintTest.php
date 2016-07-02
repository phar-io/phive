<?php
namespace PharIo\Phive;

/**
 * @covers \PharIo\Phive\AbstractVersionConstraint
 */
class AbstractVersionConstraintTest extends \PHPUnit_Framework_TestCase {

    public function testAsString() {
        /** @var AbstractVersionConstraint|\PHPUnit_Framework_MockObject_MockObject $constraint */
        $constraint = $this->getMockForAbstractClass(AbstractVersionConstraint::class, ['foo']);
        $this->assertSame('foo', $constraint->asString());
    }

}
