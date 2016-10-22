<?php
/**
 * Created by PhpStorm.
 * User: kan
 * Date: 15.07.16
 * Time: 16:53
 */

namespace App\Additional;
use Exception;


/**
 * AngryCurl custom exception
 */
class AngryCurlException extends Exception
{
    public function __construct($message = "", $code = 0 /*For PHP < 5.3 compatibility omitted: , Exception $previous = null*/)
    {
        AngryCurl::add_debug_msg($message);
        parent::__construct($message, $code);
    }
}