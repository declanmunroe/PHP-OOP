<?php

class Application_Service_ShowResults
{
    
    public static function showResults() 
    {
        $db = new Application_Model_Checkbox_Checkboxform();

        $select = $db->select()
                     ->from(array('t1' => 'checkbox'), array('t1.contact', 't1.photo'));

        return $db->fetchAll($select)->toArray();
    }

  
}

?>
