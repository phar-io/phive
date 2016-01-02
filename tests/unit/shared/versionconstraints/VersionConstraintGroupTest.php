<?php
namespace PharIo\Phive {

    /**
     * @covers PharIo\Phive\VersionConstraintGroup
     */
    class VersionConstraintGroupTest extends \PHPUnit_Framework_TestCase
    {

        public function testReturnsFalseIfOneConstraintReturnsFalse()
        {
            $firstConstraint = $this->getMock(VersionConstraint::class);
            $secondConstraint = $this->getMock(VersionConstraint::class);

            $firstConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(true));

            $secondConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(false));

            $group = new VersionConstraintGroup('foo', [$firstConstraint, $secondConstraint]);
            $this->assertFalse($group->complies(new Version('1.0.0')));
        }

        public function testReturnsTrueIfAllConstraintsReturnsTrue()
        {
            $firstConstraint = $this->getMock(VersionConstraint::class);
            $secondConstraint = $this->getMock(VersionConstraint::class);

            $firstConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(true));

            $secondConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(true));

            $group = new VersionConstraintGroup('foo', [$firstConstraint, $secondConstraint]);
            $this->assertTrue($group->complies(new Version('1.0.0')));
        }

    }

}

