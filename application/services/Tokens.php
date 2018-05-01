<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 30/04/2018
 * Time: 09:14
 */
class Application_Service_Tokens
{
    public static function moodlehash($email)
    {
        return md5(strtolower($email));
    }
}