<?php

class Application_Model_PracticeForm_FormPractice extends Zend_Db_Table_Abstract
{
    protected $_name = 'practice_form';


    public function addForminfo($firstName, $lastName, $contact, $photo)
    {
        $data = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'share_contact_info' => $contact,
            'allow_photos' => $photo,
        );
        $this->insert($data);
    }
}