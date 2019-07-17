<?php

class TestcurlController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = array('first_name' => 'Joe', 'last_name' => 'Munroe', 'age' => 59);
        
        $result = $this->curldata($data);
        
        $insertdata = new Application_Service_ShowEventsList();
        $response = $insertdata->insertCurlData($result['response']['first_name'], $result['response']['last_name'], $result['response']['age'], $result['response']['job']);
//        print_r($response);
        
        if ($response) {
            $this->_helper->json(['ngrok' => $result]);
            echo "New job added : " . $result['response']['job'];
        } else {
            echo "Something went wrong";
        }
//        print_r($result);
    }
    
    public function processcurlAction() {
        
        $array_two = array('job' => 'Test ngrok after push');
        
        $formData = $this->getRequest()->getPost();
        
        $merge = array_merge($formData, $array_two);
        $this->_helper->json(['response' => $merge]);
    }
    
    private function curldata($data) {
        $url = 'https://fafc79ff.ngrok.io/testcurl/processcurl';
        
        // Initialize curl
        $curl = curl_init();
        
        // Url to submit to
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // Return output instead of outputting it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // We are doing a post request
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Adding the post variables to the request
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
        // This is set to true so will display errors on the screen if errors occur
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        
        // Execute the request and fetch the response and check for errors below
        $response = curl_exec($curl);
        
        if ($response === false) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        
        // Close and free up the curl handle
        curl_close($curl);
        
        $result = json_decode($response, true);
        
        return $result;
    }
    
    public function getapiAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        
        $url = 'http://zendcode.localhost/api/index/api';
        
        // Initialize curl
        $curl = curl_init();
        
        // Url to submit to
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // Return output instead of outputting it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // This is set to true so will display errors on the screen if errors occur
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        
        // Execute the request and fetch the response and check for errors below
        $response = curl_exec($curl);
        
        if ($response === false) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        
        // Close and free up the curl handle
        curl_close($curl);
        
        print_r($response);
        
        // Needs some work
    }
    
}

