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
        
        file_put_contents("ipnresult.txt", $response);
        
        die("!");
        
    }
    
}

