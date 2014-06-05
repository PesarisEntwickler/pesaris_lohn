<?php

class auszahlen {

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
