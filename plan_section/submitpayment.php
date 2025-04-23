
<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET,PUT,PATCH,DELETE');
header("Content-Type: application/json");
header("Accept: application/json");
header('Access-Control-Allow-Headers:Access-Control-Allow-Origin,Access-Control-Allow-Methods,Content-Type');

if(isset($_POST['action']) && $_POST['action'] === 'payOrder') {
    // Validate required fields
    if(empty($_POST['billing_name']) || empty($_POST['billing_mobile']) || 
       empty($_POST['billing_email']) || empty($_POST['payAmount'])) {
        echo json_encode(['res'=>'error', 'info'=>'Missing required fields']); 
        exit;
    }

    // Sanitize inputs
    $billing_name = htmlspecialchars($_POST['billing_name']);
    $billing_mobile = htmlspecialchars($_POST['billing_mobile']);
    $billing_email = filter_var($_POST['billing_email'], FILTER_SANITIZE_EMAIL);
    $payAmount = filter_var($_POST['payAmount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Validate email
    if(!filter_var($billing_email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['res'=>'error', 'info'=>'Invalid email format']);
        exit;
    }

    // Validate amount
    if(!is_numeric($payAmount) || $payAmount <= 0) {
        echo json_encode(['res'=>'error', 'info'=>'Invalid amount']);
        exit;
    }

    $note = "Payment of amount Rs. ".$payAmount;

    $postdata = array(
        "amount" => $payAmount*100,
        "currency" => "INR",
        "receipt" => $note,
        "notes" => array(
            "notes_key_1" => $note,
            "notes_key_2" => ""
        )
    );

$razorpay_mode='test';

$razorpay_test_key='your_razopray_test_key'; //Your Test Key
$razorpay_test_secret_key='your_razorpay_test_secret_key'; //Your Test Secret Key

$razorpay_live_key= 'Your_Live_Key';
$razorpay_live_secret_key='Your_Live_Secret_Key';

if($razorpay_mode=='test'){
    
    $razorpay_key=$razorpay_test_key;
    
$authAPIkey="Basic ".base64_encode($razorpay_test_key.":".$razorpay_test_secret_key);

}else{
    
	$authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
	$razorpay_key=$razorpay_live_key;

}

// Set transaction details
$order_id = uniqid(); 



$note="Payment of amount Rs. ".$payAmount;

$postdata=array(
"amount"=>$payAmount*100,
"currency"=> "INR",
"receipt"=> $note,
"notes" =>array(
	          "notes_key_1"=> $note,
	          "notes_key_2"=> ""
              )
);
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.razorpay.com/v1/orders',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($postdata),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Authorization: '.$authAPIkey
  ),
));

$response = curl_exec($curl);
if(curl_errno($curl)){
    echo json_encode(['res'=>'error', 'info'=>'Curl error: ' . curl_error($curl)]); 
    exit;
}
curl_close($curl);

$orderRes = json_decode($response);

if(isset($orderRes->id)){
    $rpay_order_id = $orderRes->id;
    $dataArr = array(
        'amount' => $payAmount,
        'description' => "Pay bill of Rs. ".$payAmount,
        'rpay_order_id' => $rpay_order_id,
        'name' => $billing_name,
        'email' => $billing_email,
        'mobile' => $billing_mobile
    );
    echo json_encode(['res'=>'success','order_number'=>$order_id,'userData'=>$dataArr,'razorpay_key'=>$razorpay_key]); 
    exit;
}else{
	echo json_encode(['res'=>'error','order_id'=>$order_id,'info'=>'Error with payment']); exit;
}
}else{
    echo json_encode(['res'=>'error']); exit;
}
       ?>   
