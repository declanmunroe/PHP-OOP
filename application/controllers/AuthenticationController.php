<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 27/04/2018
 * Time: 08:52
 */
class AuthenticationController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {

    }

    public function loginAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $this->redirect('event/addmultibleregistrants');
        }

        $this->_helper->layout->disableLayout();
        $form = new Application_Form_LoginAdmin();
        // die($form);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {

                $authAdapter = $this->getAuthAdapter();

                $username = $formData['username'];
                $password = $formData['password'];

//                $username = 'declan.munroe@ics.ie';
//                $password = 'declan';

                $authAdapter->setIdentity($username)
                    ->setCredential($password);

                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid())
                {
                    $identity = $authAdapter->getResultRowObject();

                    $authStorage = $auth->getStorage();
                    $authStorage->write($identity);

                    $this->redirect('/event/index');
                }
                else
                {
                    echo "Invalid";
                }


            }
            else
            {
                die("Incorrect username or password");
            }
        }
//        else
//        {
//            die("Invalid Form data");
//        }
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->redirect('index/index');
    }

    private function getAuthAdapter()
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('ps_admin')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');

        return $authAdapter;
    }

    public function hashAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $email = 'declan.munroe@ics.ie';

        $hashemail = new Application_Service_Tokens();
        $result = $hashemail->moodlehash($email);
        die(var_dump($result));
    }
}