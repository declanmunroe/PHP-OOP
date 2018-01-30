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
        $this->view->data = array(array("FirstName"=>"Declan", "LastName"=>"Munroe", "Age"=>25),
                            array("FirstName"=>"Jim", "LastName"=>"Parsons", "Age"=>50),
                            array("FirstName"=>"Francis", "LastName"=>"Everard", "Age"=>51),
                            array("FirstName"=>"Stephen", "LastName"=>"Sheridan", "Age"=>40),
                            array("FirstName"=>"Martin", "LastName"=>"Drugan", "Age"=>67),
                            array("FirstName"=>"Michael", "LastName"=>"Hoey", "Age"=>60),
                            array("FirstName"=>"Peader", "LastName"=>"Farrel", "Age"=>72)
                            );
        
    }

    public function otherAction()
    {
        $members = new Application_Model_DbTable_Members();
        $this->view->members = $members->fetchAll();
    }
}