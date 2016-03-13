<?php

namespace Nephila\Curl;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\Log\LoggerAwareInterface;

/**
 * Class Client
 * @package Nephila\Curl
 *
 * Simple class for wrapping over cURL. Implemented without adding too much abstraction over.
 * How to use: call setUrl() and any other setter methods. Finally, run an exec() call.
 * Please note this is still an incomplete wrapping. There are missing many features.
 */
class Client implements LoggerAwareInterface
{
    /**
     * @var resource
     */
    private $handle;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    private $cookies = [];

    /**
     * Client constructor.
     */
    final public function __construct()
    {
        $this->handle = curl_init();
        $this->logger = new NullLogger();
        $this->setDefaultOptions();
    }

    /**
     * Resets all curl options to default values.
     * Please keep in mind this values may not be cURL default values, just default values I choose on my own.
     * @return void
     */
    public function setDefaultOptions()
    {
        $this->options = [
            CURLOPT_URL => null,
            CURLOPT_COOKIE => null,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_PUT => false,
            CURLOPT_POST => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPGET => true,
        ];
        if (false === curl_setopt_array($this->getHandle(), $this->options)) {
            $this->logger->error('Error setting default curl options');
        }
    }

    /**
     * Sets a logger instance on the object
     * @see LoggerAwareInterface
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Launch a request to predefined URL
     * @return string|bool  This method returns a string if
     */
    public function exec()
    {
        // Using curl_exec on a single sentence to avoid data copy on a temporal variable.
        return curl_exec($this->getHandle())
        || call_user_func(
            function () {
                $error_code = curl_error($this->getHandle());
                if ($error_code > 0) {
                    throw new Exception(sprintf('cURL error %s: %s', $error_code, curl_error($this->getHandle())));
                }
                return $this->getReturnTransfer() ? '' : false;
            }
        );
    }

    /**
     * Set return transfer flag.
     * @see exec()
     * @param bool $value True for getting response string on exec method. False otherwise.
     * @return void
     */
    public function setReturnTransfer($value = true)
    {
        $this->setOptAndLog(CURLOPT_RETURNTRANSFER, $value, 'returnTransfer');
    }

    /**
     * Get return transfer flag.
     * @return bool
     */
    public function getReturnTransfer()
    {
        return $this->getOpt(CURLOPT_RETURNTRANSFER);
    }

    /**
     * Set request URL
     * @param string $url URL, with or without query parameters.
     * @param array|string|null $queryParameters Array of parameters, unencoded query string or nothing at all.
     * @return void
     */
    public function setUrl($url, $queryParameters = null)
    {
        if (false == is_null($queryParameters)) {
            $url .= '?' . (is_array($queryParameters)
                    ? http_build_query($queryParameters)
                    : urlencode($queryParameters));
        }
        $this->setOptAndLog(CURLOPT_URL, $url, 'URL');
    }

    /**
     * Get request URL
     * @return string
     */
    public function getUrl()
    {
        return $this->getOpt(CURLOPT_URL);
    }

    /**
     * Sets a HTTP request method.
     * @param string $method
     * @return void
     */
    public function setMethod($method)
    {
        $this->disableAllMethodOptions(); // reset array options, disabling all methods before enabling just one
        switch ($method) {
            case Constants::METHOD_GET :
                $this->setOptAndLog(CURLOPT_HTTPGET, true, 'httpGet');
                break;
            case Constants::METHOD_POST :
                $this->setOptAndLog(CURLOPT_POST, true, 'post');
                break;
            case Constants::METHOD_HEAD :
                $this->setOptAndLog(CURLOPT_NOBODY, true, 'nobody');
                break;
            case Constants::METHOD_PUT :
                $this->setOptAndLog(CURLOPT_PUT, true, 'put');
                break;
            case Constants::METHOD_DELETE :
            case Constants::METHOD_OPTIONS :
            case Constants::METHOD_TRACE :
            case Constants::METHOD_PATCH :
            default : // Any custom value may work, that depends on server configuration.
                $this->setOptAndLog(CURLOPT_CUSTOMREQUEST, $method, 'customRequest');
                break;
        }
    }

    /**
     * Returns current HTTP request method.
     * @return string
     */
    public function getMethod()
    {
        if (true === $this->getOpt(CURLOPT_HTTPGET)) {
            $method = Constants::METHOD_GET;
        } elseif (true === $this->getOpt(CURLOPT_POST)) {
            $method = Constants::METHOD_POST;
        } elseif (true === $this->getOpt(CURLOPT_PUT)) {
            $method = Constants::METHOD_PUT;
        } elseif (true === $this->getOpt(CURLOPT_NOBODY)) {
            $method = Constants::METHOD_HEAD;
        } else {
            $method = $this->getOpt(CURLOPT_CUSTOMREQUEST);
        }
        return $method;
    }

    /**
     * Sets transfer timeout (seconds)
     * @param int $value Timeout (seconds)
     * @return void
     */
    public function setTimeout($value)
    {
        $this->setOptAndLog(CURLOPT_TIMEOUT, $value, 'timeout');
    }

    /**
     * Returns transfer timeout (seconds)
     * @return string
     */
    public function getTimeout()
    {
        return $this->getOpt(CURLOPT_TIMEOUT);
    }

    /**
     * Sets connection timeout (seconds)
     * @param int $value Timeout (seconds)
     * @return void
     */
    public function setConnectionTimeout($value)
    {
        $this->setOptAndLog(CURLOPT_CONNECTTIMEOUT, $value, 'connectionTimeout');
    }

    /**
     * Returns connection timeout (seconds)
     * @return int
     */
    public function getConnectionTimeout()
    {
        return (int)($this->getOpt(CURLOPT_CONNECTTIMEOUT));
    }

    /**
     * Add a cookie key/value pair.
     * @param string $name
     * @param string|int|float $value
     * @param bool $setNow
     * @return void
     */
    public function addCookie($name, $value, $setNow = false)
    {
        $this->cookies[(string)$name] = (string)$value;
        if (true === $setNow) {
            $this->setCookies();
        }
    }

    /**
     * Remove all cookies.
     * @param bool $setNow
     * @return void
     */
    public function removeCookies($setNow = false)
    {
        $this->cookies = [];
        if (true === $setNow) {
            $this->setCookies();
        }
    }

    /**
     * Set cookie pairs for sending them with the request.
     * @param array $cookies COOKIE array with name as key and value as value.
     *                       Leave empty for setting previously added cookies.
     * @return void
     * @see addCokie()
     * @see removeCookies()
     */
    public function setCookies(array $cookies = array())
    {
        if (false == empty($cookies)) {
            $this->cookies = $cookies;
        } else {
            $cookies = $this->cookies; // set previous used cookies
        }
        if (empty($cookies)) {
            $this->setCookiesAsOption(null);
        } elseif (true == array_walk($cookies, function (&$value, $key) {
                $value = sprintf('%s=%s', $key, $value);
            })
        ) {
            $this->setCookiesAsOption(implode('; ', $cookies));
        } else {
            $this->logger->error('Error setting cookies, invalid array');
        }
    }

    /**
     * Get cookie pairs of name and value as an associative array.
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Method for clearing method options array.
     * @see setMethod()
     * @return void
     */
    protected function disableAllMethodOptions()
    {
        $this->options[CURLOPT_HTTPGET] = false;
        $this->options[CURLOPT_POST] = false;
        $this->options[CURLOPT_PUT] = false;
        $this->options[CURLOPT_CUSTOMREQUEST] = null;
        $this->options[CURLOPT_NOBODY] = false;
    }

    /**
     * @return resource
     */
    final protected function getHandle()
    {
        return $this->handle;
    }

    /**
     * Get cookie pairs of name and value as an associative array.
     * @deprecated Method currently unused.
     * @return array
     */
    protected function parseCookies()
    {
        $cookies = [];
        $value = $this->getOpt(CURLOPT_COOKIE);
        $token = strpos($value, ';') !== false
            ? strtok($value, ';')
            : $value;
        while ($token !== false && strpos($token, '=') !== false) {
            list($cookie_name, $cookie_value) = explode($token, '=', 2);
            $cookies[$cookie_name] = $cookie_value;
            $token = strtok(';');
        }
        return $cookies;
    }

    /**
     * Sets a curl option value and log on return and failure.
     * @param int $option
     * @param mixed $value
     * @param $changed_entity
     * @return bool
     */
    final protected function setOptAndLog($option, $value, $changed_entity)
    {
        if (true == $this->setOpt($option, $value)) {
            $this->logger->info('Option ' . $changed_entity . ' changed to {value}', ['value' => $value]);
        } else {
            $this->logger->error('Error setting option ' . $changed_entity . ' to {value}', ['value' => $value]);
        }
    }

    /**
     * Sets a curl option value.
     * @param int $option
     * @param mixed $value
     * @return bool
     * @see setOptAndLog()
     */
    final protected function setOpt($option, $value)
    {
        $this->options[$option] = $value;
        return curl_setopt($this->getHandle(), $option, $value);
    }

    /**
     * Returns a curl option value.
     * @param int $option
     * @return string
     * @throws \RuntimeException
     */
    final protected function getOpt($option)
    {
        if (false == array_key_exists($option, $this->options)) {
            throw new Exception('Invalid option ' . $option);
        }
        return $this->options[$option];
    }

    /**
     * @param string $cookie_pairs
     * @return void
     */
    final protected function setCookiesAsOption($cookie_pairs)
    {
        if (true == $this->setOpt(CURLOPT_COOKIE, $cookie_pairs)) {
            $this->logger->info('Option cookie changed to {value}', ['value' => isset($cookie_pairs) ? $cookie_pairs : 'null']);
        } else {
            $this->logger->error('Error setting option cookie to {value}', ['value' => isset($cookie_pairs) ? $cookie_pairs : 'null']);
        }
    }

    /**
     *
     */
    final public function __destruct()
    {
        if (true === is_resource($this->handle)) {
            curl_close($this->handle);
        }
    }


}