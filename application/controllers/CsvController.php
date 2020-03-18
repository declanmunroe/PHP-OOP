<?php

class CsvController extends Zend_Controller_Action
{
    
    public function indexAction() {
        
        $results = array_map('str_getcsv', file('C:\Users\declan.munroe\Desktop\unsubscribed_members.csv'));
        
        $dummy = array();
        
        $errors = 0;
        
        $count = sizeof($results);
        
        for ($i=0; $i<$count; $i++) {
            $dummy[] = array_combine($results[0], $results[$i]);
        }
        
        $newArray = array_values(array_slice($dummy, 1));
        
        $count2 = sizeof($newArray);
        //die(var_dump($count2));
        
        //die(print_r($newArray[1455]));
        
        $db = new Zend_Db_Table('mailchimp_unsubscribed');
        
        for ($v=0; $v<$count2; $v++) {
            if (is_array($newArray[$v])) {
                $db->insert($newArray[$v]);
            } else {
                $errors = $errors + 1;
            }
        }
        
        echo 'Done<br>';
        echo $errors . ' rows with an extra comma<br>';
        die('!');

    }
    
}