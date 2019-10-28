<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 19/04/2018
 * Time: 10:25
 */
class EventController extends Zend_Controller_Action
{

    public function init()
    {
        
        $auth = Zend_Auth::getInstance();
        // Because this page uses a session variable New_Portal we need to specify it below to make sure we can call it after it was created when the user loged in
        // in the previous controller/action
        // If we dont set it here the Zend session storage defaults back to Zend_Auth
        // This article explains the situation
        // https://stackoverflow.com/questions/3859427/zend-auth-when-i-set-a-custom-storage-namespace-it-does-not-exist-when-i-redir
        $auth->setStorage(new Zend_Auth_Storage_Session('New_Portal'));
        //die(var_dump(print_r(Zend_Auth::getInstance())));
        //die(var_dump(print_r(Zend_Auth::getInstance()->getStorage())));
        //die(var_dump(print_r(Zend_Auth::getInstance()->getStorage()->read())));
        //die(var_dump(print_r(Zend_Auth::getInstance()->hasIdentity())));

        if (!$auth->hasIdentity()) {
            $urlOptions = array('controller' => 'authentication', 'action' => 'login', 'module' => 'default');

            $this->_helper->redirector->gotoRoute($urlOptions);
        }
    }

    public function indexAction()
    {
//        $this->view->listings =  array('1','2','3');
        $eventList =  new Application_Service_ShowEventsList();
        $this->view->listings = $eventList->listofevents();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $event_id = $formData['whatselected'];
            $firstName = $formData['f_name'];
            $lastName = $formData['l_name'];
            $email = $formData['email'];

            $eventRegistrants = new Application_Service_ShowEventsList();
            $eventRegistrants->insertNewRegistrant($event_id, $firstName, $lastName, $email);
            $this->view->registrants = $eventRegistrants->getRegistrantsForEvent($event_id);
        }
        else
        {
            $eventRegistrants = new Application_Service_ShowEventsList();
            $this->view->registrants = $eventRegistrants->getRegistrantsForEvent("2");
        }
    }

    public function addmultibleregistrantsAction()
    {
//        $this->view->hello = "Hello registrants";
        $eventList =  new Application_Service_ShowEventsList();
        $this->view->listings = $eventList->listofevents();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $event_id = $formData['whatselected'];
            $first_name = $formData['first_name'];
            $last_name = $formData['last_name'];
            $email = $formData['email'];
            $booked_by = $formData['booked_by'];

            $eventRegistrants = new Application_Service_ShowEventsList();

//            foreach ($email as $key => $value)
//            {
//                $emailvalues[] = $value;
//            }
            $entries = array_filter($email);

            $count = sizeof($entries);

            for ($i=0; $i<$count; $i++)
            {
                $newRegistrants[] = new Application_Service_EventRegistrant($first_name[$i], $last_name[$i], $email[$i]);
            }

            $eventRegistrants->insertMultipleNewRegistrant($event_id, $newRegistrants, $booked_by);

            $this->view->registrants = $eventRegistrants->getRegistrantsForEvent($event_id);
        }
        else
        {
            $eventRegistrants = new Application_Service_ShowEventsList();
            $this->view->registrants = $eventRegistrants->getallregistrants();
        }
    }

    public function editregistrantAction()
    {
        $registrant_id = (int) $this->getParam('id', 0);
        $registrant = new Application_Service_ShowEventsList();

        $this->view->registrantdetails = $registrant->getRegistrantDetails($registrant_id);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $id = $formData['id'];
            $first_name = $formData['first_name'];
            $last_name = $formData['last_name'];
            $email = $formData['email'];


            $updateRegistrant = new Application_Service_ShowEventsList();
            $updateRegistrant->updateRegistrant($id, $first_name, $last_name, $email, $registrant_id);

            $urlOptions = array(
                'controller' => 'event',
                'action' => 'addmultibleregistrants',
                'module' => 'default'
            );

            $this->_helper->redirector->gotoRoute($urlOptions);
        }
    }

    public function deleteregistrantAction()
    {
        $this->view->registrant_id = $registrant_id = (int) $this->getParam('id', 0);

        $registrant = new Application_Service_ShowEventsList();

        $this->view->registrantdetails = $registrant->getRegistrantDetails($registrant_id);

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $id = $formData['id'];
            $answer = $formData['decision'];
//            die($id);

            if ($answer == "yes")
            {
                $deleteRegistrant = new Application_Service_ShowEventsList();
                $deleteRegistrant->deleteRegistrant($id);

                $urlOptions = array(
                    'controller' => 'event',
                    'action' => 'addmultibleregistrants',
                    'module' => 'default'
                );

                $this->_helper->redirector->gotoRoute($urlOptions);
            }
            else if ($answer == "no")
            {
                $urlOptions = array(
                    'controller' => 'event',
                    'action' => 'addmultibleregistrants',
                    'module' => 'default'
                );

                $this->_helper->redirector->gotoRoute($urlOptions);
            }
        }

    }

    public function datatableAction()
    {
        // $people = new Application_Service_ShowEventsList();

        // for ($i=1; $i<2500; $i++)
        // {
        //     $people->insertPeopleDatatable("First name_[$i]", "Last name_[$i]", "Email_[$i]");
        // }
        // script to populate database with 2500 rows

        $db = new Zend_Db_Table('data_table');
        
        $this->view->rows = $db->fetchAll();
        //die(print_r($rows));
    }
    
    public function testdebounceangularAction() {
        $body = $this->getRequest()->getRawBody();
        
        $db = new Zend_Db_Table('jos_eb_registrants');
        
        $row = $db->fetchRow("email = '$body'");
        
        if ($row) {
            $this->_helper->json(true);
        } else {
            $this->_helper->json(false);
        }
        
    }
}