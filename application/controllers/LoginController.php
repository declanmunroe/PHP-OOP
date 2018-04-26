<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 26/04/2018
 * Time: 14:55
 */
class LoginController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $form = new Application_Form_LoginAdmin();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $username = $formData['username'];
//            die($username);

            if ($form->isValid($formData)) {

                $service = new Application_Service_Login();
                $result = $service->loginAdmin($form);
//                die(print_r($result));

                if ($result == $username) {
                    $urlOptions = array(
                        'controller' => 'event',
                        'action' => 'addmultibleregistrants',
                        'module' => 'default'
                    );

                $this->_helper->redirector->gotoRoute($urlOptions);
                } else {
                    die("Incorrect username or password");
                }
            } else {
                die("Invalid Form data");
            }
        }
    }
}

?>