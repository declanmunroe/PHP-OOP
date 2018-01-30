<?php

class Application_Service_Members
{
    
    public static function all() 
    {
        $members = [
            
            
            ['FirstName'=>'Declan', 'LastName'=>'Munroe', 'Age'=>25],
            ['FirstName'=>'Jim', 'LastName'=>'Parsons', 'Age'=>50],
            ['FirstName'=>'Francis', 'LastName'=>'Everard', 'Age'=>51],
            ['FirstName'=>'Stephen', 'LastName'=>'Sheridan', 'Age'=>40],
            ['FirstName'=>'Martin', 'LastName'=>'Drugan', 'Age'=>67],
            ['FirstName'=>'Michael', 'LastName'=>'Hoey', 'Age'=>60],
            ['FirstName'=>'Peader', 'LastName'=>'Farrel', 'Age'=>72]
        ];

        return $members;
    }

  
}
