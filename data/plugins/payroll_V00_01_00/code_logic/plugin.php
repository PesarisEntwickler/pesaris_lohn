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
		case 'payroll.auszahlen.GenerateDataFiles':
			require_once('payroll_reports.php');
			$reports = new payroll_BL_reports();		
			return $reports->generateAuszahlDataReports($functionParameters[0], $functionParameters[1], $functionParameters[2]);
		case 'payroll.auszahlen.getAuszahlMitarbeiteranzahl':
			return $auszahlen->getAuszahlMitarbeiteranzahl();
		case 'payroll.auszahlen.getCalculationCurrentPeriodEmployeeList':
			return $auszahlen->getCalculationCurrentPeriodEmployeeList($functionParameters[0], $functionParameters[1]);//("8000",  " < 0.001 ") oder ("8000, 8001, 8002", " >= 0.001 ")
		case 'payroll.auszahlen.getActualPeriodID':
			return $auszahlen->getActualPeriodID();
		case 'payroll.auszahlen.getCompany':
			return $auszahlen->getCompany($functionParameters[0]);//$company_ID
		case 'payroll.auszahlen.getActualPeriodName':
			return $auszahlen->getActualPeriodName();
		case 'payroll.auszahlen.getActualPeriodDir':
			return $auszahlen->getActualPeriodenDir();
		case 'payroll.auszahlen.getActualPeriodenDaten':
			return $auszahlen->getActualPeriodenDaten($functionParameters[0]);
		case 'payroll.auszahlen.resetActualPeriodenAuszahlFlags':
			return $auszahlen->resetActualPeriodenAuszahlFlags($functionParameters[0]);
		case 'payroll.auszahlen.getZahlstellenListe':
			return $auszahlen->getZahlstellenListe($functionParameters[0]);//$companyID
		case 'payroll.auszahlen.getDestinationBankAccount':
			return $auszahlen->getDestinationBankAccount($functionParameters[0],$functionParameters[1],"");//=$employeeID, $bankDestID, "Y"/"N" is_sandard_bank
		case 'payroll.auszahlen.getStandardDestinationBankAccount':
			return $auszahlen->getStandardDestinationBankAccount($functionParameters[0]);//=$employeeID
		case 'payroll.auszahlen.getCurrentPeriodAccountAmount':
			return $auszahlen->getCurrentPeriodAccountAmount($functionParameters[0],$functionParameters[1]);//=$account_ID, $employee_ID
		case 'payroll.auszahlen.getAllDestinationBankAccounts':
			return $auszahlen->getAllDestinationBankAccounts($functionParameters[0]);//=$employeeID
		case 'payroll.auszahlen.getPaymentSplit':
			return $auszahlen->getPaymentSplit($functionParameters[0],$functionParameters[1],$functionParameters[2],$functionParameters[3]);//=$employeeID, $bankID, $Zahlstelle, $processing_order
		case 'payroll.auszahlen.getEmplFromTrackingTable':
			return $auszahlen->getEmplFromTrackingTable($functionParameters[0]);
		case 'payroll.auszahlen.initTrackingTable':
			return $auszahlen->initTrackingTable();
		case 'payroll.auszahlen.truncateTrackingTable':
			return $auszahlen->truncateTrackingTable();
		case 'payroll.auszahlen.setAmountAvailableTrackingTable':
			return $auszahlen->setAmountAvailableTrackingTable($functionParameters[0],$functionParameters[1],$functionParameters[2],$functionParameters[3],$functionParameters[4]);//$periodeID, $employeeID, $splitID, $processingOrder, $newAmountAvalable
		case 'payroll.auszahlen.getAmountAvailableFromTrackingTable':
			return $auszahlen->getAmountAvailableFromTrackingTable($functionParameters[0],$functionParameters[1]);//$periodeID, $employeeID
		case 'replaceUmlaute':
			return $auszahlen->replaceUmlaute($functionParameters[0]);//=$uebergabeWort
		case 'rundungAuf5Rappen':
			return $auszahlen->rundungAuf5Rappen($functionParameters[0]);//=$payAmount
			
		case 'payroll.getEmplZahlstelle':
			return $payrollPayment->getEmplZahlstelle($functionParameters[0]);//$employeeId
		case 'payroll.initZahlungssplitt':
			return $payrollPayment->initZahlungssplitt($functionParameters[0],$functionParameters[1],$functionParameters[2]);//$employeeId, $zahlstelleId, $destBankId
		case 'payroll.getPaymentSplitList':
			return $payrollPayment->getPaymentSplitList($functionParameters[0]);//$payrollEmployeeID
		case 'payroll.getPaymentSplitDetail':
			return $payrollPayment->getPaymentSplitDetail($functionParameters[0]);
		case 'payroll.savePaymentSplitDetail':
			return $payrollPayment->savePaymentSplitDetail($functionParameters[0]);
		case 'payroll.saveBankDestinationUndSplit':
			return $payrollPayment->saveBankDestinationUndSplit($functionParameters[0]);
		case 'payroll.savePaymentSplitOrder':
			return $payrollPayment->savePaymentSplitOrder($functionParameters[0]);
		case 'payroll.deletePaymentSplitDetail':
			return $payrollPayment->deletePaymentSplitDetail($functionParameters[0]);
		case 'payroll.getDestBankDetail':
			return $payrollPayment->getDestBankDetail($functionParameters[0]);
		case 'payroll.getBankSourceDetail':
			return $payrollPayment->getBankSourceDetail($functionParameters[0]);
		case 'payroll.saveBankDestDetail':
			return $payrollPayment->saveBankDestinationDetail($functionParameters[0]);
		case 'payroll.saveBankSourceDetail':
			return $payrollPayment->saveBankSourceDetail($functionParameters[0]);
		case 'payroll.deleteDestBankDetail':
			return $payrollPayment->deleteDestBankDetail($functionParameters[0]);
		case 'payroll.deleteBankSourceDetail':
			return $payrollPayment->deleteBankSourceDetail($functionParameters[0]);
		case 'payroll.getClearingBanks':
			return $payrollPayment->getClearingBanks($functionParameters[0]);//BCNr
		case 'payroll.getClearingBank':
			return $payrollPayment->getClearingBank($functionParameters[0]);//IBAN


		case 'payroll.getNextCompanyId':
			return $variousFunctions->getNextCompanyId();
		case 'payroll.getCountryList':
			return $variousFunctions->getCountryList();
		case 'payroll.onBootComplete':
			return $variousFunctions->onBootComplete();
		case 'payroll.getLanguageList':
			return $variousFunctions->getLanguageList($functionParameters[0]);
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
		case 'payroll.getCurrencyForexRate':
			return $variousFunctions->getCurrencyForexRate($functionParameters[0]);//"EUR" oder "USD"
		case 'payroll.saveCurrencyForexRate':
			return $variousFunctions->saveCurrencyForexRate($functionParameters[0],$functionParameters[1]);//"EUR", "1.2222"
		case 'payroll.prepareCalculation':
			return $variousFunctions->prepareCalculation($functionParameters[0]);
//		case 'payroll.getpayrollountFormOverview':
//			require_once('payroll-fin.php');
//			$payrollfin = new payroll_BL_fin();
//			return $payrollfin->getpayrollountForm($functionParameters[0], $functionParameters[1], $functionParameters[2], $functionParameters[3]); //$parampayrollountNumber, $bMonthlySummary, $bAllEntries, $arrYearMonth
//			break;

		case 'payroll.getEmployee':
			return $employee->getEmployee($functionParameters[0]);
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
 			return $payrollPayment->processPayment($functionParameters[0]); 

		case 'payroll.editFinMgmtAccountingConfig':
			return $finMgmtAccounting->editFinMgmtAccountingConfig($functionParameters[0]);
		case 'payroll.getFinMgmtAccountingInfo':
			return $finMgmtAccounting->getFinMgmtAccountingInfo();
		case 'payroll.processFinMgmtAccountingEntry':
			return $finMgmtAccounting->processFinMgmtAccountingEntry($functionParameters[0]);

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

		case 'payroll.calculationReport':
				require_once('payroll_reports.php');
				$reports = new payroll_BL_reports();

				switch($functionParameters[0]) {
				case 'GenerateAuszahlenReports':
					//communication_interface::alert("generate");
					//return $reports->GenerateAuszahlenReports($functionParameters[1]);
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
	
}
$SYS_PLUGIN["bl"]["payroll"] = new payroll_BL();
?>
