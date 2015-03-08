<?php
class account {

	public function getPayrollAccountList() {
		///////////////////////////////////////////////////
		// get the current year
		// (if not configured yet, stop with an error code
		///////////////////////////////////////////////////
		$system_database_manager = system_database_manager::getInstance();
		$resYear = $system_database_manager->executeQuery("SELECT MAX(id) as currentYear FROM payroll_year", "payroll_getPayrollAccountList");
		if(count($resYear)!=1) {
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "error while querying current year";
			return $response;
		}
		$currentYear = $resYear[0]["currentYear"];

		$result = $system_database_manager->executeQuery("SELECT payroll_account.id,payroll_account.payroll_year_ID,payroll_account_label.label,payroll_account.sign,payroll_account.var_fields FROM payroll_account INNER JOIN payroll_account_label ON payroll_account.id=payroll_account_label.payroll_account_ID AND payroll_account.payroll_year_ID=payroll_account_label.payroll_year_ID WHERE payroll_account.payroll_year_ID=".$currentYear." AND payroll_account_label.language='".session_control::getSessionInfo("language")."'", "payroll_getPayrollAccountList");

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

	public function getPayrollAccountDetail($id) {
		///////////////////////////////////////////////////
		// account id must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $id)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid account id";
			return $response;
		}

		///////////////////////////////////////////////////
		// get the current year
		// (if not configured yet, stop with an error code)
		///////////////////////////////////////////////////
		$system_database_manager = system_database_manager::getInstance();
		$resYear = $system_database_manager->executeQuery("SELECT MAX(id) as currentYear FROM payroll_year", "payroll_getPayrollAccountDetail");
		if(count($resYear)!=1) {
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "error while querying current year";
			return $response;
		}
		$currentYear = $resYear[0]["currentYear"];

		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_account WHERE id='".addslashes($id)."' AND payroll_year_ID=".$currentYear, "payroll_getPayrollAccountDetail");
		$resTrnsl = $system_database_manager->executeQuery("SELECT language,label,quantity_unit,rate_unit FROM payroll_account_label WHERE payroll_account_ID='".addslashes($id)."' AND payroll_year_ID=".$currentYear, "payroll_getPayrollAccountDetail");
		foreach($resTrnsl as $trnsRow) {
			$result[0]["label_".$trnsRow["language"]] = $trnsRow["label"];
			$result[0]["quantity_unit_".$trnsRow["language"]] = $trnsRow["quantity_unit"];
			$result[0]["rate_unit_".$trnsRow["language"]] = $trnsRow["rate_unit"];
		}

		$incoming = $system_database_manager->executeQuery("SELECT payroll_account_ID as id, field_assignment, fwd_neg_values, invert_value, child_account_field FROM payroll_account_linker WHERE payroll_child_account_ID='".addslashes($id)."' AND payroll_year_ID=".$currentYear, "payroll_getPayrollAccountDetail");
		$target = $system_database_manager->executeQuery("SELECT payroll_child_account_ID as id, field_assignment, fwd_neg_values, invert_value, child_account_field FROM payroll_account_linker WHERE payroll_account_ID='".addslashes($id)."' AND payroll_year_ID=".$currentYear, "payroll_getPayrollAccountDetail");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
			$response["incomingAccounts"] = $incoming;
			$response["targetAccounts"] = $target;
		}
		return $response;
	}

	public function savePayrollAccount($id, $rawFieldData) {
		///////////////////////////////////////////////////
		// account id must be alphanumeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $id)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid account id";
			return $response;
		}

		///////////////////////////////////////////////////
		// get the current year
		// (if not configured yet, stop with an error code
		///////////////////////////////////////////////////
		$system_database_manager = system_database_manager::getInstance();
		$resYear = $system_database_manager->executeQuery("SELECT MAX(id) as currentYear FROM payroll_year", "payroll_savePayrollAccount");
		if(count($resYear)!=1) {
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "error while querying current year";
			return $response;
		}
		$currentYear = $resYear[0]["currentYear"];

		///////////////////////////////////////////////////
		// the account number (unique id) must not be changed
		// if there already are entries on this particular account
		///////////////////////////////////////////////////

		if(isset($rawFieldData["id"]) && preg_match( '/^[0-9a-zA-Z]{1,5}$/', $rawFieldData["id"])) {
			//The submitted field "id" must not be "0"
			if(is_numeric($rawFieldData["id"]) && intval($rawFieldData["id"])==0) {
				$response["success"] = false;
				$response["errCode"] = 11;
				$response["errText"] = "id must not be zero";
				return $response;
			}

			//Checking for duplicate IDs!
			if( $rawFieldData["id"] != $id ) {
				$resDblctChk = $system_database_manager->executeQuery("SELECT id FROM payroll_account WHERE id='".$rawFieldData["id"]."' AND payroll_year_ID=".$currentYear, "payroll_savePayrollAccount");
				if(count($resDblctChk)>0) {
					$response["success"] = false;
					$response["errCode"] = 12;
					$response["errText"] = "id already exists";
					return $response;
				}
			}
		}

		//set limits_aux_account_ID to ZERO if there are no limits to calculate
		if($rawFieldData["having_limits"]!="1") $rawFieldData["limits_aux_account_ID"]=0;

		//field definitions (used to check the incoming data)
		$fieldDef = array();
		$fieldDef[] = array("fieldName"=>"id", "type"=>"text", "rgx"=>"[0-9a-zA-Z]{1,5}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"label", "type"=>"translation", "rgx"=>".{0,50}", "mandatory"=>true, "tragetTable"=>"payroll_account_label");
		$fieldDef[] = array("fieldName"=>"max_limit", "type"=>"number", "rgx"=>"[0-9]{1,6}(\.[0-9]{1,2})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"deduction", "type"=>"number", "rgx"=>"[0-9]{1,6}(\.[0-9]{1,2})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"min_limit", "type"=>"number", "rgx"=>"[0-9]{1,6}(\.[0-9]{1,2})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"factor", "type"=>"number", "rgx"=>"-?[0-9]{1,8}(\.[0-9]{1,5})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"surcharge", "type"=>"number", "rgx"=>"-?[0-9]{1,8}(\.[0-9]{1,5})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"amount", "type"=>"number", "rgx"=>"-?[0-9]{1,8}(\.[0-9]{1,5})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"quantity", "type"=>"number", "rgx"=>"-?[0-9]{1,8}(\.[0-9]{1,5})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"rate", "type"=>"number", "rgx"=>"-?[0-9]{1,8}(\.[0-9]{1,5})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"round_param", "type"=>"number", "rgx"=>"[0-9]{1,2}(\.[0-9]{1,4})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"quantity_conversion", "type"=>"number", "rgx"=>"[0-9]{1,5}(\.[0-9]{1,4})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"quantity_unit", "type"=>"translation", "rgx"=>".{0,10}", "mandatory"=>false, "tragetTable"=>"payroll_account_label");
		$fieldDef[] = array("fieldName"=>"rate_conversion", "type"=>"number", "rgx"=>"[0-9]{1,5}(\.[0-9]{1,4})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"rate_unit", "type"=>"translation", "rgx"=>".{0,10}", "mandatory"=>false, "tragetTable"=>"payroll_account_label");
		$fieldDef[] = array("fieldName"=>"amount_conversion", "type"=>"number", "rgx"=>"[0-9]{1,5}(\.[0-9]{1,4})?", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"sign", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"print_account", "type"=>"number", "rgx"=>"[0123]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"var_fields", "type"=>"number", "rgx"=>"[01234567]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"quantity_decimal", "type"=>"number", "rgx"=>"[0-5]{1,1}|10", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"quantity_print", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"rate_decimal", "type"=>"number", "rgx"=>"[0-5]{1,1}|10", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"rate_print", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"amount_decimal", "type"=>"number", "rgx"=>"[0-5]{1,1}|10", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"amount_print", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"having_limits", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"having_calculation", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"having_rounding", "type"=>"bool", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"input_assignment", "type"=>"number", "rgx"=>"[0-5]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"output_assignment", "type"=>"number", "rgx"=>"[0345]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"limits_calc_mode", "type"=>"bool", "rgx"=>"[012]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"limits_aux_account_ID", "type"=>"number", "rgx"=>"[0-9a-zA-Z]{1,5}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"payroll_formula_ID", "type"=>"number", "rgx"=>"[0-9]{1,5}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"carry_over", "type"=>"number", "rgx"=>"[0345]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"bold", "type"=>"number", "rgx"=>"[01]{1,1}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"space_before", "type"=>"number", "rgx"=>"[0-9]{1,2}", "mandatory"=>true, "tragetTable"=>"payroll_account");
		$fieldDef[] = array("fieldName"=>"space_after", "type"=>"number", "rgx"=>"[0-9]{1,2}", "mandatory"=>true, "tragetTable"=>"payroll_account");

		//load language list
		$arrLanguagesUsed = array();
		$errFields = array();
		require_once('payroll_various_functions.php'); 
		$variousFunctions = new variousFunctions();
		$rt = $variousFunctions->getLanguageList("UseForAccounts");
		
		foreach($rt["data"] as $langRow) $arrLanguagesUsed[] = $langRow["core_intl_language_ID"];

		foreach($fieldDef as $currentFieldDef) {
			switch($currentFieldDef["type"]) {
			case "text":
				$dbFieldName = $currentFieldDef["fieldName"];
				if(isset($rawFieldData[$dbFieldName])) {
					$curRawValue = $rawFieldData[$dbFieldName];
					if(preg_match("/^".$currentFieldDef["rgx"]."$/", $curRawValue)) {
						$sqlFieldCollector[$currentFieldDef["tragetTable"]][$dbFieldName] = "'".addslashes($curRawValue)."'";
					}else $errFields[] = $dbFieldName;
				}else{
					if($currentFieldDef["mandatory"]) $errFields[] = $dbFieldName;
				}
				break;
			case "number":
			case "bool":
				$dbFieldName = $currentFieldDef["fieldName"];
				if(isset($rawFieldData[$dbFieldName])) {
					$curRawValue = $rawFieldData[$dbFieldName];
					if(preg_match("/^".$currentFieldDef["rgx"]."$/", $curRawValue)) {
						$sqlFieldCollector[$currentFieldDef["tragetTable"]][$dbFieldName] = addslashes($curRawValue);
					}else $errFields[] = $dbFieldName;
				}else{
					if($currentFieldDef["mandatory"]) $errFields[] = $dbFieldName;
				}
				break;
			case "translation":
				foreach($arrLanguagesUsed as $currentLanguageCode) {
					$dbFieldName = $currentFieldDef["fieldName"]."_".$currentLanguageCode;
					if(isset($rawFieldData[$dbFieldName])) {
						$curRawValue = $rawFieldData[$dbFieldName];
						if(preg_match("/^".$currentFieldDef["rgx"]."$/", $curRawValue)) {
							$sqlFieldCollector[$currentFieldDef["tragetTable"]][$currentLanguageCode][$currentFieldDef["fieldName"]] = "'".addslashes($curRawValue)."'";
						}else $errFields[] = $dbFieldName;
					}else{
						if($currentFieldDef["mandatory"]) $errFields[] = $dbFieldName;
					}
				}
/*
				$fragments = array();
				if(preg_match('/^([0-9]{1,4})[-\/.]?([0-9]{1,4})[-\/.]?([0-9]{1,4})$/', $dateStr, &$fragments)) $check = true;
				else $check = false;
(label_){1,1}([a-z]{2,2})
*/
				break;
			}
		}
		$errFields = array_unique($errFields); //remove duplicate field names (if any)
//error_log("\n\n".print_r($sqlFieldCollector,true)."\n\n", 3, "/var/log/copronet-application.log");
		if(count($errFields)>0) {
//error_log("\nerr20: ".print_r($errFields,true)."\n", 3, "/var/log/copronet-application.log");
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid or missing fields";
			$response["fieldNames"] = $errFields;
			return $response;
		}

/*
$("#loacInpLOA option").each(function(index) { ip.push([$(this).attr('value'),$(this).attr('fwdfield'),$(this).attr('fwdnegval'),$(this).attr('invertval'),$(this).attr('targetfield')]); });
jsonItem["inploa"] = ip;
*/
		$inpoutSections = array("inploa", "outloa");
		$inpoutChecks = array("[0-9a-zA-Z]{1,5}", "[345]{1,1}", "[01]{1,1}", "[01]{1,1}", "[012345]{1,1}");
		$inpoutRecs = array();
		$errFields = array();
		$errSections = array();
		foreach($inpoutSections as $currentSection) {
			foreach($rawFieldData[$currentSection] as $rawFieldRow) {
				for($i=0;$i<5;$i++) if(!preg_match("/^".$inpoutChecks[$i]."$/", $rawFieldRow[$i])) { $errFields[] = $i; $errSections[] = $currentSection; }

				if($currentSection=="outloa") $inpoutRecs[] = array("@", $currentYear, "'".$rawFieldRow[0]."'", $rawFieldRow[1], $rawFieldRow[2], $rawFieldRow[3], $rawFieldRow[4]);
				else $inpoutRecs[] = array("'".$rawFieldRow[0]."'", $currentYear, "@", $rawFieldRow[1], $rawFieldRow[2], $rawFieldRow[3], $rawFieldRow[4]);
			}
		}
		$errFields = array_unique($errFields); //remove duplicate field names (if any)
		$errSections = array_unique($errSections); //remove duplicate field names (if any)
		if(count($errFields)>0) {
//error_log("\nerr30: ".print_r($errSections,true).print_r($errFields,true)."\n", 3, "/var/log/copronet-application.log");
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "invalid or missing fields in input/output assignments";
			$response["sections"] = $errSections;
			$response["fieldIndex"] = $errFields;
			return $response;
		}

		if($id!="0") {	//UPDATE existing data
/*
UPDATE (id=12345)
UPDATE payroll_account SET `id`='12345',`max_limit`=0.00,`deduction`=0.00,`min_limit`=0.00,`factor`=0.00000,`surcharge`=0.00000,`quantity`=0.00000,`rate`=0.00000,`round_param`=0.0000,`quantity_conversion`=1.0000,`rate_conversion`=1.0000,`amount_conversion`=1.0000,`sign`=0,`print_account`=0,`var_fields`=0,`quantity_decimal`=10,`quantity_print`=1,`rate_decimal`=10,`rate_print`=1,`amount_decimal`=10,`amount_print`=1,`having_limits`=0,`having_calculation`=1,`having_rounding`=0,`input_assignment`=5,`output_assignment`=3,`limits_calc_mode`=0,`limits_aux_account_ID`=0,`payroll_formula_ID`=1 WHERE id='12345' AND payroll_year_ID=2012
DELETE FROM payroll_account_label WHERE id='12345' AND payroll_year_ID=2012
*/
//error_log("\n\n\nUPDATE (id=".$id.")\n", 3, "/var/log/copronet-application.log");
			$system_database_manager->executeUpdate("BEGIN", "payroll_savePayrollAccount");
//error_log("DONE.\n", 3, "/var/log/copronet-application.log");
			$sqlFields = array();
			$escapedExistingID = "'".addslashes($id)."'";
			$escapedNewID = "";
			foreach($sqlFieldCollector["payroll_account"] as $fieldName=>$fieldValue) {
				if($fieldName=="id" && $fieldValue!=$escapedExistingID) $escapedNewID = $fieldValue;
				$sqlFields[] = "`".$fieldName."`=".$fieldValue;
			}
			$sql = "UPDATE payroll_account SET ".implode(",", $sqlFields)." WHERE id=".$escapedExistingID." AND payroll_year_ID=".$currentYear;
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
			$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
//error_log("DONE.\n", 3, "/var/log/copronet-application.log");

			$sql = "DELETE FROM payroll_account_linker WHERE (payroll_account_ID=".$escapedExistingID." OR payroll_child_account_ID=".$escapedExistingID.") AND payroll_year_ID=".$currentYear;
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
			$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
//error_log("DONE.\n", 3, "/var/log/copronet-application.log");
			$sql = "DELETE FROM payroll_account_label WHERE payroll_account_ID=".$escapedExistingID." AND payroll_year_ID=".$currentYear;
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
			$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
//error_log("DONE.\n", 3, "/var/log/copronet-application.log");
			if($escapedNewID!="") $escapedExistingID=$escapedNewID;

			foreach($sqlFieldCollector["payroll_account_label"] as $languageCode=>$fieldset) {
				$sqlFields = array();
				$sqlValues = array();
				$sqlFields[] = "`payroll_account_ID`";
				$sqlValues[] = $escapedExistingID;
				$sqlFields[] = "`payroll_year_ID`";
				$sqlValues[] = $currentYear;
				$sqlFields[] = "`language`";
				$sqlValues[] = "'".$languageCode."'";
				foreach($fieldset as $fieldName=>$fieldValue) {
					$sqlFields[] = "`".$fieldName."`";
					$sqlValues[] = $fieldValue;
				}
				$sql = "INSERT INTO payroll_account_label(".implode(",", $sqlFields).") VALUES(".implode(",", $sqlValues).")";
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
				$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
//error_log("DONE.\n", 3, "/var/log/copronet-application.log");
			}

			$sqlValues = array();
			foreach($inpoutRecs as $fieldValues) {
				$sqlValues[] = "(".implode(",", $fieldValues).")";
			}
			if(count($sqlValues)>0) {
				$sql = "INSERT INTO payroll_account_linker(payroll_account_ID, payroll_year_ID, payroll_child_account_ID, field_assignment, fwd_neg_values, invert_value, child_account_field) VALUES".implode(",", $sqlValues);
				$sql = str_replace("@",$escapedExistingID,$sql);
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
				$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
//error_log("DONE.\n", 3, "/var/log/copronet-application.log");
			}

			$system_database_manager->executeUpdate("COMMIT", "payroll_savePayrollAccount");
		}else{	//ADD/INSERT new data
//error_log("\n\n\nINSERT (id=".$id.")", 3, "/var/log/copronet-application.log");
		$system_database_manager->executeUpdate("BEGIN", "payroll_savePayrollAccount");
			$sqlFields = array();
			$sqlValues = array();
			$escapedNewID = "";
			$sqlFields[] = "`payroll_year_ID`";
			$sqlValues[] = $currentYear;
			foreach($sqlFieldCollector["payroll_account"] as $fieldName=>$fieldValue) {
				if($fieldName=="id" && $fieldValue!=$escapedExistingID) $escapedNewID = $fieldValue;
				$sqlFields[] = "`".$fieldName."`";
				$sqlValues[] = $fieldValue;
			}
			$sql = "INSERT INTO payroll_account(".implode(",", $sqlFields).") VALUES(".implode(",", $sqlValues).")";
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
			$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");

//			$sql = "DELETE FROM payroll_account_label WHERE id=".$escapedNewID." AND payroll_year_ID=".$currentYear; // <--- nicht noetig: wir muessen davon ausgehen, dass diese Recs bei Neuanlage noch nicht exisiterien
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
//			$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");

			foreach($sqlFieldCollector["payroll_account_label"] as $languageCode=>$fieldset) {
				$sqlFields = array();
				$sqlValues = array();
				$sqlFields[] = "`payroll_account_ID`";
				$sqlValues[] = $escapedNewID;
				$sqlFields[] = "`payroll_year_ID`";
				$sqlValues[] = $currentYear;
				$sqlFields[] = "`language`";
				$sqlValues[] = "'".$languageCode."'";
				foreach($fieldset as $fieldName=>$fieldValue) {
					$sqlFields[] = "`".$fieldName."`";
					$sqlValues[] = $fieldValue;
				}
				$sql = "INSERT INTO payroll_account_label(".implode(",", $sqlFields).") VALUES(".implode(",", $sqlValues).")";
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
				$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
			}

			$sqlValues = array();
			foreach($inpoutRecs as $fieldValues) {
				$sqlValues[] = "(".implode(",", $fieldValues).")";
			}
			if(count($sqlValues)>0) {
				$sql = "INSERT INTO payroll_account_linker(payroll_account_ID, payroll_year_ID, payroll_child_account_ID, field_assignment, fwd_neg_values, invert_value, child_account_field) VALUES".implode(",", $sqlValues);
				$sql = str_replace("@",$escapedNewID,$sql);
//error_log($sql."\n", 3, "/var/log/copronet-application.log");
				$system_database_manager->executeUpdate($sql, "payroll_savePayrollAccount");
			}

			$system_database_manager->executeUpdate("COMMIT", "payroll_savePayrollAccount");
		}
//		$dmy = $system_database_manager->executeQuery("Call payroll_prc_group_accounts()", "payroll_savePayrollAccount");
		$system_database_manager->executeUpdate("Call payroll_prc_group_accounts()", "payroll_savePayrollAccount");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deletePayrollAccount($param) {
		///////////////////////////////////////////////////
		// account id must be alphanumeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid account id";
			return $response;
		}
		$payrollAccountID = $param["id"];

		///////////////////////////////////////////////////
		// get the current year
		// (if not configured yet, stop with an error code
		///////////////////////////////////////////////////
		$system_database_manager = system_database_manager::getInstance();
		$resYear = $system_database_manager->executeQuery("SELECT MAX(id) as currentYear FROM payroll_year", "payroll_getPayrollAccountList");
		if(count($resYear)!=1) {
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "error while querying current year";
			return $response;
		}
		$currentYear = $resYear[0]["currentYear"];

		$system_database_manager->executeUpdate("BEGIN", "payroll_deletePayrollAccount");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_account_label` WHERE `payroll_year_ID`=".$currentYear." AND `payroll_account_ID`='".$payrollAccountID."'", "payroll_deletePayrollAccount");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_account_linker` WHERE `payroll_year_ID`=".$currentYear." AND `payroll_account_ID`='".$payrollAccountID."'", "payroll_deletePayrollAccount");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_account_linker` WHERE `payroll_year_ID`=".$currentYear." AND `payroll_child_account_ID`='".$payrollAccountID."'", "payroll_deletePayrollAccount");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_account` WHERE `payroll_year_ID`=".$currentYear." AND `id`='".$payrollAccountID."'", "payroll_deletePayrollAccount");
		$system_database_manager->executeUpdate("COMMIT", "payroll_deletePayrollAccount");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function copyPayrollAccount($param) {
		if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $param["src"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid account id (source)";
			return $response;
		}
		if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $param["dest"])) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid account id (destination)";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$resYear = $system_database_manager->executeQuery("SELECT MAX(id) as currentYear FROM payroll_year", "payroll_copyPayrollAccount");
		if(count($resYear)!=1) {
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "error while querying current year";
			return $response;
		}
		$currentYear = $resYear[0]["currentYear"];

		$srcAccExists = $system_database_manager->executeQuery("SELECT COUNT(`id`) as myCount FROM `payroll_account` WHERE `id`='".$param["src"]."' AND `payroll_year_ID`=".$currentYear, "payroll_copyPayrollAccount");
		if( $srcAccExists[0]["myCount"] != 1 ) {
			$response["success"] = false;
			$response["errCode"] = 40;
			$response["errText"] = "source account does not exist";
			return $response;
		}
		$destAccExists = $system_database_manager->executeQuery("SELECT COUNT(`id`) as myCount FROM `payroll_account` WHERE `id`='".$param["dest"]."' AND `payroll_year_ID`=".$currentYear, "payroll_copyPayrollAccount");
		if( $destAccExists[0]["myCount"] != 0 ) {
			$response["success"] = false;
			$response["errCode"] = 50;
			$response["errText"] = "destination account already exists";
			return $response;
		}

		$system_database_manager->executeUpdate("BEGIN", "payroll_copyPayrollAccount");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_account`(`id`, `payroll_year_ID`, `processing_order`, `sign`, `print_account`, `var_fields`, `input_assignment`, `output_assignment`, `having_limits`, `having_calculation`, `having_rounding`, `payroll_formula_ID`, `surcharge`, `factor`, `quantity`, `rate`, `amount`, `round_param`, `limits_aux_account_ID`, `limits_calc_mode`, `max_limit`, `min_limit`, `deduction`, `quantity_conversion`, `quantity_decimal`, `quantity_print`, `rate_conversion`, `rate_decimal`, `rate_print`, `amount_conversion`, `amount_decimal`, `amount_print`, `mandatory`, `carry_over`, `insertion_rules`, `bold`, `space_before`, `space_after`) SELECT '".$param["dest"]."', `payroll_year_ID`, `processing_order`, `sign`, `print_account`, `var_fields`, `input_assignment`, `output_assignment`, `having_limits`, `having_calculation`, `having_rounding`, `payroll_formula_ID`, `surcharge`, `factor`, `quantity`, `rate`, `amount`, `round_param`, `limits_aux_account_ID`, `limits_calc_mode`, `max_limit`, `min_limit`, `deduction`, `quantity_conversion`, `quantity_decimal`, `quantity_print`, `rate_conversion`, `rate_decimal`, `rate_print`, `amount_conversion`, `amount_decimal`, `amount_print`, `mandatory`, `carry_over`, `insertion_rules`, `bold`, `space_before`, `space_after` FROM `payroll_account` WHERE id='".$param["src"]."' AND `payroll_year_ID`=".$currentYear, "payroll_copyPayrollAccount");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_account_label`(`payroll_account_ID`, `payroll_year_ID`, `language`, `label`, `quantity_unit`, `rate_unit`) SELECT '".$param["dest"]."', `payroll_year_ID`, `language`, `label`, `quantity_unit`, `rate_unit` FROM `payroll_account_label` WHERE `payroll_account_ID`='".$param["src"]."' AND `payroll_year_ID`=".$currentYear, "payroll_copyPayrollAccount");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_account_linker`(`payroll_account_ID`, `payroll_year_ID`, `payroll_child_account_ID`, `field_assignment`, `fwd_neg_values`, `invert_value`, `child_account_field`) SELECT '".$param["dest"]."', `payroll_year_ID`, `payroll_child_account_ID`, `field_assignment`, `fwd_neg_values`, `invert_value`, `child_account_field` FROM `payroll_account_linker` WHERE `payroll_account_ID`='".$param["src"]."' AND `payroll_year_ID`=".$currentYear, "payroll_copyPayrollAccount");
		$system_database_manager->executeUpdate("COMMIT", "payroll_copyPayrollAccount");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getPayrollAccountMappingList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_account_mapping", "payroll_getPayrollAccountMappingList");

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

	public function getPayrollAccountMappingDetail($param) {
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
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_account_mapping WHERE id=".$param["id"], "payroll_getPayrollAccountMappingDetail");

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


	public function savePayrollAccountMappingDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			$response["fieldNames"] = array("id");
			return $response;
		}
		///////////////////////////////////////////////////
		// account id must be alphanumeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $param["payroll_account_ID"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid account id";
			$response["fieldNames"] = array("payroll_account_ID");
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("UPDATE `payroll_account_mapping` SET `payroll_account_ID`='".$param["payroll_account_ID"]."' WHERE `id`=".$param["id"], "payroll_savePayrollAccountMappingDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}




	public function getDedAtSrcGlobalSettings() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_das_account` WHERE `AccountType`>2 AND `DedAtSrcCanton`='' ORDER BY `AccountType`", "payroll_getDedAtSrcGlobalSettings");
		$res = array();
		foreach($result as $rec) {
			$res["AccountType".$rec["AccountType"]] = $rec["payroll_account_ID"];
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["data"] = $res;
		return $response;
	}

	public function saveDedAtSrcGlobalSettings($param) {
		$arrAccountType = array(3,4,5,6,7,9,11,12,13,14,15,16,17,18);
//communication_interface::alert(print_r($param,true));

		$errFields = array();
		$updateStatements = array();
		foreach($arrAccountType as $curAccountType) {
			$curFieldName = "AccountType".$curAccountType;
			if(!preg_match( '/^[a-zA-Z0-9]{1,5}$/', $param[$curFieldName])) $errFields[] = $curFieldName;
			$updateStatements[] = "UPDATE `payroll_das_account` SET `payroll_account_ID`='".$param[$curFieldName]."' WHERE `AccountType`=".$curAccountType." AND `DedAtSrcCanton`=''";
		}
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}
//communication_interface::alert(print_r($updateStatements,true));

		$system_database_manager = system_database_manager::getInstance();
		foreach($updateStatements as $sql) $system_database_manager->executeUpdate($sql, "payroll_saveDedAtSrcGlobalSettings");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getDedAtSrcCantonDetail($param=null) {
		if(is_null($param)) {
			//ohne Parameter werden alle verfaegbaren Kantone gelistet
			$sqlAux = "";
		}else{
			if(!preg_match( '/^[a-zA-Z]{2,2}$/', $param["id"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "invalid id";
				return $response;
			}
			$sqlAux = " WHERE dascnt.`DedAtSrcCanton`='".strtoupper($param["id"])."'";
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery(
		"SELECT dascnt.`DedAtSrcCanton` as id, dascnt.*
		, MAX(IF(dasacc.`AccountType`=1,dasacc.`payroll_account_ID`,'')) as dasacc1
		, MAX(IF(dasacc.`AccountType`=2,dasacc.`payroll_account_ID`,'')) as dasacc2 
		FROM `payroll_das_canton` dascnt 
		INNER JOIN `payroll_das_account` dasacc ON dascnt.`DedAtSrcCanton`=dasacc.`DedAtSrcCanton`".$sqlAux." 
		GROUP BY dascnt.`DedAtSrcCanton`", "payroll_getFieldModifierDetail");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = is_null($param) ? $result : $result[0];
		}
		return $response;
	}

	public function saveDedAtSrcCantonDetail($param) {
//communication_interface::alert("saveBL: ".print_r($param,true));
		if(trim($param["id"])=="" || trim($param["id"])=="0") {
			$updateMode = false;
		}else{
			if(!preg_match( '/^[a-zA-Z]{2,2}$/', $param["id"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "invalid id";
				return $response;
			}
			$updateMode = true;
			$param["DedAtSrcCanton"] = $param["id"];
		}
		$fieldCfg = array(
					"DedAtSrcCanton"=>array("regex"=>"[a-zA-Z]{2,2}","addQuotes"=>true, "targetTable"=>"payroll_das_canton"),
					"TaxArbeitgebernummer"=>array("regex"=>".{1,50}","addQuotes"=>true, "targetTable"=>"payroll_das_canton"),
					"TaxAdminName"=>array("regex"=>".{1,50}","addQuotes"=>true, "targetTable"=>"payroll_das_canton"),
					"TaxAdminStreet"=>array("regex"=>".{1,50}","addQuotes"=>true, "targetTable"=>"payroll_das_canton"),
					"TaxAdminZIP"=>array("regex"=>"[0-9]{4,4}","addQuotes"=>true, "targetTable"=>"payroll_das_canton"),
					"TaxAdminCity"=>array("regex"=>".{1,50}","addQuotes"=>true, "targetTable"=>"payroll_das_canton"),
					"AnnualSettlementMode"=>array("regex"=>"[01]{1,1}","addQuotes"=>false, "targetTable"=>"payroll_das_canton"),
					"DaysPerMonth"=>array("regex"=>"[01]{1,1}","addQuotes"=>false, "targetTable"=>"payroll_das_canton"),
					"commission"=>array("regex"=>"[0-9]{1,2}(\.[0-9]{1,4})?","addQuotes"=>false, "targetTable"=>"payroll_das_canton"),
					"dasacc1"=>array("regex"=>"[a-zA-Z0-9]{0,5}","addQuotes"=>true, "targetTable"=>"payroll_das_account", "AccountType"=>"1"),
					"dasacc2"=>array("regex"=>"[a-zA-Z0-9]{1,5}","addQuotes"=>true, "targetTable"=>"payroll_das_account", "AccountType"=>"2")
				);

		///////////////////////////////////////////////////
		// Mandatory and validity checks
		///////////////////////////////////////////////////
		$errFields = array();
		foreach($fieldCfg as $fieldName=>$fieldParam) if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) $errFields[] = $fieldName;
		if($param["AnnualSettlementMode"]=="1" && $param["dasacc1"]=="") $errFields[] = "dasacc1";
		$errFields = array_unique($errFields);
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}

		if($updateMode) {
			$sqlUPDATE = array();
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["targetTable"]=="payroll_das_canton") {
					if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
					else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
				}
			}
			$sql = "UPDATE `payroll_das_canton` SET ".implode(",",$sqlUPDATE)." WHERE `DedAtSrcCanton`='".addslashes($param["DedAtSrcCanton"])."'";
		}else{
			$sqlFIELDS = array();
			$sqlVALUES = array();
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["targetTable"]=="payroll_das_canton") {
					$sqlFIELDS[] = "`".$fieldName."`";
					if($fieldParam["addQuotes"]) $sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
					else $sqlVALUES[] = addslashes($param[$fieldName]);
				}
			}
			$sql = "INSERT INTO `payroll_das_canton`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("BEGIN", "payroll_saveDedAtSrcCantonDetail");
		$result = $system_database_manager->executeUpdate($sql, "payroll_saveDedAtSrcCantonDetail");
//communication_interface::alert("sql: ".$sql);

		foreach($fieldCfg as $fieldName=>$fieldParam) {
			if($fieldParam["targetTable"]=="payroll_das_account") {
				$sql = "DELETE FROM `payroll_das_account` WHERE `DedAtSrcCanton`='".addslashes($param["DedAtSrcCanton"])."' AND `AccountType`=".$fieldParam["AccountType"];
				$result = $system_database_manager->executeUpdate($sql, "payroll_saveDedAtSrcCantonDetail");
				$sql = "INSERT INTO `payroll_das_account`(`payroll_account_ID`,`DedAtSrcCanton`,`AccountType`) VALUES('".addslashes($param[$fieldName])."','".addslashes($param["DedAtSrcCanton"])."',".$fieldParam["AccountType"].")";
				$result = $system_database_manager->executeUpdate($sql, "payroll_saveDedAtSrcCantonDetail");
			}
		}
		$result = $system_database_manager->executeUpdate("COMMIT", "payroll_saveDedAtSrcCantonDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteDedAtSrcCantonDetail($param) {
		if(!preg_match( '/^[a-zA-Z]{2,2}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$resYear = $system_database_manager->executeQuery("SELECT MAX(id) as currentYear FROM payroll_year", "payroll_deleteDedAtSrcCantonDetail");
		if(count($resYear)!=1) {
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "error while querying current year";
			return $response;
		}
		$currentYear = $resYear[0]["currentYear"];

		//Gibt es im aktuellen Jahr bereits berechnete/verbuchte/ausbezahlte/fixierte Abrechnungen mit dem entsprechenden Kanton? Falls ja, darf der Kanton nicht geloescht werden!
		$resPrd = $system_database_manager->executeQuery("SELECT 1 FROM `payroll_period_employee` pemp INNER JOIN `payroll_period` prd ON prd.`id`=pemp.`payroll_period_ID` AND prd.`payroll_year_ID`=".$currentYear." WHERE pemp.`processing`>0 AND pemp.`DedAtSrcCanton`='".$param["id"]."' LIMIT 1");
		if(count($resPrd)!=0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "canton in use";
			return $response;
		}

		$system_database_manager->executeUpdate("BEGIN", "payroll_deleteDedAtSrcCantonDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_das_account` WHERE `DedAtSrcCanton`='".addslashes($param["id"])."' AND `AccountType` IN (1,2)", "payroll_deleteDedAtSrcCantonDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_das_canton` WHERE `DedAtSrcCanton`='".addslashes($param["id"])."'", "payroll_deleteDedAtSrcCantonDetail");
		$system_database_manager->executeUpdate("COMMIT", "payroll_deleteDedAtSrcCantonDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function importDedAtSrcRates($param) {
		if(!preg_match( '/^[a-zA-Z0-9]{32,32}$/', $param["tmpDirToken"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid token";
			return $response;
		}
		if(!preg_match( '/^[-._a-zA-Z0-9]{5,32}$/', $param["fileName"])) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid file name";
			return $response;
		}
		$validCantons = array("AG", "AI", "AR", "BE", "BL", "BS", "FR", "GE", "GL", "GR", "JU", "LU", "NE", "NW", "OW", "SG", "SH", "SO", "SZ", "TG", "TI", "UR", "VD", "VS", "ZG", "ZH");

		
		$fm = new file_manager();
		if( $fm->setTmpDir($param["tmpDirToken"]) ) {
			$arr = explode(".",$param["fileName"]);
			if(strtolower($arr[count($arr)-1])!="txt") {
				$fm->deleteDir(); //weil falsche Dateierweiterung, kann TmpDir gleich wieder geloescht werden
				//Fehler Dateiendung stimmt nicht!
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errText"] = "wrong file extension";
				return $response;
			}else{
				$response["success"] = false;
				$response["errCode"] = 40;
				$response["errText"] = "";
				//soweit ist alles OK -> wir rufen jetzt die BL-Funktion fuer den Import der Tarifdatei auf
				$system_database_manager = system_database_manager::getInstance();
				$system_database_manager->executeUpdate("TRUNCATE TABLE `payroll_tmp_qst_import`", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("BEGIN", "payroll_importDedAtSrcRates");

				$system_database_manager->executeUpdate("LOAD DATA INFILE '".$fm->getFullPath().$param["fileName"]."' INTO TABLE `payroll_tmp_qst_import` LINES TERMINATED BY '\n' STARTING BY '' IGNORE 1 LINES (@var1) SET transaction_type=SUBSTR(@var1,4,1), canton=SUBSTR(@var1,5,2), rate_code=SUBSTR(@var1,7,10), canton=SUBSTR(@var1,5,2), date_from=str_to_date(SUBSTR(@var1,17,8),'%Y%m%d'), taxable_income=SUBSTR(@var1,25,9)/100, step_rate=SUBSTR(@var1,34,9)/100, sex=SUBSTR(@var1,43,1), children=SUBSTR(@var1,45,1), tax_amount=SUBSTR(@var1,46,9)/100, tax_rate=SUBSTR(@var1,55,5)", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_qst_import WHERE rate_code=''", "payroll_importDedAtSrcRates");

				$fm->deleteDir(); //sobald die Datei eingelesen ist, wird sie nicht mehr benoetigt

				
				//Wurden ueberhaupt Daten eingelesen?
				$res = $system_database_manager->executeQuery("SELECT COUNT(*) as rateCount FROM `payroll_tmp_qst_import`", "payroll_processPayment");
				if($res[0]["rateCount"]==0) {
					$response["success"] = false;
					$response["errCode"] = 50;
					$response["errText"] = "no import data (empty table)";
					return $response;
				}

				//Kanton auslesen -> es darf nur 1 Kanton sein -> der Kantonscode muss gueltig sein
				$res = $system_database_manager->executeQuery("SELECT DISTINCT canton FROM `payroll_tmp_qst_import`", "payroll_processPayment");
				if(count($res)!=1) {
					$response["success"] = false;
					$response["errCode"] = 60;
					$response["errText"] = "wrong number of cantons (allowed: one)";
					return $response;
				}else{
					if(!in_array(strtoupper($res[0]["canton"]), $validCantons)) {
						$response["success"] = false;
						$response["errCode"] = 70;
						$response["errText"] = "invalid canton token";
						return $response;
					}
					$response["canton"] = $res[0]["canton"];
				}

				// -> es koennen nur Daten eingelesen werden, wenn der Kanton konfiguriert wurde
				$res = $system_database_manager->executeQuery("SELECT dascant.`DedAtSrcCanton` FROM `payroll_tmp_qst_import` dasimp INNER JOIN `payroll_das_canton` dascant ON dasimp.`canton`=dascant.`DedAtSrcCanton` LIMIT 1", "payroll_processPayment");
				if(count($res)==0) {
					$response["success"] = false;
					$response["errCode"] = 80;
					$response["errText"] = "canton not yet configured";
					return $response;
				}

				$system_database_manager->executeUpdate("UPDATE payroll_tmp_qst_import SET sex='F' WHERE sex='w'", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("UPDATE payroll_tmp_qst_import SET sex='M' WHERE sex='m'", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("DELETE taxrates FROM payroll_das_tax_rates taxrates INNER JOIN (SELECT canton FROM payroll_tmp_qst_import LIMIT 1) tmpct ON taxrates.DedAtSrcCanton=tmpct.canton", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("INSERT INTO `payroll_das_tax_rates`
															(`DedAtSrcCanton`,`DedAtSrcCode`,`Sex`,`IncomeFrom`,`IncomeTo`,`amount`,`rate`) 
															SELECT canton,rate_code,sex,taxable_income,taxable_income+step_rate,tax_amount,tax_rate FROM payroll_tmp_qst_import WHERE taxable_income<20000", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("INSERT INTO `payroll_das_tax_rates`
															(`DedAtSrcCanton`,`DedAtSrcCode`,`Sex`,`IncomeFrom`,`IncomeTo`,`amount`,`rate`) 
															SELECT canton,rate_code,sex,MIN(taxable_income),MAX(taxable_income)+step_rate,IF(COUNT(*)=1,tax_amount,0),tax_rate 
															FROM payroll_tmp_qst_import WHERE taxable_income>19999 GROUP BY canton,rate_code,sex,tax_rate", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("UPDATE `payroll_das_tax_rates` dest INNER JOIN (SELECT `DedAtSrcCanton`,`DedAtSrcCode`,`Sex`,MAX(`IncomeFrom`) as maxIncomeFrom FROM `payroll_das_tax_rates` 
															WHERE `DedAtSrcCanton`='".$response["canton"]."' 
															GROUP BY `DedAtSrcCanton`,`DedAtSrcCode`,`Sex`) src ON src.`DedAtSrcCanton`=dest.`DedAtSrcCanton` AND src.`DedAtSrcCode`=dest.`DedAtSrcCode` AND src.`Sex`=dest.`Sex` AND src.`maxIncomeFrom`=dest.`IncomeFrom` 
															SET dest.`IncomeTo`=99999999", "payroll_importDedAtSrcRates"); //das Maximum pro QST-Code beim von-bis-Einkommen wird hier auf 999'999'999 gesetzt. Damit sollte auch fuer sehr hohe Loehne der QST-Tarif ermittelt werden koennen
				$system_database_manager->executeUpdate("COMMIT", "payroll_importDedAtSrcRates");
				$system_database_manager->executeUpdate("TRUNCATE TABLE `payroll_tmp_qst_import`", "payroll_importDedAtSrcRates");
			}
		}else{
			//Fehler TmpDir existiert nicht
			$response["success"] = false;
			$response["errCode"] = 40;
			$response["errText"] = "tmp directory does not exist";
			return $response;
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
}
?>
