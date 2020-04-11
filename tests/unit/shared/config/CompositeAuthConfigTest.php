<?php declare(strict_types = 1);
namespace PharIo\Phive;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\Phive\CompositeAuthConfig
 */
class CompositeAuthConfigTest extends TestCase {
    public function testMultipleFirstHasAuthentication(): void {
        $first = $this->getAuthConfigMock();
        $first->expects($this->once())->method('hasAuthentication')->with('example.com')->willReturn(true);
        $last = $this->getAuthConfigMock();
        $last->expects($this->never())->method('hasAuthentication')->with('example.com')->willReturn(true);

        $composite = new CompositeAuthConfig([$first, $last]);

        $result = $composite->hasAuthentication('example.com');

        $this->assertTrue($result);
    }

    public function testMultipleLastHasAuthentication(): void {
        $first = $this->getAuthConfigMock();
        $first->expects($this->once())->method('hasAuthentication')->with('example.com')->willReturn(false);
        $last = $this->getAuthConfigMock();
        $last->expects($this->once())->method('hasAuthentication')->with('example.com')->willReturn(true);

        $composite = new CompositeAuthConfig([$first, $last]);

        $result = $composite->hasAuthentication('example.com');

        $this->assertTrue($result);
    }

    public function testMultipleNoneHasAuthentication(): void {
        $first = $this->getAuthConfigMock();
        $first->expects($this->once())->method('hasAuthentication')->with('example.com')->willReturn(false);
        $last = $this->getAuthConfigMock();
        $last->expects($this->once())->method('hasAuthentication')->with('example.com')->willReturn(false);

        $composite = new CompositeAuthConfig([$first, $last]);

        $result = $composite->hasAuthentication('example.com');

        $this->assertFalse($result);
    }

    private function getAuthConfigMock(): AuthConfig {
        return $this->createMock(AuthConfig::class);
    }
}
