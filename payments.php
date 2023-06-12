<?php
    require_once 'vendor/autoload.php';
$paynow = new Paynow\Payments\Paynow(
    '12569',
    '12c032be-541e-44eb-9f8b-75d92c941ad2',
    'http://example.com/gateways/paynow/update',

    // The return url can be set at later stages. You might want to do this if you want to pass data to the return url (like the reference of the transaction)
    'http://example.com/return?gateway=paynow'
);

# $paynow->setResultUrl('');
# $paynow->setReturnUrl('');

$payment = $paynow->createPayment('Invoice 35', 'godknowskaunda@gmail.com');

$payment->add('Sadza and Beans', 1.25);

$response = $paynow->sendMobile($payment, '0782495137', 'ecocash');


if($response->success()) {
    // Or if you prefer more control, get the link to redirect the user to, then use it as you see fit
    // Get the poll url (used to check the status of a transaction). You might want to save this in your DB
    $pollUrl = $response->pollUrl();

    // Get the instructions
    $instrutions = $response->instructions();

    // Check the status of the transaction
    $status = $paynow->pollTransaction($pollUrl);

	if($status->paid()) {
	    // Yay! Transaction was paid for
	    echo "Paid";
	} else {
	    print_r($instrutions);
	}

}else{
	print_r($response);
}