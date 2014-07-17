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
	
	public function getCompany($companyID) {
		$system_database_manager = system_database_manager::getInstance();
		$result_company = $system_database_manager->executeQuery("
			SELECT * FROM  
			payroll_company 
			WHERE id = ".$companyID.";");
		return array("short"=>$result_company[0]["company_shortname"]
					, "name"=>$result_company[0]["HR-RC-Name"]
					, "str" =>$result_company[0]["Street"]
					, "zip" =>$result_company[0]["ZIP-Code"]
					, "city"=>$result_company[0]["City"]
					, "land"=>$result_company[0]["Country"]
					, "uid" =>$result_company[0]["UID-EHRA"]
					, "bur" =>$result_company[0]["BUR-REE-Number"]
					);		 
	}
	
	public function getZahlstelle($ZahlstellenID) {
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_source = $system_database_manager->executeQuery("" .
				"SELECT * FROM  " .
				"payroll_bank_source " .
				"WHERE id = ".$ZahlstellenID.";");
		
		return array("IBAN"    =>$result_bank_source[0]["bank_source_IBAN"]
					, "desc"   =>$result_bank_source[0]["description"]
					, "type"   =>$result_bank_source[0]["source_type"]
					, "line1"  =>$result_bank_source[0]["bank_source_desc1"]
					, "line2"  =>$result_bank_source[0]["bank_source_desc2"]
					, "line3"  =>$result_bank_source[0]["bank_source_desc3"]
					, "line4"  =>$result_bank_source[0]["bank_source_desc4"]
					, "company"=>$result_bank_source[0]["payroll_company_ID"]
					, "carrier"=>$result_bank_source[0]["bank_source_carrier"]
					, "currency"=>$result_bank_source[0]["bank_source_currency_code"]
					);		 
	}
	public function getZahlstellenDaten() {
		$system_database_manager = system_database_manager::getInstance();
		$resBankSrc = $system_database_manager->executeQuery("
			SELECT *
			, payroll_bank_source.id AS zsID 
			FROM
			  payroll_bank_source
			, payroll_bank_source_type 
			, payroll_company
			WHERE
				payroll_company.id = payroll_bank_source.payroll_company_ID
			AND
			  payroll_bank_source.source_type = payroll_bank_source_type.type_id
			ORDER BY payroll_bank_source.id
				;", "payroll_getZahlstellenListe"
				); 
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
		if (strlen($MAlist)<1) {
			$MAlist = "0";
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
		communication_interface::alert("- Auszahlungssdaten der Periode sind zurueckgesetzt.\n- Die dta- und pdf-Dateien sind geloescht.");
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
		$employeeList = $this->getMitarbeiterZurAuszahlung("8000", "amount > 0.001", "isFullPayed <> 'Y'","");
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
				AND prdempl.".$isFullPayed."
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
	
	public function getEmployeesCurrentPeriod($accountList, $emplIdList) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT  *
			FROM
			  lohndev.payroll_calculation_current 
			, lohndev.payroll_employee			
			WHERE payroll_employee_ID  IN (".$emplIdList.")
				AND payroll_account_ID IN (".$accountList.")
				AND payroll_employee.id = payroll_calculation_current.payroll_employee_ID
		    ; ");
		return $result;
	}

	public function getEmployeesCurrentPeriodAmount($accountList, $emplIdList) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT amount FROM payroll_calculation_current 
			WHERE payroll_employee_ID IN (".$emplIdList.")
			AND   payroll_account_ID IN (".$accountList.")
		    ; ");
		$amount = "0.00";
		if (count($result)>0) {
			$arrAmt = explode(".", $result[0]['amount']);
			$amount = $arrAmt[0].",".substr(rtrim($arrAmt[1], "0")."00", 0,2);	//formatiert auf 123.45			
		}
		return $amount;
	}

	public function getBeneficiaryAddress($employeeID) {
		$retArray = array();
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_destination = $system_database_manager->executeQuery("				
			SELECT * FROM
			 payroll_bank_destination
			WHERE payroll_employee_ID = ".$employeeID."
			ORDER BY destination_type 
			;");
		$idx = 0;	 				
		$retArray['bank_account'] = "";
		$retArray['beneAddress1'] = "";
		$retArray['beneAddress2'] = "";
		$retArray['beneAddress3'] = "";
		$retArray['beneAddress4'] = "";
		$retArray['beneBankDescription'] = "";
		$retArray['beneBank1'] = "";
		$retArray['beneBank2'] = "";
		$retArray['beneBank3'] = "";
		$retArray['beneBank4'] = "";
		if ( count($result_bank_destination) > 0 ) {
			$retArray['bank_account'] = trim( $result_bank_destination[0]['bank_account'] ) ;
			$retArray['beneAddress1'] = $result_bank_destination[0]['beneficiary1_line1'];
			$retArray['beneAddress2'] = $result_bank_destination[0]['beneficiary1_line2'];
			$retArray['beneAddress3'] = $result_bank_destination[0]['beneficiary1_line3'];
			$retArray['beneAddress4'] = $result_bank_destination[0]['beneficiary1_line4'];
			$retArray['beneBankDescription'] = $result_bank_destination[0]['description'];
			$retArray['beneBank1'] = $result_bank_destination[0]['beneficiary_bank_line1'];
			$retArray['beneBank2'] = $result_bank_destination[0]['beneficiary_bank_line2'];
			$retArray['beneBank3'] = $result_bank_destination[0]['beneficiary_bank_line3'];
			$retArray['beneBank4'] = $result_bank_destination[0]['beneficiary_bank_line4'];
//			communication_interface::alert(
//							"employeeID:".$employeeID."  idx:".$idx
//							."\n bank_account:".$retArray[$idx]['bank_account']
//							."\n beneAddress1:".$retArray[$idx]['beneAddress1']
//							."\n beneAddress2:".$retArray[$idx]['beneAddress2']
//							."\n beneAddress3:".$retArray[$idx]['beneAddress3']
//							."\n beneAddress4:".$retArray[$idx]['beneAddress4']
//							);
		}
		return $retArray;
	}
	
	public function replaceUmlaute($uebergabeWort) {
		$umlaute = array("'","ä","ö","ü","Ä","Ö","Ü","ß","à","â","á","é","è","Ç","ç","ñ","Ñ","ó","õ","ú");
		$ersatz = array(" ","ae","oe","ue","Ae","Oe","Ue","ss","a","a","a","e","e","C","c","n","N","o","o","u");
		$uebergabeWort = str_replace($umlaute,$ersatz,$uebergabeWort);
		
		$umlaute = array("&auml;","&ouml;","&uuml;","&Auml;","&Ouml;","&Uuml;","&eacute;","&Eacute;","&ATILDE;&FRAC14;","&Atilde;","&atilde;","&frac14;","Ã¼","Ã¶","¶","Ã©");
		$ersatz = array( "ae"    ,"oe"    ,"ue"    ,"AE"    ,"OE"    ,"UE"    ,"e"       ,"E"      ,"UE"              ,"UE"       ,"ue"      ,""        ,"ue","o" ,"o","e");
		return str_replace($umlaute,$ersatz,$uebergabeWort);
	}
	
	
}
?>
