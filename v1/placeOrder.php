<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require 'PHPMailer-master/src/Exception.php';
	require 'PHPMailer-master/src/PHPMailer.php';
	require 'PHPMailer-master/src/SMTP.php';

	header('Access-Control-Allow-Origin: *');
	$serverName = "127.0.0.1";
	$userName = "root";
	$password = "zqecxasdwrty290";
	date_default_timezone_set('Asia/Kolkata');

	$conn = mysqli_connect($serverName, $userName, $password);
	if($conn){
		mysqli_select_db($conn, "apnaocean");

		$cartid = $_GET['cartid'];
		$name = $_GET['name'];
		$number = $_GET['number'];
		$email_id = $_GET['email_id'];
		$address = $_GET['address'];
		$pincode = $_GET['pincode'];
		$city = $_GET['city'];
		$state = $_GET['state'];
		$paymentmode = $_GET['paymentmode'];

		$query1 = "SELECT * FROM address;";
		$result1 = mysqli_query($conn, $query1);
		$num_rows = mysqli_num_rows($result1);

		$randomString = generateRandomString(20);
		$addressId = "apnaocean_water" . "_address_" . date("Y-m-d") . "_" . date("H:i:s") . "_" . $num_rows . "_" . $randomString;

		$query2 = 'insert into address values("' . $addressId . '", "' . $name . '", "' . $email_id . '", "' . $number . '", "' . $address . '", "' . $pincode . '", "' . $city . '", "' . $state . '");';
		$result2 = mysqli_query($conn, $query2);

		if($result2){
			$query3 = "SELECT * FROM orders;";
			$result3 = mysqli_query($conn, $query3);
			$num_rows = mysqli_num_rows($result3);

			$randomString = generateRandomString(20);
			$orderId = "apnaocean_water" . "_orders_" . date("Y-m-d") . "_" . date("H:i:s") . "_" . $num_rows . "_" . $randomString;
			
			$query4 = 'insert into orders(id, cartid, addressid, paymentmode) values("' . $orderId . '", "' . $cartid . '", "' . $addressId . '", "' . $paymentmode . '");';
			$result4 = mysqli_query($conn, $query4);
			if($result4){
				http_response_code(200);
				$jsonString = '{"result": "true", "message": "Successfully placed your order"}';
				$jsonDecodeString = json_decode($jsonString);
				$json = json_encode($jsonDecodeString);
				sendMail($name , $number , $email_id , $address , $pincode , $city , $state, $conn, $cartid, $orderId);
				echo $json;
			}else{
				http_response_code(302);
				$jsonString = '{"result": "fail", "message": "Something went wrong. Please try after sometime."}';
				$jsonDecodeString = json_decode($jsonString);
				$json = json_encode($jsonDecodeString);
				echo $json;
			}
		}else{
			http_response_code(302);
			$jsonString = '{"result": "fail", "message": "Something went wrong. Please try after sometime."}';
			$jsonDecodeString = json_decode($jsonString);
			$json = json_encode($jsonDecodeString);
			echo $json;
		}
	}else{
		http_response_code(302);
		$jsonString = '{"result": "fail", "message": "Something went wrong. Please try after sometime."}';
		$jsonDecodeString = json_decode($jsonString);
		$json = json_encode($jsonDecodeString);
		echo $json;
	}


	function sendMail($name , $number , $email_id , $address , $pincode , $city , $state, $conn, $cartid, $orderId){
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 0;
		$mail->SMTPAuth = TRUE;
		$mail->SMTPSecure = "tls";
		$mail->Port     = 587;  
		$mail->Username = "dubeyanishkumar";
		$mail->Password = "zqecxasdw90";
		$mail->Host     = "smtp.gmail.com";
		$mail->Mailer   = "smtp";
		$mail->SetFrom("dubeyanishkumar@gmail.com", "from apnaocean");
		$mail->AddReplyTo("dubeyanishkumar@gmail.com", "PHPPot");
		$mail->AddAddress("anishakd4@gmail.com");
		//$mail->AddAddress("foxindia143@gmail.com");
		$mail->AddAddress("shubham83@gamil.com");
		$mail->Subject = "Order placed";
		$mail->WordWrap   = 200;
		$content = "<b>Order Id: " . $orderId . "<br><br></b>" . 
			"<b>Address:</b>" . 
			"<br/>" . "name: " . $name . 
			"<br/>" . "mobile: ".$number . 
			"<br/>" . "email: ".$email_id . 
			"<br/>" . "address: ".$address . 
			"<br/>" . "pincode: " . $pincode . 
			"<br/>" . "city: " . $city . 
			"<br/>" . "state: " . $state; 
		$query5 = 'select * from cartid where cartid="' . $cartid . '";';
		$result5 = mysqli_query($conn, $query5);
		$orderInfo = "";
		if(mysqli_num_rows($result5) > 0){
			while ($row = mysqli_fetch_row($result5)) {
				$skusString = unserialize($row[1]);
				$itemObject = array();
				foreach($skusString as $sku => $qty) {
				    $query6 = 'select * from products where sku=' . '"' . $sku . '";';
				    $result6 = mysqli_query($conn, $query6);
				    if(mysqli_num_rows($result6) > 0){
				    	while ($row = mysqli_fetch_row($result6)) {
				    		$object = array();
				    		$object["sku"] = $row[0];
					    	$object["name"] = $row[1];
					    	$object["rate"] = "₹" . $row[2];
					    	$object["description"] = $row[3];
					    	$object["image"] = $row[4];
					    	$object["expected_delivery"] = "Within 30 minutes";
					    	$object["quantity"] = $qty;
					    	$object["totalValueString"] = "₹" . ($row[2] * $qty);
					    	$object["totalValue"] = $row[2] * $qty;
					    	array_push($itemObject, $object);
				    	}
				    }
				}
				$itemObjectCount = 0;
				foreach ($itemObject as &$itemss) {
    				$orderInfoItem = "<b>item number " . $itemObjectCount . ":</b>" .
    					 "<br>" ."sku: " . $itemss["sku"] .
    					 "<br/>" . "name: " . $itemss["name"] .
    					 "<br/>" . "rate: " . $itemss["rate"] .
    					 "<br/>" . "description: " . $itemss["description"] .
    					 "<br/>" . "quantity: " . $itemss["quantity"] .
    					 "<br/>" . "totalValueString: " . $itemss["totalValueString"];
    				$orderInfo = $orderInfo . "<br><br>" . $orderInfoItem;
    				$itemObjectCount = $itemObjectCount + 1;
				}
				$content = $content .  "<br><br>" . $orderInfo;
				$moneyInfo = "";
				$cartObject = array();
				$cartObject["items"] = $itemObject;
				$cartTotalValue = 0;
				$cartTotalQuantity = 0;
				foreach ($itemObject as $item) {
					$cartTotalValue = $cartTotalValue + $item["totalValue"];
					$cartTotalQuantity = $cartTotalQuantity + $item["quantity"];
				}
				$cartDeliveryCharge = 0;
				$cartTotalValue = $cartTotalValue + $cartDeliveryCharge;
				$cartObject["totalValueString"] = "₹" . $cartTotalValue;
				$cartObject["totalValue"] = $cartTotalValue;
				$cartObject["total_quantity"] = $cartTotalQuantity;
				$cartObject["delivery_charge"] = "₹" . $cartDeliveryCharge;
				$summary = array();
				$summary["Subtotal"] = "₹" . $cartTotalValue;
				$summary["Delivey Charge"] = "₹" . $cartDeliveryCharge;
				$summary["Grand Total"] = "₹" . $cartTotalValue;
				$cartObject["summary"] = $summary;
				$moneyInfo = "<b>Money Info:</b>" . 
					"<br/>" . "Total value: " . $cartObject["totalValueString"] .
					"<br/>" . "Total Quantity: " . $cartObject["total_quantity"];
				$content = $content . "<br><br>" . $moneyInfo;
			}
		}
		$mail->MsgHTML($content);
		$mail->IsHTML(true);
		$mail->Send();
	}

	function generateRandomString($length) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = "";
	    for ($i = 0; $i < $length; $i++) {
	        $randomString = $randomString . $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}


?>