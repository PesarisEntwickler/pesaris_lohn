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
			$response["errCode"] = 102;
			$response["errText"] = "no data found";
		}
		return $response;
		
	}

	public function auszahlDaten() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM core_intl_country,core_intl_country_names WHERE core_intl_country.id=core_intl_country_names.core_intl_country_ID AND core_intl_country_names.country_name_language='de' ORDER BY core_intl_country_names.country_name", "pim_getCountryList");
		
		if(count($result) == 0) {
			$retval["success"] 	= false;
			$retval["errCode"]  = 10;
			$retval["errText"]  = "Keine Daten gefunden.";
		}else{
			$retval["success"] 	= true;
			$retval["errCode"]  = 0;
			$retval["data"] 	= $result;
		}
		return $retval;
	}

	
}
?>
