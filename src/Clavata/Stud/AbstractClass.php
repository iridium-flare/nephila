<?php

namespace Nephila\Clavata\Stud;

use Nephila\Clavata\Stud\Event\PreExecution;
use Nephila\Clavata\Stud\Event\PostExecution;
use SplObserver;

/**
 * Class AbstractClass
 * @package Nephila\Clavata\Stud
 *
 * This is the base class for every Stud component. Implements an observer subject (an observed component).
 * @see Observer pattern.
 */
abstract class AbstractClass implements \SplSubject
{
    /**
     * @var SplObserver[]
     */
    private $observers = [];

    /**
     * Reporting event.
     * @var Event\AbstractClass
     */
    private $event;

    /**
     *
     * @return void
     */
    abstract protected function execImplementation();


    /**
     * This method will be called by Core and report events through observers.
     * Observers should get the reported event: @see getEvent()
     * @return void
     */
    final public function exec()
    {
        $this->preExecution();
        $this->execImplementation();
        $this->postExecution();
    }

    /**
     * @return void
     */
    protected function preExecution()
    {
        $this->setEvent(new PreExecution($this));
        $this->notify();
    }

    /**
     * @return void
     */
    protected function postExecution()
    {
        $this->setEvent(new PostExecution($this));
        $this->notify();
    }

    /**
     * @param Event\AbstractClass $event
     * @return void
     */
    final protected function setEvent(Event\AbstractClass $event)
    {
        $this->event = $event;
    }

    /**
     * @return Event\AbstractClass
     * @throws \LogicException
     */
    final public function getEvent()
    {
        if (false == ($this->event instanceof Event\AbstractClass)) {
            throw new \LogicException('Invalid event triggered');
        }
        return $this->event;
    }

    /**
     * Attach an SplObserver
     * @link http://php.net/manual/en/splsubject.attach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to attach.
     * </p>
     * @return void
     * @since 5.1.0
     */
    final public function attach(SplObserver $observer)
    {
        $this->observers[get_class($observer)] = $observer;
    }

    /**
     * Detach an observer
     * @link http://php.net/manual/en/splsubject.detach.php
     * @param SplObserver $observer <p>
     * The <b>SplObserver</b> to detach.
     * </p>
     * @return void
     * @since 5.1.0
     */
    final public function detach(SplObserver $observer)
    {
        $class = get_class($observer);
        if (true == array_key_exists($class, $this->observers)) {
            unset($this->observers[$class]);
        }
    }

    /**
     * Notify an observer
     * @link http://php.net/manual/en/splsubject.notify.php
     * @return void
     * @since 5.1.0
     */
    final public function notify()
    {
        foreach ($this->observers as $value) {
            $value->update($this);
        }
    }
}