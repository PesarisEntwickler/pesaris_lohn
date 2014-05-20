<?php
class payroll_BL_payment {
	

	public function getPaymentSplitList($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) { // id = payroll_employee_ID
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid employee ID";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT pps.*, IF(pbs.description IS NULL,'',pbs.description) as src_bank_label, IF(pbd.description IS NULL,'',pbd.description) as dest_bank_label FROM payroll_payment_split pps LEFT JOIN payroll_bank_source pbs ON pbs.id=pps.payroll_bank_source_ID LEFT JOIN payroll_bank_destination pbd ON pbd.id=pps.payroll_bank_destination_ID WHERE pps.payroll_employee_ID=".$param["id"]." ORDER BY pps.processing_order", "payroll_getPaymentSplitList");

		if(count($result) != 0) {
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}else{
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}
		return $response;
	}

	public function getPaymentSplitDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) { // id = payroll_payment_split_ID
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid payment split ID";
			return $response;
		}
		if(!preg_match( '/^[0-9]{1,9}$/', $param["empID"])) { // id = payroll_employee_ID
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "invalid employee ID";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_payment_split` WHERE `id`=".$param["id"], "payroll_getPaymentSplitDetail");
		$resBankSrc = $system_database_manager->executeQuery("SELECT * FROM `payroll_bank_source`", "payroll_getPaymentSplitDetail"); 
		$resBankDest = $system_database_manager->executeQuery("SELECT `id`, `description` FROM `payroll_bank_destination` WHERE `payroll_employee_ID`=".$param["empID"], "payroll_getPaymentSplitDetail");
		if(count($result) != 0) {
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}else{
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}
		$response["dbview_payroll_bank_source"] = $resBankSrc;
		$response["bankDestination"] = $resBankDest;
		return $response;
	}

	public function savePaymentSplitDetail($param) {
		$updateMode = !isset($param["id"]) || $param["id"]==0 || $param["id"]=="" ? false : true;
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_employee_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false),
					"payroll_bank_source_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_bank_destination_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"processing_order"=>array("regex"=>"[0-9]{1,2}","addQuotes"=>false, "default"=>99),
					"split_mode"=>array("regex"=>"1|2|3","addQuotes"=>false),
					"payroll_account_ID"=>array("regex"=>"[0-9a-zA-Z]{0,5}","addQuotes"=>true),
					"amount"=>array("regex"=>"[0-9]{1,8}(\.[0-9]{1,2})?","addQuotes"=>false),
					"payroll_currency_ID"=>array("regex"=>"[A-Z]{3,3}","addQuotes"=>true),
					"major_period"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"minor_period"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"major_period_bonus"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"major_period_num"=>array("regex"=>"[0-9]{1,1}|1[0-4]{1,1}","addQuotes"=>false),
					"minor_period_num"=>array("regex"=>"[0-9]{1,1}|1[0-4]{1,1}","addQuotes"=>false),
					"major_period_bonus_num"=>array("regex"=>"0|15|16","addQuotes"=>false),
					"having_rounding"=>array("regex"=>"[01]{1,1}","addQuotes"=>false, "default"=>0),
					"round_param"=>array("regex"=>"[0-9]{1,3}(\.[0-9]{1,2})?","addQuotes"=>false)
				);
		if($param["having_rounding"]==1) $param["round_param"]="0.0";
//communication_interface::alert(print_r($param,true));
		////////////////////////////////
		// Mandatory and validity checks
		////////////////////////////////
		$errFields = array();
		foreach($fieldCfg as $fieldName=>$fieldParam) 
			if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) 
				if(isset($fieldParam["default"])) $param[$fieldName]=$fieldParam["default"];
				else $errFields[] = $fieldName;
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
			$recID = $param["id"];
			unset($fieldCfg["id"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			unset($fieldCfg["payroll_employee_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_payment_split` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$recID;
		}else{
			$sqlFIELDS = array();
			$sqlVALUES = array();
			unset($fieldCfg["id"]);
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				$sqlFIELDS[] = "`".$fieldName."`";
				if($fieldParam["addQuotes"]) $sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
				else $sqlVALUES[] = addslashes($param[$fieldName]);
			}
			$sql = "INSERT INTO `payroll_payment_split`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
		}
//communication_interface::alert("id:".$param["id"]." / ".$sql);

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function savePaymentSplitOrder($param) {
		if(!isset($param["data"]) || !is_array($param["data"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "missing data";
			return $response;
		}
		$updateSQL = array();
		foreach($param["data"] as $row) {
			if(!preg_match( '/^[0-9]{1,9}$/', $row[0]) || !preg_match( '/^[0-9]{1,9}$/', $row[1])) {
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "invalid data";
				return $response;
			}
			$updateSQL[] = "UPDATE `payroll_payment_split` SET `processing_order`=".$row[1]." WHERE `id`=".$row[0];
		}

		$system_database_manager = system_database_manager::getInstance();
		foreach($updateSQL as $sql) $system_database_manager->executeUpdate($sql, "payroll_savePaymentSplitOrder");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deletePaymentSplitDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) { // id = payroll_payment_split_ID
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid payment split ID";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("DELETE FROM payroll_payment_split WHERE id=".$param["id"], "payroll_deletePaymentSplitDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

///////////////////////////////////////////////////////
////  BANKVERBINDUNG       destination bank        ////
///////////////////////////////////////////////////////
	public function getDestBankDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid bank ID";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_bank_destination WHERE id=".$param["id"], "payroll_getDestBankDetail");

		if(count($result) != 0) {
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}else{
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}
		return $response;
	}

	public function saveDestBankDetail($param) {
		//communication_interface::alert("[payroll.code_logic] saveDestBankDetail() id:".$param["id"]);
		$updateMode = !isset($param["id"]) || $param["id"]==0 || $param["id"]=="" ? false : true;
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_employee_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false),
					"description"=>array("regex"=>".{1,25}","addQuotes"=>true),
					"destination_type"=>array("regex"=>"1|2|3","addQuotes"=>false),
					"core_intl_country_ID"=>array("regex"=>"[A-Z]{2,2}","addQuotes"=>true),
					"bank_clearing"=>array("regex"=>"[0-9]{0,6}","addQuotes"=>true),
					"bank_account"=>array("regex"=>"[.0-9a-zA-Z\\s]{0,34}","addQuotes"=>true),
					"postfinance_account"=>array("regex"=>"[-0-9a-zA-Z]{0,16}","addQuotes"=>true),
					"beneficiary_bank_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary_bank_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary_bank_line3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"expense"=>array("regex"=>"[012]{0,1}","addQuotes"=>true),
					"beneficiary1_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary1_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary1_line3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary1_line4"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line4"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line5"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"notice_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"notice_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"notice_line3"=>array("regex"=>".{0,32}","addQuotes"=>true)
				);
		$errFields = array();
		switch($param["destination_type"]) {
		case 1: //Bank
			$arrClearFields = array();
			$arrMandatoryFields = array("beneficiary_bank_line1","beneficiary_bank_line3","beneficiary1_line1","beneficiary1_line4");
			foreach($arrMandatoryFields as $fldName) if(trim($param[$fldName])=="") $errFields[] = $fldName;
			break;
		case 2: //Post
			$arrClearFields = array("bank_clearing","bank_account","beneficiary_bank_line1","beneficiary_bank_line2","beneficiary_bank_line3");
			break;
		case 3: //Bar
			$arrClearFields = array("bank_clearing","bank_account","postfinance_account","beneficiary_bank_line1","beneficiary_bank_line2","beneficiary_bank_line3","expense","beneficiary1_line1","beneficiary1_line2","beneficiary1_line3","beneficiary1_line4","beneficiary2_line1","beneficiary2_line2","beneficiary2_line3","beneficiary2_line4","beneficiary2_line5","notice_line1","notice_line2","notice_line3");
			break;
		}
		foreach($arrClearFields as $fldName) $param[$fldName]="";


		////////////////////////////////
		// Mandatory and validity checks
		////////////////////////////////
		foreach($fieldCfg as $fieldName=>$fieldParam) 
			if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) 
				if(isset($fieldParam["default"])) $param[$fieldName]=$fieldParam["default"];
				else $errFields[] = $fieldName;
		$errFields = array_unique($errFields);
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value [10]";
			$response["fieldNames"] = $errFields;
			return $response;
		}

		if($updateMode) {
			$sqlUPDATE = array();
			$recID = $param["id"];
			unset($fieldCfg["id"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			unset($fieldCfg["payroll_employee_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_bank_destination` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$recID;
		}else{
			$sqlFIELDS = array();
			$sqlVALUES = array();
			unset($fieldCfg["id"]);
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				$sqlFIELDS[] = "`".$fieldName."`";
				if($fieldParam["addQuotes"]) $sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
				else $sqlVALUES[] = addslashes($param[$fieldName]);
			}
			$sql = "INSERT INTO `payroll_bank_destination`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
		}
//communication_interface::alert("id:".$param["id"]." / ".$sql);

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function saveBankSourceDetail($param) {
		//communication_interface::alert("0 saveBankSourceDetail() id:".$param["id"]);
		$updateMode = !isset($param["id"]) || $param["id"]==0 || $param["id"]=="" ? false : true;
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
				/*  "payroll_company_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false),  */
					"description"=>array("regex"=>".{1,25}","addQuotes"=>true),
					"source_type"=>array("regex"=>"1|2|3","addQuotes"=>false),
					"bank_source_carrier"=>array("regex"=>"1|2|3","addQuotes"=>false),
					"bank_source_currency_code"=>array("regex"=>"[A-Z]{3,3}","addQuotes"=>true),
					"bank_source_IBAN"=>array("regex"=>"[.0-9A-Z\\s]{0,27}","addQuotes"=>true),
					"bank_source_desc1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"bank_source_desc2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"bank_source_desc3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"bank_source_desc4"=>array("regex"=>".{0,32}","addQuotes"=>true)
				);
		
		$errFields = array();
		////////////////////////////////
		// Mandatory and validity checks
		////////////////////////////////
		foreach($fieldCfg as $fieldName=>$fieldParam) 
			if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) 
				if(isset($fieldParam["default"])) $param[$fieldName]=$fieldParam["default"];
				else $errFields[] = $fieldName;
		$errFields = array_unique($errFields);
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 13;
			$response["errText"] = "invalid field value [13]";
			$response["fieldNames"] = $errFields;
			//communication_interface::alert("err saveBankSourceDetail() id:".$param["id"]." =".$errFields[0]);
			return $response;
		}

		//communication_interface::alert("3 id:".$param["id"]." / updateMode=".$updateMode);
		$erfolg = "DS ";
		if($updateMode) {
			$sqlUPDATE = array();
			$recID = $param["id"];
			unset($fieldCfg["id"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			unset($fieldCfg["payroll_company_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_bank_source` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$recID;
			$erfolg .= "UPDATED";
		}else{
			//communication_interface::alert("Inserting");
			$sqlFIELDS = array();
			$sqlVALUES = array();
			unset($fieldCfg["id"]);
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				$sqlFIELDS[] = "`".$fieldName."`";
				if($fieldParam["addQuotes"]) $sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
				else $sqlVALUES[] = addslashes($param[$fieldName]);
			}
			$sql = "INSERT INTO `payroll_bank_source`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
			$erfolg .= "INSERTED";
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");

		//communication_interface::alert($erfolg);
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	public function deleteDestBankDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) { // id = payroll_bank_destination_ID
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid payment split ID";
			return $response;
		}
		//wenn die zu loeschende Bank im Personalstamm als Standard-Bankverbindung hinterlegt wurde, darf nicht geloescht werden. 
		//Stattdessen ist eine Warnung/Fehlermeldung anzuzeigen.
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_deleteDestBankDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_bank_destination` WHERE `id`=".$param["id"], "payroll_deleteDestBankDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_payment_split` WHERE `payroll_bank_destination_ID`=".$param["id"], "payroll_deleteDestBankDetail");
		$system_database_manager->executeUpdate("COMMIT", "payroll_deleteDestBankDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
///////////////////////////////////////////////////////
//// ZAHLSTELLE bank source                        ////
///////////////////////////////////////////////////////

	public function getBankSourceDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid bank ID";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_bank_source WHERE id=".$param["id"], "payroll_getBankSourceDetail");
		if(count($result) != 0) {
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}else{
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}
		$resBankSourceCurrencies = $system_database_manager->executeQuery("SELECT * FROM `payroll_currency`", "payroll_getBankSourceDetail");
		$response["dbview_payroll_bank_sourcewaehrungen"] = $resBankSourceCurrencies;
		return $response;
	}
	public function deleteBankSourceDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) { // id = payroll_bank_source_ID
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid payment split ID";
			return $response;
		}
//TODO: wenn die zu loeschende Bank im Personalstamm als Standard-Bankverbindung hinterlegt wurde, darf nicht geloescht werden. 
//Stattdessen ist eine Warnung/Fehlermeldung anzuzeigen.
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_deleteBankSourceDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_bank_source` WHERE `id`=".$param["id"], "payroll_deleteBankSourceDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_payment_split` WHERE `payroll_bank_source_ID`=".$param["id"], "payroll_deleteBankSourceDetail");
		$system_database_manager->executeUpdate("COMMIT", "payroll_deleteBankSourceDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
}
?>
