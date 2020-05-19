<?php

class Application_Service_Config
{
    public static function getStripePrivateKey($source)
    {
        // Right now I am using the same keys but its ready to use a different set of api keys based on source
        // Good way to work with two stripe accounts in same application code
        $api_key = ($source == 'skills') ? STRIPE_SECRET_KEY : (($source == 'member') ? STRIPE_SECRET_KEY : '');
            
        return $api_key;
    }
}

