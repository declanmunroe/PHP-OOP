<?php

class Application_Service_ServiceForm
{
    public function submitToTable($firstName, $lastName, $email, $booking_firstname, $booking_lastname, $booking_email)
    {
        $db = new Zend_Db_Table('service_form');

        $data = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'booking_first_name' => $booking_firstname,
            'booking_last_name' => $booking_lastname,
            'booking_email' => $booking_email
        );
        $db->insert($data);
    }
}

?>