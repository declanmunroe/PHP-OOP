<?php

/**
 * Created by PhpStorm.
 * User: declan.munroe
 * Date: 20/04/2018
 * Time: 15:44
 */
class Application_Service_EventRegistrant {

    public $name_f;
    public $name_l;
    public $email;

    public function __construct($name_f,$name_l, $email) {



        foreach($name_f as $firstName) {
            $this->name_f = $firstName;
        }

        foreach($name_l as $lastName) {
            $this->name_l = $lastName;
        }

        foreach($email as $singleEmail) {
            $this->email = $singleEmail;
        }


    }

}

?>