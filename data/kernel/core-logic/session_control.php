<?php
//require_once("../common-functions/configuration.php");

class session_control {
	private static $instance = 0;
	private $isDebug;		//	true/false if true log messages will be written in log file otherwise not to write 
	private static $sessionCachePath;
	private $sessionToken;
	private $sessionInfo;
	private $saveSessionFile = false;

	private function __construct() {
		$this->isDebug = false;
        global $aawConfig;
		$this->sessionCachePath = $aafwConfig["paths"]["session_control"]["sessionCachePath"];
        //$this->sessionCachePath = "/usr/local/www/apache22/data/kernel/cache/sessions/";
//		$this->sessionCachePath = "kernel/cache/sessions/"; // <--- relativer Pfad hat Probleme im Destruktor bereitet... darum nur absoluten Pfad verwenden!
	}

	public function __destruct() {
		if($this->saveSessionFile) file_put_contents($this->sessionCachePath.$this->sessionToken.".dat", serialize($this->sessionInfo));
	}

//	private function logError($message) {
//		if ($this->isDebug === true) {
//			logError($message);
//		}
//	}


	// **
//	 * Returns a reference to the session_control object
//	 *
//	 *

	public static function getInstance() {
		if (session_control::$instance === 0) {
			session_control::$instance = new session_control();
//			session_control::$instance->logError("internationalization Instance Created");
		}

//		session_control::$instance->logError("internationalization->getInstance()");
		return session_control::$instance;
	}

	public function getSessionToken() {
		return $this->sessionToken;
	}

	public function setSessionToken($sessionToken) {
		// Da wir mit dem $sessionToken einen File-Pfad zusammensetzen, besteht INTRUSION GEFAHR!! Unbedingt pruefen!
		if(trim($sessionToken)!="" && preg_match( '/^[a-zA-Z0-9]*$/', $sessionToken)) {
			//TRUE
		}else{
			return false;
		}

		$this->sessionToken = $sessionToken;
		$sessionFile = $this->sessionCachePath.$sessionToken.".dat";
		if(file_exists($sessionFile)) {
			$this->sessionInfo = unserialize(file_get_contents($sessionFile));
			//soweit so gut... jetzt muss aber noch ueberprueft werden, ob die Session nicht abgelaufen ist
			//ggf. muss auch noch der IP-Range (gegen SUBNET-MASKE) geprueft werden
			//ggf. muss auch noch die Uhrzeit und Wochentag geprueft werden
			//und wenn bis hierhin alle i.O. ist, dann wird jetzt die DB ausgewaehlt
			$system_database_manager = system_database_manager::getInstance();
			$system_database_manager->selectDB($this->sessionInfo["db_name"]);
			$system_database_manager->defaultDatabaseName($this->sessionInfo["db_name"]);

			return true;
		}else{
			return false;
		}
	}

	public function terminateSession($sessionToken) {
		// Da wir mit dem $sessionToken einen File-Pfad zusammensetzen, besteht INTRUSION GEFAHR!! Unbedingt pruefen!
		if(trim($sessionToken)!="" && preg_match( '/^[a-zA-Z0-9]*$/', $sessionToken)) {
			$sessionFile = $this->sessionCachePath.$sessionToken.".dat";
			if(file_exists($sessionFile)) unlink($sessionFile);
		}
	}

	public function getSessionInfo($itemName) {
		$sessionControl = session_control::getInstance();
		return $sessionControl->sessionInfo[$itemName];
	}

	public function getConfiguration($section) {
		global $aafwConfig;
		return $aafwConfig[$section];
	}

	public function validateLogin($customerID, $userID, $password) {
//		defuseText()	//text entschaerfen
//		defuseInteger	//INT entschaerfen
//		defuseDecimal	//Fliesskommazahl entschaerfen
		require_once("system_database_manager.php");
		$system_database_manager = system_database_manager::getInstance();

//		if(!preg_match( '/^[_a-zA-Z0-9]{2,15}$/', $customerID)) {
//			return false;
//		}

        //$system_database_manager->selectDatabase()
        //		$system_database_manager->overrideDatabaseName($customerID);
        $result = $system_database_manager->executeQuery("SELECT `databaseName` FROM `customer` WHERE `id`='".addslashes($customerID)."' AND `active`=1", "authenticate employee login");
        if(count($result) > 0) {
            $dbName = $result[0]["databaseName"];
            $system_database_manager->selectDB($dbName);
            $system_database_manager->defaultDatabaseName($dbName);
        }else{
            return false;
        }
       
		$result = $system_database_manager->executeQuery("SELECT id, full_name, email, plugin_settings, uid, language FROM core_user WHERE uid='".addslashes($userID)."' AND pwd=PASSWORD('".addslashes($password)."')", "authenticate employee login");
		if(count($result) > 0) {
			$sessionControl = session_control::getInstance();

			//Loop until we found a sessionToken that does not exist
			do {
				$sessionToken = md5($result[0]["full_name"].$result[0]["email"].microtime(true));
				$sessionFile = $sessionControl->sessionCachePath.$sessionToken.".dat";
			} while(file_exists($sessionFile));

			$sessionControl->sessionToken = $sessionToken;

			$sessionControl->sessionInfo["customer_login_name"] = $customerID;
			$sessionControl->sessionInfo["db_name"] = $dbName;
			$sessionControl->sessionInfo["full_name"] = $result[0]["full_name"];
			$sessionControl->sessionInfo["email"] = $result[0]["email"];
			$sessionControl->sessionInfo["uid"] = $result[0]["uid"];
			$sessionControl->sessionInfo["id"] = $result[0]["id"];
			$sessionControl->sessionInfo["language"] = $result[0]["language"];
//			$this->sessionInfo["ttl"] = $result[0]["ttl"]; //time-to-live in minutes
			$sessionControl->sessionInfo["DateTimeLogin"] = time();
			$sessionControl->sessionInfo["DateTimeLastAction"] = time();
//			$system_database_manager->executeUpdate("REPLACE INTO core_registry(path,name,type,value) VALUES('$registryPath','$registryName','$registryType','".addslashes($registryValue)."')", "setUserInformation");
			$resGrp = $system_database_manager->executeQuery("SELECT path,name,value FROM core_registry WHERE path LIKE 'GLOBAL/SETTINGS/%'", "validate login: read GROUP settings");
			foreach($resGrp as $row) $sessionControl->sessionInfo["SETTINGS"][substr($row["path"], 16)][$row["name"]] = $row["value"];
			$resUsr = $system_database_manager->executeQuery("SELECT path,name,value FROM core_registry WHERE path LIKE 'USERS/".$result[0]["id"]."/SETTINGS/%'", "validate login: read GROUP settings");
			$pluginNameOffset = strlen("USERS/".$result[0]["id"]."/SETTINGS/");
			foreach($resUsr as $row) $sessionControl->sessionInfo["SETTINGS"][substr($row["path"], $pluginNameOffset)][$row["name"]] = $row["value"];

//			$sessionControl->sessionInfo["pluginSettings"] = unserialize($result[0]["plugin_settings"]);
			$sessionControl->sessionInfo["plugins"] = array();
			$res = $system_database_manager->executeQuery("SELECT name,version FROM core_plugins ORDER BY sort_order", "validate login");
			foreach($res as $row) $sessionControl->sessionInfo["plugins"][$row["name"]]["version"] = $row["version"];

			if(file_put_contents($sessionFile, serialize($sessionControl->sessionInfo))===false) return false;
			return true;
		}else{
			return false;
		}
	}

	public function loader() {
//	public function loader(&$objResponse) {
//		fireEvent("core.loader"); //-> class event_control
//		fireEvent("core.cron");
//		registerEvent("myPlugin.myFunctionName", "core.loader");
		require_once("system_database_manager.php");
		$system_database_manager = system_database_manager::getInstance();
        global $aafwConfig;
		require_once($aafwConfig["paths"]["session_control"]["rootPathData"]."kernel/core_ui.php");
		//$core_ui = core_ui::getInstance();
		$result = $system_database_manager->executeQuery("SELECT name FROM core_plugins ORDER BY sort_order", "loader");
		$out = "";
		foreach($result as $row) {
			$out .= $row["name"]."*";
//			$SYS_PLUGIN["ui"][$row["name"]]->loader($objResponse);
//			uiFunctionCall($row["name"].".sysLoader", $objResponse);
			uiFunctionCall($row["name"].".sysLoader");
		}
//		uiFireEvent("core.bootComplete");
//		$objResponse->alert($out);
	}

	public function pluginExists($pluginName) {
		$sessionControl = session_control::getInstance();
		return isset($sessionControl->sessionInfo["plugins"][$pluginName]);
//		return isset($this->sessionInfo["plugins"][$pluginName]);
	}

	public function getPluginVersion($pluginName) {
		$sessionControl = session_control::getInstance();
		return $sessionControl->sessionInfo["plugins"][$pluginName]["version"];
//		return $this->sessionInfo["plugins"][$pluginName]["version"];
	}

	public function setSessionSettings($pluginName, $settingName, $settingValue, $setPermanently=false, $basePath="USERS", $groupID=0) {
		if($basePath!="USERS" && $basePath!="GROUPS" && $basePath!="GLOBAL") return false;
		if(strlen($pluginName)==0) return false;
		$sessionControl = session_control::getInstance();
		$sessionControl->sessionInfo["SETTINGS"][$pluginName][$settingName] = $settingValue;
		$sessionControl->saveSessionFile = true;
		if($setPermanently) {
			$ret = $sessionControl->setRegistryInformation(($basePath=="USERS" ? $sessionControl->sessionInfo["id"] : $groupID), "SETTINGS/".$pluginName, $settingName, $settingValue, 0, $basePath);
			return $ret["success"];
		}
		return true;
	}

	public function getSessionSettings($pluginName, $settingName="") {
		$sessionControl = session_control::getInstance();
		return ($settingName=="" ? $sessionControl->sessionInfo["SETTINGS"][$pluginName] : $sessionControl->sessionInfo["SETTINGS"][$pluginName][$settingName]);
/*
beim Code des coreFunktion Plugins bedienen....

CORE:
	uiLanguage
	date_short_dm
	date_short_ddmmyy
	date_medium_ddmmyyyy
	date_long_ddmmyyyy
	number_thousand_separator
	number_decimal_separator
*/
	}

	private function setRegistryInformation($core_user_ID, $registryPath, $registryName, $registryValue, $registryType = 0, $registryBasePath) {
		///////////////////////////////////////////////////
		// userID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(preg_match( '/^[0-9]+$/', $core_user_ID)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 102;
			$response["errText"] = "invalid user id";
			return $response;
		}

		///////////////////////////////////////////////////
		// $registryPath must contain only contain digits, letters, "_" and "/"
		// At least 3 characters!
		///////////////////////////////////////////////////
		if(preg_match( '/^[\\/_0-9A-Za-z]{3,}$/', $registryPath)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 103;
			$response["errText"] = "invalid path";
			return $response;
		}

		///////////////////////////////////////////////////
		// $registryName must whether contain only "*" or fullfill the
		// following rules:
		//
		// $registryName must contain only contain digits, letters and "_"
		// At least 1 character
		///////////////////////////////////////////////////
		if(preg_match( '/^[_0-9A-Za-z]+$/', $registryName)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 104;
			$response["errText"] = "invalid name";
			return $response;
		}

		///////////////////////////////////////////////////
		// registryType must be numeric and non-decimal and only 1 digit long
		///////////////////////////////////////////////////
		if(preg_match( '/^[0-9]{1,1}$/', $registryType)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 103;
			$response["errText"] = "invalid type";
			return $response;
		}

		switch($registryBasePath) {
		case "USERS":
		case "GROUPS":
			$registryPath = $registryBasePath."/".$core_user_ID."/".$registryPath; //$registryBasePath='USERS' or 'GROUPS'
			break;
		case "GLOBAL":
			$registryPath = $registryBasePath."/".$registryPath;
			break;
		}
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("REPLACE INTO core_registry(path,name,type,value) VALUES('$registryPath','$registryName','$registryType','".addslashes($registryValue)."')", "setUserInformation");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["errText"] = "";
		return $response;
	}
}

//$session_control = session_control::getInstance();
//echo $session_control->validateLogin("aafw", "dwm", "samira") ? "TRUE" : "FALSE";

//$sc = new session_control();
//echo $sc->validateLogin("aafw", "dwm", "samira") ? "TRUE" : "FALSE";
//echo session_control::validateLogin("aafw", "dwm", "samira") ? "TRUE" : "FALSE";
//session_control::loader($test);
?>
