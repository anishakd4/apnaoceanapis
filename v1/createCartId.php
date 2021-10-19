<?php
	header('Access-Control-Allow-Origin: *');
	$servername = "127.0.0.1";
	$username = "root";
	$password = "zqecxasdwrty290";

	$conn = mysqli_connect($servername, $username, $password);

	if ($conn) {
	    mysqli_select_db($conn, "apnaocean");

	    $query1 = "SELECT * FROM cartid;";
		$result1 = mysqli_query($conn, $query1);

		if(mysqli_num_rows($result1) > 0){
			$num_rows = mysqli_num_rows($result1);

			date_default_timezone_set('Asia/Kolkata');
			$randomString = generateRandomString(20);
			$cartid = "apnaocean_water" . "_cartid_" . date("d-m-Y") . "_" . date("H:i:s") . "_" . $num_rows . "_" . $randomString;
		
			$query2 = 'INSERT INTO cartid VALUES("' . $cartid . '" , "" );';
			$result2 = mysqli_query($conn, $query2);

			if($result2){
				http_response_code(200);
				$cartidJsonString = '{"cart_id": ' . '"' . $cartid . '"' . "}";
			    $cartidJsonObject = json_decode($cartidJsonString);
				$cartidJson = json_encode($cartidJsonObject);
				echo $cartidJson;
			}else{
				http_response_code(302);
				$jsonString = '{"result": "fail", "message": "Something went wrong"}';
				$jsonDecodeString = json_decode($jsonString);
				$json = json_encode($jsonDecodeString);
				echo $json;
			}
		}else{
			http_response_code(302);
			$jsonString = '{"result": "fail", "message": "Something went wrong"}';
			$jsonDecodeString = json_decode($jsonString);
			$json = json_encode($jsonDecodeString);
			echo $json;
		}
	}else{
		http_response_code(302);
		$jsonString = '{"result": "fail", "message": "Something went wrong"}';
		$jsonDecodeString = json_decode($jsonString);
		$json = json_encode($jsonDecodeString);
		echo $json;
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