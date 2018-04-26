<?php
class Application_Service_Login
{

    public function loginAdmin($formData)
    {
        $dbUserTable = new Zend_Db_Table('ps_admin');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        $query = $select->from(array('u' => 'ps_admin'), array(

            'u.admin_id',
            'u.username'
        ))
            ->where('u.username = ?',$formData);

        return $dbUserTable->fetchRow($query);
    }
}
