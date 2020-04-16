<?php

use \Firebase\JWT\JWT;

class JwtController extends Zend_Controller_Action
{
    // https://www.techiediaries.com/php-jwt-authentication-tutorial/
    public function loginAction() {
        
        $body = $this->getRequest()->getRawBody();
        
        $response = json_decode($body, true);
        
        $password = isset($response['password']) ? $response['password'] : '';
        
        $loggedInUserData = [];
        
        $declanUserData = array('first_name' => 'John', 'last_name' => 'Rowley', 'company' => 'ICS', 'country' => 'Ireland',
                                'address1' => '87-89 Pembroke Road', 'address2' => 'Ballsbridge', 'town' => 'Dublin 4', 'county' => 'Co. Dublin',
                                'zip' => 'A094T43', 'email' => 'john@ics.ie', 'phone' => '087 2457746');
        
        $kevinUserData = array('first_name' => 'Kevin', 'last_name' => 'Munroe', 'company' => 'UCD', 'country' => 'Ireland',
                                'address1' => 'Creevagh House', 'address2' => 'Baldongan', 'town' => 'Lusk', 'county' => 'Co. Dublin',
                                'zip' => 'WA 1236', 'email' => 'jevin@gmail.com', 'phone' => '086 1234567');
        
        if ($password == 'letmein') {
            $loggedInUserData = $declanUserData;
        } else if ($password == 'letmein01') {
            $loggedInUserData = $kevinUserData;
        } else {
            http_response_code(401);
            $this->_helper->json(array("message" => "Login failed","password" => $password));
        }
        
        $secret_key = "declan*learning*jwt";
        
        $now_seconds = time();
        
        $token = array(
            "iss" => "http://zendcode.localhost",
            "aud" => "http://zendcode.localhost",
            "iat" => $now_seconds,
            "nbf" => $now_seconds,
            "exp" => $now_seconds + 60,
            "data" => $loggedInUserData
        );
        
        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key);
        
        $this->_helper->json(array("message" => "Successful login.","access_token" => $jwt));
        
    }
    
    public function verifytokenAction() {
//        # Might be needed for cors second request
//        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//            header('Access-Control-Allow-Origin: *');
//            header("Access-Control-Allow-Headers: *");
//            header("Access-Control-Allow-Methods: *");
//            header("HTTP/1.1 200 OK");
//            exit;
//        }
        
        $secret_key = "declan*learning*jwt";
        $jwt = null;
        
        $data = json_decode(file_get_contents("php://input"));
        
        # https://stackoverflow.com/questions/26475885/authorization-header-missing-in-php-post-request
        # Needed to add to htaccess file so I can access AUTHORIZATION in headers so I can validate against my bearer token
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        
        $arr = explode(" ", $authHeader);
        
        $jwt = $arr[1];
        
        if($jwt){

            try {

                JWT::decode($jwt, $secret_key, array('HS256'));

                // Access is granted. Add code of the operation here 

                $this->_helper->json(array("message" => "Access", "status" => true));

            }catch (Exception $e){

                http_response_code(401);

                $this->_helper->json(array("message" => "Access denied.", "status" => false));
            }

        } else {
            
            http_response_code(401);

            $this->_helper->json(array("message" => "Access denied.", "status" => false));
                
        }
    }
}