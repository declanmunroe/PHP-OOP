<?php

class Api_IndexController extends Zend_Controller_Action
{
    public $todolist;
    public $api_details = array();
    
    public function apidetails(){
        $model = new Api_Model_ApiDetails();
        
        $this->api_details = $model->alltododetails();
//        die(print_r($this->api_details));
    }
    
    public function apiAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->todolist = new Api_Model_ApiJson();
        $this->apidetails();
        
        $count = sizeof($this->api_details);
        echo $count;
        
        
            $this->todolist->construct_apijsonobject
            (
                $this->api_details
            );
                    
            $this->_helper->json($this->todolist);
        
//        $this->todolist->construct_apijsonobject
//        (
//            $this->api_details[60]['id'],
//            $this->api_details[60]['task_for'],
//            $this->api_details[60]['task_details'],
//            $this->api_details[60]['complete_on'],
//            $this->api_details[60]['pic_url']
//        );
//
//        $this->_helper->json($this->todolist);
    }

    public function indexAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
}