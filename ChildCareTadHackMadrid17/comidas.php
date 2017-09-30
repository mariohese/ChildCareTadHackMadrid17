<?php
error_reporting(E_ALL);



/**
 * This function sends SMS messages from specified Apifonica phone number to specified mobile number
 * @param string $api_url URL to retrieve Apifonica API
 * @param string $accountSID Your Apifonica account identifier
 * @param string $password Password for your Apifonica account
 * @param string $from Apifonica number used as a message sender
 * @param string $to Recipient�s mobile phone number
 * @param string $message Message text
 * @return array
 */
function sendSMS($api_url, $accountSID, $password, $from, $to, $message) {

	$body = array(
		'from' => $from,
		'to' => $to,
		'text' => $message,
	);

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $api_url.'/v2/accounts/'.$accountSID.'/messages');
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, 1);
	// Set user and password
	curl_setopt ($curl, CURLOPT_USERPWD, $accountSID.':'.$password);
	// Do not check SSL
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	// Add header
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	// Set POST
	curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));

	$result = curl_exec($curl);

	if ($result) {
		$result = json_decode($result, true);
	} else {
		$result = array(
			'error_text' => curl_error($curl),
			'error_code' => curl_errno($curl),
			'status_code' => 600,
		);
	}

	return $result;
}

/**
 * This function checks the current status of the message
 * @param string $api_url URL to retrieve Apifonica API
 * @param string $accountSID Your Apifonica account identifier
 * @param string $password Password for your Apifonica account
 * @param string $smsuri SMS URL for check status
 * @return array
 */
function checkSMS($api_url, $accountSID, $password, $smsuri) {

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $api_url.$smsuri);
	curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, 1);
	// Set user and password
	curl_setopt ($curl, CURLOPT_USERPWD, $accountSID.':'.$password);
	// Do not check SSL
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	// Add header
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

	$result = curl_exec($curl);

	if ($result) {
		$result = json_decode($result, true);
	} else {
		$result = array(
			'error_text' => curl_error($curl),
			'error_code' => curl_errno($curl),
			'status_code' => 600,
		);
	}

	return $result;
}


/**
 * Set default variables
 */

// Specify Apifonica API URL
$api_url = 'https://api.apifonica.com';
// Specify your Apifonica account SID
$accountSID = 'acc9f244f52-58a6-329c-9b99-9443826e48b4';
// Specify your Apifonica account password
$password = 'aute86cdb8a-ab5b-39ec-a9d7-64baf837877b';
// Specify the message sender's number (this number must belong to the Apifonica account you have specified)
$from = '34668692527';
// Specify the mobile number to receive SMS messages sent from the web form
$to = '34696782580';

/**
 * set variables from POST
 */
$action = isset($_POST['action'])?$_POST['action']:'default';
$email =  isset($_POST['email'])?trim($_POST['email']):'';
$name =  isset($_POST['name'])?trim($_POST['name']):'';
$message =  isset($_POST['message'])?trim($_POST['message']):'';
$smsuri =  isset($_POST['smsuri'])?trim($_POST['smsuri']):'';

$text = 'Name: '.$name.'; '.'E-mail: '.$email.'; '.'Text: '.$message;

$result = false;

/**
 * Sending SMS
 */
if ($action == 'sendsms') {

	$result = sendSMS($api_url, $accountSID, $password, $from, $to, $text);

	if ($result['status_code'] > 299) {
		// In case SMS send action is failed, display an error message and web contact form
		$action = 'view';
	}

	/**
	 * Check SMS status
	 */
} else if ($action == 'checksms') {
	$result = checkSMS($api_url, $accountSID, $password, $smsuri);
	//prn($result);
	$check_text = 'Unknown status';
	if ($result && isset($result['status'])) {
		switch ($result['status']) {
			case 'queued':
				$check_text = 'Message is on its way!';
				break;
			case 'sent':
				$check_text = 'Message is successfully sent.';
				break;
			case 'delivered':
				$check_text = 'Message is delivered to recipient\'s phone. ';
				break;
			case 'failed':
				$check_text = 'Message delivery failed.';
				break;
			default:
		}
	}
}
/**
 * Default view form
 */

?>

	<!DOCTYPE html>
	<html lang="en">

	<head>

		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">

		<title>Child Care</title>

		<!-- Bootstrap Core CSS -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

		<!-- Font Awesome CSS -->
		<link href="css/font-awesome.min.css" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="css/animate.css" rel="stylesheet">

		<!-- Custom CSS -->
		<link href="css/style.css" rel="stylesheet">

		<!-- Custom Fonts -->
		<link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>


		<!-- Template js -->
		<script src="js/jquery-2.1.1.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="js/jquery.appear.js"></script>

		<script src="js/jqBootstrapValidation.js"></script>
		<script src="js/modernizr.custom.js"></script>
		<script src="js/script.js"></script>

		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->

	</head>
	<body>

	<!-- Start Logo Section -->
	<section id="logo-section" class="text-center">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="logo text-center">
						<img src="images/ChildCare.png" class="col-lg-4 col-lg-offset-4">
						<span class="col-md-12">Enjoy your life, don´t care about it</span>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Logo Section -->
	<div class="col-lg-5"></div>
	<div class="col-lg-5">
		<p>
		<form method="post" action="" class="col-md-6">
			<?php if ($result && isset($result['status_code']) && $result['status_code'] > 299) { ?>
				<div class="bg-danger form-group">
					<h4>An error occured while sending the request :(</h4>
					<ul><li><?php echo $result['error_text']; ?></li></ul>
				</div>
			<?php } ?>
			<?php if ($action == 'sendsms') { ?>
				<input type="hidden" name="action" value="checksms"/>
				<input type="hidden" name="smsuri" value="<?php echo $result['uri']; ?>"/>
				<div class="bg-success form-group">
					<h4>Message is on its way!</h4>
				</div>
				<div>
					<button type="submit" class="btn btn-info">Update Delivery Status</button>
					&nbsp;
					<a href="" class="btn btn-default">Send Another Message</a>
				</div>
			<?php } else if ($action == 'checksms') { ?>
				<input type="hidden" name="action" value="checksms"/>
				<input type="hidden" name="smsuri" value="<?php echo $smsuri; ?>"/>
				<div class="bg-success form-group">
					<h4><?php echo $check_text; ?></h4>
				</div>
				<div>
					<button type="submit" class="btn btn-info">Update Delivery Status</button>
					&nbsp;
					<a href="" class="btn btn-default">Send Another Message</a>
				</div>
			<?php } else { ?>
				<input type="hidden" name="action" value="sendsms"/>
				<div class="section-title text-center">
					<h3>Eating</h3>
				</div>

				<div class="form-group">
					<label for="InputName">Your name</label>
					<input type="text" class="form-control" id="InputName" name="name" value="<?php echo $name; ?>" required>
				</div>
				<div class="form-group">
					<label for="InputEmail">Your email</label>
					<input type="email" class="form-control" id="InputEmail" name="email" value="<?php echo $email; ?>" required>
				</div>
				<div class="form-group">
					<label for="InputMessage">Please writewhen and how many baby has eaten</label>
					<textarea class="form-control" id="InputMessage" name="message" required><?php echo $message ?></textarea>
				</div>
				<div class="col-lg-6">

					<a href="niñeras.html" class="btn btn-primary">Back</a>

				</div>
				<div class="col-lg-6">

					<button type="submit" class="btn btn-default">Send </button>

				</div>			<?php } ?>
		</form>
		</p>
	</div>
	<div class="col-lg-1"></div>

	</body>
	</html>
<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 30/09/2017
 * Time: 11:42
 */