<?php 
	header('Access-Control-Allow-Origin: *');
	$serverName = "127.0.0.1";
	$username = "root";
	$password = "zqecxasdwrty290";

	$myfile = fopen("/home/ubuntu/anish/serverlogs.txt", "a");
	$txt = "\n\n*************************************\n\n";
	$txt = $txt . print_r(apache_request_headers(), 1);
	fwrite($myfile, $txt);
	fclose($myfile);

	$conn = mysqli_connect($serverName, $username, $password);

	if($conn){
		mysqli_select_db($conn, "apnaocean");
		
		$query1 = 'select * from city';
		$result1 = mysqli_query($conn, $query1);
		$mainObject = array();
		if(mysqli_num_rows($result1) > 0){
			while ($row = mysqli_fetch_row($result1)) {
				$cityObject = array();
				$cityObject["city"] = $row[1];
				$query2 = 'select * from ' . $row[1] . '';;
				$result2 = mysqli_query($conn, $query2);
				if(mysqli_num_rows($result2) > 0){
					$areasObject = array();
					while ($row = mysqli_fetch_row($result2)) {
						$areaObject = array();
						$areaObject["name"] = $row[1];
						$query3 = 'select * from t' . $row[0] . ';';
						$result3 = mysqli_query($conn, $query3);
						$bottleObjects = array();
						if(mysqli_num_rows($result3) > 0){
							while ($roww = mysqli_fetch_row($result3)) {
								$bottleObject = array();
								$bottleObject["sku"] = $roww[0];
								$bottleObject["name"] = $roww[1];
								$bottleObject["price"] = $roww[2];
								$bottleObject["description"] = $roww[3];
								$bottleObject["image"] = $roww[4];
								array_push($bottleObjects, $bottleObject);
							}
						}
						$areaObject["bottles"] = $bottleObjects;
						array_push($areasObject, $areaObject);
					}
					$cityObject["areas"] = $areasObject;
				}
				array_push($mainObject, $cityObject);
			}
			http_response_code(200);
			$json = json_encode($mainObject);
			echo $json;
		}
	}else{
		http_response_code(500);
		$jsonString = '{"result": "fail", "message": "Something went wrong..Please try after sometime."}';
		$jsonDecodeString = json_decode($jsonString);
		$json = json_encode($jsonDecodeString);
		echo $json;
	}
?>
