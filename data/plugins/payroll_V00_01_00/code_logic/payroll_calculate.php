<?php
class payroll_BL_calculate {

	private function recursiveAccountActivation(&$accountCollection, $account2activate) {
		if($accountCollection[$account2activate]["active"]) return;
		$accountCollection[$account2activate]["active"] = true;
		foreach($accountCollection[$account2activate]["parents"] as $parentAcc) $this->recursiveAccountActivation($accountCollection, $parentAcc);
		return;
	}

   public function calculate($calculateAll=true) {

		$uid = session_control::getSessionInfo("id");
		$system_database_manager = system_database_manager::getInstance();

		$result = $system_database_manager->executeQuery("SELECT `id`,`payroll_year_ID`,`major_period`,`minor_period`,`major_period_associated` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_calculate");
		if(count($result)>0) {
			$payrollPeriodID = $result[0]["id"];
			$currentYear = $result[0]["payroll_year_ID"];
			$periodAssociated = $result[0]["major_period_associated"];
			$minorPeriod = $result[0]["minor_period"];
			$majorPeriod = $result[0]["major_period"];
			if($result[0]["minor_period"]!=0) $periodType = "minor_period";
			else if($result[0]["major_period"]>14) $periodType = "major_period_bonus";
			else $periodType = "major_period";
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
		}

		$dedAtSrcInit = 1;

		$system_database_manager->executeUpdate("BEGIN", "payroll_calculate");
		if($calculateAll) {
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng` (`core_user_ID`,`numID`) " .
				"SELECT ".$uid.",`payroll_employee_ID` " .
				"FROM `payroll_period_employee` " .
				"WHERE `payroll_period_ID`=".$payrollPeriodID." " .
					"AND `processing`=1", "payroll_calculate");
		}

		$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_acclnk` " .
				"SELECT * FROM `payroll_account_linker` " .
				"WHERE `payroll_year_ID`=".$currentYear, "payroll_calculate");

		//Wurde einem MA ein Code zugewiesen, der eine Netto-Brutto-Aufrechnung ausloest?
		$resNetGross = $system_database_manager->executeQuery("SELECT emp.`id`, IF(emp.`CodeAHV`=3 OR emp.`CodeAHV`=6,1,0) as processAHV, IF(emp.`CodeALV`=3,1,0) as processALV, IF(emp.`DedAtSrcCompany`=1,1,0) as processDAS FROM `payroll_employee` emp INNER JOIN `payroll_tmp_change_mng` emplList ON emp.`id`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." WHERE emp.`CodeAHV`=3 OR emp.`CodeAHV`=6 OR emp.`CodeALV`=3 OR (emp.`DedAtSrcCompany`=1 AND emp.`DedAtSrcMode`!=1)", "payroll_calculate");
		$netGrossMode = count($resNetGross)!=0 ? true : false;
		//Wurde einem MA eine Trigger-LOA zugewiesen, die eine Nettolohnkorrektur ausloest?
		$resNetAdj = $system_database_manager->executeQuery("SELECT emplacc.`payroll_employee_ID`,netadj.`trigger_account`,netadj.`adjustment_account`,netadj.`add_to_net_salary` FROM `payroll_employee_account` emplacc INNER JOIN `payroll_tmp_change_mng` emplList ON emplacc.`payroll_employee_ID`=emplList.`numID` AND emplList.`core_user_ID`=".$uid." INNER JOIN `payroll_net_salary_adjust` netadj ON emplacc.`payroll_account_ID`=netadj.`trigger_account` WHERE (emplacc.`PayrollDataType`<5 OR emplacc.`PayrollDataType`=8 OR emplacc.`PayrollDataType`=10) AND (emplacc.`DateFrom`='0000-00-00' OR emplacc.`DateFrom`<=LAST_DAY('".$currentYear."-".substr("0".$periodAssociated,-2)."-01')) AND (emplacc.`DateTo`='0000-00-00' OR emplacc.`DateTo`>='".$currentYear."-".substr("0".$periodAssociated,-2)."-01') AND emplacc.`".$periodType."`=1 GROUP BY emplacc.`payroll_employee_ID`,netadj.`trigger_account`,netadj.`adjustment_account` ORDER BY netadj.`add_to_net_salary`", "payroll_calculate");
		$netAdjMode = count($resNetAdj)!=0 ? true : false;

		$now = microtime(true); //TODO: PROFILING START	//TODO:profiling
		if($netGrossMode || $netAdjMode) {
			//Wenn die betroffenen MA nur eine Teilmenge aller insg. abzurechnender MA darstellt, 
			//dann muessen alle MA in einen Array eingelesen werden und die Teilmenge in einen separaten Array.
			//Zudem ist payroll_tmp_change_mng mit den IDs der Teilmenge zu f�ellen.
			$fullset = array();
			$res = $system_database_manager->executeQuery("SELECT `numID` FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			foreach($res as $row) $fullset[] = $row["numID"];

			$employees = array();
			$terminationAccounts = array();

			$subsetNetGross = array();
			$processNetGrossAHV = false;
			$processNetGrossALV = false;
			$processNetGrossDAS = false;
			foreach($resNetGross as $row) {
				if($row["processAHV"]==1) $processNetGrossAHV = true;
				if($row["processALV"]==1) $processNetGrossALV = true;
				if($row["processDAS"]==1) $processNetGrossDAS = true;
				$employees[$row["id"]]["overallAccuracy"] = 0.0;
				$employees[$row["id"]]["NetGross"]["completed"] = false;
				$employees[$row["id"]]["NetGross"]["diff"] = 0.0;
				$employees[$row["id"]]["NetGross"]["amountSum"] = 0.0;
				$employees[$row["id"]]["NetGross"]["estimationEnabled"] = true;
				$employees[$row["id"]]["NetGross"]["AHV"]["process"] = $row["processAHV"]==1 ? true : false;
				$employees[$row["id"]]["NetGross"]["AHV"]["oldRate"] = 0.0;
				$employees[$row["id"]]["NetGross"]["AHV"]["result"] = 0.0;
				$employees[$row["id"]]["NetGross"]["AHV"]["quantity"] = 0.0;
				$employees[$row["id"]]["NetGross"]["AHV"]["amount"] = 0.0;
				$employees[$row["id"]]["NetGross"]["AHV"]["weight"] = 0.0;
				$employees[$row["id"]]["NetGross"]["ALV"]["process"] = $row["processALV"]==1 ? true : false;
				$employees[$row["id"]]["NetGross"]["ALV"]["oldRate"] = 0.0;
				$employees[$row["id"]]["NetGross"]["ALV"]["result"] = 0.0;
				$employees[$row["id"]]["NetGross"]["ALV"]["quantity"] = 0.0;
				$employees[$row["id"]]["NetGross"]["ALV"]["amount"] = 0.0;
				$employees[$row["id"]]["NetGross"]["ALV"]["weight"] = 0.0;
				$employees[$row["id"]]["NetGross"]["DAS"]["process"] = $row["processDAS"]==1 ? true : false;
				$employees[$row["id"]]["NetGross"]["DAS"]["oldRate"] = 0.0;
				$employees[$row["id"]]["NetGross"]["DAS"]["result"] = 0.0;
				$employees[$row["id"]]["NetGross"]["DAS"]["quantity"] = 0.0;
				$employees[$row["id"]]["NetGross"]["DAS"]["amount"] = 0.0;
				$employees[$row["id"]]["NetGross"]["DAS"]["weight"] = 0.0;
				$subsetNetGross[] = $row["id"];
			}
			$subsetNetGross = array_unique($subsetNetGross);

			$netAdjMaxAdjAccCount = 0;
			$subsetNetAdj = array();
			$AdjAccCollection = array();
			$netAdjTriggerAcc = array();
			foreach($resNetAdj as $row) {
				$employees[$row["payroll_employee_ID"]]["overallAccuracy"] = 0.0;
				$employees[$row["payroll_employee_ID"]]["NetAdj"]["completed"] = false;
				$employees[$row["payroll_employee_ID"]]["NetAdj"]["goal"] = 0.0;

				if(!isset($employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]])) {
					$employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]]["triggerAccounts"] = array();
					$employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]]["add_to_net_salary"] = false;
				}
				$employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]]["result"] = 0.0;
				$employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]]["proposedResult"] = 0.0;
				$employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]]["triggerAccounts"][] = array("trigger_account"=>$row["trigger_account"], "add_to_net_salary"=>$row["add_to_net_salary"]==1 ? true : false);
				if($row["add_to_net_salary"]==1) $employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$row["adjustment_account"]]["add_to_net_salary"] = true;

				if(!isset($AdjAccCollection[(string)$row["adjustment_account"]])) $AdjAccCollection[(string)$row["adjustment_account"]] = array();
				$AdjAccCollection[(string)$row["adjustment_account"]][] = $row["payroll_employee_ID"];
				if(!isset($netAdjTriggerAcc[(string)$row["adjustment_account"]])) $netAdjTriggerAcc[(string)$row["adjustment_account"]] = array();
				$netAdjTriggerAcc[(string)$row["adjustment_account"]][] = $row["trigger_account"];
				$subsetNetAdj[] = $row["payroll_employee_ID"];
			}
			$subsetNetAdj = array_unique($subsetNetAdj);
			foreach($AdjAccCollection as $accNo=>$arrEmplID) $AdjAccCollection[(string)$accNo] = array_unique($arrEmplID);
			foreach($netAdjTriggerAcc as $accNo=>$adjacc) $netAdjTriggerAcc[(string)$accNo] = array_unique($adjacc);

			$res = $system_database_manager->executeQuery("SELECT `payroll_account_ID`,IF(`ProcessingMethod`=1,'NetAdj',IF(`AccountType`=1,'AHV',IF(`AccountType`=2,'ALV','ALVZ'))) as fieldDescriptor FROM `payroll_account_mapping` WHERE (`ProcessingMethod`=1 AND `AccountType`=20) OR `ProcessingMethod`=6 UNION SELECT `payroll_account_ID`,CONCAT('DAS',`AccountType`) as fieldDescriptor FROM `payroll_das_account` WHERE `AccountType` IN (7,9,16,17)", "payroll_calculate"); //XXX: NEU WEGEN QST !
			foreach($res as $row) $resultAccount[$row["fieldDescriptor"]] = $row["payroll_account_ID"];

			//Benoetigter LOA-Baum rekursiv ermitteln
			//	* zuerst sind die Start-LOA fuer die rekursive LOA-Ermittlung zu bestimmen: bei NL-Korrektur ist der Nettolohn die Start-LOA, bei Netto-Brutto-Aufrechnung ist es von den ausgewählen Codes bei QST, AHV und ALV abhängig
			//	* Benoetigte LOAs in Tabelle payroll_tmp_calculation_acc speichern
			$arrPayrollLOA = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_account` WHERE `payroll_year_ID`=".$currentYear, "payroll_calculate");
			$arrPayrollLinker = $system_database_manager->executeQuery("SELECT `payroll_account_ID`,`payroll_child_account_ID` FROM `payroll_account_linker` WHERE `payroll_year_ID`=".$currentYear, "payroll_calculate");
			$arrLoaTree = array();
			foreach($arrPayrollLOA as $row) {
				$arrLoaTree[$row["id"]]["parents"] = array();
				$arrLoaTree[$row["id"]]["active"] = false;
			}
			foreach($arrPayrollLinker as $row) $arrLoaTree[$row["payroll_child_account_ID"]]["parents"][] = $row["payroll_account_ID"];

			if($netAdjMode) $this->recursiveAccountActivation($arrLoaTree, $resultAccount["NetAdj"]);
			if($processNetGrossAHV) $this->recursiveAccountActivation($arrLoaTree, $resultAccount["AHV"]);
			if($processNetGrossALV) { $this->recursiveAccountActivation($arrLoaTree, $resultAccount["ALV"]); $this->recursiveAccountActivation($arrLoaTree, $resultAccount["ALVZ"]); }
			if($processNetGrossDAS) foreach(array(7,9,16,17) as $n) $this->recursiveAccountActivation($arrLoaTree, $resultAccount["DAS".$n]); //XXX: NEU WEGEN QST !

			//define the set of payroll accounts to use for calculation (default: use all accounts)
			$accInsertValues = array();
			foreach($arrLoaTree as $accNr=>$accParam) if($accParam["active"]) $accInsertValues[] = "(".$uid.",'".$accNr."')";
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation_acc` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation_acc`(`core_user_ID`,`payroll_account_ID`) VALUES".implode(",",$accInsertValues), "payroll_calculate");

			if($netGrossMode) {
				//Korrektur-LOA fuer Netto-Brutto-Aufrechnung ermitteln
				$res = $system_database_manager->executeQuery("SELECT `payroll_account_ID`,IF(`AccountType`=1,'DAS',IF(`AccountType`=2,'AHV','ALV')) as fieldDescriptor FROM `payroll_account_mapping` WHERE `ProcessingMethod`=7", "payroll_calculate");
				foreach($res as $row) $adjustmentAccount[$row["fieldDescriptor"]] = $row["payroll_account_ID"];

				$arrNetGrossLoaFilter = array();
				if($processNetGrossAHV) $arrNetGrossLoaFilter[] = "'".$resultAccount["AHV"]."'";
				if($processNetGrossALV) { $arrNetGrossLoaFilter[] = "'".$resultAccount["ALV"]."'"; $arrNetGrossLoaFilter[] = "'".$resultAccount["ALVZ"]."'"; }
				if($processNetGrossDAS) foreach(array(7,9,16,17) as $n) $arrNetGrossLoaFilter[] = "'".$resultAccount["DAS".$n]."'"; //XXX: NEU WEGEN QST !
				$sqlFilterNetGrossLOA = implode(",",$arrNetGrossLoaFilter);
			}

			///////////////////////////////////////////////////////////////////////////////
			// LOOP fuer die BERECHNUNG der Nettolohnkorrektur und Netto-Brutto-Aufrechnung
			///////////////////////////////////////////////////////////////////////////////
			$mainIterationActive = true;
			$adjAccountProcessed = null;
			$saveOverallAccuracy = false;
			$netGrossIterationActive = $netGrossMode;
			$netAdjIterationActive = $netAdjMode;
			$netGrossTypeIdentification = array($resultAccount["AHV"]=>"AHV", $resultAccount["ALV"]=>"ALV", $resultAccount["ALVZ"]=>"ALV"); //XXX: NEU WEGEN QST !
			$employeesCombinedProcessing = array();
			$employeesCombinedProcessingLoopCount = 1;
			foreach(array(7,9,16,17) as $n) $netGrossTypeIdentification[$resultAccount["DAS".$n]] = "DAS"; //XXX: NEU WEGEN QST !
			do{
				if($netGrossMode && $netAdjMode) $saveOverallAccuracy = true;

				// N-B-Aufrechnung
				if($netGrossIterationActive) {
					$arrEmployeesDone = array();
					//Betroffene Mitarbeiter in `payroll_tmp_change_mng` einlesen
					$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
					$employeSetInsertValues = array();
					foreach($subsetNetGross as $row) $employeSetInsertValues[] = "(".$uid.",".$row.")";
					$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) VALUES".implode(",",$employeSetInsertValues), "payroll_calculate");

					//Loop fuer Netto-Brutto-Aufrechnung (maximal 20 Iterationen!)
					for($i=1;$netGrossIterationActive && $i<21;$i++) {
						$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation` WHERE `core_user_ID`=".$uid, "payroll_calculate");

						// FALLS DIES nicht der erste Durchgang ist, so muessen hier allfällige NL-Korrektur-LOA eingefaegt werden!
						if($employeesCombinedProcessingLoopCount != 1) {
							//INSERT Net-Adjustment-Accounts
							$sqlValues = array();
							
							foreach($subsetNetAdj as $emplID) {
								foreach($adjAccountProcessed as $currentAP) {
									if(isset($employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"])) 
										if($employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"]!="") $sqlValues[] = "(".$uid.",".$currentYear.",'".$currentAP."',".$emplID.",0,0,0,0,0,".$employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"].",0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0)";
								}
							}
							if(count($sqlValues)!=0) {
								$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`) VALUES".implode(", ", $sqlValues), "payroll_calculate");
							}
						}

						if($i!=1) {
							//korrektur LOA einfuegen, resp. Werte bei Korrektur-LOA setzen (feld betrag)
							$sqlValues = array();
							$activeEmployees = array_diff($subsetNetGross, $arrEmployeesDone);
							foreach($activeEmployees as $emplID) {
								foreach(array("AHV","ALV","DAS") as $currentNetGrossType) {
									if($employees[$emplID]["NetGross"][$currentNetGrossType]["process"]) {
										$employees[$emplID]["NetGross"][$currentNetGrossType]["result"] = $employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"];
										if($employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"]!="") $sqlValues[] = "(".$uid.",".$currentYear.",'".$adjustmentAccount[$currentNetGrossType]."',".$emplID.",0,0,0,0,0,".$employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"].",0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0)";
									}
								}
							}
							if(count($sqlValues)!=0) {
								$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`) VALUES".implode(", ", $sqlValues), "payroll_calculate");
							}
						}
						//start calculation
						$system_database_manager->executeUpdate("Call payroll_prc_calculate(".$uid.",".$dedAtSrcInit.",0)", "payroll_calculate"); //initialisierung der QST-Parameter nur beim ersten Run noetig!
						$dedAtSrcInit = 0;

						//LOA of interest auslesen und mit Werten im Array ... vergleichen.
						$currentEmployeeSet["AHV"] = array();
						$currentEmployeeSet["ALV"] = array();
						$currentEmployeeSet["DAS"] = array();
						$res = $system_database_manager->executeQuery("SELECT `payroll_employee_ID`,`payroll_account_ID`,`quantity`,`amount` FROM `payroll_tmp_calculation` WHERE `payroll_account_ID` IN (".$sqlFilterNetGrossLOA.") AND `core_user_ID`=".$uid, "payroll_calculate");
						foreach($res as $row) {
							$currentEmployee = &$employees[$row["payroll_employee_ID"]]["NetGross"];
							$currentNetGrossType = $netGrossTypeIdentification[$row["payroll_account_ID"]]; //"AHV", "ALV" oder "DAS"
							if($currentEmployee[$currentNetGrossType]["process"]) {
								$currentEmployee[$currentNetGrossType]["quantity"] += $row["quantity"];
								$currentEmployee[$currentNetGrossType]["amount"] -= $row["amount"];
								$currentEmployeeSet[$currentNetGrossType][] = $row["payroll_employee_ID"];
							}
							$employees[$row["payroll_employee_ID"]]["NetGross"]["diff"] = 0.0; //Differenzbetrag zuraecksetzen
						}
						$arrEmployeeProcessed = array();
						foreach(array("AHV","ALV","DAS") as $currentNetGrossType) {
							$currentEmployeeSet[$currentNetGrossType] = array_unique($currentEmployeeSet[$currentNetGrossType]);
							foreach($currentEmployeeSet[$currentNetGrossType] as $curEmployeeID) {
								$arrEmployeeProcessed[] = $curEmployeeID;
								$currentEmployee = &$employees[$curEmployeeID]["NetGross"][$currentNetGrossType];
								if($currentEmployee["process"]) {
									if($i==1) $employees[$curEmployeeID]["NetGross"]["amountSum"] += $currentEmployee["amount"]; //XXX	BETRAG summieren. Nur in erstem Durchgang um prozentuele Verteilung zu ermitteln
									$employees[$curEmployeeID]["NetGross"]["diff"] += $currentEmployee["amount"] - $currentEmployee["result"];
									$curRate = round($currentEmployee["amount"]/$currentEmployee["quantity"]*100, 2);
									$res = 0.0;
									if($employees[$curEmployeeID]["NetGross"]["estimationEnabled"] && $curRate != $currentEmployee["oldRate"]) {
										$employees[$curEmployeeID]["NetGross"]["estimationEnabled"] = false;
										//aktueller Prozentsatz unterscheidet sich von vorangehendem... also erstellen wir eine neue Zielwertprognose
										if($currentEmployee["weight"]!=0.0) $zr = 100/$curRate/$currentEmployee["weight"];
										else $zr = 100/$curRate;

										$totalPow = ceil($curRate*0.4+2) + 1;
										for($k=1;$k<$totalPow;$k++) {
											$pw = pow($zr, $k);
											$res += $currentEmployee["amount"]/$pw;
										}

										$currentEmployee["oldRate"] = $curRate;
									}
									if($res == 0.0) $currentEmployee["proposedResult"] = $currentEmployee["amount"];
									else $currentEmployee["proposedResult"] = $currentEmployee["amount"] + $res;
									if($i!=1) $currentEmployee["amount"] = 0.0;
									$currentEmployee["quantity"] = 0.0;
								}
							}
						}

						if($i==1) { 
							foreach($employees as $curEmployeeID=>$ep) {
								foreach(array("AHV","ALV","DAS") as $ngt) {
									if($employees[$curEmployeeID]["NetGross"][$ngt]["process"]) {
										$employees[$curEmployeeID]["NetGross"][$ngt]["weight"] = $employees[$curEmployeeID]["NetGross"][$ngt]["amount"] / $employees[$curEmployeeID]["NetGross"]["amountSum"];
										$employees[$curEmployeeID]["NetGross"][$ngt]["amount"] = 0.0;
									}
								}
							}
						}


						//Ueberpraefen welche MA keine Differenzen mehr aufweisen, diese muessen nicht weiter iteriert werden. Wenn keiner der MA mehr Diff aufweist, kann Loop beendet werden
						$arrEmployeeProcessed = array_unique($arrEmployeeProcessed);
						foreach($arrEmployeeProcessed as $empId) {
							if(abs($employees[$empId]["NetGross"]["diff"]) < 0.05) $arrEmployeesDone[] = $empId;
						}


						if(count($arrEmployeesDone)!=0) $system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid." AND `numID` IN (".implode(",",$arrEmployeesDone).")", "payroll_calculate");
						if(count($subsetNetGross)==count($arrEmployeesDone)) $netGrossIterationActive = false;

						//temp. Berechnungsdaten loeschen
						$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation` WHERE `core_user_ID`=".$uid, "payroll_calculate");
					}
				}

				////////////////////////////// NL-Korrektur
				if($netAdjIterationActive) {
					$adjAccountProcessed = array();
					//alle LOAs loeschen, die etwas mit der NL-Korrektur zu tun haben (also: Trigger-LOA und Korrektur-LOA)
					//die LOAs werden erst bei Bedarf im Verlauf des Iterierens hinzugefaegt
					$system_database_manager->executeUpdate("DELETE dest FROM `payroll_tmp_calculation_acc` dest INNER JOIN (SELECT `trigger_account` as payroll_account_ID FROM `payroll_net_salary_adjust` UNION SELECT `adjustment_account` as payroll_account_ID FROM `payroll_net_salary_adjust` GROUP BY payroll_account_ID) src ON dest.`payroll_account_ID`=src.`payroll_account_ID` WHERE dest.`core_user_ID`=".$uid, "payroll_calculate");

					foreach($AdjAccCollection as $currentAdjAcc=>$currentEmplCollection) {
						if($employeesCombinedProcessingLoopCount != 1) {
							// Wenn $employeesCombinedProcessingLoopCount != 1, dann soll die Schnittmenge von $currentEmplCollection + $employeesCombinedProcessing verwendet werden, anstatt nur $currentEmplCollection
							// Achtung: Wenn resultierender Array leer ist, muss Iteration aebersprungen werden!
							$currentEmplCollection = array_intersect($currentEmplCollection, $employeesCombinedProcessing);
							if(count($currentEmplCollection)==0) continue; //wenn keine MA, dann im Parent-FOREACH ein Element vorraecken (aktuelle Iteration wird aebersprungen)
						}
						$netAdjIterationActive = true;
						//ermitteln, welche MA fuer diesen Durchgang berechnet werden muessen (emplID, "adjustment_account", "trigger_account")
						$arrEmployeesDone = array();
						//Betroffene Mitarbeiter in `payroll_tmp_change_mng` einlesen
						$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
						$employeSetInsertValues = array();
						$subsetNetGrossIntersection = array();
						foreach($currentEmplCollection as $row) {
							$employeSetInsertValues[] = "(".$uid.",".$row.")";
							if(isset($employees[(string)$row]["NetGross"])) $subsetNetGrossIntersection[] = $row;
						}
						$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) VALUES".implode(",",$employeSetInsertValues), "payroll_calculate");
						//Loop fuer Netto-Brutto-Aufrechnung (maximal 20 Iterationen!)
						for($i=1;$netAdjIterationActive && $i<21;$i++) {
							$activeEmployees = array_diff($currentEmplCollection, $arrEmployeesDone);
							if(count($arrEmployeesDone)!=0) $system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid." AND `numID` IN (".implode(",",$arrEmployeesDone).")", "payroll_calculate");

							$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation` WHERE `core_user_ID`=".$uid, "payroll_calculate");

							//INSERT NetGross-Adjustment-Accounts (nur bei MA mit entsprechender Verarbeitung)
							//bei mitarbeitern mit N-B-Aufrechnung, muessen die entsprechenden LOA inkl. Beträge eingefaegt werden
							if(count($subsetNetGrossIntersection)!=0) {
								$sqlValues = array();
								foreach($subsetNetGrossIntersection as $emplID) {
									foreach(array("AHV","ALV","DAS") as $currentNetGrossType) {
										if($employees[$emplID]["NetGross"][$currentNetGrossType]["process"]) {
											$employees[$emplID]["NetGross"][$currentNetGrossType]["result"] = $employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"];
											if($employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"]!="") $sqlValues[] = "(".$uid.",".$currentYear.",'".$adjustmentAccount[$currentNetGrossType]."',".$emplID.",0,0,0,0,0,".$employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"].",0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0)";
										}
									}
								}
								if(count($sqlValues)!=0) $system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`) VALUES".implode(", ", $sqlValues), "payroll_calculate");
							}
							switch($i) {
							case 1: //1. Berechnung ohne "adjustment_account" und "trigger_account" laufen lassen

//								$acc = array();
//								$acc[] = "'".$currentAdjAcc."'";
//								foreach($netAdjTriggerAcc[(string)$currentAdjAcc] as $curAcc) $acc[] = "'".$curAcc."'";
//								$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation_acc` WHERE `core_user_ID`=".$uid." AND `payroll_account_ID` IN (".implode(",",$acc).")", "payroll_calculate");

								break;
							case 2: //2. Berechnung nur mit "trigger_account" laufen lassen
								$acc = array();
								foreach($netAdjTriggerAcc[(string)$currentAdjAcc] as $curAcc) $acc[] = "(".$uid.",'".$curAcc."')";
								$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation_acc`(`core_user_ID`,`payroll_account_ID`) VALUES".implode(",",$acc), "payroll_calculate");
								break;
							case 3: //3. Berechnung mit "adjustment_account" und "trigger_account" laufen lassen. Der BETRAG von "adjustment_account" muss gesetzt werden
								$adjAccountProcessed[] = $currentAdjAcc;
								$acc = array();
								$acc[] = "(".$uid.",'".$currentAdjAcc."')";
								$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation_acc`(`core_user_ID`,`payroll_account_ID`) VALUES".implode(",",$acc), "payroll_calculate");
								break;
							}

							//korrektur LOA einfuegen, resp. Werte bei Korrektur-LOA setzen (feld betrag)
							$sqlValues = array();
							foreach($activeEmployees as $emplID) {
								foreach($adjAccountProcessed as $currentAP) {
									if($currentAdjAcc==$currentAP) $employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["result"] = $employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"];
									if(isset($employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"])) 
										if($employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"]!="") $sqlValues[] = "(".$uid.",".$currentYear.",'".$currentAP."',".$emplID.",0,0,0,0,0,".$employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"].",0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0)";
								}
							}
							if(count($sqlValues)!=0) {
								$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`) VALUES".implode(", ", $sqlValues), "payroll_calculate");
							}

							//start calculation
							$system_database_manager->executeUpdate("Call payroll_prc_calculate(".$uid.",".$dedAtSrcInit.",0)", "payroll_calculate"); //initialisierung der QST-Parameter nur beim ersten Run noetig!
							$dedAtSrcInit = 0;

							//aktive MA loopen, nettobetrag auslesen und mit Soll-Betrag (bei add_to_net... muss addiert werden!) vergleichen... neues "proposedResult" generieren
							//...und feststellen, welche MA fertig sind -> in $arrEmployeesDone eintragen!
							switch($i) {
							case 1: //fuer jeden MA den Nettobetrag als SOLL-Wert auslesen -> Achtung: falls add_to_net_salary, muss die Summe aller BETRAeGE von den trigger_accounts dem Nettobetrag addiert werden!
								$res = $system_database_manager->executeQuery("SELECT `payroll_employee_ID`,`amount` FROM `payroll_tmp_calculation` WHERE `payroll_account_ID`='".$resultAccount["NetAdj"]."' AND `core_user_ID`=".$uid, "payroll_calculate");
								foreach($res as $row) {
									$employees[$row["payroll_employee_ID"]]["NetAdj"]["goal"] = $row["amount"];
								}
								break;
							case 2: //Netto-Betrag auslesen und Differenz zum SOLL-Wert ermitteln + "proposedResult" mit Zielwertprognose faellen
								//Handelt es sich um eine $currentAdjAcc, wo add_to_net_salary=1?
								$resATN = $system_database_manager->executeQuery("SELECT MAX(`add_to_net_salary`) as isAddToNet FROM `payroll_net_salary_adjust` WHERE `adjustment_account`='".$currentAdjAcc."' GROUP BY `adjustment_account`", "payroll_calculate");
								if($resATN[0]["isAddToNet"]==1) {
									$resAS = $system_database_manager->executeQuery("SELECT calc.`payroll_employee_ID`, SUM(calc.`amount`) as amountSum FROM `payroll_tmp_calculation` calc INNER JOIN `payroll_net_salary_adjust` pnsa ON calc.`payroll_account_ID`=pnsa.`trigger_account` AND pnsa.`adjustment_account`='".$currentAdjAcc."' WHERE calc.`core_user_ID`=".$uid." GROUP BY calc.`payroll_employee_ID`", "payroll_calculate");
									foreach($resAS as $rec) {
										$employees[$rec["payroll_employee_ID"]]["NetAdj"]["goal"] += $rec["amountSum"];
									}
								}
							default: //Netto-Betrag auslesen und Differenz zum SOLL-Wert ermitteln / "proposedResult"=Differenz-Betrag
								$res = $system_database_manager->executeQuery("SELECT `payroll_employee_ID`,`amount` FROM `payroll_tmp_calculation` WHERE `payroll_account_ID`='".$resultAccount["NetAdj"]."' AND `core_user_ID`=".$uid, "payroll_calculate");
								foreach($res as $row) {
									$delta = $employees[$row["payroll_employee_ID"]]["NetAdj"]["goal"] - $row["amount"];
									if($i==2) $employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$currentAdjAcc]["firstDelta"] = $delta;
									else if($i==3) {
										$ir = $delta / $employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$currentAdjAcc]["firstDelta"];
										$delta += $delta*$ir+$delta*$ir*$ir+$delta*$ir*$ir*$ir;
									}
									$employees[$row["payroll_employee_ID"]]["NetAdj"]["adjustmentAccounts"][(string)$currentAdjAcc]["proposedResult"] += $delta;
									if(abs($delta) < 0.05) $arrEmployeesDone[] = $row["payroll_employee_ID"];
									$employees[$row["payroll_employee_ID"]]["latestAmount"] = $row["amount"];
								}
								break;
							}

							if(count($currentEmplCollection)==count($arrEmployeesDone)) $netAdjIterationActive = false;
						}
					}
				}
				if($netGrossMode && $netAdjMode) {
					if($employeesCombinedProcessingLoopCount==1) {
						$subsetNetGross_ORIGIN = $subsetNetGross;
						$subsetNetAdj_ORIGIN = $subsetNetAdj;
					}
					$mainIterationActive = false;
					$employeesCombinedProcessing = array();
					foreach($employees as $empId=>$empParam) {
						if(isset($empParam["NetGross"]) && isset($empParam["NetAdj"])) {
							//es handelt sich um einen MA, bei dem beides verarbeitet werden muss: NB-Aufrechnung und NL-Korrektur -> also muessen wir hier die Start-Differenz aeberpraefen
							$delta = abs($empParam["overallAccuracy"]-$empParam["latestAmount"]);
							if($delta > 0.10) {
								//Die Differenz ist noch zu hoch, der Gesamtloop darf noch nicht beendet werden
								$mainIterationActive = true;
								$employeesCombinedProcessing[] = $empId;
								$employees[$empId]["overallAccuracy"] = $empParam["latestAmount"];
							}
						}
					}
					$employeesCombinedProcessingLoopCount++;
					if($employeesCombinedProcessingLoopCount>10) $mainIterationActive = false;
					if(!$mainIterationActive) {
						$subsetNetGross = $subsetNetGross_ORIGIN;
						$subsetNetAdj = $subsetNetAdj_ORIGIN;
					}else{
						$netGrossIterationActive = true;
						$netAdjIterationActive = true;
					}
				}else $mainIterationActive = false;
			}while($mainIterationActive);
		}else{
			//define the set of payroll accounts to use for calculation (default: use all accounts)
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation_acc` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation_acc`(`core_user_ID`,`payroll_account_ID`) SELECT ".$uid.",`id` FROM `payroll_account` WHERE `payroll_year_ID`=".$currentYear, "payroll_calculate");
		}

		/////////////////////////////////////////////
		// SCHLUSSLAUF
		/////////////////////////////////////////////
		$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation` WHERE `core_user_ID`=".$uid, "payroll_calculate");

		if($netGrossMode || $netAdjMode) {
			//da bei Nettolohnkorrektur und Netto-Brutto-Aufrechnung zuerst mit reduzierten LOA-Stamm gerechnet wird, muessen fuer den Schlusslauf wieder alle LOA aktiviert werden!
			//define the set of payroll accounts to use for calculation (use all accounts)
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation_acc` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation_acc`(`core_user_ID`,`payroll_account_ID`) SELECT ".$uid.",`id` FROM `payroll_account` WHERE `payroll_year_ID`=".$currentYear, "payroll_calculate");

			//Alle MA einfuegen: Zuerst werden nur die MA berechnet, die von Nettolohnkorrektur und/oder Netto-Brutto-Aufrechnung betroffen sind. fuer den Schlusslauf muessen nun aber alle MA berechnet werden
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			$employeSetInsertValues = array();
			foreach($fullset as $row) $employeSetInsertValues[] = "(".$uid.",".$row.")";
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) VALUES".implode(",",$employeSetInsertValues), "payroll_calculate");

			//INSERT NetGross-Adjustment-Accounts
			$sqlValues = array();
			foreach($subsetNetGross as $emplID) {
				foreach(array("AHV","ALV","DAS") as $currentNetGrossType) {
					if($employees[$emplID]["NetGross"][$currentNetGrossType]["process"]) {
						$employees[$emplID]["NetGross"][$currentNetGrossType]["result"] = $employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"];
						if($employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"]!="") $sqlValues[] = "(".$uid.",".$currentYear.",'".$adjustmentAccount[$currentNetGrossType]."',".$emplID.",0,0,0,0,0,".$employees[$emplID]["NetGross"][$currentNetGrossType]["proposedResult"].",0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0)";
					}
				}
			}
			if(count($sqlValues)!=0) $system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`) VALUES".implode(", ", $sqlValues), "payroll_calculate");

			//INSERT Net-Adjustment-Accounts
			$sqlValues = array();
			foreach($subsetNetAdj as $emplID) {
				foreach($adjAccountProcessed as $currentAP) {
					if(isset($employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"])) 
						if($employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"]!="") $sqlValues[] = "(".$uid.",".$currentYear.",'".$currentAP."',".$emplID.",0,0,0,0,0,".$employees[$emplID]["NetAdj"]["adjustmentAccounts"][(string)$currentAP]["proposedResult"].",0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0)";
				}
			}
			if(count($sqlValues)!=0) {
				$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_calculation`(`core_user_ID`,`payroll_year_ID`,`payroll_account_ID`,`payroll_employee_ID`,`input`,`surcharge`,`factor`,`quantity`,`rate`,`amount`,`output`,`having_limits`,`input_assignment`,`having_calculation`,`output_assignment`,`round_param`,`limits_aux_account_ID`,`process_status`,`process_order`,`sign`,`having_rounding`,`payroll_formula_ID`,`max_limit`,`min_limit`,`deduction`,`limits_calc_mode`) VALUES".implode(", ", $sqlValues), "payroll_calculate");
			}
		}
		//start calculation
		$system_database_manager->executeUpdate("Call payroll_prc_calculate(".$uid.",1,1)", "payroll_calculate");

		//LOA, die ausschliesslich 0.0 an die nachfolgenden LOA aebergeben, sollen geloescht werden
		$system_database_manager->executeUpdate("DELETE calc FROM `payroll_tmp_calculation` calc INNER JOIN (SELECT acc.`id`, acc.`payroll_year_ID`, MAX(IF(acclnk.`field_assignment`=3,1,0)) as outQty, MAX(IF(acclnk.`field_assignment`=4,1,0)) as outRate, MAX(IF(acclnk.`field_assignment`=5,1,0)) as outAmnt FROM `payroll_account` acc INNER JOIN `payroll_account_linker` acclnk ON acc.`id`=acclnk.`payroll_account_ID` AND acc.`payroll_year_ID`=acclnk.`payroll_year_ID` WHERE acc.`payroll_year_ID`=".$currentYear." AND acc.`mandatory`=1 GROUP BY acc.id, acc.`payroll_year_ID`) outlnk ON calc.`payroll_account_ID`=outlnk.`id` WHERE calc.`core_user_ID`=".$uid." AND (outlnk.`outQty`=1 OR outlnk.`outRate`=1 OR outlnk.`outAmnt`=1) AND (calc.`quantity`*outlnk.`outQty` + calc.`rate`*outlnk.`outRate` + calc.`amount`*outlnk.`outAmnt`)=0.0", "payroll_calculate");

		$debugTime1 = number_format((microtime(true) - $now), 3); //TODO: DELETE

		$now = microtime(true); //TODO: PROFILING START
		$system_database_manager->executeUpdate("DELETE calc FROM `payroll_calculation_current` calc INNER JOIN `payroll_tmp_change_mng` ids ON calc.`payroll_employee_ID`=ids.`numID` AND ids.`core_user_ID`=".$uid, "payroll_calculate");
		$system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_current`(`payroll_year_ID`,`payroll_period_ID`,`payroll_employee_ID`,`payroll_account_ID`,`quantity`,`rate`,`amount`,`allowable_workdays`,`label`,`code`,`position`) SELECT calc.`payroll_year_ID`,".$payrollPeriodID.",calc.`payroll_employee_ID`,calc.`payroll_account_ID`,calc.`quantity`,calc.`rate`,calc.`amount`,calc.`allowable_workdays_mt`,IF(empacc.`account_text` IS NULL,'',empacc.`account_text`),calc.`code`,0 FROM `payroll_tmp_calculation` calc LEFT JOIN `payroll_employee_account` empacc ON empacc.`id`=calc.`payroll_employee_account_ID` AND empacc.`account_text`!='' WHERE calc.`core_user_ID`=".$uid, "payroll_calculate");

		$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_calculation` WHERE `core_user_ID`=".$uid, "payroll_calculate");
		$system_database_manager->executeUpdate("Call payroll_prc_paymentsplit(".$uid.",".$payrollPeriodID.",".$majorPeriod.",".$minorPeriod.")", "payroll_calculate");
		$system_database_manager->executeUpdate("COMMIT", "payroll_calculate");
		$debugTime2 = number_format((microtime(true) - $now), 3);
		communication_interface::alert( "Berechnung erfolgreich beendet!\n" .
										"Performance:\n- - - - - - - - - - - - -\n" .
										$debugTime1." Sek. Berechnung\n" .
										$debugTime2." Sek. Speicherung\n" .
										number_format(($debugTime1+$debugTime2), 3)." Sek. TOTAL");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
   }
   
   
   
   
   
	public function calculationDataSave($rawData) {
		require_once('chkDate.php');
		$chkDate = new chkDate();
				
		$arrDeleteIDs = array();
		$arrAdd = array();
		$arrEdit = array();
		$arrErr = array();
		$arrAffectedEmployeeID = array();
		foreach($rawData as $currentEmployeeId=>$employeeRows) {
			$arrAffectedEmployeeID[] = $currentEmployeeId;
			foreach($employeeRows as $recID=>$row) {
				switch($row["action"]) {
				case 'add':
					if(!preg_match( '/^[0-9]{1,10}$/', $currentEmployeeId)) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"payroll_employee_id");
					if(!preg_match( '/^[134]{1,1}$/', $row["PayrollDataType"])) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"PayrollDataType");
					if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $row["payroll_account_ID"])) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"payroll_account_ID");

					if(isset($row["quantity"]) && $row["quantity"]!="") {
						if(!preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $row["quantity"]))
							$arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"quantity");
						$fldQuantity = $row["quantity"];
					}else $fldQuantity = 0;

					if(isset($row["rate"]) && $row["rate"]!="") {
						if(!preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $row["rate"]))
							$arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"rate");
						$fldRate = $row["rate"];
					}else $fldRate = 0;

					if(isset($row["amount"]) && $row["amount"]!="") {
						if(!preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $row["amount"]))
							$arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"amount");
						$fldAmount = $row["amount"];
					}else $fldAmount = 0;

					if(isset($row["dateFrom"]) && $row["dateFrom"]!="") {
						if($chkDate->chkDate($row["dateFrom"], 1, $retDate)) {
							$retDateFrom = $retDate;
						}else $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"dateFrom");
					}else $retDateFrom = "0000-00-00";

					if(isset($row["dateTo"]) && $row["dateTo"]!="") {
						if($chkDate->chkDate($row["dateTo"], 1, $retDate)) {
							$retDateTo = $retDate;
						}else $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"dateTo");
					}else $retDateTo = "0000-00-00";

					if(count($arrErr)>0) {
						$response["success"] = false;
						$response["errCode"] = 20;
						$response["errText"] = "error in ADD record";
						$response["errFields"] = $arrErr;
						return $response;
					}

					$arrAdd[] = "(".$currentEmployeeId.",'".addslashes($row["payroll_account_ID"])."',".$row["PayrollDataType"].",'".(isset($row["account_text"]) ? addslashes($row["account_text"]) : "")."',".$fldQuantity.",".$fldRate.",".$fldAmount.",0,0,0,'".(isset($row["CostCenter"]) ? addslashes($row["CostCenter"]) : "")."','".$retDateFrom."','".$retDateTo."',1,1,1)";
					break;
				case 'edit':
					if(!preg_match( '/^[0-9]{1,10}$/', $currentEmployeeId)) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"payroll_employee_id");
					if(!preg_match( '/^[0-9]{1,10}$/', $recID)) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"recid");
					if(!preg_match( '/^[134]{1,1}$/', $row["PayrollDataType"])) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"PayrollDataType");
					if(!preg_match( '/^[0-9a-zA-Z]{1,5}$/', $row["payroll_account_ID"])) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"payroll_account_ID");

					if(isset($row["quantity"]) && $row["quantity"]!="") {
						if(!preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $row["quantity"]))
							$arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"quantity");
						$fldQuantity = $row["quantity"];
					}else $fldQuantity = 0;

					if(isset($row["rate"]) && $row["rate"]!="") {
						if(!preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $row["rate"]))
							$arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"rate");
						$fldRate = $row["rate"];
					}else $fldRate = 0;

					if(isset($row["amount"]) && $row["amount"]!="") {
						if(!preg_match( '/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/', $row["amount"]))
							$arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"amount");
						$fldAmount = $row["amount"];
					}else $fldAmount = 0;

					if(isset($row["dateFrom"]) && $row["dateFrom"]!="") {
						if($chkDate->chkDate($row["dateFrom"], 1, $retDate)) {
							$retDateFrom = $retDate;
						}else $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"dateFrom");
					}else $retDateFrom = "0000-00-00";

					if(isset($row["dateTo"]) && $row["dateTo"]!="") {
						if($chkDate->chkDate($row["dateTo"], 1, $retDate)) {
							$retDateTo = $retDate;
						}else $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"dateTo");
					}else $retDateTo = "0000-00-00";

					if(count($arrErr)>0) {
						$response["success"] = false;
						$response["errCode"] = 30;
						$response["errText"] = "error in EDIT record";
						$response["errFields"] = $arrErr;
						return $response;
					}

					$arrEdit[] = "UPDATE `payroll_employee_account` SET `payroll_employee_ID`=$currentEmployeeId,`payroll_account_ID`='".addslashes($row["payroll_account_ID"])."',`PayrollDataType`=".$row["PayrollDataType"].",`account_text`='".(isset($row["account_text"]) ? addslashes($row["account_text"]) : "")."',`quantity`=".$fldQuantity.",`rate`=".$fldRate.",`amount`=".$fldAmount.",`CostCenter`='".(isset($row["CostCenter"]) ? addslashes($row["CostCenter"]) : "")."',`DateFrom`='".$retDateFrom."',`DateTo`='".$retDateTo."' WHERE id=".$recID;
					break;
				case 'delete':
					if(!preg_match( '/^[0-9]{1,10}$/', $recID)) $arrErr[] = array("payroll_employee_id"=>$currentEmployeeId, "recid"=>$recID, "field"=>"recid");

					if(count($arrErr)>0) {
						$response["success"] = false;
						$response["errCode"] = 40;
						$response["errText"] = "error in DELETE record";
						$response["errFields"] = $arrErr;
						return $response;
					}

					$arrDeleteIDs[] = $recID;
					break;
				}
			}
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_calculationDataSave");
		if(count($arrDeleteIDs)>0) {
			$sql = "DELETE FROM payroll_employee_account WHERE id IN (".implode(",",$arrDeleteIDs).")";
			$system_database_manager->executeUpdate($sql, "payroll_calculationDataSave");
		}
		if(count($arrAdd)>0) {
			$sql = "INSERT INTO `payroll_employee_account`(`payroll_employee_ID`,`payroll_account_ID`,`PayrollDataType`,`account_text`,`quantity`,`rate`,`amount`,`max_limit`,`min_limit`,`deduction`,`CostCenter`,`DateFrom`,`DateTo`,`major_period`,`minor_period`,`major_period_bonus`) VALUES".implode(",",$arrAdd);
			$system_database_manager->executeUpdate($sql, "payroll_calculationDataSave");
		}
		foreach($arrEdit as $sql) {
			$system_database_manager->executeUpdate($sql, "payroll_calculationDataSave");
		}
		$system_database_manager->executeUpdate("COMMIT", "payroll_calculationDataSave");

		//Betroffene MA neu berechnen
		$arrAffectedEmployeeID = array_unique($arrAffectedEmployeeID);
		if(count($arrAffectedEmployeeID)!=0) {
			$uid = session_control::getSessionInfo("id");
			$sqlVal = array();
			foreach($arrAffectedEmployeeID as $empId) $sqlVal[] = "(".$uid.",".$empId.")";
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_ID`=".$uid, "payroll_calculate");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_ID`,`numID`) VALUES".implode(",",$sqlVal), "payroll_calculate");
			require_once('payroll_calculate.php');
			$calcs = new payroll_BL_calculate();
			$calcs->calculate(false); //$calculateAll=false (calculate only selected employees)
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
	
	
	
	
	
	
	
	

	public function getCalculationData($param) {
		if(!isset($param["columns"])) $param["columns"] = array("*");
		if(!isset($param["prepend_id"]) || $param["columns"] == "*") $param["prepend_id"] = false;
		$queryFilter = "";
		if(isset($param["query_filter"])) {
			$arrFilter = array();
			foreach($param["query_filter"] as $singleCriterium) {
				if(count($singleCriterium)==2) $arrFilter[] = $singleCriterium[0]."=".$singleCriterium[1];
			}
			$queryFilter = " WHERE ".implode(" AND ",$arrFilter);
		}

		$columns = implode(",", $param["columns"]);
		if($param["prepend_id"]) $columns = "id,".$columns;

		$system_database_manager = system_database_manager::getInstance();
//communication_interface::alert("SELECT ".$columns." FROM payroll_employee");
//return;
		$result = $system_database_manager->executeQuery("SELECT ".$columns." FROM payroll_employee_account".$queryFilter, "payroll_getCalculationData");
//communication_interface::alert("SELECT ".$columns." FROM payroll_employee_account".$queryFilter);

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

   
   
   
   
   
   
}
?>
