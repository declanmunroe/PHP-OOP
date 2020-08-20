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
    
    #Read a list of emails from a scv file and generate a string that I can copy to use for an insert statement in mysql
    public function alexAction()
    {
        $emails = [];
        $quates = '""';
        
        $results = array_map('str_getcsv', file('C:\Users\declan.munroe\Downloads\ba_opps.csv'));
        
        for ($i=0; $i<sizeof($results); $i++) {
            $emails[] = '('.$quates[0].$results[$i][6].$quates[1].')';
        }
        
        $email_string = implode(",", $emails);
        
        die($email_string);
    }
    
}