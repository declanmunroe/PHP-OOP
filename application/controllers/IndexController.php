<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $urlOptions = array('controller' => 'authentication', 'action' => 'login', 'module' => 'default');

            $this->_helper->redirector->gotoRoute($urlOptions);
        }
    }

    public function indexAction()
    {
        $this->redirect('/event/index');
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
    
    public function diceAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $dice1 = array('0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6');
        $dice2 = array('0' => '1', '1' => '2', '2' => '3', '3' => '4', '4' => '5', '5' => '6');
        
        for ($i=0; $i<6; $i++){
            foreach ($dice1 as $key => $value) {
            echo $value.",".$dice2[$i].'<br>';
        }
        echo '<br>';
        }
    }


}

