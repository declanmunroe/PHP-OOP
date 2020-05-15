<?php

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\StripeEvent;

class StripecheckoutController extends Zend_Controller_Action
{
    public function init()
    {
       // You dont need to set a try catch around stripe setApiKey
       // This is here for purly for setting api key
       // You need to wrap the try catch around the stripe service method that is been used
       // This way you can write your exception handling code
       // If stripe api key is incorect then the Session::create() method will throw an exception as
       // there is an incorect api key set so the Session::create() call does not know what stripe account to post to
       Stripe::setApiKey(STRIPE_SECRET_KEY);
    }
    
    public function indexAction()
    {  
        //die("Updated");
    }
    
    // For this action we dont need to add any exception handling
    // If stripe key is not set then Session::create() below will throw an exception because no api set
    // The session will not be created so a simple error catch on ajax post will be enough to catch the error 
    // and show user there was an error on posting. See view for this action for the error catch
    // If perhaps api key is correct but there is a piece of broken code of an unaccepital paramater in Session::create() then
    // an exception will be thrown and catch on client will pick up error
    // If exception is thrown then the methods bellow Session:create() will not run in relation to updating payment intent
    // if perhaps Session:create() does successfully create a Stripe session with a payment intent and the methods to retrieve and update
    // payment intent below fail then the subsequent values for (metadata,description) wont be added/updated
    // If this is the case and Stripe session is created but metadata fails to update then I will not be able to pull back important values for closing off
    // stripe transactions such as user_id, invoice_id etc on sunsequent methods that are called withing success action
    // But then i dont have to wory about this scenario because if Stripe session is created and meta data was not able to update I am going to be
    // performing a check in all the different triggers that happen in success action. All the data that I pull back from intent object retrieved from stripe
    // I am going to create a method that I will pass all values stored in an array and I will loop through value and check all values.
    // If any of the values are null or empty when they should contain a value I will return false so code will not try to close off invoice for example
    // and instead mark transaction as incomplete, send user to a transaction error page and notify IT team. Should still have a tranascation error page in
    // peopleserver for first version of stripe before new version of api
    // IF ANYTHING FAILS IN THIS ACTION THE USER WILL NOT BE BROUGHT TO STRIPE TO PAY. A SIMPLE CATCH ON CLIENT SIDE WILL BE ENOUGH TO LET USER KNOW SOMETHING WENT WRONG
    // IF A SESSION IS CREATED BUT AN UPDATE FAILS, USER WILL NOT BE BROUGHT TO STRIPE BUT DATA FOR THIS SESSION WILL EXIST IN STRIPE API BUT WE WONT CARE ABOUT THIS
    // IT WILL DISSAPEAR DOWN THE LINE OF ALL THE INCOMPLETE OR CANCELLED TRANSACTIONS. tHE ONLY DIFFERENCE IS IT MAY NOT HAVE A DESCRIPTION OR METADATA ASSOCIATED WITH SESSION
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
    
    public function shopStripeAction() {
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
            $stripe_items[] = array('name' => $item['products_name'], 
                                    'description' => $this->showDiscount($item['products_id'], $response['discount_unit_price'], $item['products_name']), 
                                    'images' => array(), 'amount' => $this->setPrice($item['products_id'], $response['discount_unit_price'], $item['products_price']), 
                                    'quantity' => $item['products_quantity'], 'currency' => 'eur');
        }
        
        $shipping = array('name' => 'Shipping', 'description' => 'Shipping', 
                                    'images' => array(), 'amount' => (25 * 100), 'quantity' => 1,
                                    'currency' => 'eur');
        
        $stripe_items[] = $shipping;
        
        $unique_id = md5(uniqid(rand(), true));
        
        $stripe_create_response = Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [$stripe_items],
          'success_url' => "https://zendcode.herokuapp.com/stripecheckout/success/uid/{$unique_id}",
          'cancel_url' => 'https://zendcode.herokuapp.com/stripecheckout/cancel',
        ]);
          
        $meta_data = array('orders_id' => $response['order_id'], 'uniqueid' => $unique_id, 'type' => 'shopOrder');
        
        if (sizeof($response['discount_unit_price']) > 0) {
            $meta_data['totatDiscountGiven'] = $response['discount_total'];
            $meta_data['couponCode'] = $response['coupon_code'];
        }
        
        PaymentIntent::update($stripe_create_response->payment_intent,['metadata' => $meta_data]); 
        
        PaymentIntent::update($stripe_create_response->payment_intent,['description' => "Shop Order Holding ID {$response['order_id']}"]);
              
        $this->_helper->json($stripe_create_response);
        
    }
    
    private function showDiscount($product_id, $discount_unit_price, $product_name) {
        
        $description = $product_name;
        
        if (sizeof($discount_unit_price) > 0) {
            foreach ($discount_unit_price as $discount) {
                if ($product_id == $discount['product_id']) {
                    $totalDiscountAmount = $discount['discount_amount'] * $discount['unit_quantity'];
                    $discountAmount = $discount['discount_amount'];
                    $description = "Discount of {$totalDiscountAmount} euro applied. €{$discountAmount} x {$discount['unit_quantity']} unit(s)";
                }
            }
        }
        
        return $description;
        
    }
    
    private function setPrice($product_id, $discount_unit_price, $product_price) {
        
        $price = $product_price;
        
        if (sizeof($discount_unit_price) > 0) {
            foreach ($discount_unit_price as $discount) {
                if ($product_id == $discount['product_id']) {
                    $price = $product_price - $discount['discount_amount'];
                }
            }
        }
        
        return $price * 100;
        
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

