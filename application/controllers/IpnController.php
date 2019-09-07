<?php

class IpnController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
        
        // Initialize curl
        $curl = curl_init();
        
        // Url to submit to
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // Return output instead of outputting it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // We are doing a post request
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Adding the post variables to the request
        curl_setopt($curl, CURLOPT_POSTFIELDS, "cmd=_notify-validate&" . http_build_query($_POST));
        
        // Execute the request and fetch the response and check for errors below
        $response = curl_exec($curl);
        
        // Close and free up the curl handle
        curl_close($curl);
        
        if ($response == "VERIFIED") {
            
            $file = fopen("ipnresult.txt", "w");
            
            foreach ($_POST as $key => $value) {
                
                fwrite($file, "$key => $value \r\n");
                
            }
            
        }
        
        die("!");
        
    }
    
    public function registerAction() {
        // Works No cors issue at all
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: *");
        
        $body = $this->getRequest()->getRawBody();
        
        $response = json_decode($body, true);
        
        $db = new Zend_Db_Table('am_user_holding');

        $sa_user_id = $db->insert($response);
        
        if ($sa_user_id > 0) {
            $this->_helper->json(['sa_user_id' => $sa_user_id, 'status' => 'success']);
        } else {
            $this->_helper->json(['status' => 'error']);
        }
        
    }
    
}

