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
        
        // $members = new Application_Model_DbTable_Members();
        // $where = array('name_f = ?' => 'Steven',
        //                'name_l = ?' => 'Foster',
        //                'phone' => '016222000');
        // $this->view->rows = $members->fetchAll($where);
        
        //$select = $members->select();
        //$select->where('name_f = ?', 'Steven');
        //$this->view->rows = $members->fetchAll($select);     both versions working
    }

    public function dateyearAction()
    {
        $this->view->currently_selected = date('Y');
        $this->view->earliest_year = 1950; 
        $this->view->latest_year = date('Y');


        $this->view->month_array = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

        if ($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            $year = $formData['year'];
            $month = $formData['month'];

            // $members = new Application_Model_DbTable_Members();                      //working version!!!!!!!!!!!!!!!!!!!
            // $select = $members->select();                                                //working version!!!!!!!!!!!!!!!!!!!
            // $select->where('register_date LIKE ?', $year.'%');                           //working version!!!!!!!!!!!!!!!!!!!
            // $select->where('register_date LIKE ?', '%-'.$month.'-%');                       // working version!!!!!!!!!!!!!!!!!!!
            // $this->view->rows = $members->fetchAll($select);                               //working version!!!!!!!!!!!!!!!!!!!

            
            $members = new Application_Model_DbTable_Members();
            $select = $members->select();
            $select->setIntegrityCheck(false);
            $select->from(array('t1' => 'jos_eb_registrants'), array('t1.first_name', 't1.last_name'));
            $select->join(array('t2' => 'jos_eb_events'), 't1.event_id=t2.id', array('t2.title'));
            //$select->where('t1.register_date LIKE ?', $year.'%');
            //$select->where('t1.register_date LIKE ?', '%-'.$month.'-%'); 
            $select->where('YEAR(t1.register_date) = ?', $year);
            $select->where('MONTH(t1.register_date) = ?', $month); 

            $this->view->rows = $members->fetchAll($select);

            echo $select;

        }
    }

    public function ajaxdataAction()
    {
        $this->view->currently_selected = date('Y');
        $this->view->earliest_year = 1950; 
        $this->view->latest_year = date('Y');


        $this->view->month_array = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

        // if ($this->getRequest()->isPost()){
        //     $formData = $this->getRequest()->getPost();
        // }
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $this->view->year = $formData['year'];
            $this->view->month = $formData['month'];
        
        // if (isset($_POST['year'], $_POST['month'])){
        //     print_r($_POST);
        // }
        }
    }

    public function testpostmanAction()
    {
        $this->view->month_array = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");
    }

    


    
}