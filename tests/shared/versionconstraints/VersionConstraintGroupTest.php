<?php
namespace PharIo\Phive {

    class VersionConstraintGroupTest extends \PHPUnit_Framework_TestCase
    {

        public function testReturnsFalseIfOneConstraintReturnsFalse()
        {
            $firstConstraint = $this->getMock(VersionConstraintInterface::class);
            $secondConstraint = $this->getMock(VersionConstraintInterface::class);

            $firstConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(true));

            $secondConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(false));

            $group = new VersionConstraintGroup([$firstConstraint, $secondConstraint]);
            $this->assertFalse($group->complies(new Version('1.0.0')));
        }

        public function testReturnsTrueIfAllConstraintsReturnsTrue()
        {
            $firstConstraint = $this->getMock(VersionConstraintInterface::class);
            $secondConstraint = $this->getMock(VersionConstraintInterface::class);

            $firstConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(true));

            $secondConstraint->expects($this->once())
                ->method('complies')
                ->will($this->returnValue(true));

            $group = new VersionConstraintGroup([$firstConstraint, $secondConstraint]);
            $this->assertTrue($group->complies(new Version('1.0.0')));
        }

    }

}

