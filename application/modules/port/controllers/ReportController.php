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
        $where = array('name_f = ?' => 'Steven',
                       'name_l = ?' => 'Foster',
                       'phone' => '016222000');
        $this->view->rows = $members->fetchAll($where);
        
        //$select = $members->select();
        //$select->where('name_f = ?', 'Steven');
        //$this->view->rows = $members->fetchAll($select);     both versions working
    }

    public function dateyearAction()
    {
        $this->view->currently_selected = date('Y');
        $this->view->earliest_year = 1950; 
        $this->view->latest_year = date('Y');

        $this->view->month_array = array("January", "February", "March", "April", "May", "June", "July", "Aughust", "September", "October", "November", "December");

        $this->view->year = "";
        $this->view->month = "";
        if ($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            $this->view->year = $formData['year'];
            $this->view->month = $formData['month'];
        }
    }
}