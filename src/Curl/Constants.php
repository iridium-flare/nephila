<?php

namespace Nephila\Curl;

/**
 * Class Constants
 * @package Nephila\Curl
 *
 * @see Client::setMethod()
 */
final class Constants
{
    // Http 1.0 methods
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_HEAD = 'HEAD';

    // HTTP 1.1 methods
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';

    // REST custom methods
    const METHOD_PATCH = 'PATCH';


    /**
     * This class is just a constant definer and should never be instantiated.
     */
    private function __construct()
    {
    }
}
