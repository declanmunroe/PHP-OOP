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
          'success_url' => "https://c9c25631.ngrok.io/stripecheckout/success/uid/{$formData['uniqueid']}",
          'cancel_url' => 'https://c9c25631.ngrok.io/stripecheckout/cancel',
        ]);
          
        PaymentIntent::retrieve($stripe_create_response->payment_intent);  // Retrieves the current state of my payment intent // Right now meta data is empty
        
        // Add the payment intent id to metadata so I can access in success after webhook is completed
        // Webhook will always be executed before success url if my webhook is checking event checkout.session.completed set out in stripe webhook in stripe account
        
        PaymentIntent::update($stripe_create_response->payment_intent,['metadata' => $formData]); // Update current state to accomodate meta_data
                                                                                                  // Metadata can only contain one array, it cannot contain array of arrays, 
                                                                                                  // throws stripe api error if so
        
        PaymentIntent::update($stripe_create_response->payment_intent,['description' => $formData['description']]);
        
        PaymentIntent::retrieve($stripe_create_response->payment_intent); // Retrieve the updated payment intent with the meta data added to my stripe object response
        
        $this->_helper->json($stripe_create_response);
        
        // Docs say to validate a propper successfull charge transaction that you should not fully rely on the success url, you should use a webhook as well.
        // But to use a webhook it is best practice if not nessacary to have seperate stripe accounts for every stripe payment option in each website
    }
    
    public function successAction()
    {
        $uid = $this->getParam('uid', 0);
        
        $db = new Zend_Db_Table('stripe_transactions');
        $row = $db->fetchRow("unique_id = '$uid'")->toArray();
        
        $intent = PaymentIntent::retrieve($row['payment_intent']);
        
        $type = $intent['charges']['data'][0]['metadata']['type'];
        
        switch ($type) {
            case "eventregister":
                header("Location: {$intent['charges']['data'][0]['metadata']['sucessurl']}?token={$intent['charges']['data'][0]['id']}");
                die();
                break;
            
            case "icdltoolkit":
                header("Location: {$intent['charges']['data'][0]['metadata']['sucessurl']}?token={$intent['charges']['data'][0]['id']}");
                die();
                break;
         
            default:
                die("Error");
        }
        
        $this->_helper->json($intent);
    }
    
    public function cancelAction()
    {
        die("On cancel page");
    }
    
    public function processstripepaymentAction() {
        // https://stripe.com/docs/payments/checkout/fulfillment   //checkout.session.completed webhook gets executed before redirect to success url
        // Ngrok url for testing webhook https://c6c1d968.ngrok.io/stripecheckout/processstripepayment
        
        /*
        $db = new Zend_Db_Table('stripe_events');
        
        $response = file_get_contents('php://input'); // This method gets the raw post data from a json body in json format

        $response_array = json_decode($response, true);
        
        $params = array();
        
        if ($response_array['data']['object']['status'] == 'succeeded') {
            
            $params = array('payment_intent_id' => $response_array['data']['object']['id'], 
                            'amount' => $response_array['data']['object']['amount'], 
                            'charge_id' => $response_array['data']['object']['charges']['data'][0]['id'],
                            'email' => $response_array['data']['object']['charges']['data'][0]['billing_details']['email'],
                            'type' => $response_array['data']['object']['charges']['data'][0]['metadata']['type'],
                            'date_created' => date('Y-m-d H:i:s', $response_array['data']['object']['created'])
                           );

            $db->insert($params);

            $this->_helper->json($params);
            
        } else {
            $this->_helper->json("Payment error");
        }
        
        */
        
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        $intent = PaymentIntent::retrieve($response_array['data']['object']['payment_intent']);
        
        $db = new Zend_Db_Table('stripe_transactions');
        $db->insert(array('unique_id' => $intent['charges']['data'][0]['metadata']['uniqueid'], 'payment_intent' => $intent['id'], 'type' => $intent['charges']['data'][0]['metadata']['type']));
        
        $this->_helper->json(array('unique_id' => $intent['charges']['data'][0]['metadata']['uniqueid'], 'payment_intent' => $intent['id'], 'type' => $intent['charges']['data'][0]['metadata']['type']));
        
    }
    
    public function angularAction() {
        
        $body = $this->getRequest()->getRawBody();
        
        $formData = Zend_Json::decode($body);
            
        $this->_helper->json($formData);
        
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
          'success_url' => "https://c9c25631.ngrok.io/stripecheckout/success/uid/{$formData['uniqueid']}",
          'cancel_url' => 'https://c9c25631.ngrok.io/stripecheckout/cancel',
        ]);
          
        $this->_helper->json($stripe_create_response);
        
    }
    
}

