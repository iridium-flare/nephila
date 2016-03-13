<?php

namespace Nephila\Clavata\Stud\Event;

/**
 * Class AbstractClass
 * @package Nephila\Clavata\Stud\Event
 *
 * This is an event class.
 * It's used to reporting events from a \SplSubject (Studs) to any \SplObserver .
 */
abstract class AbstractClass
{
    /**
     * @var \SplSubject
     */
    private $subject;

    /**
     * AbstractClass constructor.
     * Needed to force Studs to report themselves when they notify a event to observers.
     * @param \SplSubject $subject
     */
    public function __construct(\SplSubject $subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return \SplSubject
     */
    public function getSubject()
    {
        return $this->subject;
    }

}
