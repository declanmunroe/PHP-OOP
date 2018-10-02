<?php

class TestcurlController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $data = array('first_name' => 'Declan', 'last_name' => 'Munroe', 'age' => 22);
        
        $result = $this->curldata($data);
        
        $insertdata = new Application_Service_ShowEventsList();
        $response = $insertdata->insertCurlData($result['response']['first_name'], $result['response']['last_name'], $result['response']['age'], $result['response']['job']);
//        print_r($response);
        
        if ($response) {
            echo "New job added : " . $result['response']['job'];
        } else {
            echo "Something went wrong";
        }
//        print_r($result);
    }
    
    public function processcurlAction() {
        
        $array_two = array('job' => 'Computers');
        
        $formData = $this->getRequest()->getPost();
        
        $merge = array_merge($formData, $array_two);
        $this->_helper->json(['response' => $merge]);
    }
    
    private function curldata($data) {
        $url = 'http://zendcode.localhost/testcurl/processcurl';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_FAILONERROR, true); // This set to true will display errors on the screen if errors occur
        
        $response = curl_exec($curl);
        
        if ($response === false) {
            echo 'cURL Error: ' . curl_error($curl);
        }
        curl_close($curl);
        
        $result = json_decode($response, true);
        
        return $result;
    }
    
}

