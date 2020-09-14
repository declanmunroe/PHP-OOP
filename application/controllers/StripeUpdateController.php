<?php

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeUpdateController extends Zend_Controller_Action
{
    public function init()
    {
        Stripe::setApiKey(Application_Service_Config::getStripePrivateKey('skills'));
    }
    
    // Works with new version of stripe api 2020-08-27
    public function checkoutWebhookAction() 
    {
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        //$this->_helper->json($response_array['data']['object']['payment_intent']); correct
        $intent = PaymentIntent::retrieve('pi_1HMzEWEosXjNQZCsyyO6stAj');
        $this->_helper->json($intent);
        // status is still succeeded and all is still captured below
        $this->_helper->json(array('unique_id' => $intent['charges']['data'][0]['metadata']['uniqueid'], 'payment_intent' => $intent['id'], 'type' => $intent['charges']['data'][0]['metadata']['type']));
    }
    
    // Works with new version of stripe api 2020-08-27
    public function paymentIntentWebhookAction()
    {
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        $this->_helper->json($response_array['data']['object']['application']);
    }
}