<?php

require_once('various_functions.php');
$variousFunctions = new variousFunctions();
			
require_once('payroll_calculate.php');
$calcs = new payroll_BL_calculate();

require_once('payroll_payment.php');
$payrollPayment = new payroll_BL_payment();
			
require_once('employee.php');
$employee = new employee();
			
require_once('payroll_insurance.php');
$insurance = new insurance();

require_once('payslip.php');
$payslip = new payslip();
			
require_once('payroll_account.php');
$account = new account();
			

class payroll_BL {

	public function sysListener($functionName, $functionParameters) {
	
		switch($functionName) {
		case 'payroll.onBootComplete':
			return $variousFunctions->onBootComplete();
		case 'payroll.getLanguageList':
			return $variousFunctions->getLanguageList($functionParameters[0]);
		case 'payroll.getCountryList':
			return $variousFunctions->getCountryList();
		case 'payroll.getFormulaList':
			return $variousFunctions->getFormulaList();
		case 'payroll.saveFormula':
			return $variousFunctions->saveFormula($functionParameters[0]);
		case 'payroll.deleteFormula':
			return $variousFunctions->deleteFormula($functionParameters[0]);
		case 'payroll.getCompanyList':
			return $variousFunctions->getCompanyList();
		case 'payroll.getCompanyDetail':
			return $variousFunctions->getCompanyDetail($functionParameters[0]);
		case 'payroll.saveCompanyDetail':
			return $variousFunctions->saveCompanyDetail($functionParameters[0]);
		case 'payroll.deleteCompanyDetail':
			return $variousFunctions->deleteCompanyDetail($functionParameters[0]);
		case 'payroll.getAttendedTimeList':
			return $variousFunctions->getAttendedTimeList();
		case 'payroll.saveAttendedTimeHours':
			return $variousFunctions->saveAttendedTimeHours($functionParameters[0]);
		case 'payroll.getCurrencyList':
			return $variousFunctions->getCurrencyList($functionParameters[0]);
		case 'payroll.saveCurrencyList':
			return $variousFunctions->saveCurrencyList($functionParameters[0]);
		case 'payroll.prepareCalculation':
			return $this->prepareCalculation($functionParameters[0]);
//		case 'payroll.getpayrollountFormOverview':
//			require_once('payroll-fin.php');
//			$payrollfin = new payroll_BL_fin();
//			return $payrollfin->getpayrollountForm($functionParameters[0], $functionParameters[1], $functionParameters[2], $functionParameters[3]); //$parampayrollountNumber, $bMonthlySummary, $bAllEntries, $arrYearMonth
//			break;
		case 'payroll.getEmployeeList':
			return $employee->getEmployeeList($functionParameters[0]);
		case 'payroll.getEmployeeFieldDef':
			return $employee->getEmployeeFieldDef();
		case 'payroll.getEmployeeFieldDetail':
			return $employee->getEmployeeFieldDetail($functionParameters[0]);
		case 'payroll.saveEmployeeFieldDetail':
			return $employee->saveEmployeeFieldDetail($functionParameters[0]);
		case 'payroll.saveEmployeeForm':
			return $employee->saveEmployeeForm($functionParameters[0]);
		case 'payroll.getEmployeeFormList':
			return $employee->getEmployeeFormList();
		case 'payroll.getEmployeeFormDetail':
			return $employee->getEmployeeFormDetail($functionParameters[0]);
		case 'payroll.deleteEmployeeForm':
			return $employee->deleteEmployeeForm($functionParameters[0]);
		case 'payroll.getEmployeeDetail':
			return $employee->getEmployeeDetail($functionParameters[0],$functionParameters[1]);

		case 'payroll.callbackEmployeeDetail':
			return $employee->callbackEmployeeDetail($functionParameters[0]);
		case 'payroll.saveEmployeeDetail':
			return $employee->saveEmployeeDetail($functionParameters[0],$functionParameters[1]);


		case 'payroll.getPayrollAccountList':
			return $account->getPayrollAccountList();
		case 'payroll.savePayrollAccount':
			return $account->savePayrollAccount($functionParameters[0],$functionParameters[1]);
		case 'payroll.deletePayrollAccount':
			return $account->deletePayrollAccount($functionParameters[0]);
		case 'payroll.copyPayrollAccount':
			return $account->copyPayrollAccount($functionParameters[0]);
		case 'payroll.getPayrollAccountDetail':
			return $account->getPayrollAccountDetail($functionParameters[0]);
		case 'payroll.getPayrollAccountMappingList':
			return $account->getPayrollAccountMappingList();
		case 'payroll.getPayrollAccountMappingDetail':
			return $account->getPayrollAccountMappingDetail($functionParameters[0]);
		case 'payroll.savePayrollAccountMappingDetail':
			return $account->savePayrollAccountMappingDetail($functionParameters[0]);

		case 'payroll.getInsuranceCompanyList':
			return $insurance->getInsuranceCompanyList();
		case 'payroll.getInsuranceCompanyDetail':
			return $insurance->getInsuranceCompanyDetail($functionParameters[0]);
		case 'payroll.saveInsuranceCompanyDetail':
			return $insurance->saveInsuranceCompanyDetail($functionParameters[0]);
		case 'payroll.deleteInsuranceCompanyDetail':
			return $insurance->deleteInsuranceCompanyDetail($functionParameters[0]);
		case 'payroll.getInsuranceCodeList':
			return $insurance->getInsuranceCodeList($functionParameters[0]);
		case 'payroll.getInsuranceCodeDetail':
			return $insurance->getInsuranceCodeDetail($functionParameters[0]);
		case 'payroll.saveInsuranceCodeDetail':
			return $insurance->saveInsuranceCodeDetail($functionParameters[0]);
		case 'payroll.deleteInsuranceCodeDetail':
			return $insurance->deleteInsuranceCodeDetail($functionParameters[0]);
		case 'payroll.getInsuranceRateList':
			return $insurance->getInsuranceRateList($functionParameters[0]);
		case 'payroll.getInsuranceRateDetail':
			return $insurance->getInsuranceRateDetail($functionParameters[0]);
		case 'payroll.saveInsuranceRateDetail':
			return $insurance->saveInsuranceRateDetail($functionParameters[0]);
		case 'payroll.deleteInsuranceRateDetail':
			return $insurance->deleteInsuranceRateDetail($functionParameters[0]);

		case 'payroll.processPayment':
			return $this->processPayment($functionParameters[0]);
		case 'payroll.editFinMgmtAccountingConfig':
			return $this->editFinMgmtAccountingConfig($functionParameters[0]);
		case 'payroll.getFinMgmtAccountingInfo':
			return $this->getFinMgmtAccountingInfo();
		case 'payroll.processFinMgmtAccountingEntry':
			return $this->processFinMgmtAccountingEntry($functionParameters[0]);

		case 'payroll.calculationReport':
				require_once('payroll_reports.php');
				$reports = new payroll_BL_reports();

				switch($functionParameters[0]) {
				case 'calculationJournal':
					return $reports->CalculationJournal($functionParameters[1]);
				case 'finAccJournal':
					return $reports->FinAccountingJournal($functionParameters[1]);
				case 'mgmtAccJournal':
					return $reports->MgmtAccountingJournal($functionParameters[1]);
				case 'payrollAccountJournal':
					return $reports->PayrollAccountJournal($functionParameters[1]);
				case 'payslip':
					if(!preg_match( '/^[0-9]{4,4}$/', $functionParameters[1]["year"]) || !preg_match( '/^[0-9]{1,2}$/', $functionParameters[1]["majorPeriod"]) || !preg_match( '/^[0-9]{1,2}$/', $functionParameters[1]["minorPeriod"])) {
						$response["success"] = false;
						$response["errCode"] = 10;
						$response["errText"] = "invalid argument";
						return $response;
					}
					$payrollPeriodID = 0;
					$system_database_manager = system_database_manager::getInstance();
					$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `payroll_year_ID`='".$functionParameters[1]["year"]."' AND `major_period`=".$functionParameters[1]["majorPeriod"]." AND `minor_period`=".$functionParameters[1]["minorPeriod"], "payroll_calculate");
					if(count($result)>0) {
						$payrollPeriodID = $result[0]["id"];
					}else{
						$response["success"] = false;
						$response["errCode"] = 20;
						return $response;
					}
					unset($result);
					if($payrollPeriodID==0) {
						$response["success"] = false;
						$response["errCode"] = 30;
						return $response;
					}
					$uid = session_control::getSessionInfo("id");
					$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
					$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) SELECT ".$uid.",`payroll_employee_ID` FROM `payroll_period_employee` WHERE `payroll_period_ID`=".$payrollPeriodID." AND `processing`!=0", "payroll_calculate");
					return $reports->Payslip(array("payroll_period_ID"=>$payrollPeriodID));
				}
			break;
		case 'payroll.calculationDataSave':
			return $calcs->calculationDataSave($functionParameters[0]);
		case 'payroll.getCalculationData':
			return $calcs->getCalculationData($functionParameters[0]);
		case 'payroll.calculate':
			return isset($functionParameters[0]) ? $calcs->calculate($functionParameters[0]) : $calcs->calculate();
		case 'payroll.closePeriod':
			return $this->closePeriod($functionParameters[0]);
		case 'payroll.checkPeriodValidity':
			return $this->checkPeriodValidity();
		case 'payroll.getPeriodInformation':
			return isset($functionParameters[0]) ? $this->getPeriodInformation($functionParameters[0]) : $this->getPeriodInformation();
		case 'payroll.savePeriodDates':
			return $this->savePeriodDates($functionParameters[0]);
		case 'payroll.addEmployee2Period':
			return $employee->addEmployee2Period($functionParameters[0]);
		case 'payroll.getEmployeeFilterList':
			return $employee->getEmployeeFilterList($functionParameters[0]);
		case 'payroll.getEmployeeFilterDetail':
			return $employee->getEmployeeFilterDetail($functionParameters[0]);
		case 'payroll.saveEmployeeFilterDetail':
			return $employee->saveEmployeeFilterDetail($functionParameters[0]);
		case 'payroll.deleteEmployeeFilterDetail':
			return $employee->deleteEmployeeFilterDetail($functionParameters[0]);

		case 'payroll.getFieldModifierList':
			return $this->getFieldModifierList();
		case 'payroll.getFieldModifierDetail':
			return $this->getFieldModifierDetail($functionParameters[0]);
		case 'payroll.saveFieldModifierDetail':
			return $this->saveFieldModifierDetail($functionParameters[0]);
		case 'payroll.deleteFieldModifierDetail':
			return $this->deleteFieldModifierDetail($functionParameters[0]);

		case 'payroll.getDedAtSrcGlobalSettings':
			return $account->getDedAtSrcGlobalSettings();
		case 'payroll.saveDedAtSrcGlobalSettings':
			return $account->saveDedAtSrcGlobalSettings($functionParameters[0]);
		case 'payroll.getDedAtSrcCantonList':
			return $account->getDedAtSrcCantonDetail(); //ohne Parameter werden alle verfuegbaren Kantone gelistet
		case 'payroll.getDedAtSrcCantonDetail':
			return $account->getDedAtSrcCantonDetail($functionParameters[0]);
		case 'payroll.saveDedAtSrcCantonDetail':
			return $account->saveDedAtSrcCantonDetail($functionParameters[0]);
		case 'payroll.deleteDedAtSrcCantonDetail':
			return $account->deleteDedAtSrcCantonDetail($functionParameters[0]);
		case 'payroll.importDedAtSrcRates':
			return $account->importDedAtSrcRates($functionParameters[0]);

		case 'payroll.getPayslipCfgList':
			return $payslip->getPayslipCfgList();
		case 'payroll.getPayslipCfgDetail':
			return $payslip->getPayslipCfgDetail($functionParameters[0]);
		case 'payroll.savePayslipCfgDetail':
			return $payslip->savePayslipCfgDetail($functionParameters[0]);
		case 'payroll.deletePayslipCfgDetail':
			return $payslip->deletePayslipCfgDetail($functionParameters[0]);
		case 'payroll.getPayslipNotifications':
			return $payslip->getPayslipNotifications($functionParameters[0]);
		case 'payroll.savePayslipNotifications':
			return $payslip->savePayslipNotifications($functionParameters[0]);

		case 'payroll.getPaymentSplitList':
			return $payrollPayment->getPaymentSplitList($functionParameters[0]);
		case 'payroll.getPaymentSplitDetail':
			return $payrollPayment->getPaymentSplitDetail($functionParameters[0]);
		case 'payroll.savePaymentSplitDetail':
			return $payrollPayment->savePaymentSplitDetail($functionParameters[0]);
		case 'payroll.savePaymentSplitOrder':
			return $payrollPayment->savePaymentSplitOrder($functionParameters[0]);
		case 'payroll.deletePaymentSplitDetail':
			return $payrollPayment->deletePaymentSplitDetail($functionParameters[0]);
		case 'payroll.getDestBankDetail':
			return $payrollPayment->getDestBankDetail($functionParameters[0]);
		case 'payroll.getBankSourceDetail':
			return $payrollPayment->getBankSourceDetail($functionParameters[0]);
		case 'payroll.saveDestBankDetail':
			return $payrollPayment->saveDestBankDetail($functionParameters[0]);
		case 'payroll.saveBankSourceDetail':
			return $payrollPayment->saveBankSourceDetail($functionParameters[0]);
		case 'payroll.deleteDestBankDetail':
			return $payrollPayment->deleteDestBankDetail($functionParameters[0]);
		case 'payroll.deleteBankSourceDetail':
			return $payrollPayment->deleteBankSourceDetail($functionParameters[0]);

		default:
			return "Funktion unbekannt";
		}

	}
	
	public function sysPermission($getContent="") {
		if($getContent!="") {
			$permissions["payrollFin"]["addressDelete"]["title"] = "Adresse loeschen";
			$permissions["payrollFin"]["addressDelete"]["type"] = "bool";
			$permissions["payrollFin"]["addressDelete"]["default"] = true;

			return $permissions[$getContent];
		}else{
			return array(array("permissionItemAdd","permissionSystem","System",""), array("permissionSubItemAdd","permissionSystem","payrollFin","Adressverwaltung",""), array("permissionSubItemAdd","permissionSystem","systemConfig","Konfiguration","") );
		}
	}

	public function sysConfig($getContent=false) {
		if($getContent) {
		}else{
			return array(array("configItemAdd","generalSettings","Allg. Einstellungen","JS:alert('test1');"), array("configSubItemAdd","generalSettings","generalSettingsFormats","Datums- und Zahlenformate","JS:alert('test2');") );
		}
	}

	public function getPluginSettings() {
		$session_control = session_control::getInstance();
		$response["SETTINGS"] = $session_control->getSessionSettings("payroll");
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}




	private function array_diff_assoc_recursive($array1, $array2) {
		$difference=array();
		foreach($array1 as $key => $value) {
			if( is_array($value) ) {
				if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
					$difference[$key] = $value;
				} else {
					$new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
					if( !empty($new_diff) )
					$difference[$key] = $new_diff;
				}
			} else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
				$difference[$key] = $value;
			}
		}
		return $difference;
	}


	public function editFinMgmtAccountingConfig($param) {
		$arrSections = array("fin_acc_assign","mgmt_acc_split","mgmt_acc_assign");

		//1=mandatory add, 2=mand edit, 3=mand del, 4=add apostrophe in SQL, 5=regex
		$arrFields = array(
			array('id'=>array(false,true,true,false,'/^[0-9]{1,9}$/'),'payroll_company_ID'=>array(true,true,false,false,'/^[0-9]{1,9}$/'),'payroll_employee_ID'=>array(true,true,false,false,'/^[0-9]{1,9}$/'),'payroll_account_ID'=>array(true,true,false,true,'/^[0-9a-zA-Z]{1,5}$/'),'cost_center'=>array(false,false,false,true,'/^.{0,15}$/'),'account_no'=>array(true,true,false,true,'/^.{1,15}$/'),'counter_account_no'=>array(false,false,false,true,'/^.{0,15}$/'),'debitcredit'=>array(true,true,false,false,'/^[01]{1,1}$/'),'entry_text'=>array(true,true,false,true,'/^.{1,50}$/'),'invert_value'=>array(true,true,false,false,'/^[01]{1,1}$/'),'processing_order'=>array(false,false,false,false,'/^[0-9]{1,2}$/')),
			array('id'=>array(false,true,true,false,'/^[0-9]{1,9}$/'),'payroll_company_ID'=>array(false,true,false,false,'/^[0-9]{1,9}$/'),'payroll_employee_ID'=>array(false,true,false,false,'/^[0-9]{1,9}$/'),'payroll_account_ID'=>array(true,true,false,true,'/^[0-9a-zA-Z]{1,5}$/'),'cost_center'=>array(false,true,false,true,'/^.{1,15}$/'),'amount'=>array(true,true,false,false,'/^[0-9]{1,3}(\.[0-9]{0,2})?$/'),'invert_value'=>array(true,true,false,false,'/^[01]{1,1}$/'),'remainder'=>array(true,true,false,false,'/^[01]{1,1}$/'),'processing_order'=>array(false,false,false,false,'/^[0-9]{1,2}$/')),
			array('id'=>array(false,true,true,false,'/^[0-9]{1,9}$/'),'payroll_company_ID'=>array(false,true,false,false,'/^[0-9]{1,9}$/'),'payroll_employee_ID'=>array(false,true,false,false,'/^[0-9]{1,9}$/'),'payroll_account_ID'=>array(true,true,false,true,'/^[0-9a-zA-Z]{1,5}$/'),'cost_center'=>array(false,false,false,true,'/^.{0,15}$/'),'account_no'=>array(true,true,false,true,'/^.{1,15}$/'),'counter_account_no'=>array(false,false,false,true,'/^.{0,15}$/'),'debitcredit'=>array(true,true,false,false,'/^[01]{1,1}$/'),'entry_text'=>array(true,true,false,true,'/^.{1,50}$/'),'invert_value'=>array(true,true,false,false,'/^[01]{1,1}$/'),'processing_order'=>array(false,false,false,false,'/^[0-9]{1,2}$/')));
		$arrModes = array("add","edit","delete");
		if(!isset($param["section"]) || !in_array($param["section"], $arrSections) ) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid section";
			return $response;
		}else $section = array_search($param["section"], $arrSections);
		if(!isset($param["mode"]) || !in_array($param["mode"], $arrModes) ) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid mode";
			return $response;
		}else $mode = array_search($param["mode"], $arrModes);
		
		///////////////////////////////////////////////////
		// mandatory and validity checks
		///////////////////////////////////////////////////
		foreach($arrFields[$section] as $curFieldName=>$curFieldParam) {
			//mandantory check
			if($curFieldParam[$mode] && (!isset($param[$curFieldName]) || trim($param[$curFieldName])=="")) {
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errText"] = "mandatory check failed";
				$response["fieldName"] = $curFieldName;
				return $response;
			}
			//validity check
			if(isset($param[$curFieldName]) && $param[$curFieldName]!="" && !preg_match($curFieldParam[4], $param[$curFieldName])) {
				$response["success"] = false;
				$response["errCode"] = 40;
				$response["errText"] = "validity check failed";
				$response["fieldName"] = $curFieldName;
				return $response;
			}
		}

//TODO: hinweis: bei ...assign darf cost_center leer sein! -> regex!! ... bei split MUSS das feld gefaellt sein!!
		if($mode!=2) { //wenn ungleich 'delete' modus
			switch($section) {
			case 0:
			case 2:
				if($param["payroll_employee_ID"]>0) $param["processing_order"]=1;
				else if($param["payroll_company_ID"]>0 && $param["cost_center"]!="") $param["processing_order"]=2;
				else if($param["payroll_company_ID"]>0 && $param["cost_center"]=="") $param["processing_order"]=3;
				else if($param["payroll_company_ID"]==0 && $param["cost_center"]!="") $param["processing_order"]=4;
				else $param["processing_order"]=9;
				break;
			case 1:
				if($param["payroll_employee_ID"]>0) $param["processing_order"]=1;
				else if($param["payroll_company_ID"]>0) $param["processing_order"]=2;
				else $param["processing_order"]=9;
				break;
			}
		}

		//asseble SQL statement
		switch($mode) {
		case 0: //add
			$sqlFlds = array();
			$sqlVals = array();
			foreach($arrFields[$section] as $curFieldName=>$curFieldParam) {
				if($curFieldName!="id") {
					$sqlFlds[] = "`".$curFieldName."`";
					if(isset($param[$curFieldName]) && $param[$curFieldName]!="") $sqlVals[] = $curFieldParam[3] ? "'".$param[$curFieldName]."'" : $param[$curFieldName];
					else $sqlVals[] = $curFieldParam[3] ? "''" : "0";
				}
			}
			$sql = "INSERT INTO `payroll_".$arrSections[$section]."`(".implode(",",$sqlFlds).") VALUES(".implode(",",$sqlVals).")";
			break;
		case 1: //edit
			$sqlSet = array();
			foreach($arrFields[$section] as $curFieldName=>$curFieldParam) {
				if($curFieldName!="id") {
					if(isset($param[$curFieldName]) && $param[$curFieldName]!="") $sqlSet[] = "`".$curFieldName."`=".($curFieldParam[3] ? "'".$param[$curFieldName]."'" : $param[$curFieldName]);
					else $sqlSet[] = "`".$curFieldName."`=".($curFieldParam[3] ? "''" : "0");
				}
			}
			$sql = "UPDATE `payroll_".$arrSections[$section]."` SET ".implode(",",$sqlSet)." WHERE `id`=".addslashes($param["id"]);
			break;
		case 2: //delete
			$sql = "DELETE FROM `payroll_".$arrSections[$section]."` WHERE `id`=".addslashes($param["id"]);
			break;
		}
//communication_interface::alert("sql:".$sql);

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate($sql, "payroll_editFinMgmtAccountingConfig");
		$id = $mode==0 ? $system_database_manager->getLastInsertId() : $param["id"];

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["id"] = $id;
//		$response["data"] = $finAccAssign; //TODO:hier muessen die Daten retourniert werden...

		return $response;
	}


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
/*
		switch($paramFilterMode) {
		case 0: //all employees
			$system_database_manager->executeUpdate("UPDATE `payroll_period_employee` SET `payment_date`='".$paramDatePayment."',`interest_date`='".$paramDateInterest."',`core_user_ID_payment`=".session_control::getSessionInfo("id").",`processing`=2 WHERE `processing`=1 AND `core_user_ID_calc`!=0 AND `payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			//move (save) calculation results from temporary table `payroll_calculation_current` to `payroll_calculation_entry`
			$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `position`) SELECT calc.`payroll_year_ID`, calc.`payroll_period_ID`, calc.`payroll_employee_ID`, calc.`payroll_account_ID`, calc.`quantity`, calc.`rate`, calc.`amount`, calc.`allowable_workdays`, calc.`position` FROM `payroll_calculation_current` calc INNER JOIN `payroll_period_employee` prdemp ON prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_employee_ID`=calc.`payroll_employee_ID` WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			//save payment split data from temp-table to def-table table
			$system_database_manager->executeUpdate("INSERT INTO `payroll_payment_entry`(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount`) SELECT `payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount` FROM `payroll_payment_current` curpmt INNER JOIN `payroll_period_employee` prdemp ON prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_employee_ID`=curpmt.`payroll_employee_ID` WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			break;
		case 1: //only employees of a certain company
			$system_database_manager->executeUpdate("UPDATE `payroll_period_employee` prdemp INNER JOIN `payroll_employee` emp ON prdemp.`payroll_employee_ID`=emp.`id` AND emp.`payroll_company_ID`=".$paramCompanyID." SET prdemp.`payment_date`='".$paramDatePayment."',prdemp.`interest_date`='".$paramDateInterest."',prdemp.`core_user_ID_payment`=".session_control::getSessionInfo("id").",prdemp.`processing`=2 WHERE prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			//move (save) calculation results from temporary table `payroll_calculation_current` to `payroll_calculation_entry`
			$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `position`) SELECT calc.`payroll_year_ID`, calc.`payroll_period_ID`, calc.`payroll_employee_ID`, calc.`payroll_account_ID`, calc.`quantity`, calc.`rate`, calc.`amount`, calc.`allowable_workdays`, calc.`position` FROM `payroll_calculation_current` calc INNER JOIN `payroll_period_employee` prdemp ON prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_employee_ID`=calc.`payroll_employee_ID` INNER JOIN `payroll_employee` emp ON prdemp.`payroll_employee_ID`=emp.`id` AND emp.`payroll_company_ID`=".$paramCompanyID." WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			//save payment split data from temp-table to def-table table
			$system_database_manager->executeUpdate("INSERT INTO `payroll_payment_entry`(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount`) SELECT `payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount` FROM `payroll_payment_current` curpmt INNER JOIN `payroll_period_employee` prdemp ON prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0 AND prdemp.`payroll_employee_ID`=curpmt.`payroll_employee_ID` INNER JOIN `payroll_employee` emp ON prdemp.`payroll_employee_ID`=emp.`id` AND emp.`payroll_company_ID`=".$paramCompanyID." WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processPayment");
			break;
		case 2: //only a certain list of employees
//TODO: assemble SQL statement by an array of employee IDs
			break;
		}
*/
		$system_database_manager->executeUpdate("COMMIT", "payroll_processPayment");
/*
$paramDatePayment
$paramDateInterest
$paramFilterMode
$paramCompanyID

ALLE MA auswählen:
SELECT * FROM `payroll_period_employee` WHERE `processing`=1 AND `core_user_ID_calc`!=0;

Nur MA die einer bestimmten Firma angehoeren auswählen:
SELECT prdemp.* FROM `payroll_period_employee` prdemp INNER JOIN `payroll_employee` emp ON prdemp.`payroll_employee_ID`=emp.`id` AND emp.`payroll_company_ID`=1 WHERE prdemp.`processing`=1 AND prdemp.`core_user_ID_calc`!=0;
*/
//		communication_interface::alert(print_r($param,true));
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getFinMgmtAccountingInfo() {
		$system_database_manager = system_database_manager::getInstance();
		$finAccAssign = $system_database_manager->executeQuery("SELECT * FROM payroll_fin_acc_assign", "payroll_getFinMgmtAccountingInfo");
		$mgmtAccAssign = $system_database_manager->executeQuery("SELECT * FROM payroll_mgmt_acc_assign", "payroll_getFinMgmtAccountingInfo");
		$mgmtAccSplit = $system_database_manager->executeQuery("SELECT * FROM payroll_mgmt_acc_split", "payroll_getFinMgmtAccountingInfo");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["dataFinAccAssign"] = $finAccAssign;
		$response["dataMgmtAccAssign"] = $mgmtAccAssign;
		$response["dataMgmtAccSplit"] = $mgmtAccSplit;

		return $response;
	}

	public function processFinMgmtAccountingEntry($param) {
		require_once('chkDate.php');
		$chkDate = new chkDate();
		
//TODO: Sicherstellen, dass diese Nasen ab sofort nicht mehr gerechnet werden!! Und processing-flag hochsetzen auf 3!
		///////////////////////////////////////////////////
		// validity check of processing flags
		///////////////////////////////////////////////////
		if(!preg_match( '/^[01]{1,1}$/', $param["fin_acc_process"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "financial accounting processing flag";
			$response["errField"] = "fin_acc_process";
			return $response;
		}else $finAccProcess = $param["fin_acc_process"]==1 ? true : false;
		if(!preg_match( '/^[01]{1,1}$/', $param["mgmt_acc_process"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "management accounting processing flag";
			$response["errField"] = "mgmt_acc_process";
			return $response;
		}else $mgmtAccProcess = $param["mgmt_acc_process"]==1 ? true : false;

		if(!$finAccProcess && !$mgmtAccProcess) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "select at least one accounting method";
			return $response;
		}

		if($finAccProcess) {
			if(!$chkDate->chkDate($param["fin_acc_date"], 1, $finAccDate)) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "date value";
				$response["errField"] = "fin_acc_date";
				return $response;
			}
		}
		if($mgmtAccProcess) {
			if(!$chkDate->chkDate($param["mgmt_acc_date"], 1, $mgmtAccDate)) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "date value";
				$response["errField"] = "mgmt_acc_date";
				return $response;
			}
			if(!preg_match( '/^[01]{1,1}$/', $param["mgmt_acc_quantity"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "quantity processing flag";
				$response["errField"] = "mgmt_acc_quantity";
				return $response;
			}else $mgmtAccQuantity = $param["mgmt_acc_quantity"]==1 ? true : false;
			if(!preg_match( '/^[01]{1,1}$/', $param["mgmt_acc_round"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "rounding processing flag";
				$response["errField"] = "mgmt_acc_round";
				return $response;
			}else $mgmtAccRound = $param["mgmt_acc_round"]==1 ? true : false;
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
		$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_getPeriodInformation");
		$payrollPeriodID = $result[0]["id"];

		$system_database_manager->executeUpdate("BEGIN", "payroll_processFinMgmtAccountingEntry");

		$uid = session_control::getSessionInfo("id");

		$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_processFinMgmtAccountingEntry");
		switch($paramFilterMode) {
		case 0: //all employees
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) SELECT ".$uid.",`payroll_employee_ID` FROM `payroll_period_employee` WHERE `processing`>=2 AND `payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");
			break;
		case 1: //only employees of a certain company
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) SELECT ".$uid.",prdemp.`payroll_employee_ID` FROM `payroll_period_employee` prdemp INNER JOIN `payroll_employee` emp ON prdemp.`payroll_employee_ID`=emp.`id` AND emp.`payroll_company_ID`=".$paramCompanyID." WHERE prdemp.`processing`>=2 AND prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");
			break;
		case 2: //only a certain list of employees
			//TODO: assemble SQL statement by an array of employee IDs
			break;
		}

		/////////////////
		// FIBU
		/////////////////
		if($finAccProcess) {
			//Zuerst nur die FIBU-Daten der betroffenen MA loeschen…
			$system_database_manager->executeUpdate("DELETE accetry FROM payroll_fin_acc_entry accetry INNER JOIN `payroll_tmp_change_mng` emplList ON accetry.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." WHERE accetry.`payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");

			//…dann die Records neu anlegen (ebenfalls nur fuer die betroffenen MA)…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_fin_acc_entry`(`payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `account_no`, `counter_account_no`, `cost_center`, `amount_local`, `debitcredit`, `entry_text`, `amount_quantity`) SELECT calc.`payroll_period_ID`, calc.`payroll_employee_ID`, accasng.`payroll_account_ID`, accasng.`account_no`, accasng.`counter_account_no`, accasng.`cost_center`, IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`debitcredit`, accasng.`entry_text`, 0 FROM payroll_fin_acc_assign accasng INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_period_ID`=".$payrollPeriodID." INNER JOIN `payroll_tmp_change_mng` emplList ON calc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM payroll_fin_acc_assign aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`cost_center`='' OR aa.`cost_center`=ep.`CostCenter`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id`", "payroll_processFinMgmtAccountingEntry");

			//…und das Datum (Buchungsdatum) in payroll_period_employee anpassen plus die ID des Benutzers speichern, der die Verbuchung durchgefaehrt hat…
			$system_database_manager->executeUpdate("UPDATE `payroll_period_employee` prdemp INNER JOIN `payroll_tmp_change_mng` emplList ON prdemp.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." SET prdemp.`fin_acc_date`='".$finAccDate."', prdemp.`core_user_ID_fin_acc`=".$uid.", prdemp.`processing`=3 WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");
		}

		/////////////////
		// BEBU
		/////////////////
		//TODO: Wenn Flag $mgmtAccQuantity=TRUE, dann muessen die nachfolgenden Statements 2x durchlaufen werden, allerdings einmal mit amount und einmal mit quantity
		if($mgmtAccProcess) {

			//Zuerst nur die BEBU-Daten der betroffenen MA loeschen…
			$system_database_manager->executeUpdate("DELETE accspl FROM `payroll_mgmt_acc_entry` accspl INNER JOIN `payroll_tmp_change_mng` emplList ON accspl.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." WHERE accspl.`payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");
//			$system_database_manager->executeUpdate("DELETE accspl FROM `payroll_tmp_mgmt_acc_split` accspl INNER JOIN `payroll_tmp_change_mng` emplList ON accspl.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." WHERE accspl.`payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_mgmt_acc_split`", "payroll_processFinMgmtAccountingEntry");

			//…dann die Records in einer temporären MEMORY-Table neu anlegen. Zuerst LOA mit expliziter Übersteuerung der Kostenstelle…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`) SELECT ".$payrollPeriodID.",emp.`payroll_company_ID`,empacc.`payroll_employee_ID`,empacc.`CostCenter`,empacc.`payroll_account_ID`,0,0,calc.`amount`,0,0,0,1,0,0 FROM `payroll_employee_account` empacc INNER JOIN `payroll_tmp_change_mng` emplList ON empacc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_employee_ID`=emplList.`numID` AND calc.`payroll_account_ID`=empacc.`payroll_account_ID` AND calc.`payroll_period_ID`= ".$payrollPeriodID." WHERE empacc.`CostCenter`!=''", "payroll_processFinMgmtAccountingEntry");

			//…als Nächstes ebenfalls Records in temporärer MEMORY-Table anlegen, aber jetzt die aebrigen %-Verteilungen. Wichtig: Bereits verarbeitete LOA ausschliessen, 100%-Zuweisungen koennen direkt verarbeitet und die enspr. Records abgeschlossen/fixiert werden...
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`) SELECT calc.`payroll_period_ID`, emp.`payroll_company_ID`, emp.`id`, IF(accasng.`payroll_employee_ID`=0 AND emp.`CostCenter`!='', emp.`CostCenter`,accasng.`cost_center`), calc.`payroll_account_ID`, calc.`amount`,calc.`amount`, IF(accasng.`amount`=100,IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`amount`) ,accasng.`processing_order`,accasng.`invert_value`,0,IF(accasng.`amount`=100,1,0),0,0 FROM `payroll_mgmt_acc_split` accasng INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_period_ID`=".$payrollPeriodID." INNER JOIN `payroll_tmp_change_mng` emplList ON calc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` LEFT JOIN `payroll_tmp_mgmt_acc_split` tas ON tas.`payroll_period_ID`=calc.`payroll_period_ID` AND tas.`payroll_employee_ID`=calc.`payroll_employee_ID` AND tas.`payroll_account_ID`=calc.`payroll_account_ID` AND tas.`processing_done`=1 INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM `payroll_mgmt_acc_split` aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id` WHERE tas.`processing_done` IS NULL", "payroll_processFinMgmtAccountingEntry");

			//…100%er wurden im obigen Statement verarbeitet. Hier werden nun %-Verteilungen <100%...
			$system_database_manager->executeUpdate("UPDATE `payroll_tmp_mgmt_acc_split` SET `amount`=`amount`/100*`amount_available` WHERE `processing_done`=0", "payroll_processFinMgmtAccountingEntry");

			//…Werte runden…
			if($mgmtAccRound) {
				$system_database_manager->executeUpdate("UPDATE `payroll_tmp_mgmt_acc_split` SET `amount`=ROUND(`amount`/0.05)*0.05 WHERE `having_rounding`=1 AND `processing_done`=0", "payroll_processFinMgmtAccountingEntry");
//				$system_database_manager->executeUpdate("UPDATE `payroll_tmp_mgmt_acc_split` SET `amount`=ROUND(`amount`/`rounding`)*`rounding` WHERE `having_rounding`=1 AND `processing_done`=0", "payroll_processFinMgmtAccountingEntry");
			}

			//…Falls es einen Restbetrag gibt, wird dieser nun noch entsprechend zugewiesen…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`,`remainder`) SELECT accsplt.`payroll_period_ID`,accsplt.`payroll_company_ID`,accsplt.`payroll_employee_ID`,IF(x1.`remainder`=1,accsplt.`cost_center`,empl.`CostCenter`),accsplt.`payroll_account_ID`,accsplt.`amount_initial`,accsplt.`amount_available`, x1.`amount_initial`-x1.`amount_sum`, 0,0,accsplt.`amount_quantity`,1,accsplt.`having_rounding`,accsplt.`rounding`,accsplt.`remainder` FROM payroll_tmp_mgmt_acc_split accsplt INNER JOIN (SELECT `payroll_employee_ID`, `payroll_account_ID`, `amount_initial`, SUM(`amount`) as amount_sum, MAX(`amount`) as amount_max, `remainder` FROM payroll_tmp_mgmt_acc_split WHERE processing_done=0 GROUP BY payroll_employee_ID, payroll_account_ID) x1 ON accsplt.`payroll_employee_ID`=x1.`payroll_employee_ID` AND accsplt.`payroll_account_ID`=x1.`payroll_account_ID` AND accsplt.`amount`=x1.amount_max INNER JOIN `payroll_employee` empl ON empl.`id`=accsplt.`payroll_employee_ID` WHERE accsplt.`processing_done`=0", "payroll_processFinMgmtAccountingEntry");


			//…"0.00" Beträge loeschen (TODO: Ist das OK?)…
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_mgmt_acc_split` WHERE `amount`=0", "payroll_processFinMgmtAccountingEntry");

			//…Records der temporären Tabelle in eine InnoDB Tabelle speichern…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_mgmt_acc_entry`(`payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `account_no`, `counter_account_no`, `cost_center`, `amount_local`, `debitcredit`, `entry_text`, `amount_quantity`) SELECT calc.`payroll_period_ID`, calc.`payroll_employee_ID`, accasng.`payroll_account_ID`, accasng.`account_no`, accasng.`counter_account_no`, calc.`cost_center`, IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`debitcredit`, accasng.`entry_text`, 0 FROM payroll_mgmt_acc_assign accasng INNER JOIN (SELECT `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `cost_center`, SUM(`amount`) as `amount` FROM payroll_tmp_mgmt_acc_split GROUP BY `payroll_employee_ID`,`payroll_account_ID`,`cost_center`) calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_period_ID`=".$payrollPeriodID." INNER JOIN `payroll_tmp_change_mng` emplList ON calc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM payroll_mgmt_acc_assign aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`cost_center`='' OR aa.`cost_center`=ep.`CostCenter`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id`", "payroll_processFinMgmtAccountingEntry");

			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_mgmt_acc_split`", "payroll_processFinMgmtAccountingEntry");

			//…und das Datum (Buchungsdatum) in payroll_period_employee anpassen plus die ID des Benutzers speichern, der die Verbuchung durchgefaehrt hat…
			$system_database_manager->executeUpdate("UPDATE payroll_period_employee prdemp INNER JOIN `payroll_tmp_change_mng` emplList ON prdemp.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." SET prdemp.`mgmt_acc_date`='".$mgmtAccDate."', prdemp.`core_user_ID_mgmt_acc`=".$uid.", prdemp.`processing`=3 WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID, "payroll_processFinMgmtAccountingEntry");
		}

		$system_database_manager->executeUpdate("COMMIT", "payroll_processFinMgmtAccountingEntry");

//		communication_interface::alert(print_r($param,true));
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getPeriodInformation($param=null) {
		$system_database_manager = system_database_manager::getInstance();
		if(is_null($param)) {
			//year unknown... get the year of the current period
			$result = $system_database_manager->executeQuery("SELECT `payroll_year_ID` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_getPeriodInformation");
			if(count($result)==1) {
				$payrollYearID = $result[0]["payroll_year_ID"];
			}else{
				$response["success"] = false;
				$response["errCode"] = 10;
				return $response;
			}
		}else{
			///////////////////////////////////////////////////
			// year must be numeric and non-decimal
			///////////////////////////////////////////////////
			if(!isset($param["year"]) || !preg_match( '/^(19|20)[0-9]{2,2}$/', $param["year"])) {
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "invalid year";
				return $response;
			}
			$payrollYearID = $param["year"];
		}
	
		$ret["years"] = array();
		$result = $system_database_manager->executeQuery("SELECT DISTINCT `payroll_year_ID` FROM `payroll_period` ORDER BY `payroll_year_ID`", "payroll_getPeriodInformation");
		foreach($result as $row) $ret["years"][] = $row["payroll_year_ID"];
	
		$ret["info"]["year"] = $payrollYearID;
		$majorPeriod = 0;
		$minorPeriod = 0;
		$currentPeriodID = 0;
		$totalPeriods = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
		$usedPeriods = array();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_period` WHERE `payroll_year_ID`=".$payrollYearID." ORDER BY `major_period`,`minor_period`", "payroll_getPeriodInformation");
		foreach($result as $row) {
			if($row["minor_period"]==0) {
				$ret["major_period"][$row["major_period"]]["info"] = $row;
				$usedPeriods[] = $row["major_period"];
			}else $ret["major_period"][$row["major_period"]]["minor_period"][$row["minor_period"]]["info"] = $row;
	
			//check if record is the current (open) period
			if($row["locked"]==0 && $row["finalized"]==0) {
				$majorPeriod = $row["major_period"];
				$minorPeriod = $row["minor_period"];
				$currentPeriodID = $row["id"];
			}
		}
		$ret["info"]["year"] = $payrollYearID;
		$ret["info"]["status"] = $majorPeriod==0 && $minorPeriod==0 ? 0 : 1; //the selected year has no open period
	
		$usedPeriods = array_unique($usedPeriods);
		$availablePeriods = array_diff($totalPeriods,$usedPeriods);
		sort($availablePeriods);
	
		if($ret["info"]["status"]==1) {
			$ret["currentPeriod"]["major_period"] = $majorPeriod;
			$ret["currentPeriod"]["minor_period"] = $minorPeriod;
	
			if($currentPeriodID!=0) {
				$arr = array("suspended","calculationUnprocessed","calculationProcessed","payout","financialAccounting","managementAccounting","total");
				foreach($arr as $tr) $ret["currentPeriod"]["processingStatus"][$tr] = 0;
	
				$statusRes = $system_database_manager->executeQuery("SELECT processing,IF(core_user_ID_calc!=0,1,0) as calc_status,IF(core_user_ID_fin_acc!=0,1,0) as fin_acc_status,IF(core_user_ID_mgmt_acc!=0,1,0) as mgmt_acc_status, COUNT(*) as employeeCount FROM payroll_period_employee WHERE payroll_period_ID=".$currentPeriodID." GROUP BY processing,calc_status,fin_acc_status,mgmt_acc_status", "payroll_getPeriodInformation");
				foreach($statusRes as $statusRec) {
					$ret["currentPeriod"]["processingStatus"]["total"] += $statusRec["employeeCount"];
					if($statusRec["processing"]==0) $ret["currentPeriod"]["processingStatus"]["suspended"] += $statusRec["employeeCount"];
					else if($statusRec["processing"]==1 && $statusRec["calc_status"]==0) $ret["currentPeriod"]["processingStatus"]["calculationUnprocessed"] += $statusRec["employeeCount"];
					else if($statusRec["processing"]==1 && $statusRec["calc_status"]==1) $ret["currentPeriod"]["processingStatus"]["calculationProcessed"] += $statusRec["employeeCount"];
					else if($statusRec["processing"]==2) {
						$ret["currentPeriod"]["processingStatus"]["calculationProcessed"] += $statusRec["employeeCount"];
						$ret["currentPeriod"]["processingStatus"]["payout"] += $statusRec["employeeCount"];
					}else if($statusRec["processing"]==3) {
						$ret["currentPeriod"]["processingStatus"]["calculationProcessed"] += $statusRec["employeeCount"];
						$ret["currentPeriod"]["processingStatus"]["payout"] += $statusRec["employeeCount"];
						if($statusRec["fin_acc_status"]==1) $ret["currentPeriod"]["processingStatus"]["financialAccounting"] += $statusRec["employeeCount"];
						if($statusRec["mgmt_acc_status"]==1) $ret["currentPeriod"]["processingStatus"]["managementAccounting"] += $statusRec["employeeCount"];
					}
				}
			}
	
			$lowestPeriod = count($availablePeriods)>0 ? $availablePeriods[0] : 17; //if there are no available periods, then use the non-existing period number 17 (dummy value)
	//communication_interface::alert("lowestPeriod: ".$lowestPeriod." / ".print_r($availablePeriods,true));
			$ret["nextPeriod"]["year"][strval($payrollYearID)] = array();
			if($lowestPeriod<15) $ret["nextPeriod"]["year"][strval($payrollYearID)][] = $lowestPeriod;
	
			if(in_array(15, $availablePeriods)) $ret["nextPeriod"]["year"][strval($payrollYearID)][] = 15;
			else if(in_array(16, $availablePeriods)) $ret["nextPeriod"]["year"][strval($payrollYearID)][] = 16;
	
			if($lowestPeriod>12) {
				$ret["nextPeriod"]["year"][strval($payrollYearID+1)] = array();
				$ret["nextPeriod"]["year"][strval($payrollYearID+1)][] = 1;
			}
			// 1 - 14 nacheinander, 15+16 Grati (frei platzierbar)
			//set proposed start dates
			$arrWageType = array("Wage_Date", "Salary_Date", "HourlyWage_Date");
			$currentPrdInfo = $ret["major_period"][$majorPeriod]["info"];
			foreach($arrWageType as $wageType) {
	//			$arrFrom = explode("-",$currentPrdInfo[$wageType."From"]);
				$arrTo = explode("-",$currentPrdInfo[$wageType."To"]);
				$ret["nextPeriod"]["proposedDates"][$wageType."From"] = date("Y-m-d", mktime(0, 0, 0, $arrTo[1], $arrTo[2]+1, $arrTo[0]));
				$ret["nextPeriod"]["proposedDates"][$wageType."To"] = date("Y-m-d", mktime(0, 0, 0, $arrTo[1], $arrTo[2], $arrTo[0]) == mktime(0, 0, 0, $arrTo[1]+1, 0, $arrTo[0]) ? mktime(0, 0, 0, $arrTo[1]+2, 0, $arrTo[0]) : mktime(0, 0, 0, $arrTo[1]+1, $arrTo[2], $arrTo[0]));
			}
		}
	
		$response["data"] = $ret;
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
	public function savePeriodDates($param) {
			require_once('chkDate.php');
			$chkDate = new chkDate();
					
		if(!preg_match( '/^[0-9]{1,9}$/', $param["payroll_period_ID"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid year";
			return $response;
		}
	
		//determine current open period
		$ret = $this->getPeriodInformation();
		$payrollYearID = $ret["data"]["info"]["year"];
		$majorPeriod = $ret["data"]["currentPeriod"]["major_period"];
		$minorPeriod = $ret["data"]["currentPeriod"]["minor_period"];
		$payrollPeriodID = $minorPeriod!=0 ? $ret["data"]["major_period"][$majorPeriod]["minor_period"][$minorPeriod]["info"]["id"] : $ret["data"]["major_period"][$majorPeriod]["info"]["id"];
		if($param["payroll_period_ID"]!=$payrollPeriodID) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "the specified period is read-only";
			return $response;
		}
	
		///////////////////////////////////////////////////
		// verifying DATE parameters
		///////////////////////////////////////////////////
		$updateItems = array();
		$arrTmp = array("HourlyWage_DateFrom", "Wage_DateFrom", "Salary_DateFrom", "HourlyWage_DateTo", "Wage_DateTo", "Salary_DateTo");
		foreach($arrTmp as $fld) {
			if($chkDate->chkDate($param[$fld], 1, $retDate)) {
				$updateItems[] = "`".$fld."`='".$retDate."'";
			}else{
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errText"] = "invalid DATE";
				$response["errFields"] = array($fld);
				return $response;
			}
		}
	
		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("UPDATE `payroll_period` SET ".implode(",", $updateItems)." WHERE `id`=".$param["payroll_period_ID"], "payroll_savePeriodDates");
	
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
	public function checkPeriodValidity() {
		$system_database_manager = system_database_manager::getInstance();
	
		//get the id of the current period
		$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_checkPeriodValidity");
		$payrollPeriodID = $result[0]["id"];
	
		$response["totalCount"] = 0; // total number of all employees of the current period (no mater which status)
		$response["processedCount"] = 0;
		$response["unprocessedCount"] = 0;
		$response["calculatedCount"] = 0;
		$response["payoutCount"] = 0;
		$response["finaccEntryCount"] = 0;
		$response["mgmtaccEntryCount"] = 0;
		$result = $system_database_manager->executeQuery("SELECT `payroll_employee_ID`, IF(`processing`!=0,1,0) as `active`, IF(`processing`!=0 AND `core_user_ID_calc`!=0,1,0) as `calculated`, IF(`processing`!=0 AND `core_user_ID_payment`!=0,1,0) as `payed`, IF(`processing`!=0 AND `core_user_ID_fin_acc`!=0,1,0) as `fin_acc`, IF(`processing`!=0 AND `core_user_ID_mgmt_acc`!=0,1,0) as `mgmt_acc` FROM `payroll_period_employee` WHERE `payroll_period_ID`=".$payrollPeriodID, "payroll_checkPeriodValidity");
		foreach($result as $row) {
			$response["totalCount"]++;
			if($row["active"]==1) $response["processedCount"]++;
			else $response["unprocessedCount"]++;
			if($row["calculated"]==1) $response["calculatedCount"]++;
			if($row["payed"]==1) $response["payoutCount"]++;
			if($row["fin_acc"]==1) $response["finaccEntryCount"]++;
			if($row["mgmt_acc"]==1) $response["mgmtaccEntryCount"]++;
		}
	
		// check if all possible employees have been inserted in the current period
		if($response["totalCount"] != $response["processedCount"]) {
			$response["warning"]["employeeSetIncomplete"] = "Not all possible employees have been processed";
			// get the list of unprocessed employees
			$response["unprocessedList"] = $system_database_manager->executeQuery("SELECT emp.`id`, emp.`EmployeeNumber`, emp.`FirstName`, emp.`Lastname` FROM `payroll_employee` emp INNER JOIN `payroll_period_employee` ppe ON ppe.`payroll_period_ID`=".$payrollPeriodID." AND ppe.`processing`=0 AND ppe.`payroll_employee_ID`=emp.`id` ORDER BY emp.`Lastname`, emp.`FirstName`, emp.`EmployeeNumber`", "payroll_checkPeriodValidity");
		}
	
		// check if all inserted employees have been calculated
		if($response["processedCount"] != $response["calculatedCount"]) $response["error"]["calculationIncomplete"] = "Not all employees have been calculated";
	
		// check if all inserted employees have been finalized and got a payout
		if($response["processedCount"] != $response["payoutCount"]) $response["error"]["payoutIncomplete"] = "Not all employees have been finalized or received a payout";
	
		// check if the entries for the financial accounting of all inserted employees have been processed
		if($response["processedCount"] != $response["finaccEntryCount"]) $response["warning"]["financialAccountingIncomplete"] = "The financial accounting process was not finalized";
	
		// check if the entries for the management accounting of all inserted employees have been processed
		if($response["processedCount"] != $response["mgmtaccEntryCount"]) $response["warning"]["managementAccountingIncomplete"] = "The management accounting process was not finalized";
	
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
	public function closePeriod($param) {
		require_once('chkDate.php');
		$chkDate = new chkDate();

		///////////////////////////////////////////////////
		// year must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!isset($param["payroll_year_ID"]) || !preg_match( '/^(19|20)[0-9]{2,2}$/', $param["payroll_year_ID"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid year";
			$response["errFields"] = array("payroll_year_ID");
			return $response;
		}
		$payrollYearID_NEW = $param["payroll_year_ID"];
	
		///////////////////////////////////////////////////
		// major period must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!isset($param["major_period"]) || !preg_match( '/^[0-9]{1,2}$/', $param["major_period"]) || $param["major_period"]<1 || $param["major_period"]>16) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid major period";
			$response["errFields"] = array("major_period");
			return $response;
		}
		$majorPeriod_NEW = $param["major_period"];
	
		///////////////////////////////////////////////////
		// verifying BOOL parameters
		///////////////////////////////////////////////////
		$arrTmp = array("FwdEmpl","FwdData");
		foreach($arrTmp as $fld) {
			if(!isset($param[$fld]) || !preg_match( '/^[01]{1,1}$/', $param[$fld])) {
	//communication_interface::alert("err 30... ".$fld."=".$param[$fld]);
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errText"] = "invalid bool parameter";
				$response["errFields"] = array($fld);
				return $response;
			}
		}
	
		///////////////////////////////////////////////////
		// verifying DATE parameters
		///////////////////////////////////////////////////
		$arrTmp = array("HourlyWage_DateFrom", "Wage_DateFrom", "Salary_DateFrom", "HourlyWage_DateTo", "Wage_DateTo", "Salary_DateTo");
		foreach($arrTmp as $fld) {
			if($chkDate->chkDate($param[$fld], 1, $retDate)) {
				$param[$fld] = $retDate;
			}else{
				$response["success"] = false;
				$response["errCode"] = 40;
				$response["errText"] = "invalid DATE";
				$response["errFields"] = array($fld);
				return $response;
			}
		}
	
		//determine current open period
		$ret = $this->getPeriodInformation();
		$payrollYearID = $ret["data"]["info"]["year"];
		$majorPeriod = $ret["data"]["currentPeriod"]["major_period"];
		$minorPeriod = $ret["data"]["currentPeriod"]["minor_period"];
		$payrollPeriodID = $minorPeriod!=0 ? $ret["data"]["major_period"][$majorPeriod]["minor_period"][$minorPeriod]["info"]["id"] : $ret["data"]["major_period"][$majorPeriod]["info"]["id"];
	
		///////////////////////////////////////////////////
		// check if submitted year and major_period are valid
		///////////////////////////////////////////////////
		if(!isset($ret["data"]["nextPeriod"]["year"][strval($payrollYearID_NEW)]) || !in_array($majorPeriod_NEW,$ret["data"]["nextPeriod"]["year"][strval($payrollYearID_NEW)])) {
			$response["success"] = false;
			$response["errCode"] = 50;
			$response["errText"] = "invalid combination of year and major period";
			return $response;
		}
	
		$uid = session_control::getSessionInfo("id");
	
		$minorPeriod_NEW = 0;
	
		$Wage_DateFrom = $param["Wage_DateFrom"];
		$Wage_DateTo = $param["Wage_DateTo"];
		$Salary_DateFrom = $param["Salary_DateFrom"];
		$Salary_DateTo = $param["Salary_DateTo"];
		$HourlyWage_DateFrom = $param["HourlyWage_DateFrom"];
		$HourlyWage_DateTo = $param["HourlyWage_DateTo"];
	
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT MAX(`id`) as payroll_year_ID FROM `payroll_year`", "payroll_closePeriod");
		$newPayrollYearID = count($result)>0 ? $result[0]["payroll_year_ID"]+1 : 0;
	
		$system_database_manager->executeUpdate("BEGIN", "payroll_closePeriod");
		//mark current period as locked and finalized
		$system_database_manager->executeUpdate("UPDATE `payroll_period` SET `locked`=1, `finalized`=1, `datetime_locked`=NOW(), `core_user_ID_locked`=".$uid.", `datetime_finalized`=NOW(), `core_user_ID_finalized`=".$uid." WHERE `id`=".$payrollPeriodID, "payroll_closePeriod");
		if($newPayrollYearID==$payrollYearID_NEW) {
			//create a new payroll year if necessary
			$system_database_manager->executeUpdate("INSERT INTO `payroll_year`(`id`,`date_start`,`date_end`) VALUES(".$payrollYearID_NEW.",'".$payrollYearID_NEW."-01-01','".$payrollYearID_NEW."-12-31')", "payroll_closePeriod");
			//TODO: beim Jahreswechsel muessen *ALLE* jahresabhängigen Daten (z.B. payroll_account) neu angelegt werden!
			$system_database_manager->executeUpdate("INSERT INTO `payroll_account`(`id`, `payroll_year_ID`, `processing_order`, `sign`, `print_account`, `var_fields`, `input_assignment`, `output_assignment`, `having_limits`, `having_calculation`, `having_rounding`, `payroll_formula_ID`, `surcharge`, `factor`, `quantity`, `rate`, `amount`, `round_param`, `limits_aux_account_ID`, `limits_calc_mode`, `max_limit`, `min_limit`, `deduction`, `quantity_conversion`, `quantity_decimal`, `quantity_print`, `rate_conversion`, `rate_decimal`, `rate_print`, `amount_conversion`, `amount_decimal`, `amount_print`, `mandatory`, `carry_over`, `insertion_rules`, `bold`, `space_before`, `space_after`) SELECT `id`, ".$payrollYearID_NEW.", `processing_order`, `sign`, `print_account`, `var_fields`, `input_assignment`, `output_assignment`, `having_limits`, `having_calculation`, `having_rounding`, `payroll_formula_ID`, `surcharge`, `factor`, `quantity`, `rate`, `amount`, `round_param`, `limits_aux_account_ID`, `limits_calc_mode`, `max_limit`, `min_limit`, `deduction`, `quantity_conversion`, `quantity_decimal`, `quantity_print`, `rate_conversion`, `rate_decimal`, `rate_print`, `amount_conversion`, `amount_decimal`, `amount_print`, `mandatory`, `carry_over`, `insertion_rules`, `bold`, `space_before`, `space_after` FROM `payroll_account` WHERE `payroll_year_ID`=".$payrollYearID, "payroll_closePeriod");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_account_label`(`payroll_account_ID`, `payroll_year_ID`, `language`, `label`, `quantity_unit`, `rate_unit`) SELECT `payroll_account_ID`, ".$payrollYearID_NEW.", `language`, `label`, `quantity_unit`, `rate_unit` FROM `payroll_account_label` WHERE `payroll_year_ID`=".$payrollYearID, "payroll_closePeriod");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_account_linker`(`payroll_account_ID`, `payroll_year_ID`, `payroll_child_account_ID`, `field_assignment`, `fwd_neg_values`, `invert_value`, `child_account_field`) SELECT `payroll_account_ID`, ".$payrollYearID_NEW.", `payroll_child_account_ID`, `field_assignment`, `fwd_neg_values`, `invert_value`, `child_account_field` FROM `payroll_account_linker` WHERE `payroll_year_ID`=".$payrollYearID, "payroll_closePeriod");
		}
	
		//`major_period_associated`
		if($majorPeriod_NEW>0 && $majorPeriod_NEW<13) $majorPeriodAssociated_NEW = $majorPeriod_NEW;
		else if($majorPeriod_NEW==13 || $majorPeriod_NEW==14) $majorPeriodAssociated_NEW = 12;
		else {
			//$majorPeriod_NEW==15 or $majorPeriod_NEW==16
			if($majorPeriod>0 && $majorPeriod<13) $majorPeriodAssociated_NEW = $majorPeriod;
			else{
				//get the latest major_period of the new period's year
				$resMPA = $system_database_manager->executeQuery("SELECT IF(MAX(`major_period`) IS NULL,0,MAX(`major_period`)) as majorPeriod FROM `payroll_period` WHERE `payroll_year_ID`=".$payrollYearID_NEW." AND `major_period`>0 AND `major_period`<13", "payroll_closePeriod");
				$majorPeriodAssociated_NEW = $resMPA[0]["majorPeriod"];
			}
		}
	
		//insert new period
		$system_database_manager->executeUpdate("INSERT INTO `payroll_period`(`payroll_year_ID`,`major_period`,`minor_period`,`major_period_associated`,`StatementDate`,`Wage_DateFrom`,`Wage_DateTo`,`Salary_DateFrom`,`Salary_DateTo`,`HourlyWage_DateFrom`,`HourlyWage_DateTo`,`datetime_created`,`core_user_ID_created`,`locked`,`datetime_locked`,`core_user_ID_locked`,`finalized`,`datetime_finalized`,`core_user_ID_finalized`) VALUES(".$payrollYearID_NEW.",".$majorPeriod_NEW.",".$minorPeriod_NEW.",".$majorPeriodAssociated_NEW.",'0000-00-00','".$Wage_DateFrom."','".$Wage_DateTo."','".$Salary_DateFrom."','".$Salary_DateTo."','".$HourlyWage_DateFrom."','".$HourlyWage_DateTo."',NOW(),".$uid.",0,'0000-00-00',0,0,'0000-00-00',0)", "payroll_closePeriod");
		$id = $system_database_manager->getLastInsertId();
		//OBSOLET, da bereits bei Auszahlung erfolgt: move (save) calculation results from temporary table `payroll_calculation_current` to `payroll_calculation_entry`
		//OBSOLET, da bereits bei Auszahlung erfolgt: $system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `position`) SELECT `payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `position` FROM `payroll_calculation_current`", "payroll_closePeriod");
	//TODO(optional): Die in der Tabelle `payroll_period_employee` aufgefaehrten MA muessen auf fixiert gesetzt werden (<-ev. gar nicht noetig, da die dazugehoerige Periode selbst schon als fixiert markiert wurde...)
	//TODO: Die in der Tabelle `payroll_period_employee` aufgefaehrten MA muessen auf Austritte abgeglichen werden (ausgetretene MA muessen in der neuen Periode geloescht werden)
	
		//update `payroll_period_employee`.`EmploymentStatus` *before* assigning the employees to the new period
		if($majorPeriod_NEW>0 && $majorPeriod_NEW<13 && $minorPeriod_NEW==0) {
			$sqlYearNew = $payrollYearID_NEW;
			$sqlMonthNew = substr("0".$majorPeriod_NEW,-2);
			$sqlYearNewP1 = $payrollYearID_NEW;
			if($majorPeriod_NEW==12) {
				$sqlYearNewP1++;
				$sqlMonthNewP1 = "01";
			}else $sqlMonthNewP1 = substr("0".($majorPeriod_NEW+1),-2);
	
			//change `payroll_employee`.`EmploymentStatus` from 4 to 3
			$system_database_manager->executeUpdate("UPDATE `payroll_employee` emp LEFT JOIN `payroll_employment_period` empprd ON emp.`id`=empprd.`payroll_employee_ID` AND empprd.`DateFrom`<'".$sqlYearNewP1."-".$sqlMonthNewP1."-01' AND (empprd.`DateTo`>'".$sqlYearNew."-".$sqlMonthNew."-01' OR empprd.`DateTo`='0000-00-00') SET emp.`EmploymentStatus`=3 WHERE emp.`EmploymentStatus`=4 AND empprd.`id` IS NULL", "payroll_closePeriod");
			//change `payroll_employee`.`EmploymentStatus` from 3 to 2
			$system_database_manager->executeUpdate("UPDATE `payroll_employee` emp LEFT JOIN `payroll_employment_period` empprd ON emp.`id`=empprd.`payroll_employee_ID` AND empprd.`DateFrom`<'".$sqlYearNewP1."-".$sqlMonthNewP1."-01' AND (empprd.`DateTo`>'".$sqlYearNew."-".$sqlMonthNew."-01' OR empprd.`DateTo`='0000-00-00') SET emp.`EmploymentStatus`=2 WHERE emp.`EmploymentStatus`=3 AND empprd.`id` IS NOT NULL", "payroll_closePeriod");
		}
		//calculate age of each employee at the beginning and the end of the new period
		$resDate = $system_database_manager->executeQuery("SELECT CONCAT(payroll_year_ID,'-',major_period,'-01') as datePeriodStart, LAST_DAY(CONCAT(payroll_year_ID,'-',major_period,'-01')) as datePeriodEnd FROM payroll_period WHERE major_period<13 ORDER BY payroll_year_ID DESC, major_period DESC LIMIT 1", "payroll_saveEmployeeDetail");
		if(count($resDate)<1) {
			$response["success"] = false;
			$response["errCode"] = 666;
			$response["errText"] = "could not get period start and end date";
		}
		$datePeriodStart = $resDate[0]["datePeriodStart"];
		$datePeriodEnd = $resDate[0]["datePeriodEnd"];
		$system_database_manager->executeUpdate("UPDATE payroll_employee SET AgeAtPeriodStart=(YEAR('".$datePeriodStart."')-YEAR(DateOfBirth))-(RIGHT('".$datePeriodStart."',5)<RIGHT(DateOfBirth,5)), AgeAtPeriodEnd=(YEAR('".$datePeriodEnd."')-YEAR(DateOfBirth))-(RIGHT('".$datePeriodEnd."',5)<RIGHT(DateOfBirth,5))", "payroll_saveEmployeeDetail");
	
		//insert all active employees into `payroll_period_employee`	-> $majorPeriodAssociated_NEW verwenden!
		$system_database_manager->executeUpdate("INSERT INTO `payroll_period_employee`(`payroll_period_ID`, `payroll_employee_ID`, `processing`) SELECT ".$id.", `payroll_employee_ID`, 0 FROM `payroll_employment_period` WHERE `DateFrom`<=LAST_DAY('".$payrollYearID_NEW."-".substr("0".$majorPeriodAssociated_NEW,-2)."-01') AND (`DateTo`>='".$payrollYearID_NEW."-".substr("0".$majorPeriodAssociated_NEW,-2)."-01' OR `DateTo`='0000-00-00') GROUP BY `payroll_employee_ID`", "payroll_closePeriod");
	//	if($majorPeriod_NEW<13) $system_database_manager->executeUpdate("INSERT INTO `payroll_period_employee`(`payroll_period_ID`, `payroll_employee_ID`, `processing`) SELECT ".$id.", `payroll_employee_ID`, 0 FROM `payroll_employment_period` WHERE `DateFrom`<=LAST_DAY('".$payrollYearID_NEW."-".substr("0".$majorPeriod_NEW,-2)."-01') AND (`DateTo`>='".$payrollYearID_NEW."-".substr("0".$majorPeriod_NEW,-2)."-01' OR `DateTo`='0000-00-00') GROUP BY `payroll_employee_ID`", "payroll_closePeriod");
	//	else $system_database_manager->executeUpdate("INSERT INTO `payroll_period_employee`(`payroll_period_ID`, `payroll_employee_ID`, `processing`) SELECT ".$id.", `id`, 0 FROM `payroll_employee` WHERE `EmploymentStatus`=2 OR `EmploymentStatus`=4", "payroll_closePeriod");
	
	/*
	//TODO: in definitiver Tabelle (`payroll_calculation_entry`) muessen auch Records fuer MA angelegt werden, die eine Periode ausgesetzt haben
		* bei alter Periode anschauen, welche MA in `payroll_period_employee` vorkommen, nicht aber in `payroll_calculation_entry`...
		* fuer diejenigen die in `payroll_calculation_entry` fehlen, muessen aus `payroll_employee_account` die Anzahl Arbeitstage ermittelt und in `payroll_calculation_entry` gespeichert werden
		* muss das auch fuer die Saldo-LOA gemacht werden???
	$id=neue Perioden id -> uninteressant hierfuer // $payrollPeriodID verwenden!
	*/
		//"CodeAHV", "CodeALV" anpassen
		$res = $system_database_manager->executeQuery("SELECT `name`,`value` FROM `core_registry` WHERE `path`='GLOBAL/SETTINGS/CORE/payroll' AND `name` LIKE 'ahv_m%_age_%'", "payroll_closePeriod");
		foreach($res as $row) $ahvAgeRange[$row["name"]] = $row["value"];
		$system_database_manager->executeUpdate("UPDATE `payroll_employee` SET `CodeAHV`=IF(`CodeAHV`=2,1,`CodeAHV`), `CodeALV`=IF(`CodeALV`=2,1,`CodeALV`) WHERE (`CodeAHV`='2' OR `CodeALV`='2') AND (Sex='F' AND ((".$payrollYearID_NEW." - YEAR(`DateOfBirth`)) >= ".$ahvAgeRange["ahv_min_age_f"].") OR (Sex='M' AND (".$payrollYearID_NEW." - YEAR(`DateOfBirth`)) >= ".$ahvAgeRange["ahv_min_age_m"]."))", "payroll_closePeriod");
		$system_database_manager->executeUpdate("UPDATE `payroll_employee` SET `CodeAHV`=IF(`CodeAHV`=1,4,IF(`CodeAHV`=3,6,`CodeAHV`)), `CodeALV`=IF(`CodeALV`=1 OR `CodeALV`=3,5,`CodeALV`) WHERE (`CodeAHV`='1' OR `CodeAHV`='3' OR `CodeALV`='1' OR `CodeALV`='3') AND `RetirementDate` < '".$payrollYearID_NEW."-".substr("0".$majorPeriodAssociated_NEW,-2)."-01'", "payroll_closePeriod");
	
		//Resultate der Lohnberechnung permanent speichern (falls noch nicht erfolgt)
		$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_change_mng WHERE core_user_id=".$uid, "payroll_closePeriod");
		$system_database_manager->executeUpdate("INSERT INTO payroll_tmp_change_mng(core_user_id, numID, alphID) SELECT ".$uid.", ppe.payroll_employee_ID, '' FROM payroll_period_employee ppe LEFT JOIN (SELECT payroll_employee_ID FROM payroll_calculation_entry WHERE payroll_period_ID=".$payrollPeriodID." GROUP BY payroll_employee_ID) pce ON ppe.payroll_employee_ID=pce.payroll_employee_ID WHERE ppe.payroll_period_ID=".$payrollPeriodID." AND pce.payroll_employee_ID IS NULL", "payroll_closePeriod");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `position`) SELECT ".$payrollYearID.", ".$payrollPeriodID.", empacc.`payroll_employee_ID`,empacc.`payroll_account_ID`,0, 0, 0, SUM(empacc.`allowable_workdays`) as curWorkdays, 0 FROM `payroll_employee_account` empacc INNER JOIN `payroll_tmp_change_mng` emplist ON empacc.`payroll_employee_ID`=emplist.`numID` AND emplist.`core_user_ID`=".$uid." WHERE empacc.`PayrollDataType`=9 GROUP BY empacc.`payroll_employee_ID`,empacc.`payroll_account_ID`", "payroll_closePeriod");
		$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_change_mng WHERE core_user_id=".$uid, "payroll_closePeriod");
		//QST: monatl. Beschäftigungsfaktor aebernehmen (betrifft nur MA, die einen Mt. ausgesetzt haben)
		$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_entry`(`payroll_year_ID`, `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `quantity`, `rate`, `amount`, `allowable_workdays`, `position`) SELECT ".$payrollYearID.", ".$payrollPeriodID.", pcc.`payroll_employee_ID`, pcc.`payroll_account_ID`, pcc.`quantity`, pcc.`rate`, pcc.`amount`, 0, 0 FROM `payroll_calculation_current` pcc INNER JOIN `payroll_das_account` pda ON pda.`AccountType`=6 AND pcc.`payroll_account_ID`=pda.`payroll_account_ID` LEFT JOIN `payroll_calculation_entry` pce ON pcc.`payroll_period_ID`=pce.`payroll_period_ID` AND pcc.`payroll_employee_ID`=pce.`payroll_employee_ID` AND pce.`payroll_account_ID`=pda.`payroll_account_ID` WHERE pcc.`payroll_period_ID`=".$payrollPeriodID." AND pce.`id` IS NULL", "payroll_closePeriod");
	
		//Assign process=1 to all employees who have been processed in the last payroll period
		if($param["FwdEmpl"]==1) $system_database_manager->executeUpdate("UPDATE `payroll_period_employee` newPrd INNER JOIN `payroll_period_employee` oldPrd ON newPrd.`payroll_employee_ID`=oldPrd.`payroll_employee_ID` AND oldPrd.`payroll_period_ID`=".$payrollPeriodID." SET newPrd.`processing`=1 WHERE oldPrd.`processing`!=0 AND newPrd.`payroll_period_ID`=".$id, "payroll_closePeriod");
	
		//Einmalige Daten loeschen: JA/NEIN?
		if($param["FwdData"]==1) $system_database_manager->executeUpdate("DELETE FROM `payroll_employee_account` WHERE `PayrollDataType`=1 OR `PayrollDataType`=7", "payroll_closePeriod"); //Records fuer einmalige Daten *UND* Saldovortrag loeschen
		else $system_database_manager->executeUpdate("DELETE FROM `payroll_employee_account` WHERE `PayrollDataType`=7", "payroll_closePeriod"); //nur Records fuer Saldovortrag loeschen
	
		//abgelaufene, befristete LOA loeschen
		$system_database_manager->executeUpdate("DELETE FROM `payroll_employee_account` WHERE `DateTo`!='0000-00-00' AND `DateTo`<'".$payrollYearID_NEW."-".$majorPeriodAssociated_NEW."-01'", "payroll_closePeriod");
	
		//insert new records of carry-over-accounts (Saldo-LOA)
	//TODO: da die recs bereits zum Zeitpunkt der Auszahlung von `payroll_calculation_current` nach `payroll_calculation_entry` geschrieben werden, sollte im nachfolgenden Query auch `payroll_calculation_entry` verwendet werden!!!
		$system_database_manager->executeUpdate("INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`TargetField`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`) SELECT calccur.`payroll_employee_ID`,acc.`id`,7,'',IF(acc.`carry_over`=3,calccur.`quantity`,0),IF(acc.`carry_over`=4,calccur.`rate`,0),IF(acc.`carry_over`=5,calccur.`amount`,0),acc.`carry_over`,0,0,0,'','0000-00-00','0000-00-00' FROM `payroll_account` acc INNER JOIN `payroll_calculation_current` calccur ON acc.`id`=calccur.`payroll_account_ID` WHERE acc.`payroll_year_ID`=".$payrollYearID." AND acc.`carry_over`!=0", "payroll_closePeriod");
	
	//TODO: Die in der Tabelle `payroll_period_employee` mit status!=0 aufgefaehrten MA muessen in Tabelle `payroll_tmp_change_mng` aebernommen werden
		$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_closePeriod");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`,`alphID`) SELECT ".$uid.",`payroll_employee_ID`,'' FROM `payroll_period_employee` WHERE `payroll_period_ID`=".$id, "payroll_closePeriod"); //." AND `processing`=1"
		//Update data in table `payroll_employee_account` in order to prepare the calculation [fill table `payroll_tmp_change_mng` first, since `payroll_prc_empl_acc` processes only the employees recorded in `payroll_tmp_change_mng`]
		$system_database_manager->executeUpdate("Call payroll_prc_empl_acc(".$uid.", 0, 1, 1, 1, 1, 1, 1)"); //userID INT, internalTransaction TINYINT, wageCodeChange TINYINT, wageBaseChange TINYINT, insuranceChange TINYINT, modifierChange TINYINT, workdaysChange TINYINT, pensiondaysChange TINYINT
	
		//QST-Kanton und QST-Code bei allen Mitarbeitern mit QST-Verarbeitung setzen
		$system_database_manager->executeUpdate("UPDATE `payroll_employee` emp INNER JOIN `payroll_period_employee` prdemp ON prdemp.`payroll_period_ID`=".$id." AND prdemp.`payroll_employee_ID`=emp.`id` SET prdemp.`DedAtSrcMode`=emp.`DedAtSrcMode`, prdemp.`DedAtSrcCanton`=emp.`DedAtSrcCanton`, prdemp.`DedAtSrcCode`=emp.`DedAtSrcCode` WHERE emp.`DedAtSrcMode`!=1", "payroll_closePeriod");
	
		//`payroll_employee`.`EmploymentStatus` praefen und ggf. anpassen
		$system_database_manager->executeUpdate("UPDATE `payroll_employee` emp LEFT JOIN `payroll_employment_period` empprd ON empprd.`payroll_employee_ID`=emp.`id` AND empprd.`DateFrom`<=LAST_DAY('".$payrollYearID_NEW."-".$majorPeriodAssociated_NEW."-01') AND (empprd.`DateTo`='0000-00-00' OR empprd.`DateTo`>='".$payrollYearID_NEW."-".$majorPeriodAssociated_NEW."-01') SET `EmploymentStatus`=IF(empprd.`id` IS NULL AND (emp.`EmploymentStatus`=2 OR emp.`EmploymentStatus`=4),3,IF(empprd.`id` IS NOT NULL AND emp.`EmploymentStatus`=1,2,emp.`EmploymentStatus`)) WHERE emp.`EmploymentStatus`!=3", "payroll_closePeriod");
	
		$system_database_manager->executeUpdate("COMMIT", "payroll_closePeriod");
		//clear temporary table `payroll_calculation_current` (after COMMIT due to a hint in the official mysql manuall: "Leerungsoperationen sind nicht transaktionssicher: Wenn Sie eine solche Operation während einer aktiven Transaktion oder einer aktiven Tabellensperrung durchfaehren wollen, tritt ein Fehler auf."
	// 	$system_database_manager->executeUpdate("TRUNCATE TABLE `payroll_calculation_current`", "payroll_closePeriod");
	 	$system_database_manager->executeUpdate("DELETE FROM `payroll_calculation_current`", "payroll_closePeriod");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_payment_current`", "payroll_closePeriod");
	
		//QST-Monatsfaktor bei allen Mitarbeitern mit QST-Verarbeitung 4+5 setzen
		$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_current`(`payroll_year_ID`,`payroll_period_ID`,`payroll_employee_ID`,`payroll_account_ID`,`quantity`,`rate`,`amount`,`allowable_workdays`,`label`,`code`,`position`) SELECT ".$payrollYearID_NEW.",".$id.",prdemp.`payroll_employee_ID`,dasacc.`payroll_account_ID`,0.0,0.0,prdemp.`allowable_workdays`/30,0,'','',0 FROM `payroll_period_employee` prdemp INNER JOIN `payroll_das_account` dasacc ON dasacc.`AccountType`=6 WHERE prdemp.`payroll_period_ID`=".$id." AND (prdemp.`DedAtSrcMode`=4 OR prdemp.`DedAtSrcMode`=5)", "payroll_closePeriod");
	
		//fill `payroll_calculation_current` with payroll accounts of type "carry over"
	 	$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_current`(`payroll_year_ID`,`payroll_period_ID`,`payroll_employee_ID`,`payroll_account_ID`,`quantity`,`rate`,`amount`,`allowable_workdays`,`position`) SELECT acc.`payroll_year_ID`,empprd.`payroll_period_ID`,empprd.`payroll_employee_ID`,acc.`id`,IF(acc.`carry_over`=3,IF(empacc.`quantity` IS NULL,0,empacc.`quantity`),0),IF(acc.`carry_over`=4,IF(empacc.`rate` IS NULL,0,empacc.`rate`),0),IF(acc.`carry_over`=5,IF(empacc.`amount` IS NULL,0,empacc.`amount`),0),0,0 FROM `payroll_period_employee` empprd INNER JOIN `payroll_account` acc ON acc.`carry_over`!=0 AND acc.`payroll_year_ID`=".$payrollYearID_NEW." LEFT JOIN `payroll_employee_account` empacc ON empacc.`payroll_account_ID`=acc.`id` AND empacc.`payroll_employee_ID`=empprd.`payroll_employee_ID` WHERE empprd.`payroll_period_ID`=".$id, "payroll_closePeriod");
		//TODO: fill `payroll_calculation_current` with accounts with assigned "allowable_workdays">0 (get values from `payroll_employee_account`)
	/*	$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_current`(`payroll_year_ID`,`payroll_period_ID`,`payroll_employee_ID`,`payroll_account_ID`,`quantity`,`rate`,`amount`,`allowable_workdays`,`position`) 
	SELECT ".$payrollYearID_NEW.",empprd.`payroll_period_ID`,empprd.`payroll_employee_ID`,empacc.`payroll_account_ID`,0,0,0,empacc.`allowable_workdays`,0 
	FROM `payroll_period_employee` empprd 
	INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=empprd.`payroll_employee_ID` AND empacc.`PayrollDataType`=9 
	WHERE empprd.`payroll_period_ID`=".$id, "payroll_closePeriod");*/
		$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_current`(`payroll_year_ID`,`payroll_period_ID`,`payroll_employee_ID`,`payroll_account_ID`,`quantity`,`rate`,`amount`,`allowable_workdays`,`position`) SELECT ".$payrollYearID_NEW.",empprd.`payroll_period_ID`,empprd.`payroll_employee_ID`,empacc.`payroll_account_ID`,0,0,0,MAX(empacc.`allowable_workdays`),0 FROM `payroll_period_employee` empprd INNER JOIN `payroll_employee_account` empacc ON empacc.`payroll_employee_ID`=empprd.`payroll_employee_ID` AND empacc.`PayrollDataType`=9 INNER JOIN `payroll_employee_account` empacc2 ON empacc2.`payroll_employee_ID`=empprd.`payroll_employee_ID` AND empacc.`payroll_account_ID`=empacc2.`payroll_account_ID` AND empacc2.`PayrollDataType`=8 WHERE empprd.`payroll_period_ID`=".$id." AND empacc.`allowable_workdays`>0 GROUP BY empprd.`payroll_employee_ID`,empacc.`payroll_account_ID`", "payroll_closePeriod");
	
		$response["data"]["predecessor"] = array("PeriodID"=>$payrollPeriodID,"year"=>$payrollYearID,"majorPeriod"=>$majorPeriod,"minorPeriod"=>$minorPeriod);
		$response["data"]["new"] = array("PeriodID"=>$id,"year"=>$payrollYearID_NEW,"majorPeriod"=>$majorPeriod_NEW,"minorPeriod"=>$minorPeriod_NEW);
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getFieldModifierList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT modif.`id`,modif.`payroll_account_ID`,modif.`payroll_empl_filter_ID`,flt.`FilterName`,modif.`processing_order`,modif.`ModifierType`,modif.`TargetField`,modif.`FieldName`,fldlbl.`label` as FieldNameUlLabel,modif.`TargetValue`,modif.`max_limit`,modif.`deduction`,modif.`min_limit` FROM `payroll_calculation_modifier` modif INNER JOIN `payroll_empl_filter` flt ON flt.`id`=modif.`payroll_empl_filter_ID` LEFT JOIN `payroll_employee_field_label` fldlbl ON modif.`FieldName`=fldlbl.`fieldName` AND fldlbl.`language`='".session_control::getSessionInfo("language")."' ORDER BY modif.`payroll_account_ID`,modif.`ModifierType`,modif.`TargetField`,modif.`processing_order`", "payroll_getFieldModifierList");

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

	public function getFieldModifierDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_calculation_modifier` WHERE `id`=".$param["id"], "payroll_getFieldModifierDetail");

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

	public function saveFieldModifierDetail($param) {
		$fieldDef = array(
					"id"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>true),
					"payroll_account_ID"=>array("regex"=>"/^[0-9a-zA-Z]{1,5}$/", "isText"=>true, "isID"=>false),
					"payroll_empl_filter_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>false),
					"processing_order"=>array("regex"=>"/^[0-9]{1,2}$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"ModifierType"=>array("regex"=>"/^[12]{1,1}$/", "isText"=>false, "isID"=>false),
					"FieldName"=>array("regex"=>"/^[-_0-9a-zA-Z]{0,30}$/", "isText"=>true, "isID"=>false),
					"TargetField"=>array("regex"=>"/^[0345]{1,1}$/", "isText"=>false, "isID"=>false),
					"TargetValue"=>array("regex"=>"/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"max_limit"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"min_limit"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"deduction"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"OverwriteOnly"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"major_period"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"minor_period"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"major_period_bonus"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false)
				);

		$testPassed = true;
		$errFields = array();
		foreach($fieldDef as $fieldName=>$fieldParam) {
			if(isset($fieldParam["defaultValue"]) && (!isset($param[$fieldName]) || $param[$fieldName]=="")) $param[$fieldName]=$fieldParam["defaultValue"];
			if(!preg_match($fieldParam["regex"], $param[$fieldName])) {
				$errFields[] = $fieldName;
				$testPassed = false;
			}
		}
		if(!$testPassed) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}

		if(trim($param["FieldName"])!="") $param["TargetValue"]=0;
		if($param["ModifierType"]==2) {
			$param["TargetValue"]=0;
			$param["FieldName"]="";
		}else{
			$param["max_limit"]=0;
			$param["min_limit"]=0;
			$param["deduction"]=0;
		}

		$system_database_manager = system_database_manager::getInstance();
		if($param["id"]>0) {
			$result = $system_database_manager->executeUpdate("UPDATE `payroll_calculation_modifier` SET `payroll_account_ID`='".addslashes($param["payroll_account_ID"])."',`payroll_empl_filter_ID`=".$param["payroll_empl_filter_ID"].",`processing_order`=".$param["processing_order"].",`ModifierType`=".$param["ModifierType"].",`FieldName`='".addslashes($param["FieldName"])."',`TargetField`=".$param["TargetField"].",`TargetValue`=".$param["TargetValue"].",`max_limit`=".$param["max_limit"].",`min_limit`=".$param["min_limit"].",`deduction`=".$param["deduction"].",`major_period`=".$param["major_period"].",`minor_period`=".$param["minor_period"].",`major_period_bonus`=".$param["major_period_bonus"].",`OverwriteOnly`=".$param["OverwriteOnly"]." WHERE `id`=".$param["id"], "payroll_saveFieldModifierDetail");
		}else{
			$result = $system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_modifier`(`payroll_account_ID`,`payroll_empl_filter_ID`,`processing_order`,`ModifierType`,`FieldName`,`TargetField`,`TargetValue`,`max_limit`,`min_limit`,`deduction`,`major_period`,`minor_period`,`major_period_bonus`,`OverwriteOnly`) VALUES('".addslashes($param["payroll_account_ID"])."',".$param["payroll_empl_filter_ID"].",".$param["processing_order"].",".$param["ModifierType"].",'".addslashes($param["FieldName"])."',".$param["TargetField"].",".$param["TargetValue"].",".$param["max_limit"].",".$param["min_limit"].",".$param["deduction"].",".$param["major_period"].",".$param["minor_period"].",".$param["major_period_bonus"].",".$param["OverwriteOnly"].")", "payroll_saveFieldModifierDetail");
		}
		
		require_once('changeManager.php');
		$changeManager = new changeManager();				
		$changeManager->changeManager("modifierChange", array("payroll_empl_filter_ID" => array(), "activeTransaction"=>false));

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteFieldModifierDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_calculation_modifier` WHERE `id`=".$param["id"], "payroll_deleteFieldModifierDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}


}
$SYS_PLUGIN["bl"]["payroll"] = new payroll_BL();
?>
