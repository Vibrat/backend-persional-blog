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
        $_SERVER['REQUEST_METHOD'] = 'POST';
      
        $mock_http = $this->createMock(Http\DataSubmit::class);
        $mock_http->expects($this->any())->method('data')
            ->willReturn([
                'GET' => [
                    'action' => true
                ]
            ]);

        $this->dependencies['http'] =  $mock_http;            
        $controller = new GroupPermissionController($this->dependencies);
        
        $this->assertEquals(null, $controller->index());
    }
}