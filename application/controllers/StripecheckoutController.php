<?php

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\StripeEvent;

class StripecheckoutController extends Zend_Controller_Action
{
    private function setStripeApiKey($source)
    {
        $stripeKey = ($source == 'skills') ? Application_Service_Config::getStripePrivateKey('skills') : (($source == 'member') ? Application_Service_Config::getStripePrivateKey('member') : '');
        
       // You dont need to set a try catch around stripe setApiKey
       // This is here for purly for setting api key
       // You need to wrap the try catch around the stripe service method that is been used
       // This way you can write your exception handling code
       // If stripe api key is incorect then the Session::create() method will throw an exception as
       // there is an incorect api key set so the Session::create() call does not know what stripe account to post to
        Stripe::setApiKey($stripeKey);
    }
    
    // Update subscription so member wont be charged automaticly, they will be sent an invoice out to pay for their next subscription cycle
    // billing property value is now called collection_method in new version of api and in api docs
    // https://stripe.com/docs/api/subscriptions/update
    public function updateSubscriptionAction()
    {
        $this->setStripeApiKey('member');
        
        $subscription = \Stripe\Subscription::update('sub_HHfuV1eGeFqbTo', ['billing' => 'send_invoice', 'days_until_due' => 7]);
        
        $this->_helper->json($subscription);
    }
    
    public function indexAction()
    {
        
    }
    
    // For this action we dont need to add any exception handling
    // If stripe key is not set then Session::create() below will throw an exception because no api set
    // The session will not be created and any subsequent code below will not run so a simple error catch on ajax post will be enough to catch the error 
    // and show user there was an error on posting. See view for this action for the error catch
    // If perhaps Session:create() does successfully create a Stripe session and the methods to retrieve and update
    // payment intent below fail then an exception will be thrown and client will catch error. The session will exist on stripe account but it will never be completed
    // as user was not brought to stripe checkout page (They were presented with a client error)
    public function chargeandcreateAction()
    {
        $formData = $this->getRequest()->getPost();
        
        $this->setStripeApiKey($formData['stripekey']);
        
        // To use Session you must have the latest version of Stripe at least 6 and above
        // Also you must have an Account name set up in https://dashboard.stripe.com/account        Declan Ics Dev was the account name i gave
        $stripe_create_response = Session::create([
          'payment_method_types' => ['card'],
          #'customer' => 'cus_HEAqNFYemruVQw', #Associate customer with payment or remove if you want to create a new customer
          'line_items' => [[
            'name' => $formData['description'],
            'description' => $formData['description'],
            'images' => [],
            'amount' => $formData['price'],
            'currency' => 'eur',
            'quantity' => 1,
          ]],
          'success_url' => "https://zendcode.herokuapp.com/stripecheckout/success/uid/{$formData['uniqueid']}/source/{$formData['stripekey']}",
          'cancel_url' => 'https://zendcode.herokuapp.com/stripecheckout/cancel',
        ]);
        
        // Cant be used for subscription. Subscription does not create a payment intent when creating session
        PaymentIntent::retrieve($stripe_create_response->payment_intent);  // Retrieves the current state of my payment intent // Right now meta data is empty
        
        // Add $formData to metadata so I can access in success after webhook is completed
        // Webhook will always be executed before user is redirected to success url if my webhook is 
        // checking event checkout.session.completed set out in stripe webhook in stripe account
         
        // Cant be used for subscription. Subscription does not create a payment intent when creating session
        PaymentIntent::update($stripe_create_response->payment_intent,['metadata' => $formData]); // Update current state to accomodate meta_data
                                                                                                  // Metadata can only contain one array, it cannot contain array of arrays, 
                                                                                                  // throws stripe api error if so
        
        // Cant be used for subscription. Subscription does not create a payment intent when creating session
        PaymentIntent::update($stripe_create_response->payment_intent,['description' => $formData['description']]);
        
        // Cant be used for subscription. Subscription does not create a payment intent when creating session
        PaymentIntent::retrieve($stripe_create_response->payment_intent); // Retrieve the updated payment intent with the meta data added to my stripe object response
        
        $this->_helper->json($stripe_create_response);
    }
    
    // Subscription
    // https://stripe.com/docs/billing/subscriptions/overview
    // https://stripe.com/docs/payments/checkout
    // Add metadata as below
    // Subscriptions do not create payment intents during session create so I cant perform an update on payment intent to add meta data
    // On webhook I will have to pull back metadata by a different method in link below
    // https://stripe.com/docs/api/events/retrieve
    
    // EVERYTHING YOU NEED TO KNOW ABOUT SUBSCRIPTION/INVOICE LIFECYCLE HOOKS
    // https://stripe.com/docs/billing/subscriptions/overview
    public function subscriptionAction()
    {
        $formData = $this->getRequest()->getPost();
        
        $this->setStripeApiKey($formData['stripekey']);
        
        $stripe_create_response = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
              'price' => $formData['plan'],
              'quantity' => 1,
              ]],
            'mode' => 'subscription',
            'metadata' => $formData,
            'success_url' => "https://zendcode.herokuapp.com/stripecheckout/success/uid/{$formData['uniqueid']}/source/{$formData['stripekey']}",
            'cancel_url' => 'https://zendcode.herokuapp.com/stripecheckout/cancel',
        ]);
        
        $this->_helper->json($stripe_create_response);
    }
    
    public function successAction()
    {
        $uid = $this->getParam('uid', 0);
        $source = $this->getParam('source', 'error');
        
        $this->setStripeApiKey($source);
        
        $db = new Zend_Db_Table('stripe_transactions');
        $row = $db->fetchRow("unique_id = '$uid'");
        
        if ($row) {
            
            $payment_mode = $row['mode'];
            
            if ($payment_mode == 'payment') {
                
                try {
                    $data = PaymentIntent::retrieve($row['payment_intent']);
                } catch (Exception $ex) {
                    die("Payment intent was not found");
                }
                
                $type = $data['charges']['data'][0]['metadata']['type'];
                
            } elseif ($payment_mode == 'subscription') {
                
                try {
                    $data = \Stripe\Event::retrieve($row['payment_intent']);
                } catch (Exception $ex) {
                    die("Event id was not found");
                }
                
                $type = $data['data']['object']['metadata']['type'];
                
            }

            // If for some reason $type variable value above can not be found in api metadata they $type will have a value of NULL 
            // and default of switch statement will be triggered
            switch ($type) {
                case "eventregister":
                    $this->eventregister($data);
                    break;

                case "icdltoolkit":
                    $this->icdltoolkit($data);
                    break;

                case "shopOrder":
                    $this->shopOrder($data);
                    break;
                
                case "membersubscription":
                    $this->membersubscription($data);
                    break;

                default:
                    die("Error on success action. Switch default hit. Type is NULL or undefined");
            }
                
            
            
        } else {
            die("Stripe transaction not found");
        }
       
    }
    
    public function cancelAction()
    {
        die("On cancel page");
    }
    
    public function processstripepaymentAction() {
        // https://stripe.com/docs/payments/checkout/fulfillment   //checkout.session.completed webhook gets executed before redirect to success url
        
        $source = $this->getParam('source', 'error');
        
        $this->setStripeApiKey($source);
        
        $response = file_get_contents('php://input');

        $response_array = json_decode($response, true);
        
        $payment_mode = $response_array['data']['object']['mode'];
        
        $db = new Zend_Db_Table('stripe_transactions');
        
        if ($payment_mode == 'payment') {
            
            try {
                $intent = PaymentIntent::retrieve($response_array['data']['object']['payment_intent']);
            
                $db->insert(array('unique_id' => $intent['charges']['data'][0]['metadata']['uniqueid'], 'payment_intent' => $intent['id'], 'type' => $intent['charges']['data'][0]['metadata']['type'], 'mode' => 'payment', 'created_dt' => new Zend_Db_Expr('NOW()')));
                
                $this->_helper->json("Payment intent transaction recorded");
            } catch (Exception $ex) {
                $db->insert(array('unique_id' => null, 'payment_intent' => 'WEBHOOK-ERROR', 'type' => 'WEBHOOK-ERROR', 'mode' => 'payment', 'created_dt' => new Zend_Db_Expr('NOW()')));
                
                $this->_helper->json($response_array);
            }
            
        } elseif ($payment_mode == 'subscription') {
            
            try {
                $subscription = \Stripe\Event::retrieve($response_array['id']);
            
                $db->insert(array('unique_id' => $subscription['data']['object']['metadata']['uniqueid'], 'payment_intent' => $subscription['id'], 'type' => $subscription['data']['object']['metadata']['type'], 'mode' => 'subscription', 'created_dt' => new Zend_Db_Expr('NOW()')));
                
                $this->_helper->json("Subscription transaction recorded");
            } catch (Exception $ex) {
                $db->insert(array('unique_id' => 'WEBHOOK-ERROR', 'payment_intent' => 'WEBHOOK-ERROR', 'type' => 'WEBHOOK-ERROR', 'mode' => 'subscription', 'created_dt' => new Zend_Db_Expr('NOW()')));
                
                $this->_helper->json($response_array);
            }
            
        } else {
            $db->insert(array('unique_id' => 'WEBHOOK-ERROR', 'payment_intent' => 'WEBHOOK-ERROR', 'type' => 'WEBHOOK-ERROR', 'mode' => 'UNKNOWN', 'created_dt' => new Zend_Db_Expr('NOW()')));
                
            $this->_helper->json($response_array);
        }
        
    }
    
    // Check to see if values taken from api response exist and valid to close off transaction
    // If not bring user to error page
    private function areValidValues($values)
    {
        foreach ($values as $val) {
            if (empty($val)) {
                $this->redirect('/stripecheckout/cancel');
            }
        }
    }
    
    private function eventregister($data)
    {
        $success_url = $data['charges']['data'][0]['metadata']['sucessurl'];
        $charge_id = $data['charges']['data'][0]['id'];
        // This value will not exist. this is here to check valid method below
        // If I pass $invoice_id to valid method below empty check in method will be triggered
        // $invoice_id = $data['charges']['data'][0]['metadata']['invoice_id'];
        
        $this->areValidValues(array($charge_id,$success_url));
        
        header("Location: {$data['charges']['data'][0]['metadata']['sucessurl']}?chargeId={$data['charges']['data'][0]['id']}");
        die();
    }
    
    private function icdltoolkit($data)
    {
        header("Location: {$data['charges']['data'][0]['metadata']['sucessurl']}?chargeId={$data['charges']['data'][0]['id']}");
        die();
    }
    
    private function shopOrder($data)
    {
        $order_holding_id = $data['charges']['data'][0]['metadata']['orders_id'];

        $result = $this->migrateShopOrder(array('orders_id' => (int) $order_holding_id));

        header("Location: https://shop-ics.herokuapp.com/shop/cart/checkout/success/{$result['orders_id']}");
        die();
    }
    
    private function membersubscription($data)
    {
        if ($data['data']['object']['metadata']['billing'] == 'manual') {
            \Stripe\Subscription::update($data['data']['object']['subscription'], ['billing' => 'send_invoice', 'days_until_due' => 7]);
        }

        $subscription = \Stripe\Subscription::retrieve($data['data']['object']['subscription']);

        $this->_helper->json($subscription);
    }
    
    ##############################################################################################################
    #
    # ALL THE CODE BELOW IS EXPERIMENT CODE SO STRIPE AND VARIOUS THIRDPART ANGULAR APPLICATIONS
    # IT WONT WORK AS I HAVENT INCLUDED THE NEW METHOD FOR SETTING STRIPE API KEY THAT I HAVE STARTED TO USE ABOVE
    # 
    ##############################################################################################################
    
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

