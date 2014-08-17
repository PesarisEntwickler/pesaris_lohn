<?php
class payroll_BL_payment {
	
	public function processPayment($param) {
		require_once('chkDate.php');
		$chkDate = new chkDate();
				
//TODO: Sicherstellen, dass diese Nasen ab sofort nicht mehr gerechnet werden!! Und processing-flag hochsetzen auf 2!
		///////////////////////////////////////////////////
		// validity check of payment and interest date
		///////////////////////////////////////////////////
		if(!$chkDate->chkDate($param["payment_date"], 1, $paramDatePayment)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "payment date value";
			$response["errField"] = "payment_date";
			return $response;
		}
		if(!$chkDate->chkDate($param["interest_date"], 1, $paramDateInterest)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "interest date value";
			$response["errField"] = "interest_date";
			return $response;
		}

		///////////////////////////////////////////////////
		// filter_mode must be numeric, non-decimal and in a certain range
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-2]{1,1}$/', $param["filter_mode"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid company id";
			$response["errField"] = "filter_mode";
			return $response;
		}else $paramFilterMode = $param["filter_mode"];

		if($paramFilterMode==1) {
			///////////////////////////////////////////////////
			// company id must be numeric and non-decimal
			///////////////////////////////////////////////////
			if(!preg_match( '/^[0-9]{1,9}$/', $param["payroll_company_ID"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "invalid company id";
				$response["errField"] = "payroll_company_ID";
				return $response;
			}
			$paramCompanyID = $param["payroll_company_ID"];
		}else $paramCompanyID = 0;

		$system_database_manager = system_database_manager::getInstance();

		//get the id of the current period
		$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_processPayment");
		$payrollPeriodID = $result[0]["id"];

		$system_database_manager->executeUpdate("BEGIN", "payroll_processPayment");

		$uid = session_control::getSessionInfo("id");

		$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_processPayment");
		switch($paramFilterMode) {
		case 0: //all employees
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) SELECT ".$uid.",`payroll_employee_ID` FROM `payroll_period_employee` WHERE `processing`=1 AND `core_user_ID_calc`!=0 AND `payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			break;
		case 1: //only employees of a certain company
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) SELECT ".$uid.",prdemp.`payroll_employee_ID` FROM `payroll_period_employee` prdemp INNER JOIN `payroll_employee` emp ON prdemp.`payroll_employee_ID`=emp.`id` AND emp.`payroll_company_ID`=".$paramCompanyID." WHERE prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			break;
		case 2: //only a certain list of employees
			//TODO: assemble SQL statement by an array of employee IDs
			break;
		}
		//update period_employee information
		$system_database_manager->executeUpdate("UPDATE `payroll_period_employee` prdemp INNER JOIN `payroll_tmp_change_mng` ids ON prdemp.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=".$uid." SET prdemp.`payment_date`='".$paramDatePayment."',prdemp.`interest_date`='".$paramDateInterest."',prdemp.`core_user_ID_payment`=".$uid.",prdemp.`processing`=2 WHERE prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
		//move (save) calculation results from temporary table `payroll_calculation_current` to `payroll_calculation_entry`
		$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `label`, `code`, `position`) SELECT calc.`payroll_year_ID`, calc.`payroll_period_ID`, calc.`payroll_employee_ID`, calc.`payroll_account_ID`, calc.`quantity`, calc.`rate`, calc.`amount`, calc.`allowable_workdays`, calc.`label`, calc.`code`, calc.`position` FROM `payroll_calculation_current` calc INNER JOIN `payroll_tmp_change_mng` ids ON calc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=".$uid." WHERE calc.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
		//save payment split data from temp-table to def-table table
		$system_database_manager->executeUpdate("INSERT INTO `payroll_payment_entry`(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount`,`amount_payout`,`payroll_currency_ID`) SELECT pmtcur.`payroll_period_ID`,pmtcur.`payroll_employee_ID`,pmtcur.`payroll_payment_split_ID`,pmtcur.`amount`,pmtcur.`amount_payout`,pmtcur.`payroll_currency_ID` FROM `payroll_payment_current` pmtcur INNER JOIN `payroll_tmp_change_mng` ids ON pmtcur.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=".$uid." WHERE pmtcur.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");

		$system_database_manager->executeUpdate("COMMIT", "payroll_processPayment");


//		communication_interface::alert(print_r($param,true));
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
	public function getZahlstelle($employeeId) {	
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT MIN(processing_order)
			FROM   payroll_payment_split 
			WHERE  payroll_employee_ID    =".$employeeId." 
			;", "payroll_getZahlstelle" 
		);
		if (count($result)>0) {
			$return = $result[0];
		} else {
			$return = array();
		}
		return $return;
	}
	
	public function getZahlstelle____($employeeId, $destBankId) {	
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT MIN(processing_order)
			FROM   payroll_payment_split 
			WHERE  payroll_employee_ID    =".$employeeId." 
			  AND  payroll_bank_source_ID =".$destBankId." 
			;", "payroll_getZahlstelle" 
		);
		if (count($result)>0) {
			$return = $result[0];
		} else {
			$return = array();
		}
		return $return;
	}
	

	public function getPaymentSplitList($payroll_employee_ID) {
		if(!preg_match( '/^[0-9]{1,9}$/', $payroll_employee_ID)) { 
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid employee ID";
			return $response;
		}
		if (intval($payroll_employee_ID) < 1) {
			$response["success"] = false;
			$response["errCode"] = 11;
			$response["errText"] = "employee ID not set";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT pps.*, IF(pbs.id IS NULL,'',pbs.id) as src_bank_label 
				        , IF(pbd.description IS NULL,'',pbd.bank_account) as dest_bank_label 
				        , IF(pbd.destination_type IS NULL,'',pbd.destination_type) as destination_type  
 
			FROM payroll_payment_split pps 
				LEFT JOIN payroll_bank_source pbs ON pbs.id=pps.payroll_bank_source_ID 
				LEFT JOIN payroll_bank_destination pbd ON pbd.id=pps.payroll_bank_destination_ID 
			WHERE pps.payroll_employee_ID=".$payroll_employee_ID." 
			ORDER BY pps.processing_order; 
			", "payroll_getPaymentSplitList" 
		);
		
		$response["count"] = count($result);
		if(count($result) != 0) {
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}else{
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
			$response["data"] = array();
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
		if(!preg_match( '/^[0-9]{1,9}$/', $param["empId"])) { // id = payroll_employee_ID
			$response["success"] = false;
			$response["errCode"] = 15;
			$response["errText"] = "invalid employee ID";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_payment_split` WHERE `id`=".$param["id"], "payroll_getPaymentSplitDetail");
		$resBankSrc = $system_database_manager->executeQuery("SELECT * FROM `payroll_bank_source`", "payroll_getPaymentSplitDetail"); 
		$resBankDest = $system_database_manager->executeQuery("SELECT `id`, `description` FROM `payroll_bank_destination` WHERE `payroll_employee_ID`=".$param["empId"], "payroll_getPaymentSplitDetail");
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

	public function saveBankDestinationUndSplit($param) { 
		//communication_interface::alert("saveBankDestinationUndSplit:\n param:".print_r($param, true));
		require_once('payroll_auszahlen.php');
		$auszahlen = new auszahlen();
		
		$paramBankDest = array();
		$paramSplitTab = array();
		
		$employeeID = $param['payroll_employee_ID'];
		if (intval($employeeID) == 0) {
			//communication_interface::alert("payroll_employee_ID=0, empId=".$param['empId']);
			$employeeID = $param['empId'];
		}
		$isStanardBank = $auszahlen->getStandardDestinationBankAccount($employeeID);
		
		$bankDestID = $param['bankID'];		
		$splitTabID = $param['splitID'];
		$hasSplit = false;
		if($isStanardBank["bank_id"]!=$param['id']  && $bankDestID > 0) {
							
			//$split = $auszahlen->getPaymentSplit($employeeID,$bankDestID,0); 
			//if ($split["count"] > 0) {
				$hasSplit = true;
				$paramSplitTab["id"] 		= $splitTabID;
				$paramSplitTab["payroll_employee_ID"] 		= $employeeID;
				$paramSplitTab["payroll_bank_source_ID"] 	= $param["selectedZahlstelle"];
				$paramSplitTab["payroll_bank_destination_ID"] 	= $bankDestID;
				$paramSplitTab["split_mode"] 				= $param["split_mode"];
				$paramSplitTab["payroll_account_ID"] 		= $param["payroll_account_ID"];
				$paramSplitTab["amount"] 					= $param["amount"];
				$paramSplitTab["payroll_currency_ID"] 		= $param["payroll_currency_ID"];
				$paramSplitTab["major_period"] = 0; $paramSplitTab["minor_period"] = 0; $paramSplitTab["major_period_bonus"] = 0; $paramSplitTab["major_period_num"] = 0; $paramSplitTab["minor_period_num"] = 0; $paramSplitTab["major_period_bonus_num"] = 0;
				if($param["period"]=="") {
					$paramSplitTab["major_period"] = 1;
					$paramSplitTab["minor_period"] = 1;
					$paramSplitTab["major_period_bonus"] = 1;
				}else if($param["period"]=="MAP") {
					$paramSplitTab["major_period"] = 1;
				}else if($param["period"]=="MIP") {
					$paramSplitTab["minor_period"] = 1;
				}else if($param["period"]=="MPB") {
					$paramSplitTab["major_period_bonus"] = 1;
				}else if($param["period"]>0 && $param["period"]<15) {
					$paramSplitTab["major_period"] = 1;
					$paramSplitTab["major_period_num"] = $param["period"];
				}else if($param["period"]>14) {
					$paramSplitTab["major_period_bonus"] = 1;
					$paramSplitTab["major_period_bonus_num"] = $param["period"];
				}
				$paramSplitTab["having_rounding"] = 1;
				$paramSplitTab["round_param"] = 0.00;
			//} else {
			//	communication_interface::alert("Problem mit BankDest & Split\n$employeeID,$bankDestID");
			//	return false;				
			//}
		}
		
		$paramBankDest["id"] 					= $bankDestID;
		$paramBankDest["payroll_employee_ID"] 	= $employeeID;
		$paramBankDest["description"] 			= $param["description"];
		$paramBankDest["destination_type"] 		= $param["destination_type"];
		$paramBankDest["core_intl_country_ID"] 	= $param["core_intl_country_ID"];
		$paramBankDest["bank_swift"] 			= strtoupper($param["bank_swift"]);
		$paramBankDest["bank_account"] 			= strtoupper($param["bank_account"]);
//		$paramBankDest["postfinance_account"]	= $param["postfinance_account"];
		$paramBankDest["beneficiary1_line1"] 	= $param["beneficiary1_line1"];
		$paramBankDest["beneficiary1_line2"] 	= $param["beneficiary1_line2"];
		$paramBankDest["beneficiary1_line3"] 	= $param["beneficiary1_line3"];
		$paramBankDest["beneficiary1_line4"] 	= $param["beneficiary1_line4"];
		$paramBankDest["beneficiary2_line1"] 	= $param["beneficiary2_line1"];
		$paramBankDest["beneficiary2_line2"] 	= $param["beneficiary2_line2"];
		$paramBankDest["beneficiary2_line3"] 	= $param["beneficiary2_line3"];
		$paramBankDest["beneficiary2_line4"] 	= $param["beneficiary2_line4"];
		$paramBankDest["beneficiary2_line5"] 	= "";
		$paramBankDest["notice_line1"] 			= $param["notice_line1"];
		$paramBankDest["notice_line2"] 			= $param["notice_line2"];
		$paramBankDest["notice_line3"] 			= "";
		$paramBankDest["notice_line4"] 			= "";
		$paramBankDest["hand_delivered"] 		= "";
		$paramBankDest["beneficiary_bank_line1"]= $param["beneficiary_bank_line1"];
		$paramBankDest["beneficiary_bank_line2"]= $param["beneficiary_bank_line2"];
		$paramBankDest["beneficiary_bank_line3"]= $param["beneficiary_bank_line3"];
		$paramBankDest["beneficiary_bank_line4"]= "";
		$paramBankDest["expense"] 				= strtoupper($param["expense"]);
		//$paramBankDest["is_standard_bank"] 		= "";
		$paramBankDest["nonstandard_banksourcezahlstelle"] =  $param["selectedZahlstelle"];
		
		if ($hasSplit) {
			//communication_interface::alert("saveBankDestinationUndSplit:".$hasSplit["count"]."\nparamSplitTab:".print_r($paramSplitTab, true));
			$splitRes = $this->savePaymentSplitDetail($paramSplitTab);			
		}
		//communication_interface::alert("saveBankDestinationUndSplit:".$hasSplit["count"]."\nparamBankDest:".print_r($paramBankDest, true));
		$bankDestRes = $this->saveBankDestinationDetail($paramBankDest);
		return true;
	}


	public function savePaymentSplitDetail($param) {
		$updateMode = !isset($param["id"]) || $param["id"]==0 || $param["id"]=="" ? false : true;
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_employee_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false),
					"payroll_bank_source_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_bank_destination_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),				
					"split_mode"=>array("regex"=>"1|2|3","addQuotes"=>false),
					"payroll_account_ID"=>array("regex"=>"[0-9a-zA-Z]{0,5}","addQuotes"=>true),
					"amount"=>array("regex"=>"[0-9]{1,8}(\.[0-9]{1,2})?","addQuotes"=>false),
					"payroll_currency_ID"=>array("regex"=>"[A-Z]{3,3}","addQuotes"=>true),
					"major_period"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"minor_period"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"major_period_bonus"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"major_period_num"=>array("regex"=>"[0-9]{1,1}|1[0-4]{1,1}","addQuotes"=>false),
					"minor_period_num"=>array("regex"=>"[0-9]{1,1}|1[0-4]{1,1}","addQuotes"=>false),
					"major_period_bonus_num"=>array("regex"=>"0|15|16","addQuotes"=>false)
				);
		////		"processing_order"=>array("regex"=>"[0-9]{1,2}","addQuotes"=>false, "default"=>99),
		
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
			$response["errCode"] = 11;
			$response["errText"] = "invalid field value [11]";
			$response["fieldNames"] = $errFields;
			$arSearch = array("Array", "(", ")");
			$arReplace = array("mandatory fields missing [pp11]:", "", "");
			$err = str_replace($arSearch, $arReplace, print_r($errFields, true));
			communication_interface::alert($err);
			return $response;
		}

		$erfolg = "";
		if($updateMode) {
			$sqlUPDATE = array();
			$recID = $param["id"];
			unset($fieldCfg["id"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			unset($fieldCfg["payroll_employee_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden soll
			unset($fieldCfg["payroll_bank_destination_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden soll
			unset($fieldCfg["processing_order"]); //entfernen, da dieses Feld nicht aktualisiert werden soll
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_payment_split` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$recID;
			$erfolg ="saved";
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
			$erfolg ="inserted";
		}

		$system_database_manager = system_database_manager::getInstance();
		$ret = $system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		//communication_interface::alert("Split ".$erfolg);
		return $response;
	}

	public function saveBankDestinationDetail($param) {
		//communication_interface::alert("saveBankDestinationDetail() id:".$param["id"]."\n".print_r($param, true));
		$updateMode = !isset($param["id"]) || $param["id"]==0 || $param["id"]=="" ? false : true;
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_employee_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false),
					"description"=>array("regex"=>".{1,25}","addQuotes"=>true, "default"=>$param["description"]),
					"destination_type"=>array("regex"=>"1|2|3","addQuotes"=>false),
					"core_intl_country_ID"=>array("regex"=>"[A-Z]{2,2}","addQuotes"=>true),
					"bank_swift"=>array("regex"=>"[0-9A-Z]{0,11}","addQuotes"=>true),
					"bank_account"=>array("regex"=>"[.0-9a-zA-Z\\s]{0,34}","addQuotes"=>true),
//					"postfinance_account"=>array("regex"=>"[-0-9a-zA-Z]{0,16}","addQuotes"=>true),
					"beneficiary1_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary1_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary1_line3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary1_line4"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line4"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary2_line5"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary_bank_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary_bank_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"beneficiary_bank_line3"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"expense"=>array("regex"=>"[012]{0,1}","addQuotes"=>true),
					"notice_line1"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"notice_line2"=>array("regex"=>".{0,32}","addQuotes"=>true),
					"nonstandard_banksourcezahlstelle"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false)
				);
		$errFields = array();
		switch($param["destination_type"]) {
		case 1: //Bank
			$arrClearFields = array();
			$arrMandatoryFields = array("bank_account","beneficiary_bank_line1","beneficiary_bank_line3","beneficiary1_line1","beneficiary1_line4");
			break;
		case 2: //Post
			$arrClearFields = array("bank_swift","beneficiary_bank_line1","beneficiary_bank_line2","beneficiary_bank_line3");
			$arrMandatoryFields = array("bank_account","beneficiary1_line1","beneficiary1_line4");
			break;
		case 3: //Cash
			$arrClearFields = array("bank_swift","bank_account","postfinance_account","beneficiary_bank_line1","beneficiary_bank_line2","beneficiary_bank_line3","expense","beneficiary1_line1","beneficiary1_line2","beneficiary1_line3","beneficiary1_line4","beneficiary2_line1","beneficiary2_line2","beneficiary2_line3","beneficiary2_line4","beneficiary2_line5","notice_line1","notice_line2");
			$arrMandatoryFields = array();
			break;
		}
		////////////////////////////////
		// Mandatory and validity checks
		////////////////////////////////
		//communication_interface::alert("arrClearFields:".print_r($arrClearFields, true)."\narrMandatoryFields:".print_r($arrMandatoryFields, true));
		foreach($arrClearFields as $fldName) $param[$fldName]="";
		foreach($arrMandatoryFields as $fldName) if(trim($param[$fldName])=="") $errFields[] = $fldName;
		foreach($fieldCfg as $fieldName=>$fieldParam) 
			if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) 
				if(isset($fieldParam["default"])) $param[$fieldName]=$fieldParam["default"];
				else $errFields[] = $fieldName;
		$errFields = array_unique($errFields);
		//communication_interface::alert("errFields:".print_r($errFields, true));
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value [10]";
			$response["fieldNames"] = $errFields;
			$arSearch = array("Array", "(", ")","bank_account","beneficiary_bank_line1","beneficiary_bank_line3","beneficiary1_line1","beneficiary1_line4");
			$arReplace = array("Bitte fehlende Informationen eingeben:", "", "","Bank Konto/IBAN","Bankname","Bank PLZ / Ort","Beguenstigter Name","Beguenstigter PLZ / Ort");
			$err = str_replace($arSearch, $arReplace, print_r($errFields, true));
			communication_interface::alert($err);
			return $response;
		}

		$erfolg = "";
		if($updateMode) {
			$sqlUPDATE = array();
			$recID = $param["id"];
			unset($fieldCfg["id"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			unset($fieldCfg["payroll_employee_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden soll
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_bank_destination` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$recID;
			$erfolg ="saved";
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
			$erfolg ="inserted";
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");
			 
		$response["success"] = true;
		$response["errCode"] = 0;
		communication_interface::alert("Bank, Post, Cash ".$erfolg);//.print_r($param, true).$sql
		communication_interface::jsExecute("$('#modalContainer').mb_close(); ");
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
		communication_interface::alert("order saved");
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

	public function getDestBankDetail($destBankID) {
	//communication_interface::alert("getDestBankDetail($destBankID)");
	$response = array();
		if(!preg_match( '/^[0-9]{1,9}$/', $destBankID)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid bank ID";
			return $response;
		}

		$response["data"]["id"] = $destBankID;
		$response["data"]["payroll_employee_ID"] = 0;
		$response["data"]["description"] = "";
		$response["data"]["bank_swift"] = "";
		$response["data"]["bank_account"] = "";
		$response["data"]["postfinance_account"] = "";
		$response["data"]["destination_type"] = 1;
		$response["data"]["beneficiary1_line1"] = "";
		$response["data"]["beneficiary1_line2"] = "";
		$response["data"]["beneficiary1_line3"] = "";
		$response["data"]["beneficiary1_line4"] = "";
		$response["data"]["beneficiary2_line1"] = "";
		$response["data"]["beneficiary2_line2"] = "";
		$response["data"]["beneficiary2_line3"] = "";
		$response["data"]["beneficiary2_line4"] = "";
		$response["data"]["beneficiary2_line5"] = "";
		$response["data"]["notice_line1"] = "";
		$response["data"]["notice_line2"] = "";
		$response["data"]["notice_line3"] = "";
		$response["data"]["notice_line4"] = "";
		$response["data"]["beneficiary_bank_line1"] = "";
		$response["data"]["beneficiary_bank_line2"] = "";
		$response["data"]["beneficiary_bank_line3"] = "";
		$response["data"]["beneficiary_bank_line4"] = "";
		$response["data"]["beneficiary_bank_line5"] = "";
		$response["data"]["expense"] = "";
		$response["data"]["is_standard_bank"] = "";
		$response["data"]["nonstandard_banksourcezahlstelle"] = "0";

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_bank_destination WHERE id=".$destBankID, "payroll_getDestBankDetail");
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

	
	public function initZahlungssplitt($employeeId, $zahlstelle, $destBankId) {
		require_once('payroll_auszahlen.php');
		$auszahlen = new auszahlen();
		/*
		 * Wenn noch kein Zahlungssplitt existiert, 
		 * dann reicht es, einfach eine Splitt-Row zu erfassen
		 * 
		 * Wenn aber schon mind. eine Splitt-Row da ist, 
		 * dann muss man 
		 * 		1.) eine BankDest-Row anlegen (initialisiert mit den Daten der schon bestehenden)
		 * 		UND 
		 * 		2.) eine weitere Splitt-Row anlegen
		 * 
		 * 	Von der neuen BankDest-Row muss die 
		 *  BankDestz-ID in die Splitt-Row eingesetzt werden
		 * */

		//Test, ob schon mind ein Zahlungssplitt existiert 
		//(existiert eine Splitt-Row auf diesem Employee)
		$hasSplit = $this->getPaymentSplitList($employeeId);
		$system_database_manager = system_database_manager::getInstance();
		if(count($hasSplit) <= 0) {
			communication_interface::alert("hat keine SplitRow");
		} else {
			//wenn eine Splitt-Row existiert, dann muss es auch eine BankDest-Row geben
			//diese wird dann kopiert
			$sqlSelectBankDest = "
				SELECT `id`, `payroll_employee_ID`, `destination_type`, `description`, `core_intl_country_ID`, `bank_account`, `postfinance_account`, `bank_swift`, `beneficiary1_line1`, `beneficiary1_line2`, `beneficiary1_line3`, `beneficiary1_line4`, `beneficiary_bank_line1`, `beneficiary_bank_line2`, `beneficiary_bank_line3`, `beneficiary_bank_line4`
				FROM  payroll_bank_destination
				WHERE payroll_employee_ID = ".$employeeId."
				LIMIT 1;
		 	";
		 	$bD = $system_database_manager->executeQuery($sqlSelectBankDest);
		 	$resMaxID = $system_database_manager->executeQuery("SELECT MAX(id) AS maxId FROM payroll_bank_destination");

			$copy = "Copy";
			switch($bD[0]["destination_type"]) {
			case 1: //Bank
				$copy = "BANK-Copy";
				break;
			case 2: //Post
				$copy = "POST-Copy";
				break;
			}
		 	
		 	//communication_interface::alert("id=".$bD[0]["id"].",\nempId=".$bD[0]["payroll_employee_ID"].",\nkonto=".$bD[0]["bank_account"].",\nmaxId:".$resMaxID[0]["maxId"]);
		 	$nextBD_id = $resMaxID[0]["maxId"]+1;
			$sqlInsertBankDest = "
				INSERT INTO payroll_bank_destination (
						 `id`, `payroll_employee_ID`, `destination_type`, `description`, `core_intl_country_ID`, `bank_account`, `postfinance_account`, `bank_swift`, `beneficiary1_line1`, `beneficiary1_line2`, `beneficiary1_line3`, `beneficiary1_line4`, `beneficiary_bank_line1`, `beneficiary_bank_line2`, `beneficiary_bank_line3`, `beneficiary_bank_line4`, `is_standard_bank`, `nonstandard_banksourcezahlstelle`) 
				VALUES (".$nextBD_id."
					  , ".$bD[0]["payroll_employee_ID"]."
					  , ".$bD[0]["destination_type"]."
					  ,'".$copy."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["core_intl_country_ID"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["bank_account"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["postfinance_account"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["bank_swift"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary1_line1"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary1_line2"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary1_line3"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary1_line4"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary_bank_line1"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary_bank_line2"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary_bank_line3"] )."'
					  ,'".$auszahlen->replaceUmlaute( $bD[0]["beneficiary_bank_line4"] )."'
					  ,'N'
					  ,'0'
					  );";
			$ret = $system_database_manager->executeUpdate($sqlInsertBankDest, "payroll_initZahlungssplitt");
		 	
		}
		$sql = "
			INSERT INTO payroll_payment_split  
			  (`payroll_employee_ID`, `payroll_bank_source_ID`, `payroll_bank_destination_ID`, `processing_order`) 
			VALUES (".$employeeId.", ".$zahlstelle.", ".$nextBD_id.", 98)
		;";
		$ret = $system_database_manager->executeUpdate($sql, "payroll_initZahlungssplitt");
		$response["success"] = true;
		$response["errCode"] = 0;
		communication_interface::alert("split initialized");
		return $response;
	}

	public function saveBankSourceDetail($param) {
		//communication_interface::alert("0 saveBankSourceDetail() id:".$param["id"]);
		$updateMode = !isset($param["id"]) || $param["id"]==0 || $param["id"]=="" ? false : true;
		$fieldCfg = array(
					"id"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false, "default"=>0),
					"payroll_company_ID"=>array("regex"=>"[0-9]{1,9}","addQuotes"=>false), 
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
		$response  = array();
		////////////////////////////////
		// Mandatory and validity checks
		////////////////////////////////
		foreach($fieldCfg as $fieldName=>$fieldParam) {
			if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) 
				if(isset($fieldParam["default"])) {
					$param[$fieldName]=$fieldParam["default"];
				} else {
					$errFields[] = $fieldName;				
				}
		}
		$errFields = array_unique($errFields);
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 13;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			//communication_interface::alert("err saveBankSourceDetail() id:".$param["id"]." =".$errFields[0]);
			return $response;
		}

		//communication_interface::alert("fieldCfg[id]:".$fieldCfg["id"].", param[id]:".$param["id"]." / updateMode=".$updateMode);
		$erfolg = "Bank-Info ";
		$sql = "";
		if($updateMode) {
			$sqlUPDATE = array();
			$recID = $param["id"];
			unset($fieldCfg["id"]); //entfernen, da dieses Feld nicht aktualisiert werden muss
			unset($fieldCfg["payroll_company_ID"]); //entfernen, da dieses Feld nicht aktualisiert werden muss/soll
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_bank_source` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$recID;
			$erfolg .= "updated";
		}else{
			$sqlFIELDS = array();
			$sqlVALUES = array();
			unset($fieldCfg["id"]);
			//$fieldCfg["payroll_company_ID"] = $param["id"]; 
			foreach($fieldCfg as $fieldName=>$fieldParam) {
				$sqlFIELDS[] = "`".$fieldName."`";
				if($fieldParam["addQuotes"]== true) {
					$sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
				} else {
					$sqlVALUES[] = addslashes($param[$fieldName]);
				}
			}
			$sql = "INSERT INTO `payroll_bank_source`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
			$erfolg .= "inserted";
		}
		//communication_interface::alert($sql);
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");

		//communication_interface::alert($erfolg);
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
	
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
		$bankSourceId = $param["id"];
		//communication_interface::alert("deleteBankSourceDetail() :".$bankSourceId);
		
		//wenn die zu loeschende Bank im Personalstamm als Standard-Bankverbindung hinterlegt wurde, darf nicht geloescht werden. 

		$system_database_manager = system_database_manager::getInstance();

		$sql = "
				SELECT *  
				FROM payroll_employee
				WHERE id IN (
							SELECT DISTINCT payroll_employee_ID 
							FROM payroll_payment_split
							WHERE payroll_bank_source_ID = ".$bankSourceId."
							)				
				";
		$result = $system_database_manager->executeQuery($sql);
		if (count($result)> 0) {
			$c = "";
			foreach ( $result as $row ) {
				$c .= " - ";
				$c .= $row['EmployeeNumber'].", ";
				$c .= $row['Firstname']." ";
				$c .= $row['Lastname'].", ";
				$c .= $row['City']."\n";
			}
			communication_interface::alert("Loeschen noch nicht moeglich!" .
										"\n\nFolgende Mitarbeiter haben noch eine\nVerbindung zu der zu loeschenden Bank:" .
										"\n\n".$c);
		} else {
			//communication_interface::alert("Loeschen von ".$bankSourceId);
			$system_database_manager->executeUpdate("BEGIN", "payroll_deleteBankSourceDetail");
			$system_database_manager->executeUpdate("DELETE FROM `payroll_bank_source` WHERE `id`=".$bankSourceId, "payroll_deleteBankSourceDetail");
			$system_database_manager->executeUpdate("DELETE FROM `payroll_payment_split` WHERE `payroll_bank_source_ID`=".$bankSourceId, "payroll_deleteBankSourceDetail");
			$system_database_manager->executeUpdate("COMMIT", "payroll_deleteBankSourceDetail");
		}
		
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
}
?>
