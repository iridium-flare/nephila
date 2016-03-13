<?php

namespace Nephila\Clavata;

use SplSubject;
use \Nephila\Clavata;
use \Nephila\Clavata\Stud\Event;

/**
 * Class Core
 * @package Nephila\Clavata
 *
 * This is the spider Core.
 * This class acts as a mediator between the components, called Studs. Logical. It's a studded spider core. ;)
 * The Core will exec Studs from a custom queue that will manage as it sees fit.
 * The Core will be also a observer, so it'll listen carefully to events reported by the Studs. @see \Nephila\Clavata\Core::update()
 * There may be other observers listening to events (a logger...).
 * The Studs will be observed subjects and they'll have normally a short lifespan once started (do work, report and bye).
 * The Studs must be totally independent. The only nexus between them is the Core, as the mediator.
 * The Studs will report events back to the Core and any other registered observer. @see \Nephila\Clavata\Stud\AbstractClass::attach()
 *
 */
final class Core implements \SplObserver
{
    /**
     * @var Event\AbstractClass
     */
    private $lastEvent;

    /**
     * @var array
     */
    private $studs = [];

    /**
     * Adds a "noop" stud for testing.
     * @return void
     */
    public function noop()
    {
        array_unshift( $this->studs, new Clavata\Stud\Noop());
    }

    /**
     * @return void
     */
    public function run()
    {
        while (false == empty($this->studs))
        {
            /** @var  Clavata\Stud\AbstractClass $stud */
            $stud = array_shift($this->studs);
            $stud->attach( $this );
            $stud->exec();
        }
    }

    /**
     * Receive update from subject
     * @link http://php.net/manual/en/splobserver.update.php
     * @param SplSubject $subject <p>
     * The <b>SplSubject</b> notifying the observer of an update.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function update(SplSubject $subject)
    {
        if ($subject instanceof Stud\AbstractClass) {
            $this->lastEvent = $subject->getEvent();
        }
    }

    /**
     * @return Event\AbstractClass
     */
    public function getLastEvent()
    {
        return $this->lastEvent;
    }
}