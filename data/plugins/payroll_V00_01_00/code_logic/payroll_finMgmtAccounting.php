<?php
class finMgmtAccounting{
	
	
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

	public function editFinMgmtAccountingConfig($param) {
		$arrSections = array("fin_acc_assign","mgmt_acc_split","mgmt_acc_assign");

		//1=mandatory add, 2=mand edit, 3=mand del, 4=add apostrophe in SQL, 5=regex
		$arrFields = array(
			array('id'=>array(false,true,true,false,'/^[0-9]{1,9}$/'),'payroll_company_ID'=>array(true,true,false,false,'/^[0-9]{1,9}$/'),'payroll_employee_ID'=>array(true,true,false,false,'/^[0-9]{1,9}$/'),'payroll_account_ID'=>array(true,true,false,true,'/^[0-9a-zA-Z]{1,5}$/'),'cost_center'=>array(false,false,false,true,'/^.{0,15}$/'),'account_no'=>array(true,true,false,true,'/^.{1,15}$/'),'counter_account_no'=>array(false,false,false,true,'/^.{0,15}$/'),'debitcredit'=>array(true,true,false,false,'/^[01]{1,1}$/'),'entry_text'=>array(true,true,false,true,'/^.{1,50}$/'),'invert_value'=>array(true,true,false,false,'/^[01]{1,1}$/'),'processing_order'=>array(false,false,false,false,'/^[0-9]{1,2}$/')),
			array('id'=>array(false,true,true,false,'/^[0-9]{1,9}$/'),'payroll_company_ID'=>array(false,true,false,false,'/^[0-9]{1,9}$/'),'payroll_employee_ID'=>array(false,true,false,false,'/^[0-9]{1,9}$/'),'payroll_account_ID'=>array(true,true,false,true,'/^[0-9a-zA-Z]{1,5}$/'),'cost_center'=>array(false,true,false,true,'/^.{1,15}$/'),'amount'=>array(true,true,false,false,'/^[0-9]{1,3}(\.[0-9]{0,2})?$/'),'invert_value'=>array(true,true,false,false,'/^[01]{1,1}$/')/*,'remainder'=>array(true,true,false,false,'/^[01]{1,1}$/')*/,'processing_order'=>array(false,false,false,false,'/^[0-9]{1,2}$/')),
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

		//assemble SQL statement
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
	

	public function processFinMgmtAccountingEntry($param) {
		require_once('chkDate.php');
		$chkDate = new chkDate();
		
//TODO: Sicherstellen, dass diese Nasen ab sofort nicht mehr gerechnet werden!! Und processing-flag hochsetzen auf 3!
		///////////////////////////////////////////////////
		// validity check of processing flags
		///////////////////////////////////////////////////
        //if(!preg_match( '/^[01]{1,1}$/', $param["fin_acc_process"])) {
        //    $response["success"] = false;
        //    $response["errCode"] = 10;
        //    $response["errText"] = "financial accounting processing flag";
        //    $response["errField"] = "fin_acc_process";
        //    return $response;
        //}else $finAccProcess = $param["fin_acc_process"]==1 ? true : false;
        //if(!preg_match( '/^[01]{1,1}$/', $param["mgmt_acc_process"])) {
        //    $response["success"] = false;
        //    $response["errCode"] = 10;
        //    $response["errText"] = "management accounting processing flag";
        //    $response["errField"] = "mgmt_acc_process";
        //    return $response;
        //}else $mgmtAccProcess = $param["mgmt_acc_process"]==1 ? true : false;

        //if(!$finAccProcess && !$mgmtAccProcess) {
        //    $response["success"] = false;
        //    $response["errCode"] = 20;
        //    $response["errText"] = "select at least one accounting method";
        //    return $response;
        //}

        //if($finAccProcess) {
        //    
        if(!$chkDate->chkDate($param["fin_acc_date"], 1, $finAccDate)) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "date value";
				$response["errField"] = "fin_acc_date";
				return $response;
			}
        //}
        //if($mgmtAccProcess) {
        //    
        if(!$chkDate->chkDate($param["mgmt_acc_date"], 1, $mgmtAccDate)) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "date value";
				$response["errField"] = "mgmt_acc_date";
				return $response;
			}
            //if(!preg_match( '/^[01]{1,1}$/', $param["mgmt_acc_quantity"])) {
            //    $response["success"] = false;
            //    $response["errCode"] = 10;
            //    $response["errText"] = "quantity processing flag";
            //    $response["errField"] = "mgmt_acc_quantity";
            //    return $response;
            //}else $mgmtAccQuantity = $param["mgmt_acc_quantity"]==1 ? true : false;
            
            if(!preg_match( '/^[01]{1,1}$/', $param["mgmt_acc_round"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "rounding processing flag";
				$response["errField"] = "mgmt_acc_round";
				return $response;
			}
            $amountRounding = $param["mgmt_acc_round"]==1 ? 0.05 : 0.01;
            $quantityRounding = 0.01;
        //}

		///////////////////////////////////////////////////
		// filter_mode must be numeric, non-decimal and in a certain range
		///////////////////////////////////////////////////
        //if(!preg_match( '/^[0-2]{1,1}$/', $param["filter_mode"])) {
        //    $response["success"] = false;
        //    $response["errCode"] = 10;
        //    $response["errText"] = "invalid company id";
        //    $response["errField"] = "filter_mode";
        //    return $response;
        //}else $paramFilterMode = $param["filter_mode"];

        //if($paramFilterMode==1) {
        //    ///////////////////////////////////////////////////
        //    // company id must be numeric and non-decimal
        //    ///////////////////////////////////////////////////
        //    if(!preg_match( '/^[0-9]{1,9}$/', $param["payroll_company_ID"])) {
        //        $response["success"] = false;
        //        $response["errCode"] = 10;
        //        $response["errText"] = "invalid company id";
        //        $response["errField"] = "payroll_company_ID";
        //        return $response;
        //    }
        //    $paramCompanyID = $param["payroll_company_ID"];
        //}else $paramCompanyID = 0;

		$uid = session_control::getSessionInfo("id");


		$system_database_manager = system_database_manager::getInstance();
        
        $sqlCommandText = "CALL `payroll_prc_fibu_bebu`(".$uid.",".$amountRounding.",".$quantityRounding.",".$mgmtAccDate.",".$finAccDate.")";
        $system_database_manager->executeUpdate($sqlCommandText);

        //Fehlerbehandlung nur möglich, falls mehrere ResultSets im database_manager abgehandelt werden.
        //if ($result == false)
        //{
        //    //Error Code
        //    $response["success"] = false;
        //    $response["errCode"] = 11;
        //    $response["errText"] = "Error executing SQL vs database.";
        //    return $response;
        //}
        //else if ($result[0][ErrorCode] != 0) 
        //{
        //    //Error Code
        //    $response["success"] = false;
        //    $response["errCode"] = $result[0][ErrorCode];
        //    $response["errText"] = "Error occured when executing the following database command: ".$sqlCommandText;
        //    return $response;
        //}
        //else 
        //{
		    $response["success"] = true;
		    $response["errCode"] = 0;
			return $response;
        //}
        
        //TODO: Delete below commands: Unreachable code. (Ch. Jossi)

		//get the id of the current period
		$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_getPeriodInformation");
		$payrollPeriodID = $result[0]["id"];

		$system_database_manager->executeUpdate("BEGIN", "payroll_processFinMgmtAccountingEntry");

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
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_mgmt_acc_split`", "payroll_processFinMgmtAccountingEntry");

			//…dann die Records in einer temporären MEMORY-Table neu anlegen. Zuerst LOA mit expliziter Übersteuerung der Kostenstelle…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`) SELECT ".$payrollPeriodID.",emp.`payroll_company_ID`,empacc.`payroll_employee_ID`,empacc.`CostCenter`,empacc.`payroll_account_ID`,0,0,calc.`amount`,0,0,0,1,0,0 FROM `payroll_employee_account` empacc INNER JOIN `payroll_tmp_change_mng` emplList ON empacc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_employee_ID`=emplList.`numID` AND calc.`payroll_account_ID`=empacc.`payroll_account_ID` AND calc.`payroll_period_ID`= ".$payrollPeriodID." WHERE empacc.`CostCenter`!=''", "payroll_processFinMgmtAccountingEntry");

			//…als Nächstes ebenfalls Records in temporärer MEMORY-Table anlegen, aber jetzt die aebrigen %-Verteilungen. Wichtig: Bereits verarbeitete LOA ausschliessen, 100%-Zuweisungen koennen direkt verarbeitet und die enspr. Records abgeschlossen/fixiert werden...
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`) SELECT calc.`payroll_period_ID`, emp.`payroll_company_ID`, emp.`id`, IF(accasng.`payroll_employee_ID`=0 AND emp.`CostCenter`!='', emp.`CostCenter`,accasng.`cost_center`), calc.`payroll_account_ID`, calc.`amount`,calc.`amount`, IF(accasng.`amount`=100,IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`amount`) ,accasng.`processing_order`,accasng.`invert_value`,0,IF(accasng.`amount`=100,1,0),0,0 FROM `payroll_mgmt_acc_split` accasng INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_period_ID`=".$payrollPeriodID." INNER JOIN `payroll_tmp_change_mng` emplList ON calc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` LEFT JOIN `payroll_tmp_mgmt_acc_split` tas ON tas.`payroll_period_ID`=calc.`payroll_period_ID` AND tas.`payroll_employee_ID`=calc.`payroll_employee_ID` AND tas.`payroll_account_ID`=calc.`payroll_account_ID` AND tas.`processing_done`=1 INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM `payroll_mgmt_acc_split` aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id` WHERE tas.`processing_done` IS NULL", "payroll_processFinMgmtAccountingEntry");
/*
Hinweis:
Im Query auf Zeile 266 liefert der zum INSERT gehörende SELECT zu viele Duplikate. Mit SELECT DISTINCT könnte das behoben werden.
Aus "...,`rounding`) SELECT calc.`payroll_period_ID`,..." müsste demnach "...,`rounding`) SELECT DISTINCT calc.`payroll_period_ID`,..." werden.

Eleganter wäre aber ein überarbeiten der JOIN-Bedingungen, damit es gar nicht erst zu den Duplikaten kommt.
*/
			//…100%er wurden im obigen Statement verarbeitet. Hier werden nun %-Verteilungen <100%...
			$system_database_manager->executeUpdate("UPDATE `payroll_tmp_mgmt_acc_split` SET `amount`=`amount`/100*`amount_available` WHERE `processing_done`=0", "payroll_processFinMgmtAccountingEntry");

			//…Werte runden…
			if($mgmtAccRound) {
				$system_database_manager->executeUpdate("UPDATE `payroll_tmp_mgmt_acc_split` SET `amount`=ROUND(`amount`/0.05)*0.05 WHERE `having_rounding`=1 AND `processing_done`=0", "payroll_processFinMgmtAccountingEntry");
			}

			//…Falls es einen Restbetrag gibt, wird dieser nun noch entsprechend zugewiesen…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`,`remainder`) SELECT accsplt.`payroll_period_ID`,accsplt.`payroll_company_ID`,accsplt.`payroll_employee_ID`,IF(x1.`remainder`=1,accsplt.`cost_center`,empl.`CostCenter`),accsplt.`payroll_account_ID`,accsplt.`amount_initial`,accsplt.`amount_available`, x1.`amount_initial`-x1.`amount_sum`, 0,0,accsplt.`amount_quantity`,1,accsplt.`having_rounding`,accsplt.`rounding`,accsplt.`remainder` FROM payroll_tmp_mgmt_acc_split accsplt INNER JOIN (SELECT `payroll_employee_ID`, `payroll_account_ID`, `amount_initial`, SUM(`amount`) as amount_sum, MAX(`amount`) as amount_max, `remainder` FROM payroll_tmp_mgmt_acc_split WHERE processing_done=0 GROUP BY payroll_employee_ID, payroll_account_ID) x1 ON accsplt.`payroll_employee_ID`=x1.`payroll_employee_ID` AND accsplt.`payroll_account_ID`=x1.`payroll_account_ID` AND accsplt.`amount`=x1.amount_max INNER JOIN `payroll_employee` empl ON empl.`id`=accsplt.`payroll_employee_ID` WHERE accsplt.`processing_done`=0", "payroll_processFinMgmtAccountingEntry");


			//…"0.00" Beträge loeschen (TODO: Ist das OK?)…
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_mgmt_acc_split` WHERE `amount`=0", "payroll_processFinMgmtAccountingEntry");

			//…Records der temporären Tabelle in eine InnoDB Tabelle speichern…
			$system_database_manager->executeUpdate("INSERT INTO `payroll_mgmt_acc_entry`(`payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `account_no`, `counter_account_no`, `cost_center`, `amount_local`, `debitcredit`, `entry_text`, `amount_quantity`) SELECT calc.`payroll_period_ID`, calc.`payroll_employee_ID`, accasng.`payroll_account_ID`, accasng.`account_no`, accasng.`counter_account_no`, calc.`cost_center`, IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`debitcredit`, accasng.`entry_text`, 0 FROM payroll_mgmt_acc_assign accasng INNER JOIN (SELECT `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `cost_center`, SUM(`amount`) as `amount` FROM payroll_tmp_mgmt_acc_split GROUP BY `payroll_employee_ID`,`payroll_account_ID`,`cost_center`) calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_period_ID`=".$payrollPeriodID." INNER JOIN `payroll_tmp_change_mng` emplList ON calc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM payroll_mgmt_acc_assign aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=".$uid." INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`cost_center`='' OR aa.`cost_center`=ep.`CostCenter`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id`", "payroll_processFinMgmtAccountingEntry");
/*
Hinweis:
Im Query auf Zeile 290 werden die Resultate der temporären Tabelle (`payroll_tmp_mgmt_acc_split`) in die BEBU-Buchungstabelle `payroll_mgmt_acc_entry` geschrieben.
Die Daten in der temp. Tbl `payroll_tmp_mgmt_acc_split` sind -- soweit ich das beurteilen kann -- korrekt. Beim Query auf Zeile 290 gibt es aber vermutlich beim joinen
noch ein Fehler, den es zu beheben gibt, denn nach dem Zusammenführen der verschiedenen Tabellen via JOIN, sind die Daten falsch (es gibt Duplikate!).

Fürs Debugging können in der SQL-Workbench die obigen Queries zusammen ausgeführt werden und nach jeder änderung können die Resultate in `payroll_tmp_mgmt_acc_split` überprüft werden:

XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
DELETE FROM payroll_tmp_change_mng WHERE core_user_id=1;
INSERT INTO payroll_tmp_change_mng(core_user_id, numID, alphID) SELECT 1, payroll_employee_ID, '' FROM payroll_period_employee;

-- Zuerst nur die BEBU-Daten der betroffenen MA loeschen…
DELETE accspl FROM `payroll_mgmt_acc_entry` accspl INNER JOIN `payroll_tmp_change_mng` emplList ON accspl.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=1 WHERE accspl.`payroll_period_ID`=78;
DELETE FROM `payroll_tmp_mgmt_acc_split`;
-- …dann die Records in einer temporären MEMORY-Table neu anlegen. Zuerst LOA mit expliziter Übersteuerung der Kostenstelle…
INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`) 
	SELECT 78,emp.`payroll_company_ID`,empacc.`payroll_employee_ID`,empacc.`CostCenter`,empacc.`payroll_account_ID`,0,0,calc.`amount`,0,0,0,1,0,0 
	FROM `payroll_employee_account` empacc 
	INNER JOIN `payroll_tmp_change_mng` emplList ON empacc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=1 
	INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` 
	INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_employee_ID`=emplList.`numID` AND calc.`payroll_account_ID`=empacc.`payroll_account_ID` AND calc.`payroll_period_ID`=78 
	WHERE empacc.`CostCenter`!='';
-- …als Nächstes ebenfalls Records in temporärer MEMORY-Table anlegen, aber jetzt die aebrigen %-Verteilungen. Wichtig: Bereits verarbeitete LOA ausschliessen, 100%-Zuweisungen koennen direkt verarbeitet und die enspr. Records abgeschlossen/fixiert werden...
INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`) 
	SELECT DISTINCT calc.`payroll_period_ID`, emp.`payroll_company_ID`, emp.`id`, IF(accasng.`payroll_employee_ID`=0 AND emp.`CostCenter`!='', emp.`CostCenter`,accasng.`cost_center`), calc.`payroll_account_ID`, calc.`amount`,calc.`amount`, IF(accasng.`amount`=100,IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`amount`) ,accasng.`processing_order`,accasng.`invert_value`,0,IF(accasng.`amount`=100,1,0),0,0 
	FROM `payroll_mgmt_acc_split` accasng 
	INNER JOIN `payroll_tmp_change_mng` emplList ON emplList.`core_user_ID`=1 
	INNER JOIN `payroll_calculation_current` calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_employee_ID`=emplList.`numID` AND calc.`payroll_period_ID`=78 
	INNER JOIN `payroll_employee` emp ON emp.`id`=emplList.`numID` 
	LEFT JOIN `payroll_tmp_mgmt_acc_split` tas ON tas.`payroll_period_ID`=calc.`payroll_period_ID` AND tas.`payroll_employee_ID`=calc.`payroll_employee_ID` AND tas.`payroll_account_ID`=calc.`payroll_account_ID` AND tas.`processing_done`=1 
	INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM `payroll_mgmt_acc_split` aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=1 INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id` AND tx.`id`=emplList.`numID` 
	WHERE tas.`processing_done` IS NULL;
-- …100%er wurden im obigen Statement verarbeitet. Hier werden nun %-Verteilungen <100%...
UPDATE `payroll_tmp_mgmt_acc_split` SET `amount`=`amount`/100*`amount_available` WHERE `processing_done`=0;
-- …Falls es einen Restbetrag gibt, wird dieser nun noch entsprechend zugewiesen…
INSERT INTO `payroll_tmp_mgmt_acc_split`(`payroll_period_ID`,`payroll_company_ID`,`payroll_employee_ID`,`cost_center`,`payroll_account_ID`,`amount_initial`,`amount_available`,`amount`,`processing_order`,`invert_value`,`amount_quantity`,`processing_done`,`having_rounding`,`rounding`,`remainder`) SELECT accsplt.`payroll_period_ID`,accsplt.`payroll_company_ID`,accsplt.`payroll_employee_ID`,IF(x1.`remainder`=1,accsplt.`cost_center`,empl.`CostCenter`),accsplt.`payroll_account_ID`,accsplt.`amount_initial`,accsplt.`amount_available`, x1.`amount_initial`-x1.`amount_sum`, 0,0,accsplt.`amount_quantity`,1,accsplt.`having_rounding`,accsplt.`rounding`,accsplt.`remainder` FROM payroll_tmp_mgmt_acc_split accsplt INNER JOIN (SELECT `payroll_employee_ID`, `payroll_account_ID`, `amount_initial`, SUM(`amount`) as amount_sum, MAX(`amount`) as amount_max, `remainder` FROM payroll_tmp_mgmt_acc_split WHERE processing_done=0 GROUP BY payroll_employee_ID, payroll_account_ID) x1 ON accsplt.`payroll_employee_ID`=x1.`payroll_employee_ID` AND accsplt.`payroll_account_ID`=x1.`payroll_account_ID` AND accsplt.`amount`=x1.amount_max INNER JOIN `payroll_employee` empl ON empl.`id`=accsplt.`payroll_employee_ID` WHERE accsplt.`processing_done`=0;
-- …"0.00" Beträge loeschen (TODO: Ist das OK?)…
DELETE FROM `payroll_tmp_mgmt_acc_split` WHERE `amount`=0;
-- …Records der temporären Tabelle in eine InnoDB Tabelle speichern…
INSERT INTO `payroll_mgmt_acc_entry`(`payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `account_no`, `counter_account_no`, `cost_center`, `amount_local`, `debitcredit`, `entry_text`, `amount_quantity`) 
	SELECT calc.`payroll_period_ID`, calc.`payroll_employee_ID`, accasng.`payroll_account_ID`, accasng.`account_no`, accasng.`counter_account_no`, calc.`cost_center`, IF(accasng.`invert_value`=1, calc.`amount`*-1, calc.`amount`), accasng.`debitcredit`, accasng.`entry_text`, 0 
	FROM payroll_mgmt_acc_assign accasng 
	INNER JOIN (SELECT `payroll_period_ID`, `payroll_employee_ID`, `payroll_account_ID`, `cost_center`, SUM(`amount`) as `amount` 
		FROM payroll_tmp_mgmt_acc_split 
		GROUP BY `payroll_employee_ID`,`payroll_account_ID`,`cost_center`) calc ON calc.`payroll_account_ID`=accasng.`payroll_account_ID` AND calc.`payroll_period_ID`=78 
		INNER JOIN `payroll_tmp_change_mng` emplList ON calc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=1 INNER JOIN (SELECT ep.`id`, aa.`payroll_account_ID`, MIN(aa.`processing_order`) as po FROM payroll_mgmt_acc_assign aa INNER JOIN `payroll_tmp_change_mng` el ON el.`core_user_ID`=1 INNER JOIN `payroll_employee` ep ON ep.`id`=el.`numID` WHERE (aa.`payroll_employee_ID`=0 OR aa.`payroll_employee_ID`=ep.`id`) AND (aa.`cost_center`='' OR aa.`cost_center`=ep.`CostCenter`) AND (aa.`payroll_company_ID`=0 OR aa.`payroll_company_ID`=ep.`payroll_company_ID`) 
	GROUP BY ep.`id`, aa.`payroll_account_ID`) tx ON tx.`payroll_account_ID`=accasng.`payroll_account_ID` AND tx.`po`=accasng.`processing_order` AND calc.`payroll_employee_ID`=tx.`id`;

SELECT * FROM `payroll_tmp_mgmt_acc_split`;
XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
*/

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

	
}
?>
