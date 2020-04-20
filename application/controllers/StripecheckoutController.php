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
        die("Verified");
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
            
            case "shopOrder":
                $order_holding_id = $intent['charges']['data'][0]['metadata']['orders_id'];
                
                //$this->_helper->json(array('orders_id' => (int) $order_holding_id));
                
                $result = $this->migrateShopOrder(array('orders_id' => (int) $order_holding_id));
                
                header("Location: https://shop-ics.herokuapp.com/shop/cart/checkout/success/{$result['orders_id']}");
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
        
        //When recieving a request from an external application like an angular application two request hit this action
        //An OPTIONS request and a POST request
        //For the request to be successfull we need to return a 200 status and set the headers again for the second request after the exit;
        //We only need to set the headers again if we dont have them set in the index.php or htaccess
        
        //die($_SERVER['REQUEST_METHOD']);
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: *");
            header("Access-Control-Allow-Methods: *");
            header("HTTP/1.1 200 OK");
            exit;
        }
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: *");
        
        $body = $this->getRequest()->getRawBody();
        
        $response = json_decode($body, true);
        
        $description = $response['description'];
        $price = $response['price'];
        
        //$description = $response['description'].' Conference';
        //$price = (int) $response['price'] + 100;
        
        //$this->_helper->json(array($description, $price));
        
        $stripe_create_response = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
            'name' => $description,
            'description' => $description, 
            'images' => [],
            'amount' => $price,
            'currency' => 'eur',
            'quantity' => 1,
          ]],
          'success_url' => "http://zendcode.localhost/stripecheckout/success",
          'cancel_url' => 'http://zendcode.localhost/stripecheckout/cancel',
        ]);
        
        PaymentIntent::retrieve($stripe_create_response->payment_intent);  
        
        PaymentIntent::update($stripe_create_response->payment_intent,['metadata' => $response]);
        
        PaymentIntent::update($stripe_create_response->payment_intent,['description' => $description]);
        
        PaymentIntent::retrieve($stripe_create_response->payment_intent);
          
        $this->_helper->json($stripe_create_response);
    }
    
    public function shopstripeAction() {
        //Set the headers for the options request
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: *");
            header("Access-Control-Allow-Methods: *");
            header("HTTP/1.1 200 OK");
            exit;
        }
        //Header are set in the index.php file so I dont need to set again
        //Look at the angularAction above to see why and code for it
        
        $body = $this->getRequest()->getRawBody();
        
        $response = json_decode($body, true);
        
        $stripe_items = array();
        
        foreach($response['cart_items'] as $item) {
            $stripe_items[] = array('name' => $item['products_name'], 'description' => $item['products_name'], 
                                    'images' => array(), 'amount' => ($item['products_price'] * 100), 'quantity' => $item['products_quantity'],
                                    'currency' => 'eur');
        }
        
        $unique_id = md5(uniqid(rand(), true));
        
        $stripe_create_response = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [$stripe_items],
          'success_url' => "https://zendcode.herokuapp.com/stripecheckout/success/uid/{$unique_id}",
          'cancel_url' => 'https://zendcode.herokuapp.com/stripecheckout/cancel',
        ]);
          
        PaymentIntent::update($stripe_create_response->payment_intent,['metadata' => array('orders_id' => $response['order_id'],
                                                                                           'uniqueid' => $unique_id,
                                                                                           'type' => 'shopOrder')
                                                                      ]); 
        
        PaymentIntent::update($stripe_create_response->payment_intent,['description' => "Shop Order Holding ID {$response['order_id']}"]);
              
        $this->_helper->json($stripe_create_response);
        
    }
    
    private function migrateShopOrder($order_id) {
        $url = 'https://api-ics.herokuapp.com/api/order';
        
        // Initialize curl
        $curl = curl_init();
        
        // Url to submit to
        curl_setopt($curl, CURLOPT_URL, $url);
        
        // Return output instead of outputting it
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // I needed to set the headers to send over to dot net api
        $header = [
            'Content-Type: application/json',
        ];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        
        // We are doing a post request
        curl_setopt($curl, CURLOPT_POST, true);
        
        // Adding the post variables to the request
        // I needed to json_encode payload in order to send over to dot net api
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($order_id));
        
        // This is set to true so will display errors on the screen if errors occur
        // If set to true is will just show a simplified error
        // If set to false it will show the full error recieved from the server 
        // (Best to leave it as false. You will still see the error on screen if curl fails)
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        
        // Execute the request and fetch the response and check for errors below
        $response = curl_exec($curl);
        
        if ($response === false) {
            echo 'cURL Error: ' . curl_error($curl);
            echo "\n" . 'HTTP code: ' . curl_errno($curl);
        }
        
        // Close and free up the curl handle
        curl_close($curl);
        
        $result = json_decode($response, true);
        
        return $result;
    }
        
}

