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
        // https://framework.zend.com/manual/1.0/en/zend.auth.html        This article on sessions does not seem to work. To get around this I've introduced a portal value in
        //                                                                the auth object below
        //die(var_dump(print_r(Zend_Auth::getInstance())));
        //die(var_dump(print_r(Zend_Auth::getInstance()->getStorage())));
        //die(var_dump(print_r(Zend_Auth::getInstance()->getStorage()->read())));
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            $this->redirect('event/addmultibleregistrants');
        }

        $this->_helper->layout->disableLayout();
        $form = new Application_Form_LoginAdmin();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {

                
                $username = $formData['username'];
                $password = $formData['password'];
                
                $hashed = $this->hashpassword($password);

                

                $auth = Zend_Auth::getInstance();
                
//                $auth->setStorage ( new Zend_Auth_Storage_Session ( 'New_Portal' ) );
//                $namespace = new Zend_Session_Namespace('New_Portal');
//                $namespace->setExpirationSeconds(7200); // 2 hours
                
                $authAdapter = $this->getAuthAdapter();
                $authAdapter->setIdentity($username)
                            ->setCredential($hashed);
                
                $result = $auth->authenticate($authAdapter);

                if ($result->isValid())
                {
                    $identity = $authAdapter->getResultRowObject();

                    $authStorage = $auth->getStorage();
                    $identity->portal = 'Declan_Admin'; // Write an additional variable to the auth object
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
        $namespace = new \Zend_Session_Namespace( 'Zend_Auth' );
        $namespace->unsetAll();
        $namespace = new \Zend_Session_Namespace( 'New_Portal' );
        $namespace->unsetAll();
        Zend_Session::destroy( true );
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

        // https://security.stackexchange.com/questions/8015/what-should-be-used-as-a-salt
        
//        $password = password_hash("kevin", PASSWORD_BCRYPT);    Strong working version
//        if (password_verify("kevin", $password)) {
//            echo "Your in";
//        } else {
//            echo "Not today";
//        }
        
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        
        $pass1 = "kevin";
        $hashed1 = sha1(md5($salt.$pass1.$salt));
        
        $pass2 = "kevin";
        $hashed2 = sha1(md5($salt.$pass2.$salt));
        
        if($hashed1 == $hashed2) {
            echo "Your in";
        } else {
            echo "Not today sunshine";
        }
    }
    
    private function hashpassword($password) {
        $salt = 'abcdefghijklmnopqrstuvwxyz0123456789';
        
        $pass = $password;
        $hashed = sha1(md5($salt.$pass.$salt));
        
        return $hashed;
    }
}