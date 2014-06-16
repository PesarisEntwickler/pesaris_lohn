<?php

class payroll_BL {

	public function sysListener($functionName, $functionParameters) {

		require_once('payroll_various_functions.php'); 
		$variousFunctions = new variousFunctions();
					
		require_once('payroll_calculate.php');
		$calcs = new payroll_BL_calculate();
		
		require_once('payroll_payment.php');
		$payrollPayment = new payroll_BL_payment();
					
		require_once('payroll_employee.php');
		$employee = new employee();
					
		require_once('payroll_insurance.php');
		$insurance = new insurance();
		
		require_once('payroll_payslip.php');
		$payslip = new payslip();
					
		require_once('payroll_account.php');
		$account = new account();
				
		require_once('payroll_periods.php');
		$periods = new periods();
				
		require_once('payroll_fieldmodifier.php');
		$fieldmodifier = new fieldmodifier();
				
		require_once('payroll_finMgmtAccounting.php');
		$finMgmtAccounting = new finMgmtAccounting();
				
		require_once('payroll_auszahlen.php');
		$auszahlen = new auszahlen();
				  
		switch($functionName) {
		case 'payroll.auszahlen.braucheDaten':
			return $auszahlen->auszahlDaten();
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
			return $variousFunctions->prepareCalculation($functionParameters[0]);
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
			return $finMgmtAccounting->editFinMgmtAccountingConfig($functionParameters[0]);
		case 'payroll.getFinMgmtAccountingInfo':
			return $finMgmtAccounting->getFinMgmtAccountingInfo();
		case 'payroll.processFinMgmtAccountingEntry':
			return $finMgmtAccounting->processFinMgmtAccountingEntry($functionParameters[0]);

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
			return $periods->closePeriod($functionParameters[0]);
		case 'payroll.checkPeriodValidity':
			return $periods->checkPeriodValidity();
		case 'payroll.getPeriodInformation':
			return isset($functionParameters[0]) ? $periods->getPeriodInformation($functionParameters[0]) : $periods->getPeriodInformation();
		case 'payroll.savePeriodDates':
			return $periods->savePeriodDates($functionParameters[0]);
			
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
			return $fieldmodifier->getFieldModifierList();
		case 'payroll.getFieldModifierDetail':
			return $fieldmodifier->getFieldModifierDetail($functionParameters[0]);
		case 'payroll.saveFieldModifierDetail':
			return $fieldmodifier->saveFieldModifierDetail($functionParameters[0]);
		case 'payroll.deleteFieldModifierDetail':
			return $fieldmodifier->deleteFieldModifierDetail($functionParameters[0]);

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
	
}
$SYS_PLUGIN["bl"]["payroll"] = new payroll_BL();
?>
