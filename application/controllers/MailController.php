<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 03/05/2018
 * Time: 11:30
 */
class MailController extends Zend_Controller_Action
{
    public function indexAction()
    {  
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        
        $headers =  'MIME-Version: 1.0' . "\r\n"; 
        $headers .= 'From: Info@declan <info@address.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 
        
        $msg = "First line of text\nSecond line of text";

        // Mail wont work on my Dell laptop because it is not connected to any mailserver in php.ini file
        // My old laptop was thats why I was able to send mails when on my localhost
        mail("declan@gmail.com","Test mail function",$msg, $headers);
    }  
}