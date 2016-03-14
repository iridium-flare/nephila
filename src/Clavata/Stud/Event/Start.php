<?php

namespace Nephila\Clavata\Stud\Event;

/**
 * Class Start
 * @package Nephila\Clavata\Stud\Event
 */
class Start extends AbstractClass
{
    /**
     * @var string
     */
    private $start_url;

    /**
     * Start constructor.
     * @param \SplSubject $subject
     * @param string $start_url
     */
    public function __construct(\SplSubject $subject, $start_url = '')
    {
        parent::__construct($subject);

        $this->start_url = $start_url;
    }

    /**
     * @return string
     */
    public function getStartUrl()
    {
        return $this->start_url;
    }

}