<?php

namespace Nephila\Curl;

use Nephila\TestCase;

/**
 * Class ClientTest
 * @package Nephila\Curl
 */
class ClientTest extends TestCase
{

    /**
     * @covers Nephila\Curl\Client::setReturnTransfer
     * @covers Nephila\Curl\Client::getReturnTransfer
     */
    public function testSetReturnTransfer()
    {
        $client = new Client();
        $this->assertTrue($client->getReturnTransfer());
        $client->setReturnTransfer(false);
        $this->assertFalse($client->getReturnTransfer());
    }

    /**
     * @covers Nephila\Curl\Client::setUrl
     * @covers Nephila\Curl\Client::getUrl
     */
    public function testSetUrl()
    {
        $client = new Client();
        $this->assertEmpty($client->getUrl());
        $client->setUrl('https://www.example.com/');
        $this->assertEquals(('https://www.example.com/'), $client->getUrl());
    }


    /**
     * @covers Nephila\Curl\Client::setMethod
     * @covers Nephila\Curl\Client::getMethod
     */
    public function testSetMethod()
    {
        $client = new Client();
        $this->assertEquals(Constants::METHOD_GET, $client->getMethod());
        foreach ([
                     Constants::METHOD_DELETE,
                     Constants::METHOD_HEAD,
                     Constants::METHOD_OPTIONS,
                     Constants::METHOD_PATCH,
                     Constants::METHOD_POST,
                     Constants::METHOD_PUT,
                     Constants::METHOD_TRACE,
                 ] as $constant) {
            $client->setMethod($constant);
            $this->assertEquals($constant, $client->getMethod());
        }

    }


    /**
     * @covers Nephila\Curl\Client::setTimeout
     * @covers Nephila\Curl\Client::getTimeout
     */
    public function testSetTimeout()
    {
        $client = new Client();
        $client->setTimeout(333);
        $this->assertSame(333, $client->getTimeout());
    }

    /**
     * @covers Nephila\Curl\Client::setConnectionTimeout
     * @covers Nephila\Curl\Client::getConnectionTimeout
     */
    public function testSetConnectionTimeout()
    {
        $client = new Client();
        $client->setConnectionTimeout(333);
        $this->assertSame(333, $client->getConnectionTimeout());
    }

    /**
     * @covers Nephila\Curl\Client::setCookies
     * @covers Nephila\Curl\Client::addCookie
     * @covers Nephila\Curl\Client::removeCookies
     * @covers Nephila\Curl\Client::getCookies
     */
    public function testSetCookies()
    {
        $client = new Client();
        $client->setCookies(['making' => 'test',]);
        $this->assertSame(['making' => 'test',], $client->getCookies());
        $client->addCookie('another', 'testing');
        $this->assertSame(['making' => 'test', 'another' => 'testing',], $client->getCookies());
        $client->removeCookies();
        $this->assertSame([], $client->getCookies());
    }

    /**
     * @covers Nephila\Curl\Client::exec
     */
    public function testExec()
    {
        $client = new Client();
        $client->setUrl('http://www.google.com/');
        $this->assertTrue($client->exec());
    }
}
