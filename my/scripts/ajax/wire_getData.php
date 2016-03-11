<?
if($routing_number){
	$url = "http://www.swiftcodesinfo.com/swift-code/results.php?post=Submit+your+choices&searchstring=$routing_number";
	//echo $url; exit;

	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($curl);
	curl_close($curl);

	$match = 0;
	if($res){
		if(preg_match('/class="odd".*'.$routing_number.'<\/b>/',$res,$matches)){
			if($matches){
				$match = $matches[0];
				$match  = trim(preg_replace('/class="odd">|<td>|<\/td>|<b>|<\/b>|'.$routing_number.'/','',$match));
			}
		}
	}

	if($match){
		echo json_encode(array("bank_name"=>$match));
	}else{
		echo json_encode(array("bank_name"=>0));
	}
}
?>
