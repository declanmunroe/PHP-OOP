<?php

use \Firebase\JWT\JWT;

class JwtController extends Zend_Controller_Action
{
    // https://www.techiediaries.com/php-jwt-authentication-tutorial/
    public function indexAction() {
        
        $secret_key = "declan*learning*jwt";
        
        $now_seconds = time();
        
        $token = array(
            "iss" => "http://zendcode.localhost",
            "aud" => "http://zendcode.localhost",
            "iat" => $now_seconds,
            "nbf" => $now_seconds,
            "exp" => $now_seconds + 60 * 60,
            "data" => array(
                "id" => 13270,
                "firstname" => "Declan",
                "lastname" => "Munroe",
                "email" => "declan.munroe@ics.ie"
        ));
        
        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        
        $this->_helper->json(array("message" => "Successful login.","jwt" => $jwt));
        
    }
}