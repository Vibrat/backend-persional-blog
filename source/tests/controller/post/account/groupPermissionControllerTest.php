<?php
use TestEngine\TestEngine;
use PHPUnit\Framework\TestCase;

/**
 * Unit-test Class
 * 
 * @property Array $dependencies DI
 * @property Object $app Application
 * @property Object $engine
 */
final class GroupPermissionControllerTest extends TestCase {
    
    /** @var TestEngine Engine class for test */
    use TestEngine;

    public function testIndex() {
        $_GET['api'] = '';
        $controller = new GroupPermissionController($this->dependencies);

        $this->assertEquals(4, $controller->index());
    }
}