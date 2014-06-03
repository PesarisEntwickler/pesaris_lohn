<?php

$aafwConfig["plugins"]["testPlugin"]["currentVersion"] = "V00_00_01";
$aafwConfig["communication"]["functionCalls"]["remoteMode"] = false;
$aafwConfig["communication"]["functionCalls"]["remoteServers"] = array("https://www.pesaris.com/", "https://mebea.pesaris.com/");

///////////////////////////////////////////////////////////////////////////////////////
// function name: functionCall
// purpose      : gets function request from ui-code and forwards them to logic-code.
//                Returns the answer to the calling function.
///////////////////////////////////////////////////////////////////////////////////////
function functionCall($fncName, $param=null) {
	global $aafwConfig;
	$pf = explode(".", $fncName);
	if(sizeof($pf) < 2) return false;
	if(!isset($aafwConfig["plugins"][$pf[0]])) return false;

	if($aafwConfig["communication"]["functionCalls"]["remoteMode"]) {
		//Call a remote function
		//encoding the request
		$comWrapper["functionName"] = $fncName;
		$comWrapper["parameters"] = $param;
		$sendBody = serialize($comWrapper);
		//now sending the function request to the remote server
		//decoding the answer
		$answer = unserialize($res);
	}else{
		//Call a local function
		require_once('../plugins/'.$pf[0]."_".$aafwConfig["plugins"][$pf[0]]["currentVersion"].'/code_logic/plugin.php');
		$prs=new financialAccounting();
		$answer = $prs->dispatcher($objResponse,$functionName,$arrArgs);
	}

	
	return $answer;
}

?>
