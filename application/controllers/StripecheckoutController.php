<?php

class StripecheckoutController extends Zend_Controller_Action
{
    public function init()
    {
       
    }
    
    public function indexAction()
    {  
 
    }
    
    public function chargeandcreateAction()
    {
        $formData = $this->getRequest()->getPost();
        
        $this->_helper->json($formData);
    }
}

