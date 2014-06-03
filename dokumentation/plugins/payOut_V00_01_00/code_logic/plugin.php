<?php
// WICHTIG: Datenbank-Zugriffe nur hier im BL Business-Logic-Plugin!

class payOut_BL {

	public function sysListener($functionName, $functionParameters) {
		switch($functionName) {
		case 'payOut.kuckuck':
			return $functionParameters[0];
			break;
		case 'payOut.braucheDaten':
			return $this->braucheDaten();
			break;
		default:
			return "Funktion unbekannt";
			break;
		}
	}

	public function braucheDaten() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery(
					"SELECT core_intl_country.id, core_intl_country_names.country_name " .
					"FROM core_intl_country,core_intl_country_names " .
					"WHERE core_intl_country.id=core_intl_country_names.core_intl_country_ID " .
					"AND core_intl_country_names.country_name_language='de' " .
					"ORDER BY core_intl_country_names.country_name", "pim_getCountryList"
				);
		
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

$SYS_PLUGIN["bl"]["payOut"] = new payOut_BL();
?>
