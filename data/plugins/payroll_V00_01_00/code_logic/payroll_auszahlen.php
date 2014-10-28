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
		//communication_interface::alert("getZahlstelle($ZahlstellenID)");
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_source = $system_database_manager->executeQuery("" .
				"SELECT * FROM  " .
				"payroll_bank_source " .
				"WHERE id = ".$ZahlstellenID.";");
		if (count($result_bank_source) > 0) {
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
		} else {
			return array();
		}
	}
	public function getZahlstellenListe($companyID) {
		$companyClause = "";
		if (isset($companyID)) {
			if (intval($companyID)> 0) {
				$companyClause = " AND payroll_company.id = ".$companyID;
			}
		}
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
				".$companyClause."
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

	public function updatePeriodenAuszahlFlag($periodID, $sqlIN, $MAlist, $flag) {
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
				AND    payroll_employee_ID ".$sqlIN." (".$MAlist.")
				;";
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
		//communication_interface::alert("- Auszahlungssdaten der Periode sind zurueckgesetzt.\n- Die dta- und pdf-Dateien sind geloescht.");
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
		if (count($filelist)>0){
			foreach($filelist as $row) {
				//communication_interface::alert("file=".$row);
				unlink($dir["absPath"].$row);
			}
		}  
		//communication_interface::alert(print_r($filelist, true));
		return count($filelist);
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

	public function getAuszahlMitarbeiteranzahl() {
//		$employeeList = $this->getMitarbeiterZurAuszahlung("8000", "amount > 0.001", "isFullPayed <> 'Y'","");
//		$effArr = array();
//		$auszahlMitarbeiteranzahl = 0;
//		if ($employeeList["count"]>0) {
//			foreach ( $employeeList['data'] as $row ){//füll Array mit allen betroffenen IDs
//				$effArr[] = $row['payroll_employee_ID'];
//			}
//			$arr = array_unique( $effArr );//mach die IDs eindeutig/unique
//			$auszahlMitarbeiteranzahl = count($arr);			
//		}	
		$sql="SELECT count(amount_available) AS anzahl FROM payroll_auszahlen_tracking WHERE amount_available > 0.00001 ;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		$auszahlMitarbeiteranzahl = $result[0]["anzahl"];
		return $auszahlMitarbeiteranzahl;
	}
	
	public function getMitarbeiterZurAuszahlung($accountList, $amountClause, $isFullPayed, $erweiterteWhereKlausel) {
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
						FROM payroll_calculation_current 
						WHERE payroll_account_ID 
							IN (".$accountList.") 
							AND ".$amountClause."
					)
				AND prdempl.".$isFullPayed."
				".$erweiterteWhereKlausel."
				;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		
//		communication_interface::alert(count($result)."\n".$sql);
//		return;
		
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
	
	public function getEmployeeDataCurrentPeriod($accountList, $emplIdList) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT  *
			FROM
			  payroll_calculation_current   AS C
			, payroll_employee				AS E	
			WHERE C.payroll_employee_ID  IN (".$emplIdList.")
			  AND C.payroll_account_ID IN (".$accountList.")
			  AND E.id = C.payroll_employee_ID
		    ; ");
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 108;
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

	public function getCalculationCurrentPeriodEmployeeList($accountList, $amountClause) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
					SELECT * FROM 
					  payroll_calculation_current AS C
					, payroll_period_employee     AS E
					WHERE C.payroll_employee_ID = E.payroll_employee_ID
					  AND C.payroll_period_ID   = E.payroll_period_ID
					  AND C.payroll_account_ID IN (".$accountList.")
					  AND C.".$amountClause." ;"
				);
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 109;
			$retval["errText"]  = "Keine Daten gefunden.";
			$retval["count"]    = 0;
			$retval["data"] 	= array();
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["count"]    = count($result);
			$retval["data"] 	= $result;
		}
		return $retval;		
	}

	function getEployeesWithoutIBAN(){
		$system_database_manager = system_database_manager::getInstance();
		$sql = "
			DELETE FROM payroll_bank_destination
			WHERE destination_type <> 3
			AND bank_account = ''
			AND description = ''
			AND beneficiary1_line1 = ''
			AND beneficiary_bank_line1 = ''
			AND beneficiary_bank_line3 = ''
			;";
		$result_dest_emp = $system_database_manager->executeUpdate($sql);				
		
		$sql = "
			SELECT * FROM
			  payroll_bank_destination
			, payroll_employee
			WHERE	payroll_bank_destination.payroll_employee_ID = payroll_employee.id
			AND		payroll_bank_destination.destination_type <> 3	
			AND		payroll_bank_destination.bank_account = '' 
			;";
		$result_dest_emp = $system_database_manager->executeQuery($sql);
		$c = "";	 				
		foreach ( $result_dest_emp as $row ) {
			$c .= $row['EmployeeNumber'].", ";
			$c .= $row['Firstname']." ";
			$c .= $row['Lastname'].", ";
			$c .= $row['City'].CRLF;
		}
		return $c;
	}

	function getPaymentSplit($employeeID, $bankDestID, $ZahlstellenID, $last_processing_order, $limit){
		$ANDwhereBankDestIdClause = "";
		if (intval($bankDestID) > 0) {
			$ANDwhereBankDestIdClause = " AND payroll_bank_destination_ID = ".$bankDestID;
		}
		$ANDwhereBankSourceIdClause = "";
		if (intval($bankDestID) > 0) {
			$ANDwhereBankSourceIdClause = " AND payroll_bank_source_ID = ".$ZahlstellenID;
		}
		$ANDwhereProcessingOrderClause = "";
		if (intval($last_processing_order) >= -1) {
			$ANDwhereProcessingOrderClause = " AND processing_order > ".$last_processing_order;
		}
		$sql = "				
			SELECT *  FROM
			          payroll_payment_split
			WHERE	  payroll_employee_ID = ".$employeeID.
			$ANDwhereBankDestIdClause.
			$ANDwhereBankSourceIdClause.
			$ANDwhereProcessingOrderClause."
			ORDER BY  processing_order
			".$limit."
		;";
				
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		$retval["sql"] 	= $sql;
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 109;
			$retval["errText"]  = "Keine Daten gefunden.";
			$retval["count"]    = 0;
			$retval["data"] 	= array();
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["count"]    = count($result);
			$retval["data"] 	= $result;
		}
		return $retval;		
	}

	public function getStandardDestinationBankAccount($employeeID) {
		$retArray = array();
		$retArray = $this->getDestinationBankAccount($employeeID, 0, "Y");
		return $retArray;
	}
	
	public function getDestinationBankAccount($employeeID, $bankDestID, $standardBank) {
		$retArray = array();
		$addOnClause = "";
		$bankDestID = intval($bankDestID);
		if ($bankDestID > 0) {
			$addOnClause = " AND id = ".$bankDestID;
		}
		if (strlen(trim($standardBank)) > 0) {
			if ($standardBank == "Y") {
				$addOnClause .= " AND is_standard_bank = 'Y' ";
			} else {
				$addOnClause .= " AND is_standard_bank <> 'Y' ";
			}
		}
		$sql = "				
			SELECT * FROM
			 payroll_bank_destination
			WHERE payroll_employee_ID = ".$employeeID."
			".$addOnClause." 
			ORDER BY destination_type, id 
			LIMIT 1 
			;";
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_destination = $system_database_manager->executeQuery($sql); 				
		$retArray['bank_id'] = "";
		$retArray['bank_account'] = "";
		$retArray['beneAddress1'] = "";
		$retArray['beneAddress2'] = "";
		$retArray['beneAddress3'] = "";
		$retArray['beneAddress4'] = "";
		$retArray['beneBankDescription'] = "";
		$retArray['beneBankSWIFT'] = "";
		$retArray['beneBank1'] = "";
		$retArray['beneBank2'] = "";
		$retArray['beneBank3'] = "";
		$retArray['beneBank4'] = "";
		$retArray['beneEndbeguenst1'] = "";
		$retArray['beneEndbeguenst2'] = "";
		$retArray['beneEndbeguenst3'] = "";
		$retArray['beneEndbeguenst4'] = "";
		$retArray['bankpostcash'] = "";
		$retArray['nonstandard_banksourcezahlstelle'] = "";
		$retArray['spesenregelung'] = "";
		$retArray['currency'] = "";
		$retArray['splitMode'] = 1;
		$retArray['success'] = false;
		if ( count($result_bank_destination) > 0 ) {
			$retArray['success'] = true;
			$retArray['bank_id'] = trim( $result_bank_destination[0]['id'] ) ;
			$retArray['bank_account'] = trim( $result_bank_destination[0]['bank_account'] ) ;
			$retArray['beneAddress1'] = $result_bank_destination[0]['beneficiary1_line1'];
			$retArray['beneAddress2'] = $result_bank_destination[0]['beneficiary1_line2'];
			$retArray['beneAddress3'] = $result_bank_destination[0]['beneficiary1_line3'];
			$retArray['beneAddress4'] = $result_bank_destination[0]['beneficiary1_line4'];
			$retArray['beneBankDescription'] = $result_bank_destination[0]['description'];
			$retArray['beneBankSWIFT'] = $result_bank_destination[0]['bank_swift'];
			$retArray['beneBank1'] = $result_bank_destination[0]['beneficiary_bank_line1'];
			$retArray['beneBank2'] = $result_bank_destination[0]['beneficiary_bank_line2'];
			$retArray['beneBank3'] = $result_bank_destination[0]['beneficiary_bank_line3'];
			$retArray['beneBank4'] = $result_bank_destination[0]['beneficiary_bank_line4'];
			$retArray['beneEndbeguenst1'] = $result_bank_destination[0]['beneficiary2_line1'];//IBAN
			$retArray['beneEndbeguenst2'] = $result_bank_destination[0]['beneficiary2_line2'];//Name
			$retArray['beneEndbeguenst3'] = $result_bank_destination[0]['beneficiary2_line3']." ".$result_bank_destination[0]['beneficiary2_line4'];
			$retArray['beneEndbeguenst4'] = $result_bank_destination[0]['beneficiary2_line5'];//Ort
			$retArray['spesenregelung']   = $result_bank_destination[0]['expense'];
			$retArray['nonstandard_banksourcezahlstelle'] = $result_bank_destination[0]['nonstandard_banksourcezahlstelle'];
			$retArray['currency'] = $result_bank_destination[0]['bank_dest_currency'];
			$retArray['splitMode'] = $result_bank_destination[0]['destination_type'];
			switch ( $result_bank_destination[0]['destination_type'] ) {
				case 1: $retArray['bankpostcash'] = "BANK"; break;
				case 2: $retArray['bankpostcash'] = "POST"; break;
				case 3: $retArray['bankpostcash'] = "CASH"; break;
			}
		}
		return $retArray;
	}
	
	public function getAllDestinationBankAccounts($employeeID) {
		$retArray = array();
		$system_database_manager = system_database_manager::getInstance();
		$result_bank_destination = $system_database_manager->executeQuery("				
			SELECT * FROM
			 payroll_bank_destination
			WHERE payroll_employee_ID = ".$employeeID."
			ORDER BY destination_type, id 
			;");
		$idx = 0;	 				
		$retArray[$idx]['bank_account'] = "";
		$retArray[$idx]['beneAddress1'] = "";
		$retArray[$idx]['beneAddress2'] = "";
		$retArray[$idx]['beneAddress3'] = "";
		$retArray[$idx]['beneAddress4'] = "";
		$retArray[$idx]['beneBankDescription'] = "";
		$retArray[$idx]['beneBank1'] = "";
		$retArray[$idx]['beneBank2'] = "";
		$retArray[$idx]['beneBank3'] = "";
		$retArray[$idx]['beneBank4'] = "";
		$retArray[$idx]['nonstandard_banksourcezahlstelle'] = "";
		if ( count($result_bank_destination) > 0 ) {
			foreach ( $result_bank_destination as $res ) {       
				$retArray[$idx]['bank_account'] = trim( $res[$idx]['bank_account'] ) ;
				$retArray[$idx]['beneAddress1'] = $res[$idx]['beneficiary1_line1'];
				$retArray[$idx]['beneAddress2'] = $res[$idx]['beneficiary1_line2'];
				$retArray[$idx]['beneAddress3'] = $res[$idx]['beneficiary1_line3'];
				$retArray[$idx]['beneAddress4'] = $res[$idx]['beneficiary1_line4'];
				$retArray[$idx]['beneBankDescription'] = $res[$idx]['description'];
				$retArray[$idx]['beneBank1'] = $res[$idx]['beneficiary_bank_line1'];
				$retArray[$idx]['beneBank2'] = $res[$idx]['beneficiary_bank_line2'];
				$retArray[$idx]['beneBank3'] = $res[$idx]['beneficiary_bank_line3'];
				$retArray[$idx]['beneBank4'] = $res[$idx]['beneficiary_bank_line4'];
				$retArray[$idx]['nonstandard_banksourcezahlstelle'] = $res[$idx]['nonstandard_banksourcezahlstelle'];
				$idx++;
			}
		}
		return $retArray;
	}
	
	public function calcSplitAmount($splitMode, $splitValue, $availAmt, $splitPayCurrency, $zahlstellenWaehrung, $employee_ID ,$account_ID) {

		$payAmount = 0;
		$wechselkursFremdZuCHF = 1;
		//$splitValue        : ist der Betrag, der die Splitt-Tabelle für den hier aktuellen Split hergibt
		//                     dieser ist aber unterschiedlich je nach split_mode
		//						- bei split_mode = 1 --> Lohnart
		//						- bei split_mode = 2 --> Prozente
		//						- bei split_mode = 3 --> Betrag (in CHF oder EUR oder USD)
		//$availAmt			 : ist der Rest, der man jetzt noch auszahlen kann
		
		$Waehrung = "CHF";
		if ($Waehrung != $splitPayCurrency) {
			$Waehrung = $splitPayCurrency;
		}
		
		switch ( $Waehrung ) {
			case "EUR":
				$wechselkursFremdZuCHF = floatval(blFunctionCall("payroll.getCurrencyForexRate", "EUR"));
			break;
			case "USD":
				$wechselkursFremdZuCHF = floatval(blFunctionCall("payroll.getCurrencyForexRate", "USD"));
			break;
		}//end switch ( $Waehrung )

		switch ( $splitMode ) {
			case 3://Betrag (im $splitValue steht der Auszahlungssbetrag)
				$payAmount = floatval($splitValue);
				break;				
			case 2: //Prozente (im $splitValue steht die ProzenteZahl)
				//ein $splitValue="50.00" wird als 50% interpretiert
				$payAmount = floatval($splitValue) / 100 * floatval($availAmt);
				break;				
			case 1://Nach Lohnart
				//hier ist der $splitValue als das Maximum zu verstehen, 
				//was man auszahlen kann (bezüglich dieser Lohnart)
				//--> man muss in der Calculation nachsehen, 
				//ob im Lohnart-Konto was drin steht und das Maximum bestimmen
				$maxLohnartAmount = $this->getCurrentPeriodAccountAmount($account_ID, $employee_ID);
				if($maxLohnartAmount <= 0){
					$maxLohnartAmount = floatval($splitValue);//max Auszahlung gemäss Splitt-Tabelle
				} 
				$payAmount = $maxLohnartAmount;	
				break;				
		}//end switch ( $splitMode )
			
		$payAmountForeignCurrency   = $payAmount;
		$payAmountSystemCurrencyCHF = $this->rundungAuf5Rappen($payAmountForeignCurrency * $wechselkursFremdZuCHF);
		
		$ret = array("payAmountSystemCurrencyCHF"=> $payAmountSystemCurrencyCHF
					,"payAmountForeignCurrency" => $payAmountForeignCurrency 
					,"payCurrency" => $Waehrung);
		return $ret;
	}
	
	public function getCurrentPeriodAccountAmount($account_ID, $employee_ID) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT amount FROM payroll_calculation_current 
			WHERE payroll_employee_ID = ".$employee_ID."
			AND   payroll_account_ID = ".$account_ID."
		    ; ");
		$amount = "0.00";
		if (count($result)>0) {
			$amount = $result[0]['amount'];
		}
		return floatval($amount);
	}
		
	public function getPeriodZahlstellenListe($periodeID) {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("
			SELECT DISTINCT payroll_bank_source_ID 
			FROM payroll_payment_split
			WHERE payroll_employee_ID IN 
			(	SELECT payroll_employee_ID 
				FROM payroll_period_employee
				WHERE payroll_period_ID = ".$periodeID."
			)
			AND payroll_bank_source_ID > 0
			ORDER BY payroll_bank_source_ID
		    ");
		$return = array();
		foreach ( $result as $row ) {
			$return[] = $row["payroll_bank_source_ID"];
		}  
		return $return;
	}
		
	public function getPeriodZahlstellenMitarbeiterListe($periodeID, $ZahlstellenID, $empList) {
		$sql = "
				SELECT payroll_employee_ID 
				FROM payroll_period_employee
				WHERE payroll_period_ID = ".$periodeID."					
				";
		if (strlen($empList)>0){
			$sql = "
					SELECT payroll_employee_ID 
					FROM payroll_period_employee
					WHERE payroll_period_ID = ".$periodeID."
					AND payroll_employee_ID IN (".$empList.")					
					";
		}
		if(intval($ZahlstellenID)>0) {
			$ANDwhereZahlstelle = " AND payroll_bank_source_ID = ".$ZahlstellenID." ";
			$sql = "
					SELECT DISTINCT payroll_employee_ID 
					FROM payroll_payment_split
					WHERE payroll_employee_ID IN 
					(	SELECT payroll_employee_ID 
						FROM payroll_period_employee
						WHERE payroll_period_ID = ".$periodeID."
					)
					AND payroll_bank_source_ID = ".$ZahlstellenID."
					ORDER BY payroll_employee_ID, processing_order
				    ";
		}		

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		$return = array();
		foreach ( $result as $row ) {
			$return[] = $row["payroll_employee_ID"];
		}  
		return $return;
	}
	
	public function getEmplFromTrackingTable() {
		$sql = "SELECT * FROM `payroll_auszahlen_tracking` WHERE amount_available > 0.001;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		$return = array();
		foreach ( $result as $row ) {
			$return[] = $row["payroll_employee_ID"];
		}  
		return $return;
	}

	public function getEmpl_Personenkreis_FromTrackingTable($emplFilter) {
		$sql = "SELECT id AS payroll_employee_ID FROM 
 lohndev.payroll_employee AS emp 
 WHERE emp.id IN (
	SELECT payroll_employee_ID FROM lohndev.`payroll_auszahlen_tracking` WHERE amount_available > 0.001
 )				
 ".$emplFilter;
		$return = array();
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		foreach ( $result as $row ) {
			$return[] = $row["payroll_employee_ID"];
		}
		return $return;
	}
	
	public function getUncompleteEmplFromTrackingTable() {
		$sql = "SELECT payroll_employee_ID FROM `payroll_auszahlen_tracking` WHERE amount_available > 0.001 AND amount_available <> amount_initial ;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		$return = array();
		foreach ( $result as $row ) {
			$return[] = $row["payroll_employee_ID"];
		}  
		return $return;
	}
	
	public function getUncompleteEmplNamesFromTrackingTable() {
		$sql = "
SELECT T.payroll_employee_ID, T.amount_available, T.amount_initial, E.Firstname, E.Lastname, E.EmployeeNumber, E.City FROM 
 `payroll_auszahlen_tracking` AS T
,`payroll_employee` AS E
WHERE T.amount_available > 0.001 
AND T.amount_available = T.amount_initial
AND T.payroll_employee_ID = E.id				
				;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);

		return $result;
	}
	
	
	public function truncateTrackingTable() {
		$sql = "TRUNCATE `payroll_auszahlen_tracking`;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate($sql);
		return true;
	}

	public function initTrackingTable() {
		$this->truncateEffectifPayoutTable();
		$this->truncateTrackingTable();
		$system_database_manager = system_database_manager::getInstance();
		$sql = "SELECT * FROM payroll_calculation_current WHERE payroll_account_ID = 8000";
		$result = $system_database_manager->executeQuery($sql);
		$insert =  "INSERT INTO `payroll_auszahlen_tracking`
					(`payroll_period_ID`,
					`payroll_employee_ID`,
					`payroll_payment_split_ID`,
					`processing_order`,
					`amount_initial`,
					`amount_available`)
					VALUES "; 
		foreach ( $result as $row ) {
			$insert .="
					('".$row['payroll_period_ID']."'
					,'".$row['payroll_employee_ID']."'
					,'0'
					,'0'
					,'".$row['amount']."'
					,'".$row['amount']."')
				    ,";
		}  
		$insert = substr($insert, 0, strlen($insert)-1);//letztes Komma wegmachen
		$result = $system_database_manager->executeUpdate($insert);

		return true;
	}

	public function setAmountAvailableTrackingTable($periodeID, $employeeID, $splitID, $processingOrder, $newAmountAvailable) {
		$sql = "
				UPDATE payroll_auszahlen_tracking
				   SET amount_available  = ".$newAmountAvailable."
				      ,payroll_payment_split_ID  = ".$splitID."
				      ,processing_order  = ".$processingOrder."
				 WHERE payroll_period_ID  = ".$periodeID."
				   AND payroll_employee_ID  = ".$employeeID."			
			  ;";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate($sql);
		return true;
	}

	public function getAmountAvailableFromTrackingTable($periodeID, $employeeID) {
		$sql = "
				SELECT MAX(id), amount_available, processing_order 
				  FROM payroll_auszahlen_tracking
			     WHERE payroll_period_ID  = ".$periodeID."
			       AND payroll_employee_ID  = ".$employeeID."		
			     ORDER BY payroll_employee_ID, processing_order		
			    ";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql);
		//communication_interface::alert($result[0]["amount_available"]."\nPO:".$result[0]["processing_order"]);
		return array( "amount_available" => $result[0]["amount_available"] 
				     ,"processing_order" => $result[0]["processing_order"] );
	}
	
	public function truncateEffectifPayoutTable() {
		$sql = "TRUNCATE `payroll_payment_current_effectifpayout`;";
		//communication_interface::alert($sql);
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate($sql);
		return true;
	}

	public function setEffectifPayoutTable($period_ID,$employee_ID,$split_ID,$amount_CHF,$amount_payout,$currency_ID,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3) {
		$sql = "
INSERT INTO `payroll_payment_current_effectifpayout`
(`payroll_period_ID`,`payroll_employee_ID`,`payroll_payment_split_ID`,`amount_CHF`,`amount_payout`,`currency_ID`,`split_mode`,`payroll_account_ID`,`benIBAN`,`beneBank1`,`beneBank2`,`beneBank3`)
VALUES
(".$period_ID.", ".$employee_ID.", ".$split_ID.", ".$amount_CHF.", ".$amount_payout.", '".$currency_ID."', ".$split_mode.", '".$account_ID."', '".$benIBAN."', '".$beneBank1."', '".$beneBank2."', '".$beneBank3."');";
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate($sql);
		return true;
	}
	
	public function replaceUmlaute($uebergabeWort) {
		$umlaute = array("'","ä","ö" ,"ü" ,"Ä" ,"Ö" ,"Ü" ,"ß" ,"à","â","á","é","è","Ç","ç","ñ","Ñ","ó","õ","ú");
		$ersatz = array("","ae","oe","ue","Ae","Oe","Ue","ss","a","a","a","e","e","C","c","n","N","o","o","u");
		$uebergabeWort = str_replace($umlaute,$ersatz,$uebergabeWort);
		
		$umlaute = array("&auml;","&ouml;","&uuml;","&Auml;","&Ouml;","&Uuml;","&eacute;","&Eacute;","&ATILDE;&FRAC14;","&Atilde;","&atilde;","&frac14;","Ã¼","Ã¶","¶","Ã©");
		$ersatz = array( "ae"    ,"oe"    ,"ue"    ,"AE"    ,"OE"    ,"UE"    ,"e"       ,"E"       ,"UE"              ,"UE"      ,"ue"      ,""        ,"ue","oe" ,"o","e");
		return str_replace($umlaute,$ersatz,$uebergabeWort);
	}
	/**
	 * Gibt einen (mixed-)Betrag als Float-Betrag auf 5 Rappen genau gerundet
	 */
	public function rundungAuf5Rappen($betrag) {
		$gerundeterBetrag = floatval($betrag);
		$flBetrag = $gerundeterBetrag;
		if($flBetrag <> 0){
			$flBetrag = $flBetrag * 20;
			$flBetrag = round($flBetrag, 0);//schneidet nach dem Runden die Kommastellen ab.
			$gerundeterBetrag = $flBetrag / 20;
		}
		return $gerundeterBetrag;
	}
		
}
?>
