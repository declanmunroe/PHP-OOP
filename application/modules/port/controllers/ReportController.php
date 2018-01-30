<?php

class Port_ReportController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function engagementAction()
    {
        // action body
        
        
        $this->view->members = Application_Service_Members::all();
        
    }

    public function otherAction()
    {
        $members = new Application_Model_DbTable_Members();
        $this->view->members = $members->fetchAll();
    }
}