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
final class PizzaControllerTest extends TestCase {
    
    /** @var TestEngine Engine class for test */
    use TestEngine;

    public function testSimple()
    {
       $pizza = new PizzaController($this->dependencies);
       $this->assertEquals(3, $pizza->testFunc());
    }
}