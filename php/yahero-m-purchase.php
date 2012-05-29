<?php

class PurchaseModel extends Model {

  function __construct(){
    parent::Model();
  }
  
  /*  ###  Beanstream API  ###  */

  function beanstream_curl($type = null, $country = null, $post_data = array(), $return_format = 'url'){
  
  	if(empty($country)) $country = $post_data['country'];
  	
    if($country == "CA"){
      $beanstream_merchant = array(
        'id' => '',
        'company' => '',
        'password' => ''
      );
    } else {
      $beanstream_merchant = array(
        'id' => '',
        'company' => '',
        'password' => ''
      );
    }
    
    // Common data for accessing recurring billing API
    if($type == ('modify' || 'onhold' || 'cancel' || 'reactivate')){
      $beanstream_api_url = "https://www.beanstream.com/scripts/recurring_billing.asp";
      $beanstream_type_fields = array(
        'merchantId' => $beanstream_merchant['id'],
        'serviceVersion' => '1.0',
        'operationType' => 'M',
        'passcode' => ''
      );
    }

    switch($type){
      case 'create':
	      $beanstream_api_url = "https://www.beanstream.com/scripts/process_transaction.asp";
	      $beanstream_type_fields = array(
	        'requestType' => 'BACKEND',
	        'merchant_id' => $beanstream_merchant['id'],
	        'errorPage' => '/configure/error/page',
	        'username' => '',
	        'password' => ''
	      );
	      break;

      case 'modify' : // Modify the account?!
	      break;

      case 'onhold' : $beanstream_type_fields['rbBillingState'] = 'O';// Places the account on hold. No future transactions will be process until re-activated.
	      break;

      case 'cancel' : $beanstream_type_fields['rbBillingState'] = 'C'; // Closes the account. No future transactions will be processed until re-activated.
	      break;

      case 'reactivate' : $beanstream_type_fields['rbBillingState'] = 'A'; // Re-activates an account that has been closed or placed on hold.
	      break;

      case 'report':
	      $beanstream_api_url = "https://www.beanstream.com/scripts/report_download.asp";
	      $beanstream_type_fields = array(
	        'loginCompany' => $beanstream_merchant['company'],
	        'loginUser' => 'admin',
	        'loginPass' => $beanstream_merchant['password'],
	        'rptNoFile' => '1',
	        'rptRange' => '1',
	        'rptIdStart' => $post_data['accountid'],
	        'rptIdEnd' => $post_data['accountid']
	      );
	      break;

      default : return false; break;
    }
  
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_URL,$beanstream_api_url);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query(array_merge($beanstream_type_fields,$post_data)));
    $beanstream_query = curl_exec($ch);
    curl_close($ch);
  
    switch($return_format){
    
      case 'url' :
        parse_str($beanstream_query,$result_array);
        return $result_array;
        break;
      
      case 'xml' :
        $this->load->library('xml');
        $this->xml->load($beanstream_query);
        return $this->xml->parse();
        break;
      
      default : return $beanstream_query;
        break;
    
    }
  }
  
  
  
  function getDefaultFormData() {

/*	
		$default_form_values = array(
			"currency"=>"USD",
			"trnRecurring" => 1,
			"rbBillingPeriod" => "M",
			"rbBillingIncrement" => 1,
			"ordName" => "Paper Tower",
			"ordEmailAddress" => "matt@papertower.com",
			"ordPhoneNumber" => 4235206288,
			"ordAddress1" => "625 Congdon Ave",
			"ordCity" => "Elgin",
			"ordProvince" => "IL",
			"ordPostalCode" => 60120,
			"ordCountry" => "US",
			"trnCardOwner" => "Paper Tower",
			"trnCardNumber" => 4030000010001234,
			"trnExpMonth" => 06,
			"trnExpYear" => 10,
			"trnCardCvd" => 123,
			"child_avatar_id" => array(true,true,true)
		);
*/
		$default_form_values = array(
			"currency"=>"USD",
			"trnRecurring" => 1,
			"rbBillingPeriod" => "M",
			"rbBillingIncrement" => 1,
			"child_avatar_id" => array(true,true,true)		
		);
		
		if ($this->session->userdata('form_data')) $default_form_values = array_merge($default_form_values, $this->session->userdata('form_data'));
	
		return $default_form_values;  
	}


	
	function validate_coupon_code($code = NULL) {
		if(is_null($code)) return false;
		else {
			$code = str_replace(" ","",$code);
			$sql = "SELECT TRUE FROM coupon_codes WHERE code=\"$code\" AND date_redeemed IS NULL LIMIT 1";
			$result = $this->db->query($sql);
			$row = $result->row();
//			pr($row);
			return $row->TRUE;
			// HERE IS WHERE THE MAGIC HAPPENS
			return false;
		}
	}

	function get_invalid_coupon_message($code) {
		if(empty($code)) return "No code provided";
			$code = str_replace(" ","",$code);
			$sql = "SELECT date_redeemed FROM coupon_codes WHERE code=\"$code\" LIMIT 1";
			$result = $this->db->query($sql);
			if($result->num_rows() == 0) return "The entered coupon code ($code) does not exist";
			else {
				$row = $result->row();
				if($row->date_redeemed) return "The entered coupon code ($code) has already been redeeemed on ".$row->date_redeemed;
			}
		return "Code is valid";
	}

	
	function get_coupon_code_value($code = NULL) {
			$code = str_replace(" ","",$code);
			$value = 29;
			$sql = "SELECT credits FROM coupon_codes WHERE code=\"$code\" LIMIT 1";
			$result = $this->db->query($sql);
			if($result->num_rows() == 0) return 0;
			$row = $result->row();
		return $row->credits;		
	}
	


	function redeem_code($code, $hero_id) {
		$code = str_replace(" ","",$code);
		$sql = "UPDATE coupon_codes SET redeemer_avatar=".$this->db->escape($hero_id).", date_redeemed=NOW() WHERE code=".$this->db->escape($code);
		$result = $this->db->query($sql);
		return;
	}
	


	function populateProductData() {
		$form_data = $this->session->userdata("form_data");
		if($form_data['country']) $country = $form_data['country'];
		else $country = $form_data['currency'];
		$product_data = $this->getProductData($form_data['product'], $country);
		$form_data = array_merge($form_data, $product_data);
		$this->session->set_userdata("form_data", $form_data);
		return;	
	}
	
	
	function getProductData($menu_id = NULL, $country = "US") {
		if(is_null($menu_id) OR is_null($country)) return false;
		
		if($country == "US") $currency = "USD";
		else if($country == "CA") $currency = "CAN";
		else $currency = $country;
		
		$sql = "SELECT price, credit, recurring, tax, players, name FROM subscriptiontype WHERE menu_id=$menu_id AND UPPER(currency) = \"".$currency."\"";
//		echo $sql;
		$query = $this->db->query($sql);
		$row = $query->row();
		$return = array();
		$return['credit'] = $row->credit;
//		$return['trnAmount'] = $row->price * ($row->tax + 100) / 100;
		$return['price'] = $row->price;
		$return['players'] = $row->players;
		$return['prod_name_1'] = $row->name;
		if($row->recurring) {
			$return["trnRecurring"] = 1;
			if($row->credit == 12) $return["rbBillingPeriod"] = "Y";
			else $return["rbBillingPeriod"] = "M";
			$return["rbBillingIncrement"] = 1;
		} else {
			$return["trnRecurring"] = 0;
		}

		return $return;
	}

	function applyTax($amount, $province) {
		$form_data = $this->session->userdata("form_data");

		if(is_null($province)) {
			$tax = 0;
		} else {
			$result = $this->db->query("SELECT rate FROM tax WHERE province = '$province' LIMIT 1");
			if($result->num_rows > 0) {
				$row = $result->row();
				$tax = round($amount * ($row->rate) / 100, 2);
			} else {
				$tax = 0;
			}
		}
		$form_data['taxAmount'] = $tax;
		$form_data['trnAmount'] = $amount + $tax;
		$this->session->set_userdata("form_data", $form_data);
		return;
	}

function getStateProvinceOptions($current) {
	$states = array(
		"<b>--Canadian Provinces--</b>"=>""
		,"Alberta"=>"AB"
		,"British Columbia"=>"BC"
		,"Manitoba"=>"MB"
		,"New Brunswick"=>"NB"
		,"Newfoundland and Labrador"=>"NL"
		,"Northwest Territories"=>"NT"
		,"Nova Scotia"=>"NS"
		,"Nunavut"=>"NU"
		,"Ontario"=>"ON"
		,"Prince Edward Island"=>"PE"
		,"Quebec"=>"QC"
		,"Saskatchewan"=>"SK"
		,"Yukon Territory"=>"YT"
		,"<b>--US States--</b>"=>""
		,"Alabama"=>"AL"
		,"Alaska"=>"AK"
		,"Arizona"=>"AZ"
		,"Arkansas"=>"AR"
		,"California"=>"CA"
		,"Colorado"=>"CO"
		,"Connecticut"=>"CT"
		,"Delaware"=>"DE"
		,"District of Columbia"=>"DC"
		,"Florida"=>"FL"
		,"Georgia"=>"GA"
		,"Hawaii"=>"HI"
		,"Idaho"=>"ID"
		,"Illinois"=>"IL"
		,"Indiana"=>"IN"
		,"Iowa"=>"IA"
		,"Kansas"=>"KS"
		,"Kentucky"=>"KY"
		,"Louisiana"=>"LA"
		,"Maine"=>"ME"
		,"Montana"=>"MT"
		,"Nebraska"=>"NE"
		,"Nevada"=>"NV"
		,"New Hampshire"=>"NH"
		,"New Jersey"=>"NJ"
		,"New Mexico"=>"NM"
		,"New York"=>"NY"
		,"North Carolina"=>"NC"
		,"North Dakota"=>"ND"
		,"Ohio"=>"OH"
		,"Oklahoma"=>"OK"
		,"Oregon"=>"OR"
		,"Maryland"=>"MD"
		,"Massachusetts"=>"MA"
		,"Michigan"=>"MI"
		,"Minnesota"=>"MN"
		,"Mississippi"=>"MS"
		,"Missouri"=>"MO"
		,"Pennsylvania"=>"PA"
		,"Rhode Island"=>"RI"
		,"South Carolina"=>"SC"
		,"South Dakota"=>"SD"
		,"Tennessee"=>"TN"
		,"Texas"=>"TX"
		,"Utah"=>"UT"
		,"Vermont"=>"VT"
		,"Virginia"=>"VA"
		,"Washington"=>"WA"
		,"West Virginia"=>"WV"
		,"Wisconsin"=>"WI"
		,"Wyoming"=>"WY"	
	);
	
	$str = "";
	foreach($states AS $display=>$code) {
		if($current == $code) $selected = "SELECTED";
		else $selected = "";
		$str .= "<option value='$code' $selected>$display</option>";
	}
	
	return $str;
	
}

  function getCountryByIP() {
    /******************
		 IP Locator Starts
		******************/

		$tmp='';
		$ipaddress=$_SERVER['REMOTE_ADDR'];
		$acc="";
		$pass="";
		$query = "http://ip2location.com/ipcountry.asp?ip=" . $ipaddress . "&acc=" . $acc . "&pass=" . $pass;
		$url = parse_url($query);
		$host = $url["host"];
		$path = $url["path"] . "?" . $url["query"];
		$fp = fsockopen ($host, 80, $errno, $errstr, 60) or die('Can not open connection to server.');
		if (!$fp) {
		echo "$errstr ($errno)<br>\n";
		} else {
		fputs ($fp, "GET $path HTTP/1.0\r\nHost: " . $host . "\r\n\r\n");
		while (!feof($fp)) {
		$tmp .= fgets ($fp, 128);
		}
		$array = explode("\r\n", $tmp);
		$country = $array[count($array)-1];
		fclose ($fp);
		}
		
		//cho "Country is $country";
		return $country;

		/******************
		 IP Locator Ends
		******************/
  }
	/*
    function insert_entry($stype,$heroid=0,$owner,$receiversEmail,$receiversname,$message,$totalslots,$usedslots)
    {

    	if($receiversEmail != null )
    	{
    		//echo 'Swapping';
    		$tt = $owner;
    		$owner = $receiversEmail;
    		$receiversEmail = $tt;
    	}

    	$q = $this->db->query("SELECT * FROM subscriptiontype s WHERE ID='$stype'");

			foreach ($q->result_array() as $r)
			{
			}

		$data = array(
				'StartDate' => date('Y-n-j H:i:s'),
				'SubscriptionType' => $stype,
				'PaypalReference' => '',
				'GiftSendersMail' => $receiversEmail,
				'Owner' => $owner,
				'GiftReceiversName' => $receiversname,
				'Message' => $message,
    			'HeroID' => $heroid,
				'TotalSlots' => $r['PlayersPerSubscription'],
				'UsedSlots' => $usedslots,
				'Status' => '0',
				'CreatedAt' => date('Y-n-j H:i:s'),
				'SubscriptionName' => $r['Name'],
				'Price' => $r['Price'],
				'Currency' => $r['Currency'],
				'Recurring' => $r['Recurring'],
				'Tax' => $r['Tax']
	           );

		$this->db->insert('subscription', $data);
		return $this->db->insert_id();
    }

	function getSubscription($email)
    {
    	$query = $this->db->query("SELECT * FROM subscription s
									WHERE Owner = '$email'
									ORDER BY StartDate DESC;");

		if ($query->num_rows() > 0)
			{
				$res = $query->first_row('array');
				return $res;
			}
		return null;
    }

	function getSubsInfo($id)
    {
    	$query = $this->db->query("SELECT * FROM subscription s
									WHERE HeroID = '$id'
									ORDER BY StartDate DESC;");

		if ($query->num_rows() > 0)
			{
				$res = $query->first_row('array');
				return $res;
			}
		return null;
    }

	function updateSubscription($id)
	{
		$data= array(
			'PaypalReference' => 'Yes',
			'Status' => '1'
			);

		$this->db->where('ID', $id);
		$this->db->update('subscription',$data);
	}

	function updateSlots($id,$usedslots)
	{
		$data= array(
			'UsedSlots' => $usedslots
			);

		$this->db->where('ID', $id);
		$this->db->update('subscription',$data);
	}

	function cancelSubscription($reason,$message,$email)
	{
		$data= array(
			'CancelationReason' => $reason,
			'CancelationFeedback' => $message,
			'PayPalReference' => 'Cancel Subscription'
			);

		$this->db->where('Owner', $email);
		$this->db->update('subscription',$data);
	}

	//	Status 1 = Valid Subscription      Status 2 = Cancelled Subscriptions
	function getSubscriptions($email, $status = 1)
    {
         $query = $this->db->query("SELECT * FROM subscription s
										WHERE status = '$status'
										AND Owner like '$email';");
		if ($query->num_rows() > 0)
			{
				return $query;
			}
		return null;
    }

	function get_all_entries($startdate, $enddate, $offset, $limit)
	{
		return $this->db->query("SELECT s.Owner AS `Subscriber` , s.GiftReceiversName AS `Gift Receiver` ,
									s.GiftSendersMail AS `Gift Senders Name` , s.SubscriptionName AS Subscription,
									s.Price, s.Currency, s.CreatedAt AS `Subscription Date` FROM subscription s
									WHERE s.CreatedAT >= '$startdate%' AND s.CreatedAt <= '$enddate%' AND PaypalReference = 'Yes'
									ORDER BY s.CreatedAt ASC limit $offset,$limit");
	}

	function total_users($startdate, $enddate)
	{
		$query = $this->db->query("SELECT count(*) as TotalUsers FROM subscription s
									WHERE s.CreatedAT >= '$startdate%' AND s.CreatedAt <= '$enddate%' AND PaypalReference = 'Yes'");
		foreach ($query->result() as $row)
		{
		  return $row->TotalUsers;
		}
	}
*/
}