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

    public function getRegistrantDetails($id)
    {
        $dbUserTable = new Zend_Db_Table('jos_eb_registrants');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        $query = $select->from(array('u' => 'jos_eb_registrants'), array(

            'u.id',
            'u.first_name',
            'u.last_name',
            'u.email'
        ))
            ->where('u.id = ?',$id);


        //  die($select->assemble());

        return $dbUserTable->fetchAll($query);
    }

    public function updateRegistrant($id, $firstName, $lastName, $email, $registrant_id)
    {
        $db = new Zend_Db_Table('jos_eb_registrants');

        $data = array(
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email
        );

        $db->update($data, "id = " . $registrant_id);
    }

    public function deleteRegistrant($id)
    {
        $db = new Zend_Db_Table('jos_eb_registrants');

        $row = $db->fetchRow('id = ' . $id);

        $row->delete();

//        $data = array('id' => $id);
//
//        $db->delete($data, 'id = ' . $registrant_id);
    }

    public function getRegistrantsForEvent($event_id)
    {
        $dbUserTable = new Zend_Db_Table('jos_eb_registrants');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        //View_Corporate_Member_Engagement

        $query = $select->from(array('u' => 'jos_eb_registrants'), array(

            'u.id',
            'u.event_id',
            'u.first_name',
            'u.last_name',
            'u.email',
            'u.booked_by',
            new Zend_Db_Expr('case when u.event_id >= 20 then "The quantity is greater" else "The quantity is less" end as case_example')
        ))
            ->join(array(
                't2' => 'jos_eb_events'
            ), 't2.event_id=u.event_id', array(
                't2.title'
            ))
            ->where('u.event_id = ?',$event_id)->limit(50)
            ->order('u.id DESC');

        //  die($select->assemble());

        return $dbUserTable->fetchAll($query);
    }

    public function getallregistrants()
    {
        $dbUserTable = new Zend_Db_Table('jos_eb_registrants');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        $query = $select->from(array('t1' => 'jos_eb_registrants'),
            array(
                't1.id',
                't1.event_id',
                't1.first_name',
                't1.last_name',
                't1.email',
                't1.booked_by',
                new Zend_Db_Expr('case when t1.event_id >= 20 then "The quantity is greater" else "The quantity is less" end as case_example')
            ))
            ->join(array(
                't2' => 'jos_eb_events'
            ), 't2.event_id=t1.event_id', array(
                't2.title'
            ))
            ->order('t1.id DESC');

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
            'booked_by' => 'SureSkills'
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
                'booked_by' => $booked_by
            );
//            die(print_r($data));
            $db->insert($data);
        }
    }
}

?>