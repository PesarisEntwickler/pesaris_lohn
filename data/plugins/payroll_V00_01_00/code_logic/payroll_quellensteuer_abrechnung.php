<?php
class payroll_quellensteuerAbrechnung {
/* * * * * * * * * * * * * * * * * * * * * * * * 
 *  Quellensteuer = DeductionAtSource = "das" = "DedAtSrc"  
XML Struktur
<Report name="QuellensteuerAbrechnung" lang="de">
	<Header>
			<Company>
				<Name>CoproNet/Systemfirma</Name>
				<Street>Stationsstrasse 1</Street>
				<ZipCity>8000 Zürich</ZipCity>
				<ContactPersName>Vorname Name</ContactPersName>
				<ContactPersTel>079 Systemfirma</ContactPersTel>
				<ContactPersEMail>vorname.name@copronet.ch</ContactPersEMail>
			</Company>
			<PrintDate>27.02.2015</PrintDate>
			<PrintTime>10:29:12</PrintTime>
		<Period>Januar  bis  November  2016</Period>
	</Header>
	<CompanyList>
		<Company>
			<Nr>2</Nr>
			<Krz>PRESIDA</Krz>
			<Name>Presida Treuhand AG</Name>
			<Strasse>Mitteldorfstrasse 37</Strasse>
			<PlzOrt>5033 Buchs</PlzOrt>
			<KontaktpersonName>Heinz</KontaktpersonName>
			<KontaktpersonTel>079 heinz</KontaktpersonTel>
			<KontaktpersonEMail>h.plues@presida</KontaktpersonEMail>
			<KantonList>
				<Kanton>
					<Name>BL</Name>
					<Arbeitgebernummer>ArbeitgebernummerBL</Arbeitgebernummer>
					<GemeindeList>
						<Gemeinde>
							<Name>4123 Allschwil</Name>
							<GdeFirma>2</GdeFirma>
							<GdeKanton>BL</GdeKanton>
							<MitarbeiterList>
								<Mitarbeiter>
									<MaName>1037, Person QST2</MaName>
									<MaAHVNummer>667.79.330.118</MaAHVNummer>
									<MaGeboren>1979-07-30</MaGeboren>
									<MaSex>M</MaSex>
									<MaZivilstand>6</MaZivilstand>
									<MaKinder>0</MaKinder>
									<MaQSTCode></MaQSTCode>
									<MaEintritt>eintritt</MaEintritt>
									<MaAustritt>austritt</MaAustritt>
									<MaQSTTarifwechsel>E(02.2016) TW(04.2016)</MaQSTTarifwechsel>
									<MaQSTPeriodeVonBis>02-04</MaQSTPeriodeVonBis>
									<MaQSTBetragBruttoLohn>49'200.00</MaQSTBetragBruttoLohn>
									<MaQSTBetragZulagen>0.00</MaQSTBetragZulagen>
									<MaQSTBetragPflichtig>14'200.00</MaQSTBetragPflichtig>
									<MaQSTBetragAbzug>-326.00</MaQSTBetragAbzug>
								</Mitarbeiter>
							</MitarbeiterList>
							<QSTGemeindeTotalPflichtig>14'203.00</QSTGemeindeTotalPflichtig>
							<QSTGemeindeTotalAbzug>-325.00</QSTGemeindeTotalAbzug>
						</Gemeinde>
					</GemeindeList>
					<QSTKantonTotalPflichtig>14'203.00</QSTKantonTotalPflichtig>
					<QSTKantonTotalAbzug>-325.00</QSTKantonTotalAbzug>
					<ProvisionProzent>4.00</ProvisionProzent>
					<QSTKantonTotalAbzugProvision>-13.00</QSTKantonTotalAbzugProvision>
					<QSTKantonTotalAbzugNachProvision>-312.00</QSTKantonTotalAbzugNachProvision>
				</Kanton>
			</KantonList>
			<QSTCompanyTotalPflichtig>14'203.00</QSTCompanyTotalPflichtig>
			<QSTCompanyTotalAbzug>-325.00</QSTCompanyTotalAbzug>
			<QSTCompanyTotalAbzugProvision>-13.00</QSTCompanyTotalAbzugProvision>
			<QSTCompanyTotalAbzugNachProvision>-312.00</QSTCompanyTotalAbzugNachProvision>
		</Company>
	</CompanyList>
	<QSTReportTotalPflichtig>119'809.00</QSTReportTotalPflichtig>
	<QSTReportTotalAbzug>-8'043.35</QSTReportTotalAbzug>
	<QSTReportTotalAbzugProvision>-155.45</QSTReportTotalAbzugProvision>
	<QSTReportTotalAbzugNachProvision>-7'887.90</QSTReportTotalAbzugNachProvision>
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


function getPeriodIDs($Jahr, $Von, $Bis) {
	if ($Von > $Bis) {
		$x = $Von;
		$Von = $Bis;
		$Bis = $x;
	}
	$sql =
"SELECT id FROM payroll_period
WHERE payroll_year_ID = ".$Jahr."
AND major_period >= ".$Von."
AND major_period <= ".$Bis.";";
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$PeriodIDs = array();
	foreach ($result as $value) {
		$PeriodIDs[]=$value["id"];
	}
	return  implode(",", $PeriodIDs);
}

function getBetroffeneMitarbeiter($PeriodIDList, $QstAcounts) {
	$sql =
"SELECT * FROM payroll_calculation_entry
WHERE payroll_period_ID IN (".implode(",", $PeriodIDList).")
AND payroll_account_ID  IN (".implode(",", $QstAcounts).")
GROUP BY payroll_employee_ID";
//communication_interface::alert($sql);
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$EmployeeIDs = array();
	foreach ($result as $value) {
		$EmployeeIDs[]	=	$value["payroll_employee_ID"];
	}
	return implode(",", $EmployeeIDs) ;
}

/* MA mit Zulagen    -> AccountTypeListe = (18) */
/* QST relevante MA  -> AccountTypeListe = (5,7,9,16,17) */
function relevanteQSTMitarbeiter($AccountTypListe) {
	$sql =
"SELECT payroll_employee_ID, amount FROM  payroll_das_account, payroll_calculation_entry
where payroll_das_account.payroll_account_ID  =  payroll_calculation_entry.payroll_account_ID
AND  payroll_year_ID >2018
AND  AccountType in (".$AccountTypListe.")
GROUP BY payroll_employee_ID
ORDER BY payroll_employee_ID ;";
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$EmployeeIDs = array();
	foreach ($result as $value) {
		$EmployeeIDs[]	=	$value["payroll_employee_ID"];
	}
	return implode(",", $EmployeeIDs) ;
	
}

function getBetroffeneMaDetailsGemeindenKantone($CompanyID, $EmployeeIDList) {
	$andWhere = "";
	if ($CompanyID >= 0) {
		$andWhere = " AND payroll_company_ID = ".$CompanyID;
	}
	$sql =
"SELECT * FROM payroll_employee
WHERE id IN (".$EmployeeIDList.")".
$andWhere.";";
//communication_interface::alert("getBetroffeneMaDetailsGemeindenKantone:\n".$sql."\n\n");
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$Company	= array();
	$Kantone 	= array();
	$Gemeinden 	= array();
	$MaDetails 	= array();
	foreach ($result as $value) {
		$Firma			= $value["payroll_company_ID"];
		if (intval($value["DedAtSrcCompany"])> 0 ) {
			$Firma	=	$value["DedAtSrcCompany"];
		}
		$Kanton = $value["ResidenceCanton"];
		if (strlen($value["DedAtSrcCanton"]) > 1) {
			$Kanton	= $value["DedAtSrcCanton"];
		}
		$ZIPCode	= $value["ZIP-Code"];
		$Gemeinde 	= $value["City"];
		if (strlen($value["DedAtSrcMunicipality"]) > 1) {
			//Wenn nur ein Wort angegeben wurde in "QST pol. Gemeinde", dann 
			//in ZIPCode/Postleitzahl halt nur den Gemeindenamen (ist Gruppierungsmerkmal)
			//und in Gemeinde dann halt den DedAtSrcCanton
			$ZIPCode	= $value["DedAtSrcMunicipality"];
			$Gemeinde	= $value["DedAtSrcCanton"];
			$z = explode(" ", $value["DedAtSrcMunicipality"]);
			if (count($z)>1) {//Test ob 2 Teile wie z.B. "5000 Aarau"
				if (floatval($z[0]) > 999) {//Test ob Postleitzahl
					$ZIPCode	=	$z[0];
					$Gemeinde	=	$z[1];
					//Falls noch mehr Wörter erfasst wurden
					if (count($z)>2) { 	$Gemeinde	.=	$z[2]; 	}
					if (count($z)>3) { 	$Gemeinde	.=	$z[3]; 	}
				}
			}
		}
		$Company[]		= $Firma;
		$Kantone[]		= $Firma."#".$Kanton;/* #0-#1 */
		$Gemeinden[]	= $Firma."#".$Kanton."#".$ZIPCode."#".$Gemeinde;/* #0-#3 */
		$MaDetails[]	= $Firma."#".$Kanton."#".$ZIPCode."#".$Gemeinde."#".$value["id"]/* #0-#4 */
 					 ."#".$value["EmployeeNumber"]."#".$value["Lastname"]."#".$value["Firstname"]."#".$value["PlaceOfOrigin"]."#".$value["Age"]/* #5-#9 */
 					 ."#".$value["AHV-AVS-Number"]."#".$value["DateOfBirth"]."#".$value["Sex"]."#".$value["CivilStatus"]."#".$value["SingleParent"]/* #10-#14 */
 					 ."#".$value["WageCode"]."#".$value["BaseWage"]."#".$value["EmploymentStatus"]."#".$value["AttendedTimeCode"]."#".$value["AttendedTimeHours"]/* #15-#19 */
					 ."#".$value["EmploymentPercentage"]."#".$value["DedAtSrcMode"]."#".$value["DedAtSrcCode"]."#".$value["DedAtSrcPercentage"]."#".$value["DedAtSrcCompany"]/* #20-#24 */
					 ."#".$value["Department"]."#".$value["SV-AS-Number"]."#"."eintritt"."#"."austritt";/* #25-#28 */
	}
	return array("Company"=>array_unique($Company),"MaDetails"=>array_unique($MaDetails),"Gemeinden"=>array_unique($Gemeinden),"Kantone"=>array_unique($Kantone));
}

function getQSTKanton($Kanton) {
	$system_database_manager = system_database_manager::getInstance();
	$sql = "SELECT * FROM payroll_das_canton WHERE DedAtSrcCanton = '".$Kanton."'";
	$result = $system_database_manager->executeQuery($sql, "");
	if (count($result)<1) {
		$sqli = "INSERT INTO payroll_das_canton 
				(`DedAtSrcCanton`,`TaxAdminName`,`DaysPerMonth`,`AnnualSettlementMode`,`commission`,`TaxArbeitgebernummer`)
				VALUES ('".$Kanton."', 'undef', 0 , 0 , 0.0 , 'undef');	";
		$result = $system_database_manager->executeUpdate($sqli, "");
	}
	$result = $system_database_manager->executeQuery($sql, "");
	return array("DedAtSrcCanton"=>$result[0]["DedAtSrcCanton"]
			, "TaxAdminName"=>$result[0]["TaxAdminName"]
			, "TaxAdminStreet" =>$result[0]["TaxAdminStreet"]
			, "TaxAdminCity" =>$result[0]["TaxAdminCity"]
			, "DaysPerMonth"=>$result[0]["DaysPerMonth"]
			, "AnnualSettlementMode"=>$result[0]["AnnualSettlementMode"]
			, "AnnualSettlementPrc"=>$result[0]["AnnualSettlementPrc"]
			, "commission"=>$result[0]["commission"]
			, "TaxArbeitgebernummer"=>$result[0]["TaxArbeitgebernummer"]
	);
}


function getAnzahlKinder($MaID, $Jahr, $Monat) {
	$sql =
"SELECT SUM(CE.quantity) AS AnzKinder
FROM payroll_das_account AS A, payroll_calculation_entry AS CE, payroll_period AS P
WHERE A.payroll_account_ID = CE.payroll_account_ID
AND CE.payroll_period_ID = P.id
AND CE.payroll_employee_ID = ".$MaID."
AND P.payroll_year_ID = ".$Jahr."
AND P.major_period = ".$Monat."
AND A.AccountType in (18);";
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$Anzahl = 0;
	if (count($result)>0) {
		$Anzahl = $result[0]["AnzKinder"];
	}
	$AnzKinderCalculation = intval($Anzahl);
	
	//Test ob in der Kinderdatei mehr Kinder drin stehen
	$sql =
"SELECT count(id) as AnzKinder FROM payroll_employee_children
where payroll_employee_ID = ".$MaID;
	$result = $system_database_manager->executeQuery($sql, "");
	$Anzahl = 0;
	if (count($result)>0) {
		$Anzahl = $result[0]["AnzKinder"];
	}
	$AnzKinderStammdaten = intval($Anzahl);
	
	$AnzKinder = $AnzKinderCalculation;
	if ($AnzKinder < $AnzKinderStammdaten) {
		$AnzKinder = $AnzKinderStammdaten;
	}
	return $AnzKinder;
}

function getZulagen($MaID, $PeriodIDList) {
	$sql = 
"SELECT SUM(amount) AS KinderUndAusbildungsZulagen 
FROM payroll_calculation_entry
WHERE payroll_employee_ID = ".$MaID."
AND payroll_period_ID IN (".$PeriodIDList.")
AND payroll_account_ID
 IN (SELECT R.payroll_account_ID FROM 
     payroll_insurance_code AS IC
    ,payroll_insurance      AS I
    ,payroll_insurance_rate AS R
    ,payroll_employee_children AS C
  WHERE I.id = IC.payroll_insurance_ID
  AND  IC.id = R.payroll_insurance_code_ID
  AND  IC.InsuranceCode = C.Code
  AND   C.payroll_employee_ID = ".$MaID.")";

	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$KinderUndAusbildungsZulagen	= 0;
	if (count($result)>0) {
		$KinderUndAusbildungsZulagen	= $result[0]["KinderUndAusbildungsZulagen"];
	}
	
	return $KinderUndAusbildungsZulagen;
}

function getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, $Typ) {
	switch ($Typ) {
		case "QSTPflichtig":			$AccountTypList = "5";	break;
		case "QSTAbzug": 				$AccountTypList = "7";	break;
		case "QSTGutschrift":			$AccountTypList = "9";	break;
		case "QSTAbzugVorperiode": 		$AccountTypList = "16";	break;
		case "QSTGutschriftVorperiode": $AccountTypList = "17";	break;
		case "Zulagen":					$AccountTypList = "18";	break;
		case "BruttoLohn":				$AccountTypList = "19";	break;
		case "AlleQSTAbzuege": 			$AccountTypList = "7,9,16,17";	break;
	}
	$whereMA = "";
	if (strlen($MaID) > 0) {
		$whereMA = "AND CE.payroll_employee_ID = ".$MaID;
	}

	$sql = 
	"SELECT payroll_employee_ID
	, min(P.major_period) AS Min
	, max(P.major_period) AS Max
	, sum(quantity) AS Q
	, sum(amount) AS S
	, code
	, min(code)   AS minCode
	, max(code)   AS maxCode
	, count(code) AS CC 
	FROM payroll_das_account AS A, payroll_calculation_entry AS CE, payroll_period AS P
	WHERE A.payroll_account_ID = CE.payroll_account_ID
	AND CE.payroll_period_ID = P.id
	AND P.payroll_year_ID = ".$Jahr."
	AND P.major_period BETWEEN ".$Von." AND ".$Bis."
	AND AccountType IN (".$AccountTypList.")
	".$whereMA."
    GROUP BY payroll_employee_ID
	ORDER BY payroll_employee_ID, payroll_period_ID
	;";
				
	$Min = $Von;
	$Max = $Bis;
	$Q = 0;
	$S = 0;
	$code = "";
	$minCode = "";
	$maxCode = "";
	$CC = 0;
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	if (count($result)>0) {
		$Q = $result[0]["Q"];
		$S = $result[0]["S"];
		$Min = $result[0]["Min"];
		$Max = $result[0]["Max"];
		$code = $result[0]["code"];
		$CC = $result[0]["CC"];
		$minCode = $result[0]["minCode"];
		$maxCode = $result[0]["maxCode"];
	}
	
	$result = $system_database_manager->executeQuery($sql, "");
	$MA = array();
	if (strlen($MaID) == 0) {
		foreach ($result as $value) {
			$MA[] = $value["payroll_employee_ID"];
		}
	}
	$MAList = implode(",", $MA);
	return array("vonPeriode"=>$Min
				,"bisPeriode"=>$Max
				,"Quantity"=>$Q
				,"Summe"=>$S
				,"Code"=>$code
				,"CountCode"=>$CC
				,"MAList"=>$MAList
				,"sql"=>$sql
				,"minCode"=>$minCode
				,"maxCode"=>$maxCode);
}


function getSummierteLohnart($MaID, $Jahr, $Von, $Bis, $LohnArt, $Attribut) {
	$sql = 
	"SELECT *
	FROM payroll_calculation_entry AS CE, payroll_period AS P
	WHERE CE.payroll_period_ID = P.id
	AND CE.payroll_employee_ID = ".$MaID."
	AND P.payroll_year_ID = ".$Jahr."
	AND P.major_period BETWEEN ".$Von." AND ".$Bis."
	AND CE.payroll_account_ID = ".$LohnArt;
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$AttrSumme = 0.0;
	foreach ($result as $r) {
		$AttrSumme += floatval($r[$Attribut]);
	}
	return $AttrSumme;
}

function getMaxEmploymentDate($MaID) {
	$sql = 
	"SELECT * FROM payroll_employment_period
	WHERE payroll_employee_ID = ".$MaID."
	ORDER BY DateFrom DESC";
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$DateFrom = "0000-00-00";
	$DateTo = "0000-00-00";
	if (count($result)>0) {
		$DateFrom = $result[0]["DateFrom"];
		$DateTo = $result[0]["DateTo"];
	}
	return array("DateFrom"=>$DateFrom, "DateTo"=>$DateTo);	
}

function getSwissDatum($DbDate) {
	$ret = "";
	if ($DbDate != "0000-00-00") {
		$DateArr = explode("-", $DbDate);
		$ret = $DateArr[2].".".$DateArr[1].".".$DateArr[0];
	}
	return $ret;
}

function getQuellensteuerAbrechnung($param) {
//	communication_interface::alert("getQuellensteuerAbrechnung(): ".print_r($param,true));
	
	$Firma	=	$param["company"];
	$Jahr	=	$param["year"];
	$Von	=	$param["von"];
	$Bis	=	$param["bis"];
	
	global $aafwConfig;
	require_once(getcwd()."/kernel/common-functions/configuration.php");
	require_once('payroll_reporting_functions.php');
	$payroll_reporting_functions = new payroll_reporting_functions();
	require_once('payroll_account.php');
	$account = new account();
	require_once('payroll_auszahlen.php');
	$ausz = new auszahlen();

	$t  		= "\n\t";
	$t_ 		= "\n\t\t";
	$t__ 		= "\n\t\t\t";
	$t___ 		= "\n\t\t\t\t";
	$t____ 		= "\n\t\t\t\t\t";
	$t_____ 	= "\n\t\t\t\t\t\t";
	$t______ 	= "\n\t\t\t\t\t\t\t";
	$t_______ 	= "\n\t\t\t\t\t\t\t\t";
	$t________ 	= "\n\t\t\t\t\t\t\t\t\t";
	$t_________	= "\n\t\t\t\t\t\t\t\t\t\t";
	
	ini_set('memory_limit', '512M');
	$periodLabels["de"] = $payroll_reporting_functions->getPeriodLabels("de");
	$minorPeriod = 0;
		
	$fm = new file_manager();
	$newTmpDirName = $fm->createTmpDir();
	$newTmpPath = $fm->getFullPath();
	$fm->setFile("metadata.dat")->putContents( serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>"Quellensteuer-Abrechnung.pdf")) );


	$periodTitle = $periodLabels[session_control::getSessionInfo("language")][$Von]."  ";
	$periodTitle.= $periodLabels[session_control::getSessionInfo("language")][0]."  ";
	$periodTitle.= $periodLabels[session_control::getSessionInfo("language")][$Bis]."  ".$Jahr;

	$system_database_manager = system_database_manager::getInstance();
	$fp = $fm->setFile("data.xml")->fopen("w");

	$ReportName = "QuellensteuerAbrechnung";
	fwrite($fp,
	"<Report name=\"".$ReportName."\" lang=\"de\">\n\t<Header>".
	$payroll_reporting_functions->getReportingCompany("Company", $Firma).
	$payroll_reporting_functions->getPrintDateTime()."
		<Period>".$periodTitle."</Period>
	</Header>");
	
	$PeriodIDList = $this->getPeriodIDs($Jahr, $Von, $Bis);
	$QstAcounts = $account->getDedAtSrcGlobalSettings();
	//$EmployeeIDList = $this->getBetroffeneMitarbeiter($PeriodIDList, $QstAcounts["data"]);
	$AlleMA_mitQSTAbzuegen	= $this->getQSTPeriodenSumme("", $Jahr, $Von, $Bis, "AlleQSTAbzuege");
	$EmployeeIDList = $AlleMA_mitQSTAbzuegen["MAList"];
	$EmplGdeKant = $this->getBetroffeneMaDetailsGemeindenKantone($Firma, $EmployeeIDList);

// 	$MaQST			= $this->getQSTPeriodenSumme(36, $Jahr, $Von, $Bis, "AlleQSTAbzuege");
// 	communication_interface::alert(print_r($MaQST, true));
	
// 	communication_interface::alert("Resultate der Vorberechnung: "
// 			."Firma $Firma, Jahr $Jahr, Von $Von, Bis $Bis "
// 			."\n=========================================="
// 			."\nPeriodIDList:".implode(",", $PeriodIDList)
// 			."\nQstAccounts :".implode(",", $QstAcounts["data"])
// 			."\nEmployees   :".implode(",", $EmployeeIDList)
// 			//."\nEmployes    :".implode(",", $EmplGdeKant["MaDetails"])
// 			."\nGemeinden   :".implode(",", $EmplGdeKant["Gemeinden"])
// 			."\nKantone     :".implode(",", $EmplGdeKant["Kantone"])
// 			."\nFirmen Nr   :".implode(",", $EmplGdeKant["Company"])  );

	$QSTReportTotalPflichtig	= 0;
	$QSTReportTotalAbzug		= 0;
	$QSTReportTotalAbzugProvision	= 0;
	$QSTReportTotalAbzugNachProvision	= 0;
	
	fwrite($fp, $t."<CompanyList>");
	foreach ($EmplGdeKant["Company"] as $CompanyID) {
		$QSTCompanyTotalPflichtig			= 0;
		$QSTCompanyTotalAbzug				= 0;
		$QSTCompanyTotalProvision			= 0;
		$QSTCompanyTotalAbzugProvision		= 0;
		$QSTCompanyTotalAbzugNachProvision	= 0;
		$Firma = $ausz->getCompany($CompanyID);
		fwrite($fp, $t_."<Company>");
		fwrite($fp, $t__."<Nr>".$CompanyID."</Nr>");
		fwrite($fp, $t__."<Krz>".$Firma["short"]."</Krz>");
		fwrite($fp, $t__."<Name>".$Firma["name"]."</Name>");
		fwrite($fp, $t__."<Strasse>".$Firma["str"]."</Strasse>");
		fwrite($fp, $t__."<PlzOrt>".$Firma["zip"]." ".$Firma["city"]."</PlzOrt>");
		fwrite($fp, $t__."<KontaktpersonName>".$Firma["cnam"]."</KontaktpersonName>");
		fwrite($fp, $t__."<KontaktpersonTel>".$Firma["ctel"]."</KontaktpersonTel>");
		fwrite($fp, $t__."<KontaktpersonEMail>".$Firma["cmail"]."</KontaktpersonEMail>");
		fwrite($fp, $t__."<KantonList>");
		foreach ($EmplGdeKant["Kantone"] as $Kanton) {
			$QSTKantonTotalPflichtig 		= 0;
			$QSTKantonTotalAbzug 			= 0;
			$QSTKantonTotalAbzugProvision	= 0;
			$CantonArr = explode("#",$Kanton);
			$FirmenID  = $CantonArr[0];
			$KantonID = $CantonArr[1];	
			if ($CompanyID == $FirmenID) {
				$Kt = $this->getQSTKanton($KantonID);
				fwrite($fp, $t___."<Kanton>");
				fwrite($fp, $t____."<Name>".$KantonID."</Name>");
				fwrite($fp, $t____."<Arbeitgebernummer>".$Kt["TaxArbeitgebernummer"]."</Arbeitgebernummer>");
				fwrite($fp, $t____."<GemeindeList>");
				foreach ($EmplGdeKant["Gemeinden"] as $Gemeinde) {
					$QSTGemeindeTotalPflichtig	= 0;
					$QSTGemeindeTotalAbzug 		= 0;
					$Gde = explode("#",$Gemeinde);
					$GdeFirmenID = $Gde[0];
					$GdeKantonID = $Gde[1];
					$GdePostLeitZahl = $Gde[2];
					if ($GdeKantonID == $KantonID && $GdeFirmenID == $FirmenID) {
						fwrite($fp, $t_____."<Gemeinde>");
						fwrite($fp, $t______."<Name>".$Gde[2]." ".$Gde[3]."</Name>");
						fwrite($fp, $t______."<GdeFirma>".$GdeFirmenID."</GdeFirma>");
						fwrite($fp, $t______."<GdeKanton>".$GdeKantonID."</GdeKanton>");
						fwrite($fp, $t______."<MitarbeiterList>");
						foreach ($EmplGdeKant["MaDetails"] as $Mitarbeiter) {
							$Ma = explode("#", $Mitarbeiter);
							$MaFirmenID		= $Ma[0];
							$MaKantonID		= $Ma[1];
							$MaPostLeitZahl = $Ma[2];
							$MaOrt 			= $Ma[3];
							$MaID 			= $Ma[4];
							$MaName			= $Ma[5].", ".$Ma[7]." ".$Ma[6];
							$MaAHVNummer	= $Ma[10];
							$MaGeboren		= $Ma[11];
							$MaSex 			= $Ma[12];
							$MaZivilstand	= $Ma[13];
							$MaSingleParent	= $Ma[14];
							$MaWageCode		= $Ma[15];
							$MaBasisLohn	= $Ma[16];
							$MaEmplPercent	= $Ma[20];
							$MaQSTModus		= $Ma[21];
							$MaQSTCodeMA	= $Ma[22];
							$MaQSTProz 		= $Ma[23];
							$MaSVASNummer	= $Ma[26];
							$EinAusTritt	= $this->getMaxEmploymentDate($MaID);
							$MaEintritt		= $this->getSwissDatum( $EinAusTritt["DateFrom"] );
							$MaAustritt		= $this->getSwissDatum( $EinAusTritt["DateTo"] );
							if ($MaPostLeitZahl == $GdePostLeitZahl && $MaKantonID == $GdeKantonID && $MaFirmenID == $GdeFirmenID) {
								if (strlen(trim($MaSVASNummer)) > 2) {
									$MaAHVNummer = $MaSVASNummer;
								}
								$MaQSTBetragBruttoLohn	= $this->getSummierteLohnart($MaID, $Jahr, $Von, $Bis, "5000", "amount");//5000[amount] ist BruttoLohn
								$MaQST 					= $this->getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, "QSTPflichtig");
								$MaQSTBetragPflichtig	= floatval($MaQST["Summe"]);
								$codeBeginMonat			= $MaQST["vonPeriode"];
								$codeEndetMonat			= $MaQST["bisPeriode"];
								$TW = "";
								if ($Von != $codeBeginMonat) {
									$TW = "E";
								}
								$MaQST 					= $this->getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, "AlleQSTAbzuege");
								$MaQSTBetragAbzug		= floatval($MaQST["Summe"]);
								$MaQSTCodeVorher 		= "";
								$MaQSTCodeAktuell		= $MaQSTCodeMA;
								if ($MaQST["minCode"] != $MaQST["maxCode"]) {
									$TW = "TW";
									if ($MaQSTCodeAktuell  != $MaQST["minCode"]) {
										$MaQSTCodeVorher ="(".$MaQST["minCode"].")";
									} else {
										$MaQSTCodeVorher ="(".$MaQST["maxCode"].")";
									}
								}
								$MaQSTPeriodeVonBis		= substr("0".$codeBeginMonat,-2)."-".substr("0".$codeEndetMonat,-2);//Echte betroffene Periode des MA innerhalb der Report-Periode
								$MaKinder				= $this->getAnzahlKinder($MaID, $Jahr, $Bis);//rechnet die Anzahl Kinder am Ende der Auswertungsperiode
 								$MaQSTBetragZulagen		= $this->getZulagen($MaID, $PeriodIDList);
								fwrite($fp, $t_______."<Mitarbeiter>");
								fwrite($fp, $t________."<MaName>".$MaName."</MaName>");
								fwrite($fp, $t________."<MaAHVNummer>".$MaAHVNummer."</MaAHVNummer>");
								fwrite($fp, $t________."<MaGeboren>".$MaGeboren."</MaGeboren>");
								fwrite($fp, $t________."<MaSex>".$MaSex."</MaSex>");
								fwrite($fp, $t________."<MaZivilstand>".$MaZivilstand."</MaZivilstand>");
								fwrite($fp, $t________."<MaKinder>".intval($MaKinder)."</MaKinder>");
								fwrite($fp, $t________."<MaQSTCodeVorher>".$MaQSTCodeVorher."</MaQSTCodeVorher>");
								fwrite($fp, $t________."<MaQSTCode>".$MaQSTCodeAktuell."</MaQSTCode>");
								fwrite($fp, $t________."<MaEintritt>".$MaEintritt."</MaEintritt>");
								fwrite($fp, $t________."<MaAustritt>".$MaAustritt."</MaAustritt>");
								fwrite($fp, $t________."<MaQSTTarifwechsel>".$TW."</MaQSTTarifwechsel>");
								fwrite($fp, $t________."<MaQSTPeriodeVonBis>".$MaQSTPeriodeVonBis."</MaQSTPeriodeVonBis>");
								fwrite($fp, $t________."<MaQSTBetragBruttoLohn>".number_format($MaQSTBetragBruttoLohn, 2, '.', "'")."</MaQSTBetragBruttoLohn>");
								fwrite($fp, $t________."<MaQSTBetragZulagen>".number_format($MaQSTBetragZulagen, 2, '.', "'")."</MaQSTBetragZulagen>");
								fwrite($fp, $t________."<MaQSTBetragPflichtig>".number_format($MaQSTBetragPflichtig, 2, '.', "'")."</MaQSTBetragPflichtig>");
								fwrite($fp, $t________."<MaQSTBetragAbzug>".number_format($MaQSTBetragAbzug, 2, '.', "'")."</MaQSTBetragAbzug>");
								fwrite($fp, $t_______."</Mitarbeiter>");
								$QSTGemeindeTotalPflichtig	+= $MaQSTBetragPflichtig;
								$QSTGemeindeTotalAbzug 	+= $MaQSTBetragAbzug;
							}//end if Postleitzahl
						}//end foreach  Mitarbeiter
						fwrite($fp, $t______."</MitarbeiterList>");
						fwrite($fp, $t______."<QSTGemeindeTotalPflichtig>".number_format($QSTGemeindeTotalPflichtig, 2, '.', "'")."</QSTGemeindeTotalPflichtig>");
						fwrite($fp, $t______."<QSTGemeindeTotalAbzug>".number_format($QSTGemeindeTotalAbzug, 2, '.', "'")."</QSTGemeindeTotalAbzug>");
						fwrite($fp, $t_____."</Gemeinde>");
						$QSTKantonTotalPflichtig	+=	$QSTGemeindeTotalPflichtig;
						$QSTKantonTotalAbzug		+=	$QSTGemeindeTotalAbzug;
					}//end if
				}//end foreach Gemeinde
				
				$QSTKantonTotalAbzugProvision = $QSTKantonTotalAbzug * 0.01 * $Kt["commission"];
				$QSTKantonTotalAbzugNachProvision = $QSTKantonTotalAbzug - $QSTKantonTotalAbzugProvision;
							
				fwrite($fp, $t____."</GemeindeList>");
				fwrite($fp, $t____."<QSTKantonTotalPflichtig>".number_format($QSTKantonTotalPflichtig, 2, '.', "'")."</QSTKantonTotalPflichtig>");
				fwrite($fp, $t____."<QSTKantonTotalAbzug>".number_format($QSTKantonTotalAbzug, 2, '.', "'")."</QSTKantonTotalAbzug>");
				fwrite($fp, $t____."<ProvisionProzent>".number_format($Kt["commission"], 2, '.', "'")."</ProvisionProzent>");
				fwrite($fp, $t____."<QSTKantonTotalAbzugProvision>".number_format($QSTKantonTotalAbzugProvision, 2, '.', "'")."</QSTKantonTotalAbzugProvision>");
				fwrite($fp, $t____."<QSTKantonTotalAbzugNachProvision>".number_format($QSTKantonTotalAbzugNachProvision, 2, '.', "'")."</QSTKantonTotalAbzugNachProvision>");
				fwrite($fp, $t___."</Kanton>");
				$QSTCompanyTotalPflichtig			+=	$QSTKantonTotalPflichtig;
				$QSTCompanyTotalAbzug				+=	$QSTKantonTotalAbzug;
				$QSTCompanyTotalAbzugProvision		+=	$QSTKantonTotalAbzugProvision;
				$QSTCompanyTotalAbzugNachProvision	+=	$QSTKantonTotalAbzugNachProvision;
			}//end if 
		}//end foreach Kanton
		fwrite($fp, $t__."</KantonList>");
		fwrite($fp, $t__."<QSTCompanyTotalPflichtig>".number_format($QSTCompanyTotalPflichtig, 2, '.', "'")."</QSTCompanyTotalPflichtig>");
		fwrite($fp, $t__."<QSTCompanyTotalAbzug>".number_format($QSTCompanyTotalAbzug, 2, '.', "'")."</QSTCompanyTotalAbzug>");
		fwrite($fp, $t__."<QSTCompanyTotalAbzugProvision>".number_format($QSTCompanyTotalAbzugProvision, 2, '.', "'")."</QSTCompanyTotalAbzugProvision>");
		fwrite($fp, $t__."<QSTCompanyTotalAbzugNachProvision>".number_format($QSTCompanyTotalAbzugNachProvision, 2, '.', "'")."</QSTCompanyTotalAbzugNachProvision>");
		fwrite($fp, $t_."</Company>");
		$QSTReportTotalPflichtig			+=	$QSTCompanyTotalPflichtig;
		$QSTReportTotalAbzug				+=	$QSTCompanyTotalAbzug;
		$QSTReportTotalAbzugProvision		+=	$QSTCompanyTotalAbzugProvision;
		$QSTReportTotalAbzugNachProvision	+=	$QSTCompanyTotalAbzugNachProvision;
	}//end foreach Firma/Company
	fwrite($fp, $t."</CompanyList>");
	fwrite($fp, $t."<QSTReportTotalPflichtig>".number_format($QSTReportTotalPflichtig, 2, '.', "'")."</QSTReportTotalPflichtig>");
	fwrite($fp, $t."<QSTReportTotalAbzug>".number_format($QSTReportTotalAbzug, 2, '.', "'")."</QSTReportTotalAbzug>");
	fwrite($fp, $t."<QSTReportTotalAbzugProvision>".number_format($QSTReportTotalAbzugProvision, 2, '.', "'")."</QSTReportTotalAbzugProvision>");
	fwrite($fp, $t."<QSTReportTotalAbzugNachProvision>".number_format($QSTReportTotalAbzugNachProvision, 2, '.', "'")."</QSTReportTotalAbzugNachProvision>");
	fwrite($fp, "\n</Report>\n\n");
	
	$fm->fclose();

	chdir($newTmpPath);	

	system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."QuellensteuerAbrechnung.xslt ./data.xml > ./compileme.tex");	
	//Zwei Mal aufrufen wegen der Seitenzahl X (-->"Seite 1 von X")
	system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
	system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
	
	system("chmod 666 *");
	
	return $newTmpDirName;
}


public function getQSTCodeLookup() {
	$sql = 
	"SELECT DedAtSrcCanton FROM payroll_das_tax_rates
	group by DedAtSrcCanton
	order by DedAtSrcCanton";
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$kantonArr = array();
	$ktHTML = array();
	foreach ($result as $value) {
		$kantonArr[] =	$value["DedAtSrcCanton"];
		$sql = "SELECT DedAtSrcCode FROM payroll_das_tax_rates
				where DedAtSrcCanton = '".$value["DedAtSrcCanton"]."'
				group by DedAtSrcCode
				order by DedAtSrcCode";
				$res = $system_database_manager->executeQuery($sql, "");
				$codeArr = array();
				foreach ($res as $val) {
					$codeArr[] = "<a href='#' onclick='js_transQSTcd(\"".$value["DedAtSrcCanton"]."\", \"".$val["DedAtSrcCode"]."\")'>".$val["DedAtSrcCode"]."</a>";
				}				
		$ktHTML[] =	"<h3 style='padding-left:30px;'>".$value["DedAtSrcCanton"]."</h3><div>".implode(", ", $codeArr)."</div>";
	}
	
	$s = implode(" ", $ktHTML);
	
	return $s;
}

}//end class
?>
