<?php
session_start();
require_once('../../../../../wp-load.php');
require_once('init.php');
$erphpdown_stripe_sk  = get_option('erphpdown_stripe_sk');

if($_POST['stripeToken'] && isset($_SESSION['stripe_trade_no']) && isset($_SESSION['stripe_price']) && $erphpdown_stripe_sk){
    //var_dump($_POST);
    // Set your secret key: remember to change this to your live secret key in production
    // See your keys here: https://dashboard.stripe.com/account/apikeys
    \Stripe\Stripe::setApiKey($erphpdown_stripe_sk);
 
    // Token is created using Stripe.js or Checkout!
    // Get the payment token submitted by the form:
    $token = $_POST['stripeToken'];
    $email = $_POST['stripeEmail'];
 
    // Charge the user's card:
    $charge = \Stripe\Charge::create(array(
      "amount" => $_SESSION['stripe_price'],
      "currency" => "cny",
      "description" => "Stripe charge",
      'metadata' => ['order_id' => $_SESSION['stripe_trade_no']],
      "source" => $token,
    ));
 
    $result =json_decode(str_replace('Stripe\Charge JSON: ','',$charge),true);
    //var_dump($result);exit;
    if($result['status'] == 'succeeded' && $email == $result['source']['name'] && $_SESSION['stripe_price'] == $result['amount']){
        $out_trade_no = $result['metadata']['order_id'];
        $total_fee = $result['amount']*0.01;

        if(strstr($out_trade_no,'MD') || strstr($out_trade_no,'FK')){
            epd_set_wppay_success($out_trade_no,$total_fee,'stripe');
        }else{
            epd_set_order_success($out_trade_no,$total_fee,'stripe');
        }
        
        if(isset($_COOKIE['erphpdown_return']) && $_COOKIE['erphpdown_return']){
            wp_redirect($_COOKIE['erphpdown_return']);
        }elseif(get_option('erphp_url_front_success')){
            wp_redirect(get_option('erphp_url_front_success'));
        }else{
            echo 'succeed';exit;
        }
    }
}
