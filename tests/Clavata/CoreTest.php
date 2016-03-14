<?php

namespace Nephila\Clavata;

use Nephila\TestCase;

/**
 * Class CoreTest
 * @package Nephila\Curl
 */
class CoreTest extends TestCase
{

    /**
     * Stud-Core communication testing with a noop Stud.
     * @covers Nephila\Clavata\Stud\Event\AbstractClass::getSubject
     * @covers Nephila\Clavata\Core::getLastEvent
     * @covers Nephila\Clavata\Core::run
     * @covers Nephila\Clavata\Core::update
     * @covers Nephila\Clavata\Core::noop
     */
    public function testNoop()
    {
        $core = new Core();
        $core->noop();
        $core->run();
        // Last event after a noop run must be a PostExecution triggered by Noop
        $this->assertInstanceOf('Nephila\\Clavata\\Stud\\Event\\PostExecution', $core->getLastEvent());
        $this->assertInstanceOf('Nephila\\Clavata\\Stud\\Noop', $core->getLastEvent()->getSubject());
    }

    /**
     * Stud-Core communication testing with a noop Stud.
     * @covers Nephila\Clavata\Stud\Event\AbstractClass::getSubject
     * @covers Nephila\Clavata\Core::getLastEvent
     * @covers Nephila\Clavata\Core::run
     * @covers Nephila\Clavata\Core::update
     * @covers Nephila\Clavata\Core::setUrl
     */
    public function testShutdown()
    {
        $core = new Core();
        $core->setUrl('');
        $core->run();
        // Last event after a invalid url run run must be a PostExecution triggered by Shutdown
        $this->assertInstanceOf('Nephila\\Clavata\\Stud\\Event\\PostExecution', $core->getLastEvent());
        $this->assertInstanceOf('Nephila\\Clavata\\Stud\\Shutdown', $core->getLastEvent()->getSubject());
    }

}