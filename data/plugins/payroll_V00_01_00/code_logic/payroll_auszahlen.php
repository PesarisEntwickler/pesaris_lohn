<?php

class auszahlen {
	
	public function getActualPeriod() {
		$system_database_manager = system_database_manager::getInstance();
		//communication_interface::alert("");
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_calculation_current LIMIT 1,1;", "");		
		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;//Verwendungs-Beispiel: $response["data"][0]['payroll_period_ID'];
	}

	public function getActualPeriodenDaten($periodenID) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_period WHERE id=".$periodenID." ; ", "");		
		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 102;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
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

	public function resetActualPeriodenAuszahlFlag($periodID) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("
			UPDATE payroll_period_employee 
			SET isFullPayed='N' 
			WHERE payroll_period_ID=".$periodID." 			
			;"); 
		$nr = $this->deleteFilesFromActualPeriod();
		communication_interface::alert("Auszahlungssdaten (DTA) der Periode zurueckgesetzt\n".$nr." Dateien geloescht (DTA, PDF)");
		return true;
	}
	
	public function getActualPeriodenDir() {
		global $aafwConfig;
		$payroll_calculation_current = blFunctionCall('payroll.auszahlen.getActualPeriod');
		$payroll_period = blFunctionCall('payroll.auszahlen.getActualPeriodenDaten', $payroll_calculation_current["data"][0]['payroll_period_ID']);
		$data["period"] = PERIODENPREFIX.$payroll_period["data"][0]['payroll_year_ID']."-".substr("00".$payroll_period["data"][0]['major_period'], -2);
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

	public function updatePeriodenAuszahlFlag($periodID, $MA, $flag) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("
			UPDATE payroll_period_employee 
			SET isFullPayed='".$flag."' 
			WHERE payroll_period_ID=".$periodID." 			
			AND   payroll_employee_ID=".$MA."
			;");
		//communication_interface::alert("updated $periodID, $MA, $flag");
		return true;
	}
	
	public function getAuszahlMitarbeiteranzahl() {
		$anzMaArr = $this->getMitarbeiterZurAuszahlung("AND `isFullPayed`='N' AND payroll_account_ID = 8000  AND amount <> 0");
		return $anzMaArr['count'];
	}

	public function getMitarbeiterZurAuszahlung($erweiterteWhereKlausel) {
		$sql="
			SELECT * FROM 
			 lohndev.payroll_calculation_current as calc
			,lohndev.payroll_employee as emp
			,lohndev.payroll_employment_period as emprd
			,lohndev.payroll_period_employee as prd
			WHERE 
			 emp.id = calc.payroll_employee_ID
			 AND emp.id = emprd.payroll_employee_ID
			 AND emp.id = prd.payroll_employee_ID
			".$erweiterteWhereKlausel."
			GROUP BY emp.id
			ORDER BY emp.id, emprd.DateTo
		;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 105;
			$retval["errText"]  = "Keine Daten gefunden.";
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["count"]    = count($result);
			$retval["data"] 	= $result;
		}
		return $retval;
		
	}
	
	public function getEmployeeData($idListe) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT FilterCriteria FROM payroll_empl_filter WHERE id IN (".$idListe.") ORDER BY FilterPriority;");
		
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 106;
			$retval["errText"]  = "Keine Daten gefunden.";
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["data"] 	= $result;
		}
		return $retval;
	}

	public function getBeneficiaryAddress($employeeID) {
		$retArray = array();
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_destination = $system_database_manager->executeQuery("				
			SELECT * FROM
			 payroll_bank_destination
			WHERE id = ".$employeeID.
			";");
		$idx = 0;	 				
		foreach ( $result_bank_destination as $row ) {
			$bene1 = $row['beneficiary1_line1'];
			$bene2 = $row['beneficiary1_line2'];
			$bene3 = $row['beneficiary1_line3'];
			$bene4 = $row['beneficiary1_line4'];
			$retArray[$idx]['bank_account'] = strtoupper( $this->replaceUmlaute( $row['bank_account']) ) ;
			$retArray[$idx]['beneAddress1'] = strtoupper( $this->replaceUmlaute( $bene1) ) ;
			$retArray[$idx]['beneAddress2'] = strtoupper( $this->replaceUmlaute( $bene2) ) ;
			$retArray[$idx]['beneAddress3'] = strtoupper( $this->replaceUmlaute( $bene3) ) ;
			$retArray[$idx]['beneAddress4'] = strtoupper( $this->replaceUmlaute( $bene4) ) ;
			//			communication_interface::alert(
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
	
	function replaceUmlaute($uebergabeWort) {
		$umlaute = array("ä","ö","ü","Ä","Ö","Ü","ß","à","â","á","é","è","Ç","ç","ñ","Ñ","ó","õ","ú");
		$ersatz = array("ae","oe","ue","Ae","Oe","Ue","ss","a","a","a","e","e","C","c","n","N","o","o","u");
		return str_replace ($umlaute,$ersatz,$uebergabeWort);
	}
	
	
}
?>
