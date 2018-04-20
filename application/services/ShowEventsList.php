<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 19/04/2018
 * Time: 11:20
 */
class Application_Service_ShowEventsList
{
    public function listofevents()
    {
//        $events = array('1','2','3','4','5');
//        return $events;
        $db = new Zend_Db_Table('jos_eb_events');

        $rows = $db->fetchAll();
        return $rows;
    }

//    public function getRegistrantsForEvent($event_id)
//    {
//        $db = new Zend_Db_Table('jos_eb_registrants');
//
//        $rows = $db->fetchAll("event_id ='.$event_id.'");
//        return $rows;
//    }

    public function getRegistrantsForEvent($event_id)
    {
        $dbUserTable = new Zend_Db_Table('jos_eb_registrants');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        //View_Corporate_Member_Engagement

        $query = $select->from(array('u' => 'jos_eb_registrants'), array(

            'u.event_id',
            'u.first_name',
            'u.last_name',
            'u.email',
            'u.booking_first_name'
        ))
            ->where('u.event_id = ?',$event_id)->limit(50);

        //  die($select->assemble());

        return $dbUserTable->fetchAll($query);
    }

    public function insertNewRegistrant($event_id, $firstName, $lastName, $email)
    {
        $db = new Zend_Db_Table('jos_eb_registrants');

        $data = array(
            'event_id' => $event_id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'booking_first_name' => 'SureSkills'
        );
        $db->insert($data);
    }

    public function insertMultipleNewRegistrant($event_id, $newRegistrants, $booked_by)
    {
        $db = new Zend_Db_Table('jos_eb_registrants');

        foreach($newRegistrants as $registrant) {

            $data = array(
                'event_id' => $event_id,
                'first_name' => $registrant->name_f,
                'last_name' => $registrant->name_l,
                'email' => $registrant->email,
                'booking_first_name' => $booked_by
            );
            die(print_r($data));
//            $db->insert($data);
        }
    }
}

?>