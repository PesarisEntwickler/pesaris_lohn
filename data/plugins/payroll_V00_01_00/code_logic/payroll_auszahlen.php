<?php

class auszahlen {
	
	public function getActualPeriodID() {
		$system_database_manager = system_database_manager::getInstance();
		//communication_interface::alert("");
		$result = $system_database_manager->executeQuery(
			"SELECT * FROM payroll_calculation_current LIMIT 1,1 ; "
			, "");		
		return $result[0]['payroll_period_ID'];
	}

	public function getActualPeriodenDaten($periodenID) {
		//communication_interface::alert("getActualPeriodenDaten($periodenID)");
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery(
			"SELECT * FROM payroll_period WHERE id=".$periodenID."  LIMIT 1 ; "
			, "");		
		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 102;
			$response["errText"] = "no data found";
			$response["data"] = "";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}
		return $response;
	}
	
	public function getActualPeriodName() {
		$periodenID = $this->getActualPeriodID();
		$periodenDaten = $this->getActualPeriodenDaten($periodenID);
		//communication_interface::alert($periodenDaten['data'][0]['payroll_year_ID']);
		$response = PERIODENPREFIX.$periodenDaten['data']['payroll_year_ID']."-".substr("00".$periodenDaten['data']['major_period'], -2);		
		return $response;
	}
	
	public function getZahlstellenDaten($param) {
		$companyID = session_control::getSessionInfo("id");
		$companyID = 600;//TODO hm !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$system_database_manager = system_database_manager::getInstance();
		$resBankSrc = $system_database_manager->executeQuery("SELECT * FROM payroll_bank_source, payroll_bank_source_type WHERE payroll_bank_source.source_type = payroll_bank_source_type.type_id AND payroll_company_ID = ".$companyID, "payroll_getZahlstellenListe"); 
		if(count($resBankSrc) != 0) {
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $resBankSrc;
		}else{
			$response["success"] = false;
			$response["errCode"] = 103;
			$response["errText"] = "no data found";
		}
		return $response;
		
	}

	public function updatePeriodenAuszahlFlag($periodID, $MAlist, $flag) {
		if (substr($MAlist, 0, 1)==",") {
			$MAlist = substr($MAlist, 1);
		}
		$sql = " 
				UPDATE payroll_period_employee 
				SET    isFullPayed='".$flag."' 
				WHERE  payroll_period_ID=".$periodID." 			
				AND    payroll_employee_ID IN (".$MAlist.")
				;"
				;
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate($sql);
//		communication_interface::alert("updated $periodID, $MA, $flag \n".$sql);
		return true;
	}
	
	public function resetActualPeriodenAuszahlFlags($periodID) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("
			UPDATE payroll_period_employee 
			SET isFullPayed='N' 
			WHERE payroll_period_ID=".$periodID." 			
			;"); 
		$nr = $this->deleteFilesFromActualPeriod();
		communication_interface::alert("Auszahlungssdaten (DTA) der Periode zurueckgesetzt\n".$nr." Dateien geloescht (DTA, TXT)");
		return true;
	}
	
	public function getActualPeriodenDir() {
		global $aafwConfig;
		$data["period"] = $this->getActualPeriodName();
		$aktuellePeriode4ListingDir = "/".AUSZAHLDIR."/".$data["period"];
		$absDataPath = $aafwConfig["paths"]["plugin"]["customerDir"].session_control::getSessionInfo("db_name")."/".$aktuellePeriode4ListingDir."/";
		return array("relPath" => $aktuellePeriode4ListingDir, "absPath" => $absDataPath);			
	}
	
	public function deleteFilesFromActualPeriod(){
		$dir = $this->getActualPeriodenDir();
		$fm = new file_manager();
		$filelist = $fm->customerSpace()->setPath($dir["relPath"])->listDir(1);  
		foreach($filelist as $row) {
			//communication_interface::alert("file=".$row);
			unlink($dir["absPath"].$row);
		}
		return count($filelist);
	}

	public function getAuszahlMitarbeiteranzahl() {
		$employeeList = $this->getMitarbeiterZurAuszahlung("8000", "amount <> 0", "isFullPayed <> 'Y'","");
		$effectedEmployeeList = "";
		if ($employeeList["count"]>0) {
			foreach ( $employeeList['data'] as $row ){
				$effectedEmployeeList .= ",".$row['payroll_employee_ID'];
			}
			$effectedEmployeeList = substr($effectedEmployeeList, 1);//erstes "," wegmachen
			$employeeList = $this->getMitarbeiterAddress($effectedEmployeeList);			
		}	
		return $employeeList['count'];
	}
	
	public function getMitarbeiterAddress($employeeIDList) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery(
		"SELECT * FROM payroll_employee WHERE id IN (".$employeeIDList.");"
		);
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 105;
			$retval["errText"]  = "Keine Daten gefunden.";
			$retval["count"]    = 0;
			$retval["data"] 	= "";
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["count"]    = count($result);
			$retval["data"] 	= $result;
		}
		return $retval;
	}

	public function getMitarbeiterZurAuszahlung($accountList, $amountClause, $isFullPayed, $erweiterteWhereKlausel) {
//		$sql="
//				SELECT * FROM payroll_employee AS emp
//				INNER JOIN payroll_period_employee emprd 
//					ON emprd.payroll_employee_ID=emp.id 
//					AND emprd.processing!=0 
//				INNER JOIN payroll_period prd  
//				WHERE prd.id=emprd.payroll_period_ID 
//					AND prd.finalized=0  
//					AND prd.locked=0
//					AND emp.id IN (
//									SELECT payroll_employee_ID 
//									FROM payroll_calculation_current 
//									WHERE payroll_account_ID IN (".$accountList.") 
//										AND ".$amountClause."
//									)
//					AND emprd.".$isFullPayed."
//					".$erweiterteWhereKlausel."
//				;";

	//Die ganze Query ist (MIR) zu komplex um die IDs nur separat zurückzubringen. 
	//Man muss nachher noch mals eine einzelne Abfrage machen mit der WHERE emp.id IN ()
	//Damit werden die Doppelten ignoriert
		$sql="
			SELECT * FROM payroll_employee AS emp
			  INNER JOIN payroll_period_employee AS prdempl 
				ON prdempl.payroll_employee_ID=emp.id 
				AND prdempl.processing!=0 
			  JOIN payroll_employment_period AS emprd 
				ON emp.id = emprd.payroll_employee_ID 
			  INNER JOIN payroll_period AS prd  
			WHERE prd.id=prdempl.payroll_period_ID 
				AND prd.finalized=0  
				AND prd.locked=0
				AND emp.id 
					IN (
						SELECT payroll_employee_ID 
						FROM lohndev.payroll_calculation_current 
						WHERE payroll_account_ID 
							IN (".$accountList.") 
							AND ".$amountClause."
					)
				AND prdempl.isFullPayed <> 'Y'
				".$erweiterteWhereKlausel."
				;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		//communication_interface::alert(count($result)."\n".$sql);
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 106;
			$retval["errText"]  = "Keine Daten gefunden.";
			$retval["count"]    = 0;
			$retval["data"] 	= "";
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["count"]    = count($result);
			$retval["data"] 	= $result;
		}
		return $retval;
		
	}
	
	public function getEmployeeFilters($Personenkreis) {
		$emplFilter = "";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery(
				"SELECT FilterCriteria FROM payroll_empl_filter " .
				"WHERE id IN (" .$Personenkreis. ") " .
				"ORDER BY FilterPriority " .
				";");
		foreach ( $result as $row ) {
			$emplFilter .= " AND " . $row['FilterCriteria'];           
		}  
		return $emplFilter;	
	}
	
	public function getEmployeesCurrentPeriodAmount($accountList, $emplId) {
		$amount = "0.00";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT amount FROM payroll_calculation_current 
			WHERE payroll_employee_ID= ".$emplId."
			AND   payroll_account_ID IN (".$accountList.")
		    ; ");
		if (count($result)>0) {
			$arrAmt = explode(".", $result[0]['amount']);
			$amount = $arrAmt[0].",".substr(rtrim($arrAmt[1], "0")."00", 0,2);				
		}
		return $amount;
	}

	public function getBeneficiaryAddress($employeeID) {
		$retArray = array();
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_destination = $system_database_manager->executeQuery("				
			SELECT * FROM
			 payroll_bank_destination
			WHERE id = ".$employeeID."
			ORDER BY destination_type 
			;");
		$idx = 0;	 				
		foreach ( $result_bank_destination as $row ) {
			$retArray[$idx]['bank_account'] =  trim( $row['bank_account'] ) ;
			$retArray[$idx]['beneAddress1'] =  $row['beneficiary1_line1'];
			$retArray[$idx]['beneAddress2'] = $row['beneficiary1_line2'];
			$retArray[$idx]['beneAddress3'] = $row['beneficiary1_line3'];
			$retArray[$idx]['beneAddress4'] = $row['beneficiary1_line4'];
			$retArray[$idx]['beneBankDescription'] = $row['description'];
			$retArray[$idx]['beneBank1'] = $row['beneficiary_bank_line1'];
			$retArray[$idx]['beneBank2'] = $row['beneficiary_bank_line2'];
			$retArray[$idx]['beneBank3'] = $row['beneficiary_bank_line3'];
			$retArray[$idx]['beneBank4'] = $row['beneficiary_bank_line4'];
			//communication_interface::alert(
			//				"employeeID:".$employeeID."  idx:".$idx
			//				."\n bank_account:".$retArray[$idx]['bank_account']
			//				."\n beneAddress1:".$retArray[$idx]['beneAddress1']
			//				."\n beneAddress2:".$retArray[$idx]['beneAddress2']
			//				."\n beneAddress3:".$retArray[$idx]['beneAddress3']
			//				."\n beneAddress4:".$retArray[$idx]['beneAddress4']
			//				);
			$idx++;
		}
		return $retArray;
	}
	
	public function replaceUmlaute($uebergabeWort) {
		$umlaute = array("'","ä","ö","ü","Ä","Ö","Ü","ß","à","â","á","é","è","Ç","ç","ñ","Ñ","ó","õ","ú","&auml;", "&ouml;", "&uuml;","&Auml;","&Ouml;","&Uuml;","&eacute;","&Eacute;");
		$ersatz = array(" ","ae","oe","ue","Ae","Oe","Ue","ss","a","a","a","e","e","C","c","n","N","o","o","u","Ae","Oe","Ue","e","E");
		return str_replace($umlaute,$ersatz,$uebergabeWort);
	}
	
	
}
?>
