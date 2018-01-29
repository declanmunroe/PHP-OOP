<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function demoAction()
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
