<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 16/11/2018
 * Time: 12:12
 */
class RegisterController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $form = new Application_Form_LoginAdmin();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
//            die(print_r($formData));

            if (!$form->isValid($formData)) {
                die("form not valid");
            }
            else {
                $email = $formData['username'];
                $password = $formData['password'];
                
                $hashed_password = $this->hashPassword($password);
//                die($hashed_password);
                $insert_registrant = $this->addRegistrant($email, $hashed_password);
                
                if ($insert_registrant) {
                    echo "Registrant added";
                } else {
                    echo "Something went wrong";
                }
            }
        }
    }
    
    private function hashPassword($password) {
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        
        $pass = $password;
        $hashed = sha1(md5($salt.$pass.$salt));
        
        return $hashed;
    }
    
    private function addRegistrant($email, $hashed_password) {
        $db = new Zend_Db_Table('ps_admin');

        $data = array(
            'username' => $email,
            'password' => $hashed_password,
            'role' => 'user'
        );
//            die(print_r($data));
        $db->insert($data);
        
        return true;
    }
}