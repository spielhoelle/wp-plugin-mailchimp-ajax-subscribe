<?php

require_once(dirname(__FILE__).'../../../../wp-config.php');


//fill in these values for with your own information
$api_key = get_option('api_key');
$datacenter = explode('-', $api_key)[1];
$list_id = get_option('list_id');
$email = $_POST['EMAIL'];


if(get_option('opt_in') === "1"){
    $status = 'pending';
} else {
    $status = 'subscribed';
}

$url = 'https://'.$datacenter.'.api.mailchimp.com/3.0/lists/'.$list_id.'/members/';
$username = 'apikey';
$password = $api_key;
$data = array("email_address" => $email,"status" => $status);
$data_string = json_encode($data);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "$username:$api_key");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);

$result=curl_exec ($ch);
curl_close ($ch);
echo $result;
