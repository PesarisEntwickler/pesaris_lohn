<?php
class payroll_quellensteuerAbrechnung {
/* * * * * * * * * * * * * * * * * * * * * * * * 
 *  Quellensteuer = DeductionAtSource = "das" = "DedAtSrc"  
 *  
 *  XML Struktur ~(DTD):
 * 	Header
 * 		Firma +
 * 			FirmaName
 * 			FirmaAdresse
 * 			FirmaPlzOrt
 * 			KontaktpersonName
 * 			KontaktpersonTel
 * 			KontaktpersonEMail
 * 			Kanton +
 * 				ProvisionProzent
 * 				Arbeitgebernummer
 * 				Gemeinde +
 * 					Person +
 * 						Perioden +
 * 							EmplNummer
 * 							EmplName
 * 							EmplAHV-Nummer
 * 							EmplZivilstand
 * 							EmplAnzahlKinder
 * 							Tarif +
 * 								PeriodeVon
 * 								PeriodeBis
 * 								PeriodeTarif +
 * 									Code
 * 									Status (E=Eintritt, TW=Tarifwechsel)
 * 									DatumBeginn
 * 									DatumEnde
 * 									BetragBruttolohn
 * 									BetragPflichtig
 * 									BetragZulagen
 * 									BetragAbzug
 * 									ProzenteAbzug
 * 
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
	return  $PeriodIDs;
}

function getBetroffeneMitarbeiter($PeriodIDList, $QstAcounts) {
	$sql =
"SELECT * FROM payroll_calculation_entry
WHERE payroll_period_ID IN (".implode(",", $PeriodIDList).")
AND payroll_account_ID  IN (".implode(",", $QstAcounts).")
GROUP BY payroll_employee_ID";
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$EmployeeIDs = array();
	foreach ($result as $value) {
		$EmployeeIDs[]	=	$value["payroll_employee_ID"];
	}
	return $EmployeeIDs;
}

function getBetroffeneMaDetailsGemeindenKantone($CompanyID, $EmployeeIDList) {
	$andWhere = "";
	if ($CompanyID != 0) {
		$andWhere = " AND payroll_company_ID = ".$CompanyID;
	}
	$sql =
"SELECT * FROM payroll_employee
WHERE id IN (".implode(",", $EmployeeIDList).")".
$andWhere.";";
//communication_interface::alert("getBetroffeneMaDetailsGemeindenKantone:\n".$sql."\n\n");
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	$Company	= array();
	$Kantone 	= array();
	$Gemeinden 	= array();
	$MaDetails 	= array();
	foreach ($result as $value) {
		$Company[]		= $value["payroll_company_ID"];
		$Kantone[]		= $value["payroll_company_ID"]."#".$value["ResidenceCanton"];/* #0-#1 */
		$Gemeinden[]	= $value["payroll_company_ID"]."#".$value["ResidenceCanton"]."#".$value["ZIP-Code"]."#".$value["City"];/* #0-#3 */
		$MaDetails[]	= $value["payroll_company_ID"]."#".$value["ResidenceCanton"]."#".$value["ZIP-Code"]."#".$value["City"]."#".$value["id"]/* #0-#4 */
 					 ."#".$value["EmployeeNumber"]."#".$value["Lastname"]."#".$value["Firstname"]."#".$value["PlaceOfOrigin"]."#".$value["Age"]/* #5-#9 */
 					 ."#".$value["AHV-AVS-Number"]."#".$value["DateOfBirth"]."#".$value["Sex"]."#".$value["CivilStatus"]."#".$value["SingleParent"]/* #10-#14 */
 					 ."#".$value["WageCode"]."#".$value["BaseWage"]."#".$value["EmploymentStatus"]."#".$value["AttendedTimeCode"]."#".$value["AttendedTimeHours"]/* #15-#19 */
					 ."#".$value["EmploymentPercentage"]."#".$value["DedAtSrcMode"]."#".$value["DedAtSrcCode"]."#".$value["DedAtSrcPercentage"]."#".$value["DedAtSrcCompany"]."#".$value["Department"]."#".$value["SV-AS-Number"];/*#20-#26 */
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

function getAnzahlKinder($MaID, $Jahr, $Von, $Bis) {
	$V = str_pad($Von,2,"0",STR_PAD_LEFT );
	$B = str_pad($Bis,2,"0",STR_PAD_LEFT );
	$MaxTag = "31";
	switch ($Bis) {
		case 2:		$MaxTag = "29"; break;
		case 4:		$MaxTag = "30"; break;
		case 6:		$MaxTag = "30"; break;
		case 9:		$MaxTag = "30"; break;
		case 11:	$MaxTag = "30"; break;
	}	
	$system_database_manager = system_database_manager::getInstance();
	$sql = 
	"SELECT count(*) AS Anzahl FROM payroll_employee_children 
	WHERE payroll_employee_ID = '".$MaID."'
	AND (DateFrom  BETWEEN '".$Jahr."-".$V."-01' AND '".$Jahr."-".$B."-".$MaxTag."')
	AND (DateTo    BETWEEN '".$Jahr."-".$V."-01' AND '".$Jahr."-".$B."-".$MaxTag."'); ";	
	$result = $system_database_manager->executeQuery($sql, "");
	$Anzahl = 0;
	if (count($result)>0) {
		$Anzahl = $result[0]["Anzahl"];
	}
	return $Anzahl;
}

function getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, $Typ) {
	switch ($Typ) {
		case "BruttoLohn":	$AccountTyp = 19;	break;
		case "Zulagen":		$AccountTyp = 18;	break;
		case "QSTPflichtig":$AccountTyp = 5;	break;
		case "QSTAbzug": 	$AccountTyp = 7;	break;
	}

	$sql = 
	"SELECT min(P.major_period) as Min, max(P.major_period) as Max, sum(amount) AS S, code, count(code) AS CC 
	FROM payroll_das_account AS A, payroll_calculation_entry AS CE, payroll_period AS P
	WHERE A.payroll_account_ID = CE.payroll_account_ID
	AND CE.payroll_period_ID = P.id
	AND CE.payroll_employee_ID = ".$MaID."
	AND P.payroll_year_ID = ".$Jahr."
	AND P.major_period BETWEEN ".$Von." AND ".$Bis."
	AND A.AccountType = ".$AccountTyp."
	GROUP BY code";
	
	$Min = $Von;
	$Max = $Bis;
	$S = 0;
	$code = "";
	$CC = 0;
	$system_database_manager = system_database_manager::getInstance();
	$result = $system_database_manager->executeQuery($sql, "");
	if (count($result)>0) {
		$S = $result[0]["S"];
		$Min = $result[0]["Min"];
		$Max = $result[0]["Max"];
		$code = $result[0]["code"];
		$CC = $result[0]["CC"];
	}
	return array("vonPeriode"=>$Min,"bisPeriode"=>$Max,"Summe"=>$S,"Code"=>$code,"CountCode"=>$CC);
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
	$AttrSumme = 0;
	foreach ($result as $r) {
		$AttrSumme += $r[$Attribut];
	}
	return $AttrSumme;
}

function getQuellensteuerAbrechnung($param) {
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

	$Firma	=	$param["company"];
	$Jahr	=	$param["year"];
	$Von	=	$param["von"];
	$Bis	=	$param["bis"];

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
	$EmployeeIDList = $this->getBetroffeneMitarbeiter($PeriodIDList, $QstAcounts["data"]);
	$EmplGdeKant = $this->getBetroffeneMaDetailsGemeindenKantone($Firma, $EmployeeIDList);
	
//	$countCmp	=	count($EmplGdeKant["Company"]);
//	if ($countCmp < 1 ) {
//		communication_interface::alert("Keine betroffenen Mitarbeiter für diese Firma/Company ".count($EmplGdeKant["Company"]));
		//break;
//	}
	communication_interface::alert("Resultate der Vorberechnung: "
			."Firma $Firma, Jahr $Jahr, Von $Von, Bis $Bis "
			."\n=========================================="
			."\nPeriodIDList:".implode(",", $PeriodIDList)
			."\nQstAccounts :".implode(",", $QstAcounts["data"])
			."\nEmployees   :".implode(",", $EmployeeIDList)
			//."\nEmployes    :".implode(",", $EmplGdeKant["MaDetails"])
			."\nGemeinden   :".implode(",", $EmplGdeKant["Gemeinden"])
			."\nKantone     :".implode(",", $EmplGdeKant["Kantone"])
			."\nFirmen Nr   :".implode(",", $EmplGdeKant["Company"])  );

	$QSTReportTotalPflichtig	= 0;
	$QSTReportTotalAbzug		= 0;
	$QSTReportTotalAbzugProvision	= 0;
	$QSTReportTotalAbzugNachProvision	= 0;
	
	fwrite($fp, $t."<CompanyList>");
	foreach ($EmplGdeKant["Company"] as $CompanyID) {
		$QSTCompanyTotalPflichtig	= 0;
		$QSTCompanyTotalAbzug		= 0;
		$QSTCompanyTotalProvision	= 0;
		$QSTCompanyTotalAbzugProvision	= 0;
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
			$QSTKantonTotalPflichtig 	= 0;
			$QSTKantonTotalAbzug 		= 0;
			$QSTKantonTotalAbzugProvision=0;
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
					$QSTGemeindeTotalPflichtig	= 3;
					$QSTGemeindeTotalAbzug 	= 1;
					$Gde = explode("#",$Gemeinde);
					$GdeFirmenID = $Gde[0];
					$GdeKantonID = $Gde[1];
					$GdePostLeitZahl = $Gde[2];
					if ($GdeKantonID == $KantonID && $GdeFirmenID == $FirmenID) {
						fwrite($fp, $t_____."<Gemeinde>");
						fwrite($fp, $t______."<Name>".$Gde[2]." ".$Gde[3]."</Name>");
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
							$MaQSTCode		= $Ma[22];
							$MaQSTProz 		= $Ma[23];
							$MaSVASNummer	= $Ma[26];
							if ($MaPostLeitZahl == $GdePostLeitZahl && $MaKantonID == $GdeKantonID && $MaFirmenID == $GdeFirmenID) {
								if (strlen(trim($MaSVASNummer)) > 2) {
									$MaAHVNummer = $MaSVASNummer;
								}
								$MaKinder	= $this->getAnzahlKinder($MaID, $Jahr, $Von, $Bis);
								$MaQSTBetragBruttoLohn	= $this->getSummierteLohnart($MaID, $Jahr, $Von, $Bis, "5000", "amount");//5000[amount] ist BruttoLohn
								$MaQST 					= $this->getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, "QSTPflichtig");
								$MaQSTBetragPflichtig	= $MaQST["Summe"];
								$codeBeginMonat			= $MaQST["vonPeriode"];
								$codeEndetMonat			= $MaQST["bisPeriode"];
								$TW = "";
								if ($Von < $codeBeginMonat) {$TW.="E(".substr("0".$codeBeginMonat,-2).".".$Jahr.")".$MaQST["Code"]." ";}
								if ($Bis > $codeEndetMonat) {$TW.="TW(".substr("0".$codeEndetMonat,-2).".".$Jahr.")";}
								
								$MaQSTPeriodeVonBis		= substr("0".$codeBeginMonat,-2)."-".substr("0".$codeEndetMonat,-2);//Echte betroffene Periode des MA innerhalb der Report-Periode
								$MaQST 					= $this->getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, "Zulagen");
								$MaQSTBetragZulagen		= $MaQST["Summe"];
								$MaQST 					= $this->getQSTPeriodenSumme($MaID, $Jahr, $Von, $Bis, "QSTAbzug");
								$MaQSTBetragAbzug		= $MaQST["Summe"];
								
								fwrite($fp, $t_______."<Mitarbeiter>");
								fwrite($fp, $t________."<MaName>".$MaName."</MaName>");
								fwrite($fp, $t________."<MaAHVNummer>".$MaAHVNummer."</MaAHVNummer>");
								fwrite($fp, $t________."<MaGeboren>".$MaGeboren."</MaGeboren>");
								fwrite($fp, $t________."<MaSex>".$MaSex."</MaSex>");
								fwrite($fp, $t________."<MaZivilstand>".$MaZivilstand."</MaZivilstand>");
								fwrite($fp, $t________."<MaKinder>".$MaKinder."</MaKinder>");
								fwrite($fp, $t________."<MaQSTCode>".$MaQSTCode."</MaQSTCode>");
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
				fwrite($fp, $t____."<ProvisionProzent>".number_format($Kt["commission"], 2, '.', "'")." %</ProvisionProzent>");
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
	
/*
	$result = $system_database_manager->executeQuery("SELECT id FROM payroll_period WHERE payroll_year_ID=".$Jahr." AND major_period=".$Von." AND minor_period=".$minorPeriod, "payroll_report_CalculationJournal");
	if(count($result)>0) $payrollPeriodID = $result[0]["id"];
	else return;

	$isCurrentPeriod = false;
	$sql = "SELECT payroll_period_ID FROM payroll_calculation_current LIMIT 1";
	$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
	if(count($result)>0) $isCurrentPeriod = $result[0]["payroll_period_ID"]==$payrollPeriodID ? true : false;

	$sql = "SELECT
	  empl.id as EmployeeID
	, empl.EmployeeNumber
	, empl.Firstname
	, empl.Lastname
	, empl.payroll_company_ID
	, empl.CodeAHV
	, empl.CodeALV
	, empl.CodeUVG
	, empl.CodeUVGZ1
	, empl.CodeUVGZ2
	, empl.CodeBVG
	, empl.CodeKTG
	, empl.EmploymentStatus
	, acc.id as AccountNumber
	, acclbl.label
	, calc.quantity
	, calc.rate
	, calc.amount
	, calc.code
	FROM ".($isCurrentPeriod ? "payroll_calculation_current" : "payroll_calculation_entry")." calc
	INNER JOIN payroll_employee empl ON empl.id=calc.payroll_employee_ID
	INNER JOIN payroll_account acc ON acc.id=calc.payroll_account_ID
	AND acc.payroll_year_ID=calc.payroll_year_ID
	INNER JOIN payroll_account_label acclbl ON acclbl.payroll_account_ID=acc.id
	AND acclbl.payroll_year_ID=acc.payroll_year_ID
	AND acclbl.language='".session_control::getSessionInfo("language")."'
	WHERE calc.payroll_period_ID=".$payrollPeriodID."
	ORDER BY empl.Lastname, empl.Firstname, calc.payroll_employee_ID, acc.id";
	$result = $system_database_manager->executeQuery($sql, "payroll_report_CalculationJournal");
	$lastEmployeeID = 0;
	$entryCollector = array();
	$singleEmployeeData = "";
	fwrite($fp,"<Employees>\n");
	foreach($result as $row) {
		if($row["EmployeeID"] != $lastEmployeeID) {
			//the employee changed!
			if($singleEmployeeData != "") {
				//there are data for writing to the XML file
				//fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
				fwrite($fp, $singleEmployeeData.str_replace(array("&","%","#"), array("\\&","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
			}
			$lastEmployeeID = $row["EmployeeID"];
			$entryCollector = array();
			$singleEmployeeData =
			"\t\t<Employee>
		<EmployeeNumber>".$row["EmployeeNumber"]."</EmployeeNumber>
		<CompanyID>".$row["payroll_company_ID"]."</CompanyID>
		<Firstname>".$row["Firstname"]."</Firstname>
		<Lastname>".$row["Lastname"]."</Lastname>
		<CodeAHV>".$row["CodeAHV"]."</CodeAHV>
		<CodeALV>".$row["CodeALV"]."</CodeALV>
		<CodeKTG>".$row["CodeKTG"]."</CodeKTG>
		<CodeUVG>".$row["CodeUVG"]."</CodeUVG>
		<CodeBVG>".$row["CodeBVG"]."</CodeBVG>
		<Status>".$row["EmploymentStatus"]."</Status>
		<Entries>\n";
		}
		$entryCollector[] = "
		<Entry>
			<AccountNumber>".$row["AccountNumber"]."</AccountNumber>
			<AccountName>".$row["label"]."</AccountName>
			<quantity>".$row["quantity"]."</quantity>
			<rate>".$row["rate"]."</rate>
			<amount>".$row["amount"]."</amount>".
			($row["code"]=="" ? "" : "
			<code>".$row["code"]."</code>")."
		</Entry>\n";
	}
	if($singleEmployeeData != "") {
		//there are still a few more data for writing to the XML file
		fwrite($fp, $singleEmployeeData.str_replace(array("&","_","%","#"), array("\\&","\\_","\\%","\\#"), implode("",$entryCollector))."\t\t\t</Entries>\n\t\t</Employee>\n");
	}
	fwrite($fp, "\t</Employees>\n</Report>\n");
*/	
	
	$fm->fclose();

	chdir($newTmpPath);

	system($aafwConfig["paths"]["utilities"]["xsltproc"]." ".$aafwConfig["paths"]["reports"]["templates"]."QuellensteuerAbrechnung.xslt ./data.xml > ./compileme.tex");
	system($aafwConfig["paths"]["utilities"]["pdflatex"]." -interaction=batchmode compileme.tex > ".$aafwConfig["paths"]["utilities"]["stdout"]);
	system("chmod 666 *");

	return $newTmpDirName;
}




}//end class
?>
