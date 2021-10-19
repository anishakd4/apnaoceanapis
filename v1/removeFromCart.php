<?php
	header('Access-Control-Allow-Origin: *');
	$serverName = "127.0.0.1";
	$username = "root";
	$password = "zqecxasdwrty290";

	$conn = mysqli_connect($serverName, $username, $password);

	if($conn){
		mysqli_select_db($conn, "apnaocean");

		$cartid = $_GET["cartid"];
		$sku = $_GET["sku"];
		$query1 = 'select * from cartid where cartid="' . $cartid . '";';
		$result1 = mysqli_query($conn, $query1);
		if(mysqli_num_rows($result1) > 0){
			while ($row = mysqli_fetch_row($result1)) {
				$skusString = unserialize($row[1]);
				unset($skusString[$sku]);
				$skusString = mysqli_real_escape_string($conn, serialize($skusString));
				$query2 = 'update cartid set data="' . $skusString . '" where cartid="' . $cartid . '";';
				$result2 = mysqli_query($conn, $query2);
				if($result2){
					$jsonString = '{"result": "true", "message": "Successfully removed from cart"}';
					$jsonDecodeString = json_decode($jsonString);
					$json = json_encode($jsonDecodeString);
					echo $json;
				}else{
					$jsonString = '{"result": "false", "message": "Please try again later"}';
					$jsonDecodeString = json_decode($jsonString);
					$json = json_encode($jsonDecodeString);
					echo $json;
				}
			}
		}
	}


?>