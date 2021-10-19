<?php
	header('Access-Control-Allow-Origin: *');
	$serverName = "127.0.0.1";
	$username = "root";
	$password = "zqecxasdwrty290";

	$conn = mysqli_connect($serverName, $username, $password);

	if($conn){
		mysqli_select_db($conn, "apnaocean");
		$cartid = $_GET["cartid"];
		$query1 = 'select * from cartid where cartid="' . $cartid . '";';
		$result1 = mysqli_query($conn, $query1);
		if(mysqli_num_rows($result1) > 0){
			while ($row = mysqli_fetch_row($result1)) {
				$skusString = unserialize($row[1]);
				$itemObject = array();
				foreach($skusString as $sku => $qty) {
				    $query2 = 'select * from products where sku=' . '"' . $sku . '";';
				    $result2 = mysqli_query($conn, $query2);
				    if(mysqli_num_rows($result1) > 0){
				    	while ($row = mysqli_fetch_row($result2)) {
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
				$json = json_encode($cartObject);
			    echo $json;
			}
		}
	}

?>