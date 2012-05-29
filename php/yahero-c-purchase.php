<?php

class Purchase extends Front_Controller {


	function Purchase() {
		parent::__construct();
//		echo FCPATH;
		$host_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
		$host_url .= "://".$_SERVER['HTTP_HOST'];
		$this->data['form_action_base'] = $host_url . str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
// TO ENABLE SSL COMMENT OUT LINE BELOW AND UNCOMMENT LINE BELOW THAT
//		$this->data['form_action_base'] = base_url();
		if(strpos($_SERVER['REQUEST_URI'], "https") === FALSE) ssl_reload();
	}

	function index()
	{
	    $this->load->model('PurchaseModel');
		$this->data['default_form_values'] = $this->PurchaseModel->getDefaultFormData();
		/* $this->render(); */
		redirect('purchase/step1');
	}

	function upgrade() {
		$this->render();
	}


	function step1() {
	    $this->load->model('PurchaseModel');
	    $this->ajax();
	    $this->data['step'] = 1;
	    $this->data['title'] = "Subscription";
	    $this->render('yahero');
	}

	function step2() { 
		$this->ajax(); 
		$this->data['step'] = 2; 
		$this->load->model('HeroModel'); 
	    $this->data['title'] = "Subscription";
		$this->data['children']=$this->HeroModel->getChildren($this->session->userdata('heroid'));
 		$this->render('yahero');
	}

	function step3() {
	//				pr($this->session->userdata('heroid'));
	    if($_POST['direction'] == "Back") redirect("/purchase/step1");
	    $this->ajax();
	    $this->data['title'] = "Subscription";
	    $this->data['step'] = 3;
	    $this->render('yahero');
  	}
	
	function confirm() {
	    if($_POST['direction'] == "Back") redirect("/purchase/step2");
	   	$this->validate();
	    $this->data['title'] = "Subscription";
			$this->data['validation_errors'] = validation_errors();
	    $this->ajax();
	    $this->data['step'] = 'confirm';
	    $this->render('yahero');
	}
	
	function validate() {
		$this->load->library('form_validation');
		$this->form_validation->set_rules('ordName', 'Full Name', 'trim|required|alpha_numeric_space|max_length[64]');
		$this->form_validation->set_rules('ordEmailAddress', 'Email', 'trim|required|max_length[64]|valid_email');
		$this->form_validation->set_rules('confirmEmail', 'Confirm Email', 'trim|required|max_length[64]|valid_email|matches[ordEmailAddress]');
		$this->form_validation->set_rules('trnCardOwner', 'Card Owner', 'trim|required|alpha_numeric_space|max_length[64]');
		$this->form_validation->set_rules('trnCardNumber', 'Card Number', 'trim|required|numeric|max_length[16]');
		$this->form_validation->set_rules('trnCardCvd', 'Security Code', 'trim|required|numeric|max_length[4]');
		$this->form_validation->set_rules('ordAddress1', 'Address', 'trim|required|alpha_numeric_space|max_length[64]');
		$this->form_validation->set_rules('ordAddress2', 'Address Line 2', 'alpha_numeric_space|max_length[64]');
		$this->form_validation->set_rules('ordCity', 'City', 'trim|required|alpha_numeric_space|max_length[32]');
		$this->form_validation->set_rules('ordPostalCode', 'Postal Code', 'trim|required|alpha_numeric|max_length[16]');
		$this->form_validation->set_rules('ordPhoneNumber', 'Phone Number', 'trim|required|xss_clean|max_length[32]');
		$this->form_validation->set_rules('termsCheck', 'Terms and Conditions', 'required');
		return $this->form_validation->run();
	}
	
	function ajax() {
		$this->load->model('PurchaseModel');

		if(!$this->session->userdata('country')) $this->session->set_userdata('country', $this->PurchaseModel->getCountryByIP());
    				
		if ($_POST) {
			$s = $this->session;
			if($s->userdata('form_data')) $s->set_userdata('form_data', array_merge($s->userdata('form_data'), $_POST)); 
			else $s->set_userdata('form_data', $_POST);

			$form_data = $s->userdata('form_data');

			if($form_data['redemptionCode']) {
//			pr($form_data);
				$this->data['msg'] = "";
				if($form_data['product'] != 1) $this->data['msg'] .= "This Coupon Code can only be applied toward a monthly subscription for one child. <a href=\"".base_url()."purchase/step1?coupon=remove\">Remove coupon</a><br />";
				if($this->PurchaseModel->validate_coupon_code($form_data['redemptionCode'])) {
					$form_data['product'] = 1;
					$form_data['coupon_credit'] = $this->PurchaseModel->get_coupon_code_value($form_data['redemptionCode']);
				} else {
					$this->data['msg'] .= $this->PurchaseModel->get_invalid_coupon_message($form_data['redemptionCode'])."<br />";
					$form_data['redemptionCode'] = NULL;
				}
				$s->set_userdata('form_data', $form_data);
			} else {
					$form_data['coupon_credit'] = 0;			
			}
			
			$this->PurchaseModel->populateProductData();
			
			$this->PurchaseModel->applyTax($form_data['price'], $form_data['ordProvince']);
			
			$form_data = $s->userdata('form_data');
			
			switch($_POST['direction']) {
				case "Next" : 
					$this->data['current_page'] = $form_data['page'] + 1;
					break;
				case "Back" : 
					$this->data['current_page'] = $form_data['page'] - 1;
					break;
				case "Submit" : 
					$this->data['current_page'] = 'confirm';
					break;
				case "Confirm" :
					break;
				default : 
					if(isset($_POST['go_to_page'])) $this->data['current_page'] = $_POST['go_to_page'];
					else $this->data['current_page'] = 1;
					break;
			}
			
		} else $this->data['current_page'] = 1;

		$this->data['default_form_values'] = $this->PurchaseModel->getDefaultFormData();
		$this->data['form_data'] = $this->session->userdata('form_data');
		$this->data['userdata'] = $this->session->all_userdata();
		unset($this->data['userdata']['form_data']);

//		pr($this->data['form_data']);

		return;
//		$this->render();
	}



	function process()
	{
	    $this->load->model('PurchaseModel');
	    $this->load->model('GiftCodeModel');
		$this->load->library('form_submission');
		
		$form_data = $this->session->userdata('form_data');

//		if($form_data['redemptionCode'] AND $this->PurchaseModel->validate_coupon_code($form_data['redemptionCode'])) {
    	if($form_data['redemptionCode'] AND $this->PurchaseModel->validate_coupon_code($form_data['redemptionCode'])) {
    		$form_data['rbCharge'] = "0";
    		$delay_days = $this->PurchaseModel->get_coupon_code_value($form_data['redemptionCode']);
	   		$form_data['rbFirstBilling'] = date('mdY', mktime()+$delay_days*60*60*24);
		} else $extra_credit = 0;

/*		KILLSWITCH ENGAGE AHHHHHHHH!!!!!

		pr("THIS IS WHERE WE WOULD PROCESS THE PAYMENT - SYSTEM KILL");
		return;
*/		
	    $this->data['beanstream_response_data'] = $this->PurchaseModel->beanstream_curl('create',$form_data['ordCountry'],$form_data);

	    $brd = $this->data['beanstream_response_data'];

	    $brd['trnDate'] = date("Y-m-d H:i:s", strtotime($brd['trnDate']));
	    $this->form_submission->store($form_data, array('form_name'=>'Subscription Purchase', 'user_id'=>$this->session->userdata('heroid')));

	    $submission_id = $this->form_submission->getSubmissionId();
	    $brd['submission_id'] = $submission_id;

	    $this->db->insert('beanstream', $brd);
	    $beanstream_id = $this->db->insert_id();
//pr($brd);
//pr($form_data);
	    if($brd['trnApproved']) {
	    	
	    	$credit_data = array();
	    	
	    	$credit_data['credit'] = $form_data['credit'];
	    	$credit_data['beanstream_id'] = $beanstream_id;
	    	$credit_data['parent_id'] = $this->session->userdata("heroid");
				$credit_data['players'] = $form_data['players'];
			
	    	if($form_data['redemptionCode'] AND $this->PurchaseModel->validate_coupon_code($form_data['redemptionCode'])) {
					if($form_data['coupon_credit'] == 29) $extra_credit = 1;
					$this->PurchaseModel->redeem_code($form_data['redemptionCode'], $form_data['child_avatar_id']);
	    		//	Apply additional credit 
				} else $extra_credit = 0;
				
				$credit_data['credit'] += $extra_credit;
			
		    if(in_array( $form_data['product'], array(5, 6))) {
		    	$credit_data['activation_date'] = 0;
		    	
		    	$gift_code = $this->GiftCodeModel->generateGiftCode(substr($form_data['ordEmail'],0,strpos($form_data['ordEmail'],'@')));
		    	
		    	$credit_data['gift_code'] = $gift_code;
		    	
		    	$credit_data['parent_gift_email'] = $form_data['parent_email'];
		    } else 		$credit_data['activation_date'] = date("Y-m-d");
	    	
	    	$this->db->insert("credit", $credit_data);
	    	
	    	// SEND EMAIL
	    	$email = $this->session->userdata('ordEmailAddress');
	    	$this->sendRegEmail($email);
	    	
	    	if(in_array( $form_data['product'], array(5, 6))) {
	    		// SEND GIFT EMAIL WITH url = /giftcode/activate?gc=$gift_code ($gift_code set above)
	    		$recemail = $this->session->userdata('parent_email');
	    		$message = "Someone gifted you a year subscription to Yahero! Activate your account and start playing by clicking the link below.<br /><br />http://www.yahero.com/giftcode/activate?gc=".$gift_code;
	    		$this->sendReceiveGiftEmail($recemail, $email, $message);
	    		$this->sendGiftEmail($email);
	    		if(mail("Nick@papertower.com","New Gift Subscription Purchased", "code: $giftcode")) ;
//	    		$this->session->set_userdata('msg', "Thanks! Your gift subscription purchase has been approved and you should be receiving an email shortly!");
	    		redirect('purchase/giftsuccess', "refresh");
	    	} else {
				
	    		$this->load->model("GameUserModel");

					if(!is_array($form_data['child_avatar_id'])) {
						$form_data['child_avatar_id'] = array($form_data['child_avatar_id']);
					}
					
					foreach($form_data['child_avatar_id'] AS $avatar) $this->GameUserModel->setSubscriptionState($avatar); 

	    	}
			
			$this->session->unset_userdata('form_data');

			redirect('myaccount/mychildren?message=subscribed', "refresh");

	    } else {
	    	$this->session->set_userdata('msg', $brd['messageText']);
	    	redirect('/purchase/confirm', "refresh");
	   }
	}


	function test_activate(){
	    		$this->load->model("GameUserModel");
				$this->GameUserModel->setSubscriptionState("TacoTiger");
				if($this->GameUserModel->getSubscriptionState("TacoTiger")) echo "Subscribed";
				else echo "not";
	}


	function giftsuccess() {
		$this->data['title'] = "Success";
	    $this->render('yahero');
	}

	
	function modify(){
	   $this->load->model('PurchaseModel');
	}



	
	function onhold(){
	   $this->load->model('PurchaseModel');
	}



	
	function cancel()
	{
	    $this->load->model('PurchaseModel');
	    /* $this->PurchaseModel-> NEED ACCESS TO rbAccountID & Country FROM DATABASE */
//	    $post_data = array("country"=>"US", "rbAccountId"=>3977497);
	    $data = $this->PurchaseModel->beanstream_curl('cancel',"",$post_data, 'xml');
//	    pr($data);
		$data['response']['action'] = "cancel";
		$this->db->insert("beanstream_mods", $data['response']);
	    $this->render('yahero');
	}



	
	function reactivate(){
		$this->load->model('PurchaseModel');
		$data = $this->PurchaseModel->beanstream_curl('reactivate',"",$this->session->all_userdata(), 'xml');
		$data['response']['action'] = "reactivate";
		pr($data['response']);
		$this->db->insert("beanstream_mods", $data['response']);
	    $this->render('yahero');
	}




	function clear()
	{
		$this->session->unset_userdata('form_data');
		$this->session->unset_userdata('rbAccountId');
	}
	



	function fake()
	{
		$this->data['fakeMessage'] = "";
		if ($_POST) {
		  $this->session->set_userdata('ordCountry', $_POST['ordCountry']);
      $this->session->set_userdata('rbAccountId', $_POST['rbAccountId']);
      $this->data['fakeMessage'] = "Great success!";
		}
    $this->render();
	}



	function test_fs()
	{
		$this->load->library('form_submission');
		$this->form_submission->run_tests();
	}

	function test_rc()
	{
		$this->load->model('PurchaseModel');
		if($this->PurchaseModel->validate_coupon_code("674997MMC")) echo "valid"; else echo "invalid";
		echo "doesn't exist ".$this->PurchaseModel->get_invalid_coupon_message("4545454");
		echo "already redeemed ".$this->PurchaseModel->get_invalid_coupon_message("674997MMC");
		echo "coupon value ".$this->PurchaseModel->get_coupon_code_value("mlotest1");
//		$this->PurchaseModel->redeem_code("674997MMC","tacotiger");
	}

	function test_credit() {
	pr($this->session->all_userdata());
		$credit_data['credit'] = 1;
		$credit_data['beanstream_id'] = 69;
		$credit_data['parent_id'] = $this->session->userdata('heroid');
	
		$credit_data['credit'] += 1;
	
		$credit_data['activation_date'] = date("Y-m-d");
		
		$this->db->insert("credit", $credit_data);	
	}
	
	function buyasubscription()
	{
		redirect("/purchase/step1");

		/******************
		 IP Locator Starts
		******************/

		$tmp='';
		$ipaddress=$_SERVER['REMOTE_ADDR'];
		$acc="demo";
		$pass="demo";
		$query = "http://ip2location.com/ipcountry.asp?ip=" . $ipaddress . "&acc=" . $acc . "&pass=" . $pass;
		$url = parse_url($query);
		$host = $url["host"];
		$path = $url["path"] . "?" . $url["query"];
		$fp = fsockopen($host, 80, $errno, $errstr, 60) or die('Can not open connection to server.');
		if (!$fp) {
			echo "$errstr ($errno)<br>\n";
		} else {
			fputs($fp, "GET $path HTTP/1.0\r\nHost: " . $host . "\r\n\r\n");
			while (!feof($fp)) {
				$tmp .= fgets($fp, 128);
			}
			$array = explode("\r\n", $tmp);
			$country = $array[count($array)-1];
			fclose($fp);
		}

		//cho "Country is $country";
		$this->data['country']=$country;

		/******************
		 IP Locator Ends
		******************/

		$this->render();
	}

	function sendRegEmail($email)
	{
		require_once BASEPATH.'application/libraries/mailchimp/MCAPI.class.php';
		$apikey = '6b0c84f7815a0d5a693246522be89a11-us1';
		$username = 'dlofranco';
		$password = 'Dlofranco456';
		$listId = '616f7cb75c';
		//$campaignId = '2543f02ce3';
		$campaignId = '2ef5d5504e';

		//some email addresses used in the examples:
		$my_email = 'info@yahero.com';
		$boss_man_email = 'info@yahero.com';

		//just used in xml-rpc examples
		$apiUrl = 'http://api.mailchimp.com/1.2/';
		$api = new MCAPI($apikey);
		$this->load->helper(array('url'));
		$this->load->library('form_validation');

		$merge_var = array();
		$merge_vars = array('FNAME'=>'Parent');

		$result = $api->listUnsubscribe($listId, $email, false, false, false);
		$retval = $api->listSubscribe($listId, $email, $merge_vars, 'html', false);
		$result = $api->campaignSendNow($campaignId);

		/*if ($api->errorCode)
		{
			echo "Unable to load listSubscribe()!\n";
			echo "\tCode=".$api->errorCode."\n";
			echo "\tMsg=".$api->errorMessage."\n";
		}
		else
		{
			echo "Returned: ".$retval."\n";
		}*/
	}





	function sendGiftEmail($email)
	{
		require_once BASEPATH.'application/libraries/mailchimp/MCAPI.class.php';
		$apikey = '6b0c84f7815a0d5a693246522be89a11-us1';
		$username = 'dlofranco';
		$password = 'Dlofranco456';
		$listId = 'b40de79f1e';
		$campaignId = '11a1788034';

		//some email addresses used in the examples:
		$my_email = 'info@yahero.com';
		$boss_man_email = 'info@yahero.com';

		//just used in xml-rpc examples
		$apiUrl = 'http://api.mailchimp.com/1.2/';
		$api = new MCAPI($apikey);
		$this->load->helper(array('url'));
		$this->load->library('form_validation');

		$merge_var = array();
		$merge_vars = array('FNAME'=>'Parent');

		$result = $api->listUnsubscribe($listId, $email, false, false, false);
		$retval = $api->listSubscribe($listId, $email, $merge_vars, 'html', false);
		$result = $api->campaignSendNow($campaignId);

		/*if ($api->errorCode)
		{
			echo "Unable to load listSubscribe()!\n";
			echo "\tCode=".$api->errorCode."\n";
			echo "\tMsg=".$api->errorMessage."\n";
		}
		else
		{
			echo "Returned: ".$retval."\n";
		}*/
	}


	function sendReceiveGiftEmail($email, $firstname, $message)
	{
		require_once BASEPATH.'application/libraries/mailchimp/MCAPI.class.php';
		$apikey = '6b0c84f7815a0d5a693246522be89a11-us1';
		$username = 'dlofranco';
		$password = 'Dlofranco456';
		$listId = '341c86df71';
		$campaignId = '0e16bf3bcb';

		//some email addresses used in the examples:
		$my_email = 'info@yahero.com';
		$boss_man_email = 'info@yahero.com';

		//just used in xml-rpc examples
		$apiUrl = 'http://api.mailchimp.com/1.2/';
		$api = new MCAPI($apikey);
		$this->load->helper(array('url'));
		$this->load->library('form_validation');

		$merge_var = array();
		$merge_vars = array('FNAME'=>$firstname, 'MESSAGE'=>$message);

		$result = $api->listUnsubscribe($listId, $email, false, false, false);
		$retval = $api->listSubscribe($listId, $email, $merge_vars, 'html', false);
		$result = $api->campaignSendNow($campaignId);

		/*if ($api->errorCode)
		{
			echo "Unable to load listSubscribe()!\n";
			echo "\tCode=".$api->errorCode."\n";
			echo "\tMsg=".$api->errorMessage."\n";
		}
		else
		{
			echo "Returned: ".$retval."\n";
		}*/
	}



	function paynow()
	{
		$this->load->library('session');
		$this->render();
	}

	function successOLD($paypalstring)
	{

		if (!isset($paypalstring)) {
			redirect('/');
		}
		//$this->output->enable_profiler(TRUE);
		if ($paypalstring==$this->session->userdata('chkstring')) {
			$this->load->model('Subscription');
			$this->load->library('session');
			$email=$this->session->userdata('semail');
			$insertid=$this->session->userdata('insertid');
			$recemail=$this->session->userdata('receiversemail');
			$message=$this->session->userdata('message');

			//echo "$email $recemail $message sshshshs";
			$this->Subscription->updateSubscription($insertid);

			if (strlen($recemail) > 1) {
				$this->sendReceiveGiftEmail($recemail, $email, $message);
				$this->sendGiftEmail($email);
			}
			else {
				$this->sendRegEmail($email);
			}


			$this->session->unset_userdata(array('chkstring' => ''));
			$this->render();
		}
		else {
			echo 'Error, please contact the Administrator. (info@yahero.com)';
			$this->render();
			//redirect('subscribe/cancel');
		}
	}

	function ipn()
	{
		//$this->load->model('PaypalItemModel');
		$this->load->model('PaypalStatementModel');
		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		// post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Host: www.paypal.com:443\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

		$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);

		// assign posted variables to local variables
		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
		$payer_id = $_POST['payer_id'];
		$payment_date = $_POST['payment_date'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$payment_type = $_POST['payment_type'];
		$memo = $_POST['memo'];
		$quantity = $_POST['quantity'];
		$mc_gross = $_POST['mc_gross'];
		$mc_currency = $_POST['mc_currency'];
		$address_name = $_POST['address_name'];
		$address_street = nl2br($_POST['address_street)']);
		$address_city = $_POST['address_city'];
		$address_state = $_POST['address_state'];
		$address_zip = $_POST['address_zip'];
		$address_country = $_POST['address_country'];
		$address_status = $_POST['address_status'];
		$payer_business_name = $_POST['payer_business_name'];
		$payment_status = $_POST['payment_status'];
		$pending_reason = $_POST['pending_reason'];
		$reason_code = $_POST['reason_code'];
		$txn_type = $_POST['txn_type'];
		$subscr_id = $_POST['subscr_id'];
		$residence_country = $_POST['residence_country'];

		$toemail = 'tnpatil@gmail.com';


		$message = $item_name . " , " . $item_number . " , " . $payment_status . " , "
			. $payment_amount . " , " . $payment_currency . " , " . $txn_id
			. " , " . $receiver_email . " , " . $payer_email .  " , " . $payer_id .  " , "
			. $payment_date  . " ,	" . $first_name . " , " . $last_name . " ' "
			. $payment_type . " , " . $memo . " , " . $quantity . " , " . $mc_gross . " , "
			. $mc_currency . " , " . $address_name . " , " . $address_street . " , " . $address_city
			. " , " . $address_state . " , " . $address_zip . " , "
			. $address_country . " , " . $address_status . " , " . $payer_business_name
			. " , " . $payment_status . " , " . $pending_reason . " , " . $reason_code . " , " . $txn_type . " , " . $subscr_id . " , " . $residence_country;
		$subject = "TEST IPN RESPONSE 1";
		$this->sendMail($toemail, $message, $subject);

		//$res=$this->PaypalStatementModel->addStatement($payer_id, $payment_date, $txn_id, $first_name, $last_name, $payer_email, $payer_status, $payment_type, $memo, $item_name, $item_number, $quantity, $mc_gross, $mc_currency, $address_name, $address_street, $address_city, $address_state, $address_zip, $address_country, $address_status, $payer_business_name, $payment_status, $pending_reason, $reason_code, $txn_type);

		//set email variables
		$From_email = "From:";
		$Subject_line = " ";
		$email_msg = " ";

		if (!$fp) {
			// HTTP ERROR
		}
		else {
			fputs($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets($fp, 1024);
				if (strcmp($res, "VERIFIED") == 0) {
					// check the payment_status is Completed
					// check that txn_id has not been previously processed
					// check that receiver_email is your Primary PayPal email
					// check that payment_amount/payment_currency are correct
					// process payment
					$mail_From = $From_email;
					$mail_To = $payer_email;
					$mail_Subject = $Subject_line;
					$mail_Body = $email_msg;
					$payer_id = $payer_id . "test1";
					//mail($mail_To, $mail_Subject, $mail_Body, $mail_From);
					$toemail = 'tnpatil@gmail.com';
					$message = $_POST;
					$subject = "TEST IPN RESPONSE 2";
					$this->sendMail($toemail, $message, $subject);
					$res=$this->PaypalStatementModel->addStatement($payer_id, $payment_date, $txn_id, $first_name, $last_name, $payer_email, $payer_status, $payment_type, $memo, $item_name, $item_number, $quantity, $mc_gross, $mc_currency, $address_name, $address_street, $address_city, $address_state, $address_zip, $residence_country, $address_status, $payer_business_name, $payment_status, $pending_reason, $reason_code, $txn_type, $subscr_id);
				}
				else if (strcmp($res, "INVALID") == 0) {
						// log for manual investigation
						$mail_From = $From_email;
						$mail_To = $receiver_email;
						$mail_Subject = "Oops!";
						$mail_Text = "Something went wrong.";
						//mail($mail_To, $mail_Subject, $mail_Text, $mail_From);
					}
			}
			fclose($fp);
		}
		//$this->render();
	}

	function sendMail($email, $message, $subject)
	{
		require_once BASEPATH.'application/libraries/mailchimp/MCAPI.class.php';
		$apikey = '';
		$username = '';
		$password = '';
		$listId = '';
		$campaignId = ''; // info removed for sharing

		//some email addresses used in the examples:
		$my_email = 'info@yahero.com';
		$boss_man_email = 'info@yahero.com';

		//just used in xml-rpc examples
		$apiUrl = 'http://api.mailchimp.com/1.2/';
		$api = new MCAPI($apikey);
		$this->load->helper(array('url'));
		$this->load->library('form_validation');

		$merge_var = array();
		$merge_vars = array('FNAME'=>$email, 'MESSAGE'=>$message, 'SUBJECT'=>$subject);

		$result = $api->listUnsubscribe($listId, $email, false, false, false);
		$retval = $api->listSubscribe($listId, $email, $merge_vars, 'html', false);
		$result = $api->campaignSendNow($campaignId);
	}

	function check_email($email)
	{
		if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
			return false;
		}else {
			return true;
		}
	}
}