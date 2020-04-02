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
    
    public function verifytokenAction() {
        
        $secret_key = "declan*learning*jwt";
        $jwt = null;
        
        $data = json_decode(file_get_contents("php://input"));
        
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        
        $arr = explode(" ", $authHeader);
        
        $jwt = $arr[1];
        
        if($jwt){

            try {

                JWT::decode($jwt, $secret_key, array('HS256'));

                // Access is granted. Add code of the operation here 

                $this->_helper->json(array("message" => "Access granted:", "error" => $e->getMessage()));

            }catch (Exception $e){

                http_response_code(401);

                $this->_helper->json(array("message" => "Access denied.", "error" => $e->getMessage()));
            }

        } else {
            
            http_response_code(401);

            $this->_helper->json(array("message" => "Access denied.", "error" => $e->getMessage()));
                
        }
    }
}