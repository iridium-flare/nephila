<?php

namespace Nephila\Clavata\Stud;

use Nephila\Clavata\Stud\Event\NewUrl;
use Nephila\Clavata\Stud\Event\Start;

/**
 * Class Startup
 * @package Nephila\Clavata\Stud
 *
 * Startup class - reads configuration and chooses starting sequence.
 */
class Startup extends AbstractClass
{
    /**
     * @var string
     */
    private $url;

    /**
     * Startup constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = (string)$url;
    }

    /**
     *
     * @return void
     */
    protected function execImplementation()
    {
        $this->setEvent(new Start($this, $this->url));
        $this->notify();
        $this->setEvent(new NewUrl($this, $this->url));
        $this->notify();
    }
}
