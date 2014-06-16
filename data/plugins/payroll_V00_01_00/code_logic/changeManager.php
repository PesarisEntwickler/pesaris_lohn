<?php
class changeManager {
	
	public function changeManager($event, $param) {
		$singleEmployeeChange = false;
		$insuranceChange = false;
		$filterChange = false;
		$wageCodeChange = false;
		$baseWageChange = false;
		$modifierChange = false;
		$workdaysChange = false;
		$pensiondaysChange = false;
		$dedAtSrcSettingsChange = false;
		$system_database_manager = system_database_manager::getInstance();
		$uid = session_control::getSessionInfo("id");

		switch($event) {
		case 'EmployeeChange':
			$coreFields = $param["affectedFields"]["payroll_employee"];
			$childrenRecords = $param["affectedFields"]["payroll_employee_children"];

			if(count($coreFields)<1 && count($childrenRecords)<1) return;
			if(count($param["payroll_employee_ID"])==1) $singleEmployeeChange = true;

			if(count($childrenRecords)>0) {
				$insuranceChange = true;
			}

			//Check if any of the insurance codes were changed
			$insuranceFields = array("CodeAHV", "CodeALV", "CodeUVG", "CodeUVGZ1", "CodeUVGZ2", "CodeBVG", "CodeKTG");
			foreach($insuranceFields as $fld) if(in_array($fld, $coreFields)) $insuranceChange = true;

			//Check if WageCode has changed
			if(in_array("WageCode", $coreFields)) $wageCodeChange = true;

			//Check if BaseWage has changed
			if(in_array("BaseWage", $coreFields)) $baseWageChange = true;

			//Check if one of the QST-specific fields has changed
			if(in_array("DedAtSrcMode", $coreFields)) $dedAtSrcSettingsChange = true;
			if(in_array("DedAtSrcCanton", $coreFields)) $dedAtSrcSettingsChange = true;
			if(in_array("DedAtSrcCode", $coreFields)) $dedAtSrcSettingsChange = true;

			//Check if one of the changed fields affects one of the filters
			$affectedFilters = array();
			$sqlFields = array();
			foreach($coreFields as $fld) $sqlFields[] = "'".$fld."'";
			if(count($sqlFields)>0) {
				$result = $system_database_manager->executeQuery("SELECT DISTINCT payroll_empl_filter_ID FROM payroll_empl_filter_crit WHERE FieldName IN (".implode(",",$sqlFields).")", "payroll_changeManager");
				foreach($result as $row) $affectedFilters[] = $row["payroll_empl_filter_ID"];
			}

			if(count($affectedFilters)>0) {
				$system_database_manager->executeUpdate("UPDATE payroll_empl_filter SET dirtyData=1 WHERE id IN (".implode(",", $affectedFilters).")", "payroll_changeManager");
				$filterChange = true;
			}

			//Check if one of the changed fields affects one of the calculation modifiers (payroll_calculation_modifier)
			if(count($sqlFields)>0) {
				$result = $system_database_manager->executeQuery("SELECT COUNT(*) as cnt FROM payroll_calculation_modifier WHERE FieldName IN (".implode(",",$sqlFields).")", "payroll_changeManager");
				if($result[0]["cnt"]>0) $modifierChange = true;
			}

//			"core_user_id_created=".session_control::getSessionInfo("id")
			$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_change_mng WHERE core_user_id=".$uid, "payroll_changeManager");
			$val = array();
			foreach($param["payroll_employee_ID"] as $eid) $val[] = "(".$uid.",".$eid.")";
			$system_database_manager->executeUpdate("INSERT INTO payroll_tmp_change_mng(core_user_id,numID) VALUES".implode(",",$val), "payroll_changeManager");
			break;
		case 'EmployeeAccountChange':
			break;
		case 'AccountChange':
			break;
		case 'FilterChange':
		case 'modifierChange':
			//bei Bedarf -> $param["payroll_empl_filter_ID"] -> array mit filterIDs	+	$param["activeTransaction"]=true | false
			$filterChange = true;
			//ermitteln welche Employees betroffen sind... im Zweifelsfall alle! IDs in payroll_tmp_change_mng faellen!
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_changeManager");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`,`alphID`) SELECT ".$uid.",`payroll_employee_ID`,'' FROM `payroll_period_employee`", "payroll_changeManager");
			break;
		case 'InsuranceChange':
			$insuranceChange = true;
			break;
		case 'CalculationModifierChange':
			break;
		}
		if($insuranceChange) {
			$workdaysChange = true;
			$pensiondaysChange = true;
		}

		if($filterChange) $system_database_manager->executeUpdate("Call payroll_prc_filter_cache(0)", "payroll_changeManager");
		if($wageCodeChange || $baseWageChange || $insuranceChange || $filterChange || $modifierChange) $system_database_manager->executeUpdate("Call payroll_prc_empl_acc(".$uid.", 0, ".($wageCodeChange?1:0).", ".($baseWageChange?1:0).", ".($insuranceChange?1:0).", ".($filterChange || $modifierChange?1:0).", ".($workdaysChange?1:0).", ".($pensiondaysChange?1:0).")"); //userID INT, internalTransaction TINYINT, wageCodeChange TINYINT, wageBaseChange TINYINT, insuranceChange TINYINT, modifierChange TINYINT, workdaysChange TINYINT, pensiondaysChange TINYINT

		//wenn change nur aufgrund der Aenderung bei einem Employee erfolgt, muessen auch nur dessen Daten neu berechnet werden
		if($singleEmployeeChange) {
			$proceed = true;

			$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_calculate");
			if(count($result)>0) $payrollPeriodID = $result[0]["id"];
			else $proceed = false;

			if($proceed) {
				//WICHTIG... employee darf nur gerechnet werden, wenn processing-Flag (payroll_period_employee) auf 1 steht!!!!
				$result = $system_database_manager->executeQuery("SELECT `processing` FROM `payroll_period_employee` WHERE `payroll_period_ID`=".$payrollPeriodID." AND `payroll_employee_ID`=".$param["payroll_employee_ID"][0], "payroll_calculate");
				if(count($result)>0) $proceed = $result[0]["processing"]==1 ? true : false;
				else $proceed = false;
			}

			if($proceed) {
				$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
				$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) VALUES(".$uid.",".$param["payroll_employee_ID"][0].")", "payroll_calculate");
				if($dedAtSrcSettingsChange) $system_database_manager->executeUpdate("UPDATE payroll_period_employee ppe INNER JOIN payroll_period prd ON prd.locked=0 AND prd.finalized=0 AND prd.id=ppe.payroll_period_ID INNER JOIN payroll_tmp_change_mng ptcm ON ppe.payroll_employee_ID=ptcm.numID AND ptcm.core_user_id=".$uid." INNER JOIN payroll_employee emp ON emp.id=ppe.payroll_employee_ID SET ppe.DedAtSrcMode=emp.DedAtSrcMode, ppe.DedAtSrcCanton=emp.DedAtSrcCanton, ppe.DedAtSrcCode=emp.DedAtSrcCode", "payroll_calculate");
				require_once('payroll_calculate.php');
				$calcs = new payroll_BL_calculate();
				$calcs->calculate(false); //$calculateAll=false (calculate just one employee)
			}
		}
		//wenn change aufgrund der Anpassung von Filterkriterien ausgeloest wird, muessen die Daten aller vom Filter betroffenen Employees neu gerechnet werden
		$system_database_manager->executeUpdate("DELETE FROM payroll_tmp_change_mng WHERE core_user_id=".$uid, "payroll_changeManager");
	}
	
}
?>
