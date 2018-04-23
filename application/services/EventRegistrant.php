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

        $this->name_f = $name_f;

        $this->name_l = $name_l;

        $this->email = $email;
    }
}

?>