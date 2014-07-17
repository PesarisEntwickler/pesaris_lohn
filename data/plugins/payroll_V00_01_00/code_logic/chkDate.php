<?php
class chkDate {
	
	public function chkDate($dateStr="2013-01-01", $action=0, &$ret="") { //$action=1 [mysql date], $action=2 [unix date], $action=3 [day,month,year separately in a map]
		$fragments = array();
		if(preg_match('/^([0-9]{1,4})[-\/.]?([0-9]{1,4})[-\/.]?([0-9]{1,4})$/', $dateStr, $fragments)) $check = true;
		else $check = false;

		if($check && sizeof($fragments)==4) {
			switch(preg_replace("/[-\/.]+/", "", str_replace("%", "", strtoupper( session_control::getSessionSettings("CORE", "dateformat_medium") )))) {
			case 'YMD':
				$dt["day"] = $fragments[3];
				$dt["month"] = $fragments[2];
				$dt["year"] = $fragments[1];
				break;
			case 'MDY':
				$dt["day"] = $fragments[2];
				$dt["month"] = $fragments[1];
				$dt["year"] = $fragments[3];
				break;
			case 'DMY':
			default:
				$dt["day"] = $fragments[1];
				$dt["month"] = $fragments[2];
				$dt["year"] = $fragments[3];
				break;
			}
			if(strlen($dt["day"])==1) $dt["day"] = "0".$dt["day"];
			if(strlen($dt["month"])==1) $dt["month"] = "0".$dt["month"];
			if(strlen($dt["year"])==1) $dt["year"] = substr(date("Y"),0,3).$dt["year"];
			else if(strlen($dt["year"])==2) $dt["year"] = substr(date("Y"),0,2).$dt["year"];
			$check = checkdate($dt["month"], $dt["day"], $dt["year"]);
		}

		if($check && $action!=0) {
			switch($action) {
			case 1: //$action=1 [mysql date]
				$ret = $dt["year"]."-".$dt["month"]."-".$dt["day"];
				break;
			case 2: //$action=2 [unix date]
				$ret = mktime(0, 0, 0, $dt["month"], $dt["day"], $dt["year"]);
				break;
			case 3: //$action=3 [day,month,year separately in a map]
				$ret = $dt;
				break;
			}
		}else{
			$ret = "";
		}

		return $check;
	}

	
}
?>
