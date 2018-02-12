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

    public function tickboxAction()
    {
        $form = new Application_Form_RegistrationTickbox();
        $form->submit->setLabel('Add');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $contact = $form->getValue('contact');
                $photo = $form->getValue('photo');
                $add = new Application_Model_Checkbox_Checkboxform();
                $add->addCheckboxinfo($contact, $photo);

                $this->_helper->redirector('tickbox');
            } else {
                $form->populate($formData);
            }
        }
    }

    public function practiceformAction()
    {
        $form = new Application_Form_PracticeForm();
        $form->submit->setLabel('Submit Now');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()){
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $first = $form->getValue('first_name');
                $last = $form->getValue('last_name');
                $contact = $form->getValue('share_contact_info');
                $photo = $form->getValue('allow_photos');
                $add = new Application_Model_PracticeForm_FormPractice();
                $add->addForminfo($first, $last, $contact, $photo);

                $this->_helper->redirector('practiceform');
            } else {
                $form->populate($formData);
            }
        }
    }

    


    
}