<?php

class Application_Model_Checkbox_Checkboxform extends Zend_Db_Table_Abstract
{
    protected $_name = 'checkbox';


    public function addCheckboxinfo($contact, $photo)
    {
        $data = array(
            'contact' => $contact,
            'photo' => $photo,
        );
        $this->insert($data);
    }
}