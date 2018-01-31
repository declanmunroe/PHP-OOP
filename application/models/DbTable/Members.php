<?php

class Application_Model_DbTable_Members extends Zend_Db_Table_Abstract
{
    protected $_name = 'jos_eb_registrants';
    protected $_name2 = 'jos_eb_events';

    public function getMembers()
    {
        $id = (int)$id;
        $name_f = $name_f;
        $name_l = $name_l;
        $row = $this->fetchRow('user_id = ' .$id. 'name_f = ' .$name_f. 'name_l = ' .$name_l );
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row->toArray();
    }

    public function get10Members()
    {
        $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('t1' => 'am_user'), array('t1.name_f', 't1.name_l'))
        ->limit(10);
    
      //  die($select->assemble());
        //  echo $select->assemble() ;
        $row = $this->fetchRow($select);
    
    
        if (!$row) {
            return null;
        }
    
        return $row->toArray();
    }

    
}