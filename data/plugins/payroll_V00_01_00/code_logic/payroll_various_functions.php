<?php
class variousFunctions {

	public function getCountryList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT core_intl_country.id, core_intl_country_names.country_name FROM core_intl_country,core_intl_country_names WHERE core_intl_country.id=core_intl_country_names.core_intl_country_ID AND core_intl_country_names.country_name_language='".session_control::getSessionInfo("language")."' ORDER BY core_intl_country_names.country_name", "payroll_getCountryList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}
	
	public function getNextCompanyId() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT max(id)+1 as nextCompanyId FROM payroll_company", "payroll.getNextCompanyId");
		return $result[0]['nextCompanyId'];
	}
		
	public function getCompanyList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_company", "payroll.getCompanyList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}
	
	public function deleteCompanyDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_company` WHERE id=".$param["id"], "payroll_deleteCompanyDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function saveCompanyDetail($param) {
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false),
					"company_shortname"=>array("regex"=>".{1,60}","addQuotes"=>true),
					"HR-RC-Name"=>array("regex"=>".{1,60}","addQuotes"=>true),
					"Street"=>array("regex"=>".{1,60}","addQuotes"=>true),
					"ZIP-Code"=>array("regex"=>"[0-9]{4,5}","addQuotes"=>true),
					"City"=>array("regex"=>".{1,50}","addQuotes"=>true),
					"UID-EHRA"=>array("regex"=>"[A-Z]{3}\-[0-9]{3}\.[0-9]{3}\.[0-9]{3}","addQuotes"=>true),
					"BUR-REE-Number"=>array("regex"=>".{0,45}","addQuotes"=>true),
				);
//					"UID-EHRA"=>array("regex"=>"CH-[0-9]{3}\.[0-9]{1}\.[0-9]{3}\.[0-9]{3}\-[0-9]{1}","addQuotes"=>true),

		///////////////////////////////////////////////////
		// Mandatory and validity checks
		///////////////////////////////////////////////////
		$errFields = array();
		foreach($fieldCfg as $fieldName=>$fieldParam) if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) $errFields[] = $fieldName;
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}

		$updateMode = isset($param["rid"]) && $param["rid"]!="" ? true : false;
		if($updateMode && !preg_match( '/^[0-9]{1,9}$/', $param["rid"])) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid record id";
			return $response;
		}

		if($updateMode) {
			$sqlUPDATE = array();
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_company` SET ".implode(",",$sqlUPDATE)." WHERE id=".$param["rid"];
		}else{
			$sqlFIELDS = array();
			$sqlVALUES = array();
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				$sqlFIELDS[] = "`".$fieldName."`";
				if($fieldParam["addQuotes"]) $sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
				else $sqlVALUES[] = addslashes($param[$fieldName]);
			}
			$sql = "INSERT INTO `payroll_company`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate($sql, "payroll_saveCompanyDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}


	public function getCompanyDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT payroll_company.id as rid,payroll_company.* FROM payroll_company WHERE id=".$param["id"], "payroll_getCompanyDetail");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}
		return $response;
	}


	public function getLanguageList($applicationSection="") {
		switch($applicationSection) {
		case 'UseForAccounts':
			$auxWHERE = " AND payroll_languages.UseForAccounts=1";
			break;
		default:
			$auxWHERE = "";
			break;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT payroll_languages.*,core_intl_language_names.language_name FROM payroll_languages INNER JOIN core_intl_language_names ON payroll_languages.core_intl_language_ID=core_intl_language_names.core_intl_language_ID WHERE core_intl_language_names.language_name_language='".session_control::getSessionInfo("language")."'".$auxWHERE." ORDER BY payroll_languages.DefaultLanguage DESC, core_intl_language_names.language_name", "payroll_getLanguageList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}

	public function getFormulaList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_formula`", "payroll_getFormulaList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}

	public function saveFormula($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid formula id";
			return $response;
		}

		$formulaAssembly = "";
		$testPassed = true;
		$criteriaTypeRegister = array(0,0);
		$invalidCriteriaTypeCombinations = array( array(1,1),array(2,2),array(2,4),array(1,3),array(3,2),array(4,1),array(4,3),array(3,4) );
		$openingBracketCount = 0;
		$closingBracketCount = 0;
		foreach($param["data"] as $rec) {
			if($rec=="factor" || $rec=="surcharge" || $rec=="quantity" || $rec=="rate" || $rec=="amount") $currentElementType = 1;
			else if($rec=="+" || $rec=="-" || $rec=="*" || $rec=="/") $currentElementType = 2;
			else if(preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $rec)) $currentElementType = 1;
			else if($rec=="(") $currentElementType = 3;
			else if($rec==")") $currentElementType = 4;
			else {
				//Element nicht zuordenbar -> also Fehler!
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "invalid formula element";
				return $response;
			}

			array_shift($criteriaTypeRegister);
			$criteriaTypeRegister[] = $currentElementType;
			if($currentElementType==3) $openingBracketCount++;
			else if($currentElementType==4) $closingBracketCount++;
			//checking for invalid CriteriaType combinations
			foreach($invalidCriteriaTypeCombinations as $compRegister) {
				if($criteriaTypeRegister == $compRegister) {
					$testPassed = false;
					$errCode = 200 + $compRegister[0]*10 + $compRegister[1];
					break;
				}
			}

			$formulaAssembly .= $rec;
			if(!$testPassed) break;
		}
		if($testPassed && $openingBracketCount != $closingBracketCount) {
			$testPassed = false;
			$errCode = 300;
		}
		if(!$testPassed) {
			$response["success"] = false;
			$response["errCode"] = $errCode;
			$response["errText"] = "error in formula element collection";
			return $response;
		}
		if(strlen($formulaAssembly)>128) {
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "formula too large";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		if($param["id"]>0) {
			$system_database_manager->executeUpdate("UPDATE `payroll_formula` SET `formula`='".$formulaAssembly."' WHERE `id`=".$param["id"], "payroll_saveFormula");
		}else{
			$system_database_manager->executeUpdate("INSERT INTO `payroll_formula`(`formula`) VALUES('".$formulaAssembly."')", "payroll_saveFormula");
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["data"] = "OK";
		return $response;
	}

	public function deleteFormula($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid formula id";
			return $response;
		}
		$system_database_manager = system_database_manager::getInstance();
		//Formel darf nur geloescht werden, wenn nicht mit LOAs verknaepft!
		$resCount = $system_database_manager->executeQuery("SELECT COUNT(*) as fcnt FROM `payroll_account` WHERE `payroll_formula_ID`=".$param["id"], "payroll_deleteFormula");
		//nur LOAs des aktuellen Jahres? --> vergangene Jahre interessieren nicht mehr, oder doch?
		if($resCount[0]["fcnt"]>0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "formula in use";
			return $response;
		}

		$system_database_manager->executeUpdate("DELETE FROM `payroll_formula` WHERE `id`=".$param["id"], "payroll_deleteFormula");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["data"] = "OK";
		return $response;
	}


	public function onBootComplete() {
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("Call payroll_prc_employee()", "payroll_onBootComplete");
		return true;
	}

	public function getAttendedTimeList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT lst.`ListItemToken` as attended_time_code, lbl.`label`, pat.`percentage`, pat.`attended_time` FROM `payroll_empl_list` lst LEFT JOIN `payroll_empl_list_label` lbl ON lbl.`payroll_empl_list_ID`=lst.`id` AND lbl.`language`='".session_control::getSessionInfo("language")."' LEFT JOIN `payroll_attended_time` pat ON pat.`id`=lst.`ListItemToken` WHERE lst.`ListGroup`=13 AND lst.`deleted`=0 AND lst.`ListItemToken`!='0' ORDER BY lst.`ListItemOrder`", "payroll_getAttendedTimeList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}

	public function saveAttendedTimeHours($param) {
		if(!isset($param["data"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "no data submitted";
		}
		$errFields = array();
		$sql = array();
		foreach($param["data"] as $row) {
			if(!isset($row["attended_time_code"]) || !preg_match( '/^[0-9]{1,2}$/', $row["attended_time_code"]) || !isset($row["attended_time"]) || !preg_match( '/^[0-9]{1,3}(\.[0-9]{1,2})$/', $row["attended_time"])) {
				$errFields[] = isset($row["attended_time_code"]) ? $row["attended_time_code"] : "";
			}else $sql[] = "UPDATE `payroll_attended_time` SET `attended_time`=".$row["attended_time"]." WHERE `id`=".$row["attended_time_code"];
		}

		if(count($errFields)!=0) {
			$errFields = array_unique($errFields);
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid field value";
			$response["attended_time_codes"] = $errFields;
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_saveAttendedTimeHours");
		foreach($sql as $s) $system_database_manager->executeUpdate($s, "payroll_saveAttendedTimeHours");
		$system_database_manager->executeUpdate("COMMIT", "payroll_saveAttendedTimeHours");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getCurrencyForexRate($curr) {
		$response = 0;
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT `forex_rate` FROM `payroll_currency` WHERE `core_intl_currency_ID` = '".$curr."' ;", "payroll_getCurrencyForexRate");
		return $result[0]["forex_rate"];
	}

	public function saveCurrencyForexRate($curr, $rate) {
		if(!preg_match( '/^[0-9]{1,9}$/', $curr)) {
			$rate = floatval(strip_tags($rate));
			if ($rate > 0.0001) {
				//communication_interface::alert("T setCurrencyForexRate($curr, $rate)");
				$system_database_manager = system_database_manager::getInstance();
				$result = $system_database_manager->executeUpdate("UPDATE `payroll_currency` SET `forex_rate` = '".$rate."'  WHERE `core_intl_currency_ID` = '".$curr."' ;", "payroll_setCurrencyForexRate");
				return true;
			} else {
				communication_interface::alert("Ungueltiger Wert ($curr, $rate)");
				return false;
			}
		} else {
			communication_interface::alert("Problem in setCurrencyForexRate($curr, $rate)");
			return false;
		}
	}


	public function getCurrencyList($param) {
		if(!isset($param["type"])) $param["type"] = "complete";
		if(!preg_match( '/^assigned|complete$/', $param["type"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid list type";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		if($param["type"] == "assigned") {
			$result = $system_database_manager->executeQuery("SELECT cl.`core_intl_currency_ID`,cn.`currency_name`,cl.`forex_rate`,cl.`default_currency` FROM `payroll_currency` cl INNER JOIN `core_intl_currency_names` cn ON cn.`core_intl_currency_ID`=cl.`core_intl_currency_ID` AND cn.`currency_name_language`='".session_control::getSessionInfo("language")."'", "payroll_getCurrencyList");
		}else{
			$result = $system_database_manager->executeQuery("SELECT `core_intl_currency_ID`, `currency_name` FROM `core_intl_currency_names` WHERE `currency_name_language`='".session_control::getSessionInfo("language")."' ORDER BY `core_intl_currency_ID`", "payroll_getCurrencyList");
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["data"] = $result;
		return $response;
	}

	public function saveCurrencyList($param) {
		if(!isset($param["mode"])) $param["mode"] = "replace";
		if(!preg_match( '/^replace$/', $param["mode"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid mode";
			return $response;
		}
		if(!isset($param["data"]) || !is_array($param["data"])) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "not a valid data array";
			return $response;
		}

		//check submitted currency array
		foreach($param["data"] as $curCurrency=>$curForexRate) {
			if(!preg_match( '/^[A-Z]{3,3}$/', $curCurrency) || !preg_match( '/^[0-9]{1,2}(\.[0-9]{1,5})?$/', $curForexRate)) {
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errCurrency"] = "CHF";
				$response["errText"] = "invalid currency item";
				return $response;
			}
		}

		$system_database_manager = system_database_manager::getInstance();
		switch($param["mode"]) {
		case 'replace':
			$system_database_manager->executeUpdate("BEGIN", "payroll_saveCurrencyList");
			$system_database_manager->executeUpdate("DELETE FROM `payroll_currency` WHERE `default_currency`=0", "payroll_saveCurrencyList");
			foreach($param["data"] as $curCurrency=>$curForexRate) $system_database_manager->executeUpdate("INSERT INTO `payroll_currency`(`core_intl_currency_ID`,`forex_rate`,`default_currency`) VALUES('".$curCurrency."',".$curForexRate.",0)", "payroll_saveCurrencyList");
			$system_database_manager->executeUpdate("COMMIT", "payroll_saveCurrencyList");
			break;
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function prepareCalculation($param) {
		$wageCodeChange = isset($param["wageCodeChange"]) && $param["wageCodeChange"]==0 ? "0" : "1";
		$wageBaseChange = isset($param["wageBaseChange"]) && $param["wageBaseChange"]==0 ? "0" : "1";
		$insuranceChange = isset($param["insuranceChange"]) && $param["insuranceChange"]==0  ? "0" : "1";
		$modifierChange = isset($param["modifierChange"]) && $param["modifierChange"]==0 ? "0" : "1";
		$workdaysChange = isset($param["workdaysChange"]) && $param["workdaysChange"]==0 ? "0" : "1";
		$pensiondaysChange = isset($param["pensiondaysChange"]) && $param["pensiondaysChange"]==0 ? "0" : "1";

		$uid = session_control::getSessionInfo("id");

		$system_database_manager = system_database_manager::getInstance();

		//get the id of the current period
		$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_processPayment");
		$payrollPeriodID = $result[0]["id"];

		$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_change_mng WHERE core_user_ID=".$uid, "payroll_prepareCalculation");
		$system_database_manager->executeUpdate("INSERT INTO payroll_tmp_change_mng(core_user_ID,numID,alphID) SELECT ".$uid.",`payroll_employee_ID`,'' FROM `payroll_period_employee` WHERE `payroll_period_ID`=".$payrollPeriodID." AND `processing`<2;", "payroll_prepareCalculation");
		$system_database_manager->executeUpdate("Call payroll_prc_empl_acc(".$uid.", 0, ".$wageCodeChange.", ".$wageBaseChange.", ".$insuranceChange.", ".$modifierChange.", ".$workdaysChange.", ".$pensiondaysChange.")", "payroll_prepareCalculation"); //userID INT, internalTransaction TINYINT, wageCodeChange TINYINT, wageBaseChange TINYINT, insuranceChange TINYINT, modifierChange TINYINT, workdaysChange TINYINT, pensiondaysChange TINYINT
		$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_change_mng WHERE core_user_ID=".$uid, "payroll_prepareCalculation");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	
}
?>
