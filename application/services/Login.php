<?php
class Application_Service_Login
{

    public function loginAdmin(Application_Form_LoginAdmin $form)
    {
        $dbUserTable = new Zend_Db_Table('ps_admin');

        $select = $dbUserTable->select()->setIntegrityCheck(false);

        $query = $select->from(array('u' => 'ps_admin'), array(

            'u.admin_id',
            'u.username'
        ))
            ->where('u.username = ?',$form->getValue('username'));

        return $dbUserTable->fetchAll($query);
    }
}
