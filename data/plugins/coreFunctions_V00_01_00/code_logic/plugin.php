<?php
class coreFunctions_BL {

	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;

		switch($functionName) {
		case 'coreFunctions.getPluginList':
			$system_database_manager = system_database_manager::getInstance();
			$result = $system_database_manager->executeQuery("SELECT name FROM core_plugins ORDER BY sort_order", "getPluginList");
			return $result;
			break;
		case 'coreFunctions.changePassword':
			$userID = $functionParameters[0];
			$curPWD = $functionParameters[1];
			$newPWD = $functionParameters[2];

			///////////////////////////////////////////////////
			// Checking for invalid characters
			// Allowed characters: a-z A-Z 0-9 $ . _ -
			///////////////////////////////////////////////////
			if(preg_match( '/^[-$._a-zA-Z0-9]*$/', $curPWD)) {
			    //TRUE
			}else{
				$response["success"] = false;
				$response["errCode"] = 102;
				$response["errText"] = "old password: invalid characters";
				return $response;
			}
			if(preg_match( '/^[-$._a-zA-Z0-9]*$/', $newPWD)) {
			    //TRUE
			}else{
				$response["success"] = false;
				$response["errCode"] = 102;
				$response["errText"] = "new password: invalid characters";
				return $response;
			}
			///////////////////////////////////////////////////
			// userID must be numeric and non-decimal
			///////////////////////////////////////////////////
			if(preg_match( '/^[0-9]+$/', $userID)) {
			    //TRUE
			}else{
				$response["success"] = false;
				$response["errCode"] = 105;
				$response["errText"] = "invalid user id";
				return $response;
			}
			///////////////////////////////////////////////////
			// Passwords (old and new) must not be empty
			///////////////////////////////////////////////////
			if(trim($curPWD)=="" || trim($newPWD)=="") {
				$response["success"] = false;
				$response["errCode"] = 103;
				$response["errText"] = "old or new password empty";
				return $response;
			}
			///////////////////////////////////////////////////
			// The new passwords must have a length of at least 6 characters
			///////////////////////////////////////////////////
			if(strlen($newPWD) < 6) {
				$response["success"] = false;
				$response["errCode"] = 104;
				$response["errText"] = "password length less than 6 characters";
				return $response;
			}

			//hat der angemeldete Nutzer die Berechtigung sein eigenes PW zu ändern?
			//hat der angemeldete Nutzer die Berechtigung das PW eines anderen Nutzers zu ändern?
			//enthalten die aebermittelten Passwörter ungaeltige Zeichen

			///////////////////////////////////////////////////
			// The "old" passwords must correspond with the current password set in the database
			///////////////////////////////////////////////////
			$system_database_manager = system_database_manager::getInstance();
			$result = $system_database_manager->executeQuery("SELECT id FROM core_user WHERE id='".addslashes($userID)."' AND pwd=PASSWORD('".addslashes($curPWD)."')", "changePassword:check");
			if(count($result) < 1) {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "old password wrong";
				return $response;
			}

			$system_database_manager->executeUpdate("UPDATE core_user SET pwd=PASSWORD('".addslashes($newPWD)."'), datetime_pwd_change=NOW(), core_user_ID_pwd_change=".session_control::getSessionInfo("id")." WHERE id='".addslashes($userID)."'", "changePassword:set");

			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			return $response;
			break;
		case 'coreFunctions.getUserList':
			if(count($functionParameters) > 0) $orderBy = " ORDER BY ".addslashes($functionParameters[0]);
			else $orderBy = "";

			$system_database_manager = system_database_manager::getInstance();
			$result = $system_database_manager->executeQuery("SELECT id, uid, full_name, email, language, active FROM core_user WHERE deleted=0$orderBy", "getSecurityUserList");
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			$response["data"] = $result;
			return $response;
			break;
		case 'coreFunctions.getUserSettings':
			if(count($functionParameters) > 0) $core_user_ID = $functionParameters[0];
			else {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "invalid user id";
				return $response;
			}

			///////////////////////////////////////////////////
			// userID must be numeric and non-decimal
			///////////////////////////////////////////////////
			if(preg_match( '/^[0-9]+$/', $core_user_ID)) {
			    //TRUE
			}else{
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "invalid user id";
				return $response;
			}

			$system_database_manager = system_database_manager::getInstance();
			$result = $system_database_manager->executeQuery("SELECT id, uid, full_name, email, language, active, timeout_minutes, allow_pwd_change, force_pwd_change, pwd_change_period_days, deleted, core_user_ID_pwd_change, datetime_pwd_change, core_user_ID_change, datetime_change, core_user_ID_create, datetime_create, core_user_ID_delete, datetime_delete FROM core_user WHERE id=".addslashes($core_user_ID), "getSecurityUserDetails");
			$resGroupAssignment = $system_database_manager->executeQuery("SELECT core_group_ID FROM core_user_group WHERE core_user_ID=".addslashes($core_user_ID), "getSecurityUser2Group-Assignments");
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			$response["data"] = $result[0];
			$response["data"]["groupAssignments"] = array();
			foreach($resGroupAssignment as $grpID) $response["data"]["groupAssignments"][] = $grpID["core_group_ID"];

			$extSecurity = $this->getUserInformation($core_user_ID, "ExtendedSecurity", "*");
			$response["data"]["securitySettings"] = $extSecurity["data"];

			return $response;
			break;
		case 'coreFunctions.getGroupList':
			if(count($functionParameters) > 0) $orderBy = " ORDER BY ".addslashes($functionParameters[0]);
			else $orderBy = "";

			$system_database_manager = system_database_manager::getInstance();
			$result = $system_database_manager->executeQuery("SELECT id, name, description, active, core_user_ID_change, core_user_ID_create, datetime_change, datetime_create FROM core_group WHERE deleted=0$orderBy", "getSecurityGroupList");
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			$response["data"] = $result;
			return $response;
			break;
		case 'coreFunctions.getGroupSettings':
			if(count($functionParameters) > 0) $core_group_ID = $functionParameters[0];
			else {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "invalid group id";
				return $response;
			}

			///////////////////////////////////////////////////
			// groupID must be numeric and non-decimal
			///////////////////////////////////////////////////
			if(preg_match( '/^[0-9]+$/', $core_group_ID)) {
			    //TRUE
			}else{
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "invalid group id";
				return $response;
			}

			$system_database_manager = system_database_manager::getInstance();
			$result = $system_database_manager->executeQuery("SELECT id, name, description, active, deleted FROM core_group WHERE id=".addslashes($core_group_ID), "getSecurityGroupDetails");
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			$response["data"] = $result[0];

			return $response;
			break;
		case 'coreFunctions.saveGroupPermission':
			if(count($functionParameters) > 0) $core_group_ID = $functionParameters[0]["rec_id"];
			else {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "invalid group id";
				return $response;
			}

			///////////////////////////////////////////////////
			// groupID must be numeric and non-decimal
			///////////////////////////////////////////////////
			if(preg_match( '/^[0-9]+$/', $core_group_ID)) {
			    //TRUE
			}else{
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "invalid group id";
				return $response;
			}

			$affectedPlugins = array();
			foreach($functionParameters[0] as $key=>$value) {
				if(substr($key, 0, 7)=="marker_") {
					$arrTmp = explode("_", $key);
					if(!isset($submodules[$arrTmp[1]])) {
						$submodules[$arrTmp[1]] = array();
					}
					$submodules[$arrTmp[1]][] = $arrTmp[2];
					$affectedPlugins[] = $arrTmp[1];
				}
			}
			if(count($affectedPlugins)>0) {
				$affectedPlugins = array_unique($affectedPlugins);

				foreach($functionParameters[0] as $key=>$value) {
					$arrTmp = explode("_", $key);
					if(count($arrTmp)>0) {
						if(in_array($arrTmp[0], $affectedPlugins)) {
							$permissionData[$arrTmp[0]][$arrTmp[1]] = $value;
						}
					}
				}

				foreach($submodules as $pluginName=>$permissionSections) {
					//Call a local function
					$pluginFile = 'plugins/'.$pluginName."_".$aafwConfig["plugins"][$pluginName]["currentVersion"].'/code_logic/plugin.php';
//communication_interface::alert($pluginFile);
					if(file_exists($pluginFile)) {
						require_once($pluginFile);

						foreach($permissionSections as $permissionSection) {
//communication_interface::alert($pluginName."/".$permissionSection);
							$answer = $SYS_PLUGIN["bl"][$pluginName]->sysPermission($permissionSection);
							foreach($answer as $prmItemName => $prmItemValues) {
								if(isset($permissionData[$pluginName][$prmItemName])) {
									switch($prmItemValues["type"]) {
									case 'bool':
										$permissionData[$pluginName][$prmItemName] = $permissionData[$pluginName][$prmItemName]==1 ? 1 : 0;
										break;
									}
								}else{
									switch($prmItemValues["type"]) {
									case 'bool':
										$permissionData[$pluginName][$prmItemName] = 0;
										break;
									}
								}
							}
						}
					}
				}
				foreach($affectedPlugins as $pluginName) {
					foreach($permissionData[$pluginName] as $key=>$value) {
						$this->setGroupInformation($core_group_ID, "ModulePermission/".$pluginName, $key, $value);
					}
				}
			}
//communication_interface::alert(print_r($permissionData, true));
/*
			$tOut = "";
			foreach($functionParameters[0] as $key=>$value) $tOut .= $key.", ";
communication_interface::alert($tOut);
rec_id, geListChange, name, description, geGrpSelect, marker_baseLayout_systemUser, marker_baseLayout_systemConfig, baseLayout_configEnabled
*/
			return $response;
			break;
		case 'coreFunctions.saveGroupSettings':
			if(count($functionParameters) != 1) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "wrong number of parameters";
				return $response;
			}
			$coreFields = array(	"id" => array(100,true,"[0-9]{1,10}"),
						"name" => array(105,true,"[-$._a-zA-Z0-9]{3,25}"),
						"description" => array(110,false,"[^']*"),	// COULD BE STILL HOT VARIABLE (INTRUSION) !!
						"active" => array(145,true,"[01]{1,1}")
			);
			foreach($coreFields as $fieldName => $fieldParameters) {
				if(($errCode = $this->checkValidity($functionParameters[0][$fieldName],$fieldParameters[0],$fieldParameters[1],$fieldParameters[2])) !== true) {
					$response["success"] = false;
					$response["errCode"] = $errCode;
					$response["errText"] = "field '$fieldName' not valid";
					return $response;
				}
			}

			$core_group_ID = $functionParameters[0]["id"];
			$name = addslashes($functionParameters[0]["name"]);
			$description = addslashes($functionParameters[0]["description"]);
			$active = $functionParameters[0]["active"];
			$core_user_ID_change = session_control::getSessionInfo("id");
			$datetime_change = "NOW()";

			$system_database_manager = system_database_manager::getInstance();
			$system_database_manager->executeUpdate("BEGIN", "saveSecurityGroupSettings:start");

			if($core_group_ID > 0) {
				$system_database_manager->executeUpdate("UPDATE core_group SET name='$name', description='$description', active=$active, core_user_ID_change=$core_user_ID_change, datetime_change=$datetime_change WHERE id=$core_group_ID", "saveSecurityGroupSettings:update group data");
			}else{
				$deleted = 0;
				$core_user_ID_create = $core_user_ID_change;
				$core_user_ID_delete = 0;
				$datetime_create = "NOW()";
				$datetime_delete = "'0000-00-00'";

				$system_database_manager->executeUpdate("INSERT INTO core_group(name, description, active, deleted, core_user_ID_change, core_user_ID_create, core_user_ID_delete, datetime_change, datetime_create, datetime_delete) VALUES('$name', '$description', $active, $deleted, $core_user_ID_change, $core_user_ID_create, $core_user_ID_delete, $datetime_change, $datetime_create, $datetime_delete)", "saveSecurityGroupSettings:insert group data");
				$core_group_ID = $system_database_manager->getLastInsertId();
			}

			$system_database_manager->executeUpdate("COMMIT", "saveSecurityGroupSettings:commit");

			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";

			return $response;
			break;
		case 'coreFunctions.saveUserSettings':
			if(count($functionParameters) != 1) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "wrong number of parameters";
				return $response;
			}

			$coreFields = array(	"core_user_ID" => array(100,true,"[0-9]{1,10}"),
						"uid" => array(105,true,"[-$._a-z0-9]{3,25}"),
						"full_name" => array(110,true,".{3,50}"),	// STILL HOT VARIABLE (INTRUSION) !!
						"email" => array(115,true,"[-!#$%&'*+\\/=?_`{|}~a-z0-9^]+(\\.[-!#$%&'*+\\/=?_`{|}~a-z0-9^]+)*@([a-z0-9]([-a-z0-9]*[a-z0-9])?\\.)+([A-Z]{2,2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)"),	// STILL HOT VARIABLE (INTRUSION) !!
						"language" => array(120,true,"[a-z]{2,2}(-[a-z]{2,2})?"),
						"active" => array(125,true,"[01]{1,1}"),
						"timeout_minutes" => array(130,true,"[0-9]{1,3}"),
						"force_pwd_change" => array(135,true,"[01]{1,1}"),
						"pwd_change_period" => array(140,true,"[0-9]{0,3}"),
						"allow_pwd_change" => array(145,true,"[01]{1,1}")
			);
			foreach($coreFields as $fieldName => $fieldParameters) {
				if(($errCode = $this->checkValidity($functionParameters[0][$fieldName],$fieldParameters[0],$fieldParameters[1],$fieldParameters[2])) !== true) {
					$response["success"] = false;
					$response["errCode"] = $errCode;
					$response["errText"] = "field '$fieldName' not valid";
					return $response;
				}
			}
			$securityFields = array(	"timeRestriction" => array(300,false,"[01]?",0),
							"bMon" => array(305,false,"[01]?",0),
							"bTue" => array(310,false,"[01]?",0),
							"bWed" => array(315,false,"[01]?",0),
							"bThu" => array(320,false,"[01]?",0),
							"bFri" => array(325,false,"[01]?",0),
							"bSat" => array(330,false,"[01]?",0),
							"bSun" => array(335,false,"[01]?",0),
							"timeFrom" => array(340,false,"[0-2]?[0-9]{1,1}[:.][0-5]{1,1}[0-9]{1,1}","06:30"),
							"timeUntil" => array(345,false,"[0-2]?[0-9]{1,1}[:.][0-5]{1,1}[0-9]{1,1}","17:30"),
							"ipRestriction" => array(350,false,"[01]?",0),
							"ipInclude1" => array(355,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"subnetInclude1" => array(360,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"ipInclude2" => array(365,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"subnetInclude2" => array(370,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"ipInclude3" => array(375,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"subnetInclude3" => array(380,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"ipInclude4" => array(385,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"subnetInclude4" => array(390,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"ipInclude5" => array(395,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""),
							"subnetInclude5" => array(400,false,"([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}",""));
			foreach($securityFields as $fieldName => $fieldParameters) {
				if(($errCode = $this->checkValidity($functionParameters[0]["securitySettings"][$fieldName],$fieldParameters[0],$fieldParameters[1],$fieldParameters[2])) !== true) {
					$response["success"] = false;
					$response["errCode"] = $errCode;
					$response["errText"] = "field '$fieldName' not valid";
					return $response;
				}
				if($functionParameters[0]["securitySettings"][$fieldName] == "") $functionParameters[0]["securitySettings"][$fieldName] = $fieldParameters[3];
			}
			foreach($functionParameters[0]["groupAssignments"] as $groupID) {
				if(($errCode = $this->checkValidity($groupID,200,true,"[1-9]{1,1}[0-9]{0,9}")) !== true) {
					$response["success"] = false;
					$response["errCode"] = $errCode;
					$response["errText"] = "field '$fieldName' not valid";
					return $response;
				}
			}

			$core_user_ID = $functionParameters[0]["core_user_ID"];
			$uid = $functionParameters[0]["uid"];
			$full_name = addslashes($functionParameters[0]["full_name"]);
			$email = addslashes($functionParameters[0]["email"]);
			$language = $functionParameters[0]["language"];
			$active = $functionParameters[0]["active"];
			$timeout_minutes = $functionParameters[0]["timeout_minutes"];
			$force_pwd_change = $functionParameters[0]["force_pwd_change"];
			$pwd_change_period = $functionParameters[0]["pwd_change_period"];
			$allow_pwd_change = $functionParameters[0]["allow_pwd_change"];

			$system_database_manager = system_database_manager::getInstance();
			$system_database_manager->executeUpdate("BEGIN", "saveSecurityUserSettings:start");

			if($core_user_ID > 0) {
				$system_database_manager->executeUpdate("UPDATE core_user SET uid='$uid', full_name='$full_name', email='$email', language='$language', active='$active', timeout_minutes='$timeout_minutes', force_pwd_change='$force_pwd_change', pwd_change_period_days='$pwd_change_period', allow_pwd_change='$allow_pwd_change', datetime_change=NOW(), core_user_ID_change=".session_control::getSessionInfo("id")." WHERE id=$core_user_ID", "saveSecurityUserSettings:update user data");
				$system_database_manager->executeUpdate("DELETE FROM core_user_group WHERE core_user_ID=$core_user_ID", "saveSecurityUserSettings:delete user group relations");
			}else{
				$system_database_manager->executeUpdate("INSERT INTO core_user(uid, pwd, full_name, email, plugin_settings, language, active, hidden, timeout_minutes, allow_pwd_change, force_pwd_change, pwd_change_period_days, datetime_pwd_change, datetime_change, core_user_ID_pwd_change, core_user_ID_change, datetime_create, core_user_ID_create, deleted, core_user_ID_delete, datetime_delete) VALUES('$uid', 'pwd', '$full_name', '$email', '', '$language', $active, 0, $timeout_minutes, $allow_pwd_change, $force_pwd_change, $pwd_change_period, '0000-00-00', NOW(), 0, ".session_control::getSessionInfo("id").", NOW(), ".session_control::getSessionInfo("id").", 0, 0, '0000-00-00')", "saveSecurityUserSettings:insert user data");
				$core_user_ID = $system_database_manager->getLastInsertId();
			}

			foreach($functionParameters[0]["groupAssignments"] as $groupID) {
				$system_database_manager->executeUpdate("INSERT INTO core_user_group(core_user_ID, core_group_ID) VALUES($core_user_ID, $groupID)", "saveSecurityUserSettings:insert user group relations");
			}

			foreach($functionParameters[0]["securitySettings"] as $fieldName => $value) {
				$this->setUserInformation($core_user_ID, "ExtendedSecurity", $fieldName, $value);
			}

			$system_database_manager->executeUpdate("COMMIT", "saveSecurityUserSettings:commit");

			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			return $response;
			break;
		case 'coreFunctions.deleteUser':
			if(($errCode = $this->checkValidity($functionParameters[0],200,true,"[1-9]{1,1}[0-9]{0,9}")) !== true) {
				$response["success"] = false;
				$response["errCode"] = $errCode;
				$response["errText"] = "user id not valid";
				return $response;
			}

			$core_user_ID = $functionParameters[0];

			$system_database_manager = system_database_manager::getInstance();
			$system_database_manager->executeUpdate("BEGIN", "saveSecurityUserSettings:start");

			$system_database_manager->executeUpdate("UPDATE core_user SET deleted=1, datetime_delete=NOW(), core_user_ID_delete=".session_control::getSessionInfo("id")." WHERE id=$core_user_ID", "deleteUser:update user data");
			$system_database_manager->executeUpdate("DELETE FROM core_user_group WHERE core_user_ID=$core_user_ID", "deleteUser:delete user group relations");

			$registryPath = "USERS/".$core_user_ID."/%";
			$system_database_manager->executeUpdate("DELETE FROM core_registry WHERE path LIKE '$registryPath'", "deleteAllUserInformation");


			$system_database_manager->executeUpdate("COMMIT", "saveSecurityUserSettings:commit");

			$response["success"] = true;
			$response["errCode"] = 0;
			$response["errText"] = "";
			return $response;
			break;
		case 'coreFunctions.terminateUserSession':
			// distinguish between terminating a single user session OR terminating ALL active sessions (except the "OWNER" session)
			break;
		case 'coreFunctions.getGroupInformation':
			if(count($functionParameters) != 3) {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "incorrect number of parameters";
			}
			$core_group_ID = $functionParameters[0];
			$registryPath = $functionParameters[1];
			$registryName = $functionParameters[2];

			$response = $this->getGroupInformation($core_group_ID, $registryPath, $registryName);
			return $response;
			break;
		case 'coreFunctions.getUserInformation':
			if(count($functionParameters) != 3) {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "incorrect number of parameters";
			}
			$core_user_ID = $functionParameters[0];
			$registryPath = $functionParameters[1];
			$registryName = $functionParameters[2];

			$response = $this->getUserInformation($core_user_ID, $registryPath, $registryName);
			return $response;
			break;
		case 'coreFunctions.setUserInformation':
			if(count($functionParameters) >= 4) {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "incorrect number of parameters";
			}
			$core_user_ID = $functionParameters[0];
			$registryPath = $functionParameters[1];
			$registryName = $functionParameters[2];
			$registryValue = $functionParameters[3];
			if(count($functionParameters) > 4) $registryType = $functionParameters[4]; //explicit type
			else $registryType = 0; //type auto-detect

			$response = $this->setUserInformation($core_user_ID, $registryPath, $registryName, $registryValue, $registryType);
			return $response;
			break;
		case 'coreFunctions.deleteUserInformation':
			if(count($functionParameters) != 3) {
				$response["success"] = false;
				$response["errCode"] = 101;
				$response["errText"] = "incorrect number of parameters";
			}
			$core_user_ID = $functionParameters[0];
			$registryPath = $functionParameters[1];
			$registryName = $functionParameters[2];

			$response = $this->deleteUserInformation($core_user_ID, $registryPath, $registryName);
			return $response;
			break;
		default:
			return "Funktion unbekannt";
			break;
		}
	}

	public function checkValidity($content,$errorBaseNumber,$mandatory,$regex) {
		if($mandatory && trim($content) == "") {
			return $errorBaseNumber;
		}

		if(trim($content) != "" && $regex!="") {
			if(preg_match('/^'.$regex.'$/i', $content)) {
			    //TRUE
			}else{
				return $errorBaseNumber+1;
			}
		}
		return true;
	}

	private function getGroupInformation($core_group_ID, $registryPath, $registryName) {
		return $this->getRegistryInformation($core_group_ID, $registryPath, $registryName, 'GROUPS');
	}

	private function getUserInformation($core_user_ID, $registryPath, $registryName) {
		return $this->getRegistryInformation($core_user_ID, $registryPath, $registryName, 'USERS');
	}

	private function getRegistryInformation($core_user_ID, $registryPath, $registryName, $registryBasePath) {
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
		if($registryName != "*") {
			if(preg_match( '/^[_0-9A-Za-z]+$/', $registryName)) {
			    //TRUE
				$getAllEntries = false;
			}else{
				$response["success"] = false;
				$response["errCode"] = 104;
				$response["errText"] = "invalid name";
				return $response;
			}
		}else $getAllEntries = true;

		$registryPath = $registryBasePath."/".$core_user_ID."/".$registryPath;
		$system_database_manager = system_database_manager::getInstance();
		if($getAllEntries) $result = $system_database_manager->executeQuery("SELECT name, value FROM core_registry WHERE path='".addslashes($registryPath)."' ORDER BY name", "getUserInformation");
		else $result = $system_database_manager->executeQuery("SELECT name, value FROM core_registry WHERE path='".addslashes($registryPath)."' AND name='".addslashes($registryName)."'", "getUserInformation");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["errText"] = "";
		if(count($result) > 0) $response["data"] = $getAllEntries ? $result : $result[0];
		else $response["data"] = array();
		return $response;
	}

	private function setGroupInformation($core_group_ID, $registryPath, $registryName, $registryValue, $registryType = 0) {
		return $this->setRegistryInformation($core_group_ID, $registryPath, $registryName, $registryValue, $registryType, 'GROUPS');
	}

	private function setUserInformation($core_user_ID, $registryPath, $registryName, $registryValue, $registryType = 0) {
		return $this->setRegistryInformation($core_user_ID, $registryPath, $registryName, $registryValue, $registryType, 'USERS');
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

		$registryPath = $registryBasePath."/".$core_user_ID."/".$registryPath; //$registryBasePath='USERS' or 'GROUPS'
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("REPLACE INTO core_registry(path,name,type,value) VALUES('$registryPath','$registryName','$registryType','".addslashes($registryValue)."')", "setUserInformation");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["errText"] = "";
		return $response;
	}

	private function deleteUserInformation($core_user_ID, $registryPath, $registryName, $recursiveDelete=true) {
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
		if($registryName != "*") {
			if(preg_match( '/^[_0-9A-Za-z]+$/', $registryName)) {
			    //TRUE
				$deleteAllEntries = false;
			}else{
				$response["success"] = false;
				$response["errCode"] = 104;
				$response["errText"] = "invalid name";
				return $response;
			}
		}else $deleteAllEntries = true;

		$registryPath = "USERS/".$core_user_ID."/".$registryPath;
		$system_database_manager = system_database_manager::getInstance();
		if($deleteAllEntries) $system_database_manager->executeUpdate("DELETE FROM core_registry WHERE ".($recursiveDelete ? "path LIKE '$registryPath%'" : "path='$registryPath'"), "deleteUserInformation");
		else $system_database_manager->executeUpdate("DELETE FROM core_registry WHERE path='$registryPath' AND name='$registryName'", "deleteUserInformation");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["errText"] = "";
		return $response;
	}

}

$SYS_PLUGIN["bl"]["coreFunctions"] = new coreFunctions_BL();
?>
