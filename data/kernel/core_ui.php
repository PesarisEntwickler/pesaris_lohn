<?php
/*
$aafwConfig["plugins"]["testPlugin"]["currentVersion"] = "V00_00_01";
$aafwConfig["communication"]["functionCalls"]["remoteMode"] = false;
$aafwConfig["communication"]["functionCalls"]["remoteServices"] = array("http://192.168.1.200/axerios/aafw/nuncius.php"); //array("https://www.pesaris.com/", "https://mebea.pesaris.com/");
*/
function do_post_request($url, $data, $optional_headers = null) {
	$params = array('http' => array('method' => 'POST', 'content' => $data));
	if ($optional_headers!== null) {
		$params['http']['header'] = $optional_headers;
	}else{
		$params['http']['header'] = "Content-type: application/x-www-form-urlencoded\r\nContent-Length: ".strlen($data)."\r\n";
	}
	$ctx = stream_context_create($params);
	$fp = @fopen($url, 'r', false, $ctx);
	if (!$fp) {
		throw new Exception("Problem with $url, $php_errormsg");
	}
	$response = @stream_get_contents($fp);
	if ($response === false) {
		throw new Exception("Problem reading data from $url, $php_errormsg");
	}
	return $response;
}

function blFunctionCall($fncName) {
	global $aafwConfig;
	global $SYS_PLUGIN;

	$arrArgs = array();
	for($i = 1; $i < func_num_args(); $i++) $arrArgs[] = func_get_arg($i);

	$pf = explode(".", $fncName);
	if(sizeof($pf) < 2) return false;
	if(!session_control::pluginExists($pf[0])) return false;

//Berechtigung muss hier nicht geprüft werden -> Security-Check hat im Plugin zu erfolgen!

	//Call a local function
	$pluginFile = 'plugins/'.$pf[0]."_".session_control::getPluginVersion($pf[0]).'/code_logic/plugin.php';

	if(file_exists($pluginFile)) {
		require_once($pluginFile);
		$answer = $SYS_PLUGIN["bl"][$pf[0]]->sysListener($fncName, $arrArgs);
	}else{
		return false;
	}

	return $answer;
}


///////////////////////////////////////////////////////////////////////////////////////
// function name: functionCall
// purpose      : gets function request from ui-code and forwards them to logic-code.
//                Returns the answer to the calling function.
///////////////////////////////////////////////////////////////////////////////////////
function uiFunctionCall($fncName) {
//function uiFunctionCall($fncName, &$objResponse=null) {
	global $aafwConfig;
	global $SYS_PLUGIN;

	$arrArgs = array();
	for($i = 1; $i < func_num_args(); $i++) $arrArgs[] = func_get_arg($i);

	$pf = explode(".", $fncName);
	if(sizeof($pf) < 2) return false;
	if(!session_control::pluginExists($pf[0])) return false;

//Berechtigung muss hier nicht geprüft werden -> Security-Check hat im Plugin zu erfolgen!

	//Call a local function
	$pluginFile = 'plugins/'.$pf[0]."_".session_control::getPluginVersion($pf[0]).'/code_ui/plugin.php';
	if(file_exists($pluginFile)) {
		require_once($pluginFile);
		$answer = $SYS_PLUGIN["ui"][$pf[0]]->sysListener($fncName, $arrArgs);
	}else{
		return false;
	}

	return $answer;
}

function blFireEvent($fncName) {
	global $aafwConfig;
	global $SYS_PLUGIN;

	$arrArgs = array();
	for($i = 1; $i < func_num_args(); $i++) $arrArgs[] = func_get_arg($i);

//	require_once('system_database_manager.php');
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery("SELECT name,version FROM core_plugins ORDER BY sort_order", "plugin names for uiFireEvent");
	foreach($result as $row) {
		$pluginFile = 'plugins/'.$row["name"]."_".$row["version"].'/code_logic/plugin.php';
		if(file_exists($pluginFile)) {
			require_once($pluginFile);
			$answer = $SYS_PLUGIN["bl"][$row["name"]]->eventListener($fncName, $arrArgs);
		}
	}

	return $answer;
}

function uiFireEvent($fncName) {
	global $aafwConfig;
	global $SYS_PLUGIN;

	$arrArgs = array();
	for($i = 1; $i < func_num_args(); $i++) $arrArgs[] = func_get_arg($i);

//	require_once('system_database_manager.php');
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery("SELECT name,version FROM core_plugins ORDER BY sort_order", "plugin names for uiFireEvent");
	foreach($result as $row) {
		$pluginFile = 'plugins/'.$row["name"]."_".$row["version"].'/code_ui/plugin.php';
		if(file_exists($pluginFile)) {
			require_once($pluginFile);
			$answer = $SYS_PLUGIN["ui"][$row["name"]]->eventListener($fncName, $arrArgs);
		}
	}

	return $answer;
}

/*
$time_start = microtime(true);

echo functionCall("testPlugin.kuckuck", "hallo erst mal");

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<br><br>$time<br><br>";
*/
?>
