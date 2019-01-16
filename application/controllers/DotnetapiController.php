<?php

class DotnetapiController extends Zend_Controller_Action
{
    public function indexAction() {
        $api_data = file_get_contents('http://localhost:51686/dictonaryapi');
        
        $this->_helper->json($api_data);
    }
    
    public function recieveAction() {
        // I sent a post request from my dot net application and am handling the request in my zend application.
        // For this to work I needed to modify the .htaccess to allow my dot net url access to my zend application.
        // I added Header set Access-Control-Allow-Origin http://localhost:51686 in the .htaccess file
        
        // This excercise was carried out on my dell laptop
        
        $formData = $this->getRequest()->getPost();
        
        $new_array = array('number' => $formData['number'], 'sum' => 4);
        
        $this->_helper->json($new_array);
    }
    
    public function postdotnetAction()
    {
        
    }
}