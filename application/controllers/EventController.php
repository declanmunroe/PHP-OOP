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
        /* Initialize action controller here */
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

            $newRegistrants[] = new Application_Service_EventRegistrant($first_name, $last_name, $email);

            $eventRegistrants->insertMultipleNewRegistrant($event_id, $newRegistrants, $booked_by);

            $this->view->registrants = $eventRegistrants->getRegistrantsForEvent($event_id);

//            $first_name = $formData['first_name'];
//            $last_name = $formData['last_name'];
//            $email = $formData['email'];
//            $booked_by = $formData['booked_by'];
//
//            $registrants = [];
//
//            die(print_r($first_name));

//            for ($i = 0; $i < $count; $i++) {
//
//                echo "<p>{$formData ['first_name'][$i]} - {$formData['last_name'][$i]} - {$formData['email'][$i]}";
//
//            }
        }
    }

}