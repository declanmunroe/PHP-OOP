<?php

class Api_Model_ApiDetails extends Zend_Db_Table_Abstract
{    
    protected $_name = "todo_app";
    protected $_id = "id";

    public function alltododetails()
    {
        $dbUserTable = new Zend_Db_Table('todo_app');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        $query = $select->from(array('t1' => 'todo_app'),
            array(
                't1.id',
                't1.task_for',
                't1.task_details',
                't1.complete_on',
                't1.pic_url'
            ));

        return $dbUserTable->fetchAll($query)->toArray();
    }
}