<?php

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\StripeEvent;

class StripecheckoutController extends Zend_Controller_Action
{
    public function init()
    {
       Stripe::setApiKey(STRIPE_SECRET_KEY);
    }
    
    public function indexAction()
    {  
 
    }
    
    public function chargeandcreateAction()
    {
        $formData = $this->getRequest()->getPost();
        
        // To use Session you must have the latest version of Stripe at least 6 and above
        // Also you must have an Account name set up in https://dashboard.stripe.com/account        Declan Ics Dev was the account name i gave
        $stripe_create_response = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
            'name' => $formData['description'],
            'description' => $formData['description'],
            'images' => [],
            'amount' => $formData['price'],
            'currency' => 'eur',
            'quantity' => 1,
          ]],
          'success_url' => "http://zendcode.localhost/stripecheckout/success",
          'cancel_url' => 'http://zendcode.localhost/stripecheckout/cancel',
        ]);
          
        PaymentIntent::retrieve($stripe_create_response->payment_intent);  // Retrieves the current state of my payment intent // Right now meta data is empty
        
        PaymentIntent::update($stripe_create_response->payment_intent,['metadata' => ['reg_id' => '5666', 'intent_pid' => $stripe_create_response->payment_intent]]); // Update current state to accomodate meta_data
        
        PaymentIntent::retrieve($stripe_create_response->payment_intent); // Retrieve the updated payment intent with the meta data added to my stripe object response
        
        $this->_helper->json($stripe_create_response);
        
        // Docs say to validate a propper successfull charge transaction that you should not fully rely on the success url, you should use a webhook as well.
        // But to use a webhook it is best practice if not nessacary to have seperate stripe accounts for every stripe payment option in each website
    }
    
    public function successAction()
    {
        // Below url is stripe payment receipt for success payment
        // https://pay.stripe.com/receipts/acct_1DsaK9EosXjNQZCs/ch_1EUsUWEosXjNQZCsdyZe3NVi/rcpt_EyqAeA74HayvH2FNMC8Bic6TSnXMvKb
        //$params = $this->getAllParams(); Next version will have a unique code attached to the success url which will be stored in the meta data like done above.
        
        $events = PaymentIntent::all(["limit" => 3]);
        
        die(print_r($events));
        
    }
    
    public function cancelAction()
    {
        die("On cancel page");
    }
}

