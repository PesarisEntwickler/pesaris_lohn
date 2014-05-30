<?php
//include("wgui/session.inc.php");
//include("wgui/wgui.class.php");

//require('logics/globalFunctions.inc');

require_once("kernel/core-logic/system_database_manager.php");
require_once("kernel/core_ui.php");
require_once("kernel/common-functions/configuration.php");
require_once("kernel/core-logic/session_control.php");
require_once("kernel/common-functions/file_manager.php");
require_once("web/xajax_core/xajax.inc.php");
require_once("kernel/core-ui/CommunicationInterface.class.php");
require_once("kernel/core-ui/wgui.class.php");
require_once("kernel/core-ui/wgui.window.class.php");

$xajax = new xajax("dispatcher.ajax.php");
//$xajax->registerFunction("form");
$xajax->registerFunction("dispatcher");


function dispatcher($functionName)
{
	global $aafwConfig;
	global $SYS_PLUGIN;
	$objResponse = new xajaxResponse();
    communication_interface::setResponseHandle($objResponse);

	$pf = explode(".", $functionName);
	if(sizeof($pf) < 2) {
		$objResponse->alert("Invalid function call.");
		return $objResponse;
	}

	//jedes element in $pf sollte auf INTRUSION geprueft werden -> erlaubt sind nur [a-z | A-Z | 0-9 | _ | -]

	$arrArgs = array();
	for($i = 1; $i < func_num_args(); $i++) $arrArgs[] = func_get_arg($i);

	if($pf[0]=="core") {
		switch($functionName) {
		case 'core.logout':
			$objResponse->script('document.cookie="aafw=null";');
			$objResponse->script('location.reload(true);');
			$session_control = session_control::getInstance();
			$session_control->terminateSession($_COOKIE["aafw"]);
			return $objResponse;
			break;
		default:
			$session_control = session_control::getInstance();

	//		require_once("kernel/".($GLOBAL["aafwConfig"]["communication"]["functionCalls"]["remoteMode"] ? "core_ui" : "core_logic")."/session_control.php");
			if($session_control->validateLogin($arrArgs[0]["ccid"], $arrArgs[0]["cuid"], $arrArgs[0]["cpwd"])) {
				$objResponse->script('document.cookie="aafw='.$session_control->getSessionToken().'";');
				$session_control->loader($objResponse);
				return $objResponse;
			}else{
				$objResponse->alert("Login fehlgeschlagen.");
				return $objResponse;
			}
			break;
		}
	}else{
		$session_control = session_control::getInstance();
		if(!$session_control->setSessionToken($_COOKIE["aafw"])) {
			$objResponse->alert('Die Session ist abgelaufen. Melden Sie sich bitte erneut an.');
			$objResponse->script('document.cookie="aafw=null";');
			$objResponse->script('location.reload(true);');
			return $objResponse;
		}
//		session_control::setSessionToken($_COOKIE["aafw"]);
//		if(!isset($aafwConfig["plugins"][$pf[0]])) {
		if(!session_control::pluginExists($pf[0])) {
			$objResponse->alert("Invalid function call.");
			return $objResponse;
		}
		//Call a local ui function
//		$pluginFile = 'plugins/'.$pf[0]."_".$aafwConfig["plugins"][$pf[0]]["currentVersion"].'/code_ui/plugin.php';
		$pluginFile = 'plugins/'.$pf[0]."_".session_control::getPluginVersion($pf[0]).'/code_ui/plugin.php';
		if(file_exists($pluginFile)) {
			require_once($pluginFile);
			$SYS_PLUGIN["ui"][$pf[0]]->sysListener($functionName, $arrArgs);
			return $objResponse;
		}else{
			$objResponse->alert("Invalid function call.");
			return $objResponse;
		}
	}
/*
	//Access-Controll (foreward $objResponse byRef)
	if($moduleName != "login" && !session_getSessionInfo()) {
		$objResponse->script("needToConfirm = false;");
		$objResponse->script("window.location = 'index.php';");
		return $objResponse;
	}

	$arrArgs = array();
	for($i = 2; $i < func_num_args(); $i++) $arrArgs[] = func_get_arg($i);
	switch($moduleName) {
	case 'login':
		if(session_validateLogin($arrArgs[0]["ccID"],$arrArgs[0]["cuid"],$arrArgs[0]["cpw"])) {
			$objResponse->assign("cpw","value","");
			$objResponse->script("window.location = 'connected.php';");
		}else{
			$gui = new wgui("public_index","de");
			$objResponse->alert($gui->processTemplate($none,"error"));
			$objResponse->assign("cpw","value","");
			return $objResponse;

		}
		break;
	case 'logout':
		session_terminate();
		$objResponse->script("needToConfirm = false;");
		$objResponse->script("window.location = 'index.php';");
		break;
	case 'accnt':
		require_once("logics/financialAccounting.class.php);
		$prs=new financialAccounting();
		$prs->dispatcher($objResponse,$functionName,$arrArgs);
		break;
	case 'relocrecon':
		require_once("logics/relocrecon.class.php");
		$prs=new relocrecon();
		$prs->dispatcher($objResponse,$functionName,$arrArgs);
		break;
	case 'filemanager':
	case 'filemanager.class.php':
		require_once("logics/filemanager.class.php");
		$prs=new filemanager();
		$prs->dispatcher($objResponse,$functionName,$arrArgs);
		break;
	case 'survey':
	case 'survey.class.php':
		require_once("logics/survey.class.php");
		$prs=new survey();
		$prs->dispatcher($objResponse,$functionName,$arrArgs);
		break;
	default:
		//require_once("gui/xliWebGUI.class.php"");
		//$prs=new xliWebGUI();
		//$prs->setAjaxHandle($objResponse);
		//$prs->msgBox(500,"System error","System error: Module not referenced in dispatching funtion!",array(array("OK","hidePopWin(false);")),"critical");
		$objResponse->alert("System error: Module not referenced in dispatching funtion!");
		break;
	}


	closeConn();
	return $objResponse;
*/
}

$xajax->processRequest();
?>
