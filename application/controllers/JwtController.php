<?php

use \Firebase\JWT\JWT;

class JwtController extends Zend_Controller_Action
{
    // https://www.techiediaries.com/php-jwt-authentication-tutorial/
    public function loginAction() {
        
        $formData = $this->getRequest()->getPost();
        $id = (int) isset($formData['user_id']) ? $formData['user_id'] : 0;
        
        $loggedInUserData = [];
        
        $declanUserData = array('id' => 13270, 'email' => 'declan.munroe@ics.ie', 'bike' => 'vitus sommet');
        $kevinUserData = array('id' => 15999, 'email' => 'kevin@gmail.com', 'bike' => 'norco dh');
        
        if ($id == 13270) {
            $loggedInUserData = $declanUserData;
        } else if ($id == 15999) {
            $loggedInUserData = $kevinUserData;
        } else {
            http_response_code(401);
            $this->_helper->json(array("message" => "Login failed","id" => $id));
        }
        
        $secret_key = "declan*learning*jwt";
        
        $now_seconds = time();
        
        $token = array(
            "iss" => "http://zendcode.localhost",
            "aud" => "http://zendcode.localhost",
            "iat" => $now_seconds,
            "nbf" => $now_seconds,
            "exp" => $now_seconds + 60 * 60,
            "data" => $loggedInUserData
        );
        
        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        
        $this->_helper->json(array("message" => "Successful login.","jwt" => $jwt));
        
    }
}