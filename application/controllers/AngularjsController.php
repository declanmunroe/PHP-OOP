<?php

class AngularjsController extends Zend_Controller_Action
{
    public function indexAction() {
        $this->_helper->layout()->disableLayout(); 
    }
    
    public function processdataAction() {
        
        $formData = $this->getRequest()->getPost();
        
//        die(print_r($formData));
        $this->_helper->json($formData);
    }
    
    public function paypalAction() {
        $formData = $this->getRequest()->getPost();
        die(print_r($formData));
    }
    
    public function customElementsAction() {
        $this->_helper->layout()->disableLayout();
    }
}

