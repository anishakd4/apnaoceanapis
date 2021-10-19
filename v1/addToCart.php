<?php
	header('Access-Control-Allow-Origin: *');
	$serverName = "127.0.0.1";
	$userName = "root";
	$password = "zqecxasdwrty290";

	$conn = mysqli_connect($serverName, $userName, $password);
	if($conn){
		mysqli_select_db($conn, "apnaocean");

		$cartid = $_GET['cartid'];
		$sku = $_GET['sku'];
		$qty = $_GET['qty'];

		$query1 = 'select * from cartid where cartid="' . $cartid . '";';
		$result1 = mysqli_query($conn, $query1);

		if(mysqli_num_rows($result1) > 0){
			while ($row = mysqli_fetch_row($result1)) {
				if(empty($row[1])){
					$dataString = array();
					$dataString[$sku] = $qty;
				}else{
					$dataString = unserialize($row[1]);
					$dataString[$sku] = $qty;
				}
				$dataString = mysqli_real_escape_string($conn, serialize($dataString));
				$query2 = 'update cartid set data="' . $dataString . '" where cartid="' . $cartid . '";';
				$result2 = mysqli_query($conn, $query2);
				if($result2){
					http_response_code(200);
					$jsonString = '{"result": "true", "message": "Successfully added to cart"}';
					$jsonDecodeString = json_decode($jsonString);
					$json = json_encode($jsonDecodeString);
					echo $json;
				}else{
					http_response_code(302);
					$jsonString = '{"result": "fail", "message": "Something went wrong."}';
					$jsonDecodeString = json_decode($jsonString);
					$json = json_encode($jsonDecodeString);
					echo $json;
				}
			}
		}else{
			http_response_code(302);
			$jsonString = '{"result": "fail", "message": "Something went wrong."}';
			$jsonDecodeString = json_decode($jsonString);
			$json = json_encode($jsonDecodeString);
			echo $json;
		}
	}else{
		http_response_code(302);
		$jsonString = '{"result": "fail", "message": "Something went wrong."}';
		$jsonDecodeString = json_decode($jsonString);
		$json = json_encode($jsonDecodeString);
		echo $json;
	}
?>