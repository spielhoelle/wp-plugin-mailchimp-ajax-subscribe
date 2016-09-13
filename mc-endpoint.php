<?php

// load in mailchimp library
include('./MailChimp.php');

require_once(dirname(__FILE__).'../../../../wp-config.php');


// namespace defined in MailChimp.php
use \DrewM\MailChimp\MailChimp;

// connect to mailchimp
$MailChimp  = new MailChimp(get_option('api_key')); // put your API key here
$list       = get_option('list_id');                // put your list ID here

$email = $_POST['EMAIL']; // Get email address from form
$id = md5(strtolower($email)); // Encrypt the email address

if(get_option('opt_in') == 1){
    $opt_in = 'pending';
} else {
    $opt_in = 'subscribed';
}

// setup th merge fields
$mergeFields = array(
	// *** YOUR FIELDS GO HERE ***
	);

// remove empty merge fields
$mergeFields = array_filter($mergeFields);

$result = $MailChimp->put("lists/$list/members/$id", array(
								'email_address'     => $email,
								'status'            => $opt_in,
								'update_existing'   => true, // YES, update old subscribers!
						));



if($result !== "") {
    $currentData = get_option('tma_subscribers');

    $new_subscriber = array (
        'email' => $email,
        'date' => time()
    );

    if(!is_array($currentData)) {
        $currentData = array();
    }
    array_push($currentData, $new_subscriber);

    update_option( 'tma_subscribers', $currentData );

}

echo json_encode($result);
