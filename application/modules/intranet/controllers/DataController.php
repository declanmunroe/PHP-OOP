<?php

class Intranet_DataController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function showAction()
    {
        $this->_helper->layout->setLayout('datalayout');
    }
}

?>