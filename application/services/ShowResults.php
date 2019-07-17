<?php

class Application_Service_ShowResults
{
    
    public static function showResults() 
    {
        $db = new Application_Model_PracticeForm_FormPractice();

        $select = $db->select()
                     ->from(array('t1' => 'practice_form'), array('t1.first_name', 't1.last_name', 't1.share_contact_info', 't1.allow_photos'));

        return $db->fetchAll($select)->toArray();

        // $members = new Application_Model_DbTable_Members();
        // $select = $members->select();
        // $select->setIntegrityCheck(false);
        // $select->from(array('t1' => 'jos_eb_registrants'), array('t1.first_name', 't1.last_name'));
        // $select->join(array('t2' => 'jos_eb_events'), 't1.event_id=t2.id', array('t2.title'));
        // //$select->where('t1.register_date LIKE ?', $year.'%');
        // //$select->where('t1.register_date LIKE ?', '%-'.$month.'-%'); 
        // $select->where('YEAR(t1.register_date) = ?', $year);
        // $select->where('MONTH(t1.register_date) = ?', $month); 

        // $this->view->rows = $members->fetchAll($select);

        // echo $select;
    }

  
}

?>
