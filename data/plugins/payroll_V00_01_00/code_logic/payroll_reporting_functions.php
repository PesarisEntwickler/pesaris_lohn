<?php
class payroll_reporting_functions {

	public function getReportingCompany($TAGCompanyOrMainCompany, $reportingCompany) {
		//TODO: Eine Checkox im Firmenstamm / Stammdaten erstellen um dieses Flag "isReportingCompany = 'Y'" zu verwalten

		$ReportCompanyXML = "
				<".$TAGCompanyOrMainCompany.">\n";
		
		//Defaultbesetzung, die erste Zeile
		$sql = "SELECT * FROM payroll_company  LIMIT 1,1 ;";//Default, die erste Firma
		if ($reportingCompany == 0) {
			$sql = "SELECT * FROM payroll_company WHERE isReportingCompany = 'Y' ;";
		} else {
			$sql = "SELECT * FROM payroll_company WHERE id = ".$reportingCompany." ;";
		}
		
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery($sql, "getReportingCompany");
		if (count($result) > 0) {
			$ReportCompanyXML = "
			<".$TAGCompanyOrMainCompany.">
				<Name>".$result[0]["HR-RC-Name"]."</Name>
				<Street>".$result[0]["Street"]."</Street>
				<ZipCity>".$result[0]["ZIP-Code"]." ".$result[0]["City"]."</ZipCity>
				<ContactPersName>".$result[0]["ContactPers-Name"]."</ContactPersName>
				<ContactPersTel>".$result[0]["ContactPers-Tel"]."</ContactPersTel>
				<ContactPersEMail>".$result[0]["ContactPers-eMail"]."</ContactPersEMail>
			</".$TAGCompanyOrMainCompany.">";
		}
		
		return $ReportCompanyXML;
	}
	
	public function getPeriodLabels($lang) {
		switch ($lang) {
			case "en":
				return array("to", "January", "February", "March", "April", "May", "June", "Julliet", "August", "September", "October", "November", "December", "", "", "Gratifikation", "Gratifikation");
				break;
			case "fr":
				return array("de", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember", "", "", "Gratifikation", "Gratifikation");
				break;
			case "it":
				return array("di", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember", "", "", "Gratifikation", "Gratifikation");
				break;
			default: //de
				return array("bis", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember", "", "", "Gratifikation", "Gratifikation");
				break;
		}
	}
	
	public function getPrintDateTime() {
		return "
			<PrintDate>".date("d.m.Y")."</PrintDate>
			<PrintTime>".date("H:i:s")."</PrintTime>";
	}
	
}

