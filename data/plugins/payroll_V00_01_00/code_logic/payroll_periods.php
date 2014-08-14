<?php
class periods {
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
			//TODO: beim Jahreswechsel muessen *ALLE* jahresabh�ngigen Daten (z.B. payroll_account) neu angelegt werden!
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
		//clear temporary table `payroll_calculation_current` (after COMMIT due to a hint in the official mysql Manual: 
		//"Leerungsoperationen sind nicht transaktionssicher: Wenn Sie eine solche Operation während einer aktiven Transaktion oder einer aktiven Tabellensperrung durchfaehren wollen, tritt ein Fehler auf."
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

	
}
?>
