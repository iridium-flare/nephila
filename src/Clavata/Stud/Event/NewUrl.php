<?php

namespace Nephila\Clavata\Stud\Event;

/**
 * Class NewUrl
 * @package Nephila\Clavata\Stud\Event
 */
class NewUrl extends AbstractClass
{
    /**
     * @var string
     */
    private $url;

    /**
     * NewUrl constructor.
     * @param \SplSubject $subject
     * @param string $url
     */
    public function __construct(\SplSubject $subject, $url = '/')
    {
        parent::__construct($subject);

        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}