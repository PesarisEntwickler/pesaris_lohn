<?php
require_once('payroll_auszahlen.php');
class auszahlfiles {
	
	public function setAuszahlFiles($ZahlstellenID, $Personenkreis, $nurZahlstelleBeruecksichtigen, $arrDueDate, $periodeID) {
		require_once(getcwd()."/web/fpdf17/fpdf.php");
        require_once(getcwd()."/kernel/common-functions/configuration.php");
		
		$auszahlen = new auszahlen();
		
		$dueDateGUI = $arrDueDate[0].".".$arrDueDate[1].".".$arrDueDate[2];
		$dtaValutaDate = substr($arrDueDate[2], 2).$arrDueDate[1].$arrDueDate[0];


		$dtaFileName_CHF = "";
		$dtaJournalFileName_CHF = "";
		$dtaRekapFileName_CHF = "";
			
		//Einschraenkung mit dem Personenfilter
		$emplFilter = "";
		$dtaPersKreisFileName = "";
		$zs = "";

		$fileNamePrefix = "std_CHF_";
		if ($nurZahlstelleBeruecksichtigen) {
			$fileNamePrefix = "z".$ZahlstellenID."_CHF_";
			$empArray = $auszahlen->getPeriodZahlstellenMitarbeiterListe($periodeID, $ZahlstellenID, "");
			$empListe = implode(",",$empArray);
			$emplFilter = " AND emp.id IN (".$empListe.")";
			$zs = $ZahlstellenID;			
		} else {
			if (substr($Personenkreis."xx",0,1)!="*") {			
				$dtaPersKreisFileName = "_p".str_replace(",", "p", $Personenkreis);
				$emplFilter = $auszahlen->getEmployeeFilters($Personenkreis);
				$arrEmpl = $auszahlen->getEmpl_Personenkreis_FromTrackingTable($emplFilter);
				//communication_interface::alert("emplFilter:".print_r($emplFilter, true)."\narrEmpl:".print_r($arrEmpl, true));			
			} else {
				$arrEmpl = $auszahlen->getEmplFromTrackingTable();
			}
			//communication_interface::alert("dtaPersKreisFileName:".$dtaPersKreisFileName."\nemplFilter:".print_r($emplFilter, true)."\nempl:".print_r($arrEmpl, true));			
			$uniqueEmployeeList = implode(",",array_unique($arrEmpl));//wenn Mehrfach-Nennungen auftauchen, werden diese singularisiert
			$empArray = $auszahlen->getPeriodZahlstellenMitarbeiterListe($periodeID, -1, $uniqueEmployeeList);
			$empListe = implode(",",$empArray);
		}

		$employeeList = $auszahlen->getEmployeeDataCurrentPeriod("8000", $empListe);
		// communication_interface::alert("empListe: ".print_r($empListe, true));
		// return false;

		$bankSource = $auszahlen->getZahlstelle($ZahlstellenID);
		$iban = str_replace(" ", "", $bankSource["IBAN"]);		
		$ZahlstelleKonto = $iban;
		$ZahlstelleL1 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line1"] ));
		$ZahlstelleL2 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line2"] ));
		$ZahlstelleL3 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line3"] ));
		$ZahlstelleL4 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line4"] ));
		$companyID = $bankSource["company"];
		$zahlstellenWaehrung = $bankSource["currency"];
		
		//communication_interface::alert("Zahlstelle:".$ZahlstellenID." \nZS-Waehrung:".$zahlstellenWaehrung." \nDTAFileName:".$dtaFileName_CHF."\nperiodeID:".$periodeID."\nPersonenkreis:".$Personenkreis.print_r($bankSource,true));
		
		if (strlen($iban) < 3) {
			$dtaFileName_CHF = $fileNamePrefix.date("Y-m-d").$dtaPersKreisFileName.".dta";
			$dtaJournalFileName_CHF = $fileNamePrefix.date("Y-m-d").$dtaPersKreisFileName."_dtaJournal";
			$dtaRekapFileName_CHF = $fileNamePrefix.date("Y-m-d").$dtaPersKreisFileName.".rekap";;
		} else {
			$dtaFileName_CHF = $fileNamePrefix.$iban.$dtaPersKreisFileName.".dta";
			$dtaJournalFileName_CHF = $fileNamePrefix.$iban.$dtaPersKreisFileName."_dtaJournal";
			$dtaRekapFileName_CHF = $fileNamePrefix.$iban.$dtaPersKreisFileName.".rekap";;
		}
		$dtaFileName_EUR		= str_replace("CHF", "EUR",	$dtaFileName_CHF); 
		$dtaJournalFileName_EUR = str_replace("CHF", "EUR",	$dtaJournalFileName_CHF); 
		$dtaRekapFileName_EUR	= str_replace("CHF", "EUR",	$dtaRekapFileName_CHF);
		
		$dtaFileName_USD		= str_replace("CHF", "USD",	$dtaFileName_CHF); 
		$dtaJournalFileName_USD = str_replace("CHF", "USD",	$dtaJournalFileName_CHF); 
		$dtaRekapFileName_USD	= str_replace("CHF", "USD",	$dtaRekapFileName_CHF);

		//communication_interface::alert($dtaFileName_CHF."\n".$dtaJournalFileName_CHF."\n".$dtaRekapFileName_CHF."\n".$dtaFileName_EUR."\n".$dtaJournalFileName_EUR."\n".$dtaRekapFileName_EUR."\n".$dtaFileName_USD."\n".$dtaJournalFileName_USD."\n".$dtaRekapFileName_USD);
			
		//Die jetztige Periode ist
		$PeriodeDieserMonat = blFunctionCall('payroll.auszahlen.getActualPeriodName');
				
		$company = $auszahlen->getCompany($companyID);
		$AuftraggeberFirmaL1 = $company["name"];						//"Presida Testunternehmung";
		$AuftraggeberFirmaL2 = $company["str"];							//"Mitteldorfstrasse 37";
		$AuftraggeberFirmaL3 = $company["zip"]." ".$company["city"];	//"5033 Buchs";
		
		$dtaContent_CHF = "";	$dtaContent_EUR = "";	$dtaContent_USD = "";
		$dtaJournal_CHF = "";	$dtaJournal_EUR = "";	$dtaJournal_USD = "";
		$dtaRekap_CHF	= "";	$dtaRekap_EUR	= "";	$dtaRekap_USD	= "";	
		$trxNr_OverAll  = 0;
		$trxNr_CHF  = 0;		$trxNr_EUR  = 0;		$trxNr_USD  = 0;
		$splitt = 0;
		$splitTrx  = 0;
		$emplCount = 0;
		$SeitenNr_CHF  = 1;		$SeitenNr_EUR  = 1;		$SeitenNr_USD  = 1;	
		$SeitenTotal_CHF = 0;	$SeitenTotal_EUR = 0;	$SeitenTotal_USD = 0;
		$GesamtTotal_CHF = 0;	$GesamtTotal_EUR = 0;	$GesamtTotal_USD = 0;
		$anzFiles  = 0;
		$anzZeilenSeite01 = 24; 
		$anzZeilenSeiteFF = 27;
		$SeitenNrRekap = 1;		
  		$RealEffectedEmployees = "";
  		//Iteration über alle Mitarbeiter mit Auszahlung 
		foreach ( $employeeList['data'] as $row ) { 
	  		$account_ID = "9999";
			$employee_ID = $row['id'];
	  		$retTracking = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
	  		$availAmt = $retTracking["amount_available"];
	  		if (floatval($availAmt) > 0) {
		  		$lastTracked_processing_order = $retTracking["processing_order"];
				$emplCount++;    
				$RealEffectedEmployees .= ",".$employee_ID;
				$employeeNumber = $row['EmployeeNumber'];
				
				//Beneficiary Linien 1-4 (Name & Adresse) werden präventiv gesetzt
				$bn1 = $auszahlen->replaceUmlaute( trim($row['Firstname']). " " . trim($row['Lastname']) );
				$bn2 = $auszahlen->replaceUmlaute( trim($row['Street']) );
				$bn3 = $auszahlen->replaceUmlaute( trim($row['AdditionalAddrLine1']). " " . trim($row['AdditionalAddrLine2']) );
				$bn4 = $auszahlen->replaceUmlaute( trim($row['ZIP-Code']). " " . trim($row['City']) );

				$hasSplit = array();
				if ($nurZahlstelleBeruecksichtigen) {
					$hasSplit = $auszahlen->getPaymentSplit($employee_ID, 0, $ZahlstellenID,$lastTracked_processing_order, "");			
				} else {
					$hasSplit = $auszahlen->getPaymentSplit($employee_ID, 0, 0, $lastTracked_processing_order, "");			
				}
				
				if($hasSplit["data"]>0) { //Wenn dieser Mitarbeiter einen Zahlungssplitt hinterlegt hat
					$max_count = count($hasSplit["data"]);
					for ( $index = 0, $max_count ; $index < $max_count; $index++ ) {
				  		$retTracking = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
				  		$availAmt = $retTracking["amount_available"];
				  		$lastTracked_processing_order = $retTracking["processing_order"];
						$splitID     			= $hasSplit["data"][$index]["id"];
						$processingOrder		= $hasSplit["data"][$index]["processing_order"];
						$account_ID  			= $hasSplit["data"][$index]["payroll_account_ID"];
						$bankDestID  			= $hasSplit["data"][$index]["payroll_bank_destination_ID"];
						$splitMode 				= $hasSplit["data"][$index]["split_mode"];
						$splitPayAmount	 		= $hasSplit["data"][$index]["amount"];
						$splitPayCurrency		= $hasSplit["data"][$index]["payroll_currency_ID"];
						$payAmountArray = $auszahlen->calcSplitAmount( $splitMode
																	  ,$splitPayAmount
																	  ,$availAmt
																	  ,$splitPayCurrency
																	  ,$zahlstellenWaehrung
																	  ,$employee_ID
																	  ,$account_ID);
						$payCurrency  = $payAmountArray["payCurrency"];																  
						$payAmount	  = $payAmountArray["payAmountForeignCurrency"];
						$payAmountCHF = $payAmountArray["payAmountSystemCurrencyCHF"];
						
//if ($payCurrency != "CHF")	{
//if ($employee_ID > 1)	{
//	communication_interface::alert(
//		"---for (split) ---\nemployeeNumber:".$employeeNumber
//		."\n".$bn1
//		."\nif (floatval(availAmt=".$availAmt.") >= floatval(payAmountCHF=".$payAmountCHF."))"
//		."\nsplitPayAmount:".$splitPayAmount
//		." splitPayCurrency:".$splitPayCurrency
//		."\npayAmountCHF:".$payAmountCHF
//		."\npayAmount:".$payCurrency.": ".$payAmount
//		."\navailAmt(CHF):".$availAmt
//		."\nemployee_ID:".$employee_ID
//		."\nsplitID:    ".$splitID
//		."\nbankDestID:".$bankDestID
//		."\nZahlstelle:".$ZahlstellenID." / ".$zahlstellenWaehrung
//		);
//}				
						if (floatval($availAmt) >= floatval($payAmountCHF)) {						
							$bene = $auszahlen->getDestinationBankAccount($employee_ID, $bankDestID, "N");
							if (strlen(trim($bene['beneAddress1'])) > 2) {
								$bn1 = $auszahlen->replaceUmlaute( trim($bene['beneAddress1']) );
								$bn2 = $auszahlen->replaceUmlaute( trim($bene['beneAddress2']) );
								$bn3 = $auszahlen->replaceUmlaute( trim($bene['beneAddress3']) );
								$bn4 = $auszahlen->replaceUmlaute( trim($bene['beneAddress4']) );
							}
							$benIBAN = str_replace(" ", "", $bene['bank_account']);
							$beneBank1 = $auszahlen->replaceUmlaute( $bene['beneBank1'] );
							$beneBank2 = $auszahlen->replaceUmlaute( trim($bene['beneBank2'])." ".trim($bene['beneBank3']) );
							$beneBank3 = $auszahlen->replaceUmlaute( $bene['beneBank4'] );
							if (strlen($beneBank1) < 3) {
								$beneBank1 = trim($auszahlen->replaceUmlaute($bene['beneBankDescription']));
							}
							$beneBankSWIFT 	   = $bene['beneBankSWIFT'];
							$spesenregelung    = $bene['spesenregelung'];
							$endBeguenst1_IBAN = $bene['beneEndbeguenst1'];
							$endBeguenst2_Nam  = $bene['beneEndbeguenst2'];
							$endBeguenst3_Adr  = $bene['beneEndbeguenst3'];
							$endBeguenst4_Ort  = $bene['beneEndbeguenst4'];
							
							//DTA bei Splitt
							
							//DTA Journal
							$split_mode = 1;
							if (strlen($account_ID) < 1) {
								$account_ID = "9999";
							}
							switch ( $payCurrency ) {
								case "USD":
									$splitt++;
									$splitTrx++;
									$trxNr_USD++;
									$trxNr_OverAll++;			
									$SeitenTotal_USD += $payAmount;
									$GesamtTotal_USD += $payAmount;
									if ($trxNr_USD == 1) {
										$dtaJournal_USD .= $this->setDTAJournalHeader("USD", $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
									}										
									if ($trxNr_USD == $anzZeilenSeite01 || ($trxNr_USD-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
										$SeitenNr_USD++;
										$dtaJournal_USD .= $this->setDTAJournalSeitenTotal("USD",$SeitenTotal_USD);
										$dtaJournal_USD .= $this->setDTAJournalFollowHeader("USD",$SeitenNr_USD, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
										$SeitenTotal_USD = 0;
									}
									$dtaJournal_USD .= $this->setDTAJournalZeile($trxNr_USD, $employeeNumber, $bn1, $bn2, $bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $payAmount, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
									$dtaContent_USD .= $this->setDTAZeile(  $dtaValutaDate, $trxNr_USD, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);

									// die systemseitige Lohnkontrolle wird in CHF gemacht
									$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, $splitID, $processingOrder, $availAmt-$payAmountCHF);
									$auszahlen->setEffectifPayoutTable($periodeID,$employee_ID,$splitID,$payAmountCHF,$payAmount,$payCurrency,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3);
									break;
									
								case "EUR":
									$splitt++;
									$splitTrx++;
									$trxNr_EUR++;
									$trxNr_OverAll++;			
									$SeitenTotal_EUR += $payAmount;
									$GesamtTotal_EUR += $payAmount;
									if ($trxNr_EUR == 1) {
										$dtaJournal_EUR .= $this->setDTAJournalHeader("EUR", $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
									}										
									if ($trxNr_EUR == $anzZeilenSeite01 || ($trxNr_EUR-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
										$SeitenNr_EUR++;
										$dtaJournal_EUR .= $this->setDTAJournalSeitenTotal("EUR", $SeitenTotal_EUR);
										$dtaJournal_EUR .= $this->setDTAJournalFollowHeader("EUR",$SeitenNr_EUR, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
										$SeitenTotal_EUR = 0;
									}
									$dtaJournal_EUR .= $this->setDTAJournalZeile($trxNr_EUR, $employeeNumber, $bn1, $bn2, $bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $payAmount, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
									$dtaContent_EUR .= $this->setDTAZeile(  $dtaValutaDate, $trxNr_EUR, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);

									// die systemseitige Lohnkontrolle wird in CHF gemacht
									$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, $splitID, $processingOrder, $availAmt-$payAmountCHF);
									$auszahlen->setEffectifPayoutTable($periodeID,$employee_ID,$splitID,$payAmountCHF,$payAmount,$payCurrency,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3);
									break;
									
								default://CHF
									$splitt++;
									$splitTrx++;
									$trxNr_CHF++;
									$trxNr_OverAll++;			
									$SeitenTotal_CHF += $payAmountCHF;
									$GesamtTotal_CHF += $payAmountCHF;
									$payAmount        = $payAmountCHF;
									if ($trxNr_CHF == 1) {
										$dtaJournal_CHF .= $this->setDTAJournalHeader("CHF", $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
									}									
									if ($trxNr_CHF == $anzZeilenSeite01 || ($trxNr_CHF-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
										$SeitenNr_CHF++;
										$dtaJournal_CHF .= $this->setDTAJournalSeitenTotal("CHF",$SeitenTotal_CHF);
										$dtaJournal_CHF .= $this->setDTAJournalFollowHeader("CHF",$SeitenNr_CHF, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
										$SeitenTotal_CHF = 0;
									}
									$dtaJournal_CHF .= $this->setDTAJournalZeile($trxNr_CHF, $employeeNumber, $bn1, $bn2, $bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $payAmount, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
									$dtaContent_CHF .= $this->setDTAZeile(  $dtaValutaDate, $trxNr_CHF, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);

									// die systemseitige Lohnkontrolle wird in CHF gemacht
									$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, $splitID, $processingOrder, $availAmt-$payAmountCHF);
									$auszahlen->setEffectifPayoutTable($periodeID,$employee_ID,$splitID,$payAmountCHF,$payAmountCHF,$payCurrency,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3);		
									break;
							}//end switch ( $payCurrency )
		
							//Abhaken Mitarbeiter mit Lohnbezug
							$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, $index);
						}//end if (floatval($availAmt) >= floatval($splitPayAmount))
											
						//Abhaken Mitarbeiter mit Lohnbezug
						$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, "Y");
					} // end for	
				} // end if($hasSplit["data"]>0)
				
				
				//Employee ohne Splitt 										--> Haben nur eine Standardbank
				//oder wenn nach dem Splitt jetzt noch was übrig ist 		--> Standardbank/Standardzahlungsweg benützen
				//bzw. wenn der letzte Splitt nicht erfüllt werden konnte	--> Standardbank/Standardzahlungsweg benützen
				//
				//Dies jedoch nicht, wenn "[z0] - Zahlstellen benutzen, die dem Mitarbeiter hinterlegt wurde" 
				//gewählt wurde und der Mitarbeiter diese aktuell im Splitt vorhandene 
				//Zahlstelle bei seiner Standardbank nicht hinterlegt hat.
		  		$retTracking = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
		  		$lastTracked_processing_order = $retTracking["processing_order"];
		  		$availAmt = $retTracking["amount_available"];
				if ($availAmt > 0.001) {
					$bene = $auszahlen->getStandardDestinationBankAccount($employee_ID);
					$banksourcezahlstelle = $bene['nonstandard_banksourcezahlstelle'];
					$beruecksichtigen = true;
					if ($nurZahlstelleBeruecksichtigen == true) {
						if ($banksourcezahlstelle != $ZahlstellenID) {
							$beruecksichtigen = false;
						}
					}
					if ($beruecksichtigen == true) {
						if (strlen(trim($bene['beneAddress1'])) > 2) {
							$bn1 = $auszahlen->replaceUmlaute( trim($bene['beneAddress1']) );
							$bn2 = $auszahlen->replaceUmlaute( trim($bene['beneAddress2']) );
							$bn3 = $auszahlen->replaceUmlaute( trim($bene['beneAddress3']) );
							$bn4 = $auszahlen->replaceUmlaute( trim($bene['beneAddress4']) );
						}
						$benIBAN = str_replace(" ", "", $bene['bank_account']);
						if (strlen(trim($bene['beneBank2']))> 0) {$beneBank2 = $bene['beneBank2']." ";}
						if (strlen(trim($bene['beneBank3']))> 0) {$beneBank2 = $beneBank2 . trim($bene['beneBank3']);}
						$beneBank1 = $auszahlen->replaceUmlaute( $bene['beneBank1'] );
						$beneBank2 = $auszahlen->replaceUmlaute( $beneBank2 );
						$beneBank3 = $auszahlen->replaceUmlaute( $bene['beneBank4'] );
						if (strlen($beneBank1) < 3) {
							$beneBank1 = trim($auszahlen->replaceUmlaute($bene['beneBankDescription']));
						}
						$beneBankSWIFT 		= $bene['beneBankSWIFT'];
						$spesenregelung   	= $bene['spesenregelung'];
						$endBeguenst1_IBAN 	= $bene['beneEndbeguenst1'];
						$endBeguenst2_Nam 	= $bene['beneEndbeguenst2'];
						$endBeguenst3_Adr 	= $bene['beneEndbeguenst3'];
						$endBeguenst4_Ort 	= $bene['beneEndbeguenst4'];
						
						$payCurrency 		= $bene['currency'];
						
						//DTA Journal
						$processingOrder = 999;
						$splitID = 999;
						if (strlen($account_ID) < 1) {
							$account_ID = "9998";
						}
						$split_mode = $bene['splitMode'];
						switch ( $payCurrency ) {
							case "USD":
								$Kurs = blFunctionCall("payroll.getCurrencyForexRate", $payCurrency);
								if (floatval($Kurs) == 0) {$Kurs=1;}
								$payAmountUSD = $availAmt / $Kurs;
								$trxNr_OverAll++;
								$trxNr_USD++;			
								if ($trxNr_USD == 1) {
									$dtaJournal_USD .= $this->setDTAJournalHeader("USD", $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
								}
									
								if ($trxNr_USD == $anzZeilenSeite01 || ($trxNr_USD-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
									$SeitenNr_USD++;
									$dtaJournal_USD .= $this->setDTAJournalSeitenTotal("USD",$SeitenTotal_USD);
									$dtaJournal_USD .= $this->setDTAJournalFollowHeader("USD",$SeitenNr_USD, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
									$SeitenTotal_USD = 0;
								}
								$SeitenTotal_USD += $payAmountUSD;
								$GesamtTotal_USD += $payAmountUSD;
								$dtaJournal_USD .= $this->setDTAJournalZeile($trxNr_USD, $employeeNumber, $bn1, $bn2, $bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $payAmountUSD, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
								$dtaContent_USD .= $this->setDTAZeile( $dtaValutaDate, $trxNr_USD, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmountUSD, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
								$auszahlen->setEffectifPayoutTable($periodeID,$employee_ID,$splitID,$availAmt,$payAmountUSD,$payCurrency,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3);
								break;
								
							case "EUR":
								$Kurs = blFunctionCall("payroll.getCurrencyForexRate", $payCurrency);
								if (floatval($Kurs) == 0) {$Kurs=1;}
								$payAmountEUR = $availAmt / $Kurs;
								$trxNr_OverAll++;
								$trxNr_EUR++;			
								if ($trxNr_EUR == 1) {
									$dtaJournal_EUR .= $this->setDTAJournalHeader("EUR", $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
								}
									
								if ($trxNr_EUR == $anzZeilenSeite01 || ($trxNr_EUR-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
									$SeitenNr_EUR++;
									$dtaJournal_EUR .= $this->setDTAJournalSeitenTotal("EUR", $SeitenTotal_EUR);
									$dtaJournal_EUR .= $this->setDTAJournalFollowHeader("EUR",$SeitenNr_EUR, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
									$SeitenTotal_EUR = 0;
								}
								$SeitenTotal_EUR += $payAmountEUR;
								$GesamtTotal_EUR += $payAmountEUR;
								$dtaJournal_EUR .= $this->setDTAJournalZeile($trxNr_EUR, $employeeNumber, $bn1, $bn2, $bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $payAmountEUR, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
								$dtaContent_EUR .= $this->setDTAZeile( $dtaValutaDate, $trxNr_EUR, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmountEUR, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
								$auszahlen->setEffectifPayoutTable($periodeID,$employee_ID,$splitID,$availAmt,$payAmountEUR,$payCurrency,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3);
								break;
								
							default://CHF
								$payAmountCHF = $auszahlen->rundungAuf5Rappen($availAmt);
								$trxNr_OverAll++;
								$trxNr_CHF++;			
								if ($trxNr_CHF == 1) {
									$dtaJournal_CHF .= $this->setDTAJournalHeader("CHF", $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
								}
									
								if ($trxNr_CHF == $anzZeilenSeite01 || ($trxNr_CHF-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
									$SeitenNr_CHF++;
									$dtaJournal_CHF .= $this->setDTAJournalSeitenTotal("CHF",$SeitenTotal_CHF);
									$dtaJournal_CHF .= $this->setDTAJournalFollowHeader("CHF",$SeitenNr_CHF, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
									$SeitenTotal_CHF = 0;
								}
								$SeitenTotal_CHF += $payAmountCHF;
								$GesamtTotal_CHF += $payAmountCHF;
								$dtaJournal_CHF .= $this->setDTAJournalZeile($trxNr_CHF, $employeeNumber, $bn1, $bn2, $bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $payAmountCHF, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
								$dtaContent_CHF .= $this->setDTAZeile( $dtaValutaDate, $trxNr_CHF, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmountCHF, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);
								$auszahlen->setEffectifPayoutTable($periodeID,$employee_ID,$splitID,$availAmt,$payAmountCHF,$payCurrency,$split_mode,$account_ID, $benIBAN, $beneBank1, $beneBank2, $beneBank3);
								break;
						}//switch ( $payCurrency )
						//Abhaken Mitarbeiter mit Lohnbezug
						$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, "Y");				
						$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, $splitID, $processingOrder, 0);
		
					}						
				}//if ($amount > 0.001) 				
			}//end if
		}//end foreach -- employee

		if (strlen($RealEffectedEmployees)>2) {
			if (substr($RealEffectedEmployees,0,1)==",") {
				$RealEffectedEmployees = substr($RealEffectedEmployees,1);
			}
	
			//Abhaken Mitarbeiter mit Lohnbezug
			if ($trxNr_OverAll > 0) {
				$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $RealEffectedEmployees, "Y");
			} 
		}

		//Abhaken Mitarbeiter ohne Lohnbezug (mit Lohn = 0 )
		if ($emplFilter == "") {
			$emplwithoutPaymentList = $auszahlen->getCalculationCurrentPeriodEmployeeList("8000", "amount < 0.001 ");
		} else {
			$emplwithoutPaymentList = $auszahlen->getMitarbeiterZurAuszahlung("8000", "amount < 0.001 ", "isFullPayed <> 'Y'",$emplFilter);
		}
		
		$emplwithoutPayment = $emplwithoutPaymentList["count"];
		$effectedNoPaymentEmployeeList = array();
		if ($emplwithoutPayment > 0) {
			foreach ( $emplwithoutPaymentList['data'] as $row ) {
				$effectedNoPaymentEmployeeList[] = $row['payroll_employee_ID'];
			}
			$effectedNoPaymentEmployeeList= implode(",",array_unique($effectedNoPaymentEmployeeList));//wenn Mehrfach-nennungen auftauchen, werden diese singularisiert
			$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $effectedNoPaymentEmployeeList, "0");
		}
				

		if ($trxNr_OverAll > 0) {		
			//Files speichern
			$fm = new file_manager();
			//$fullPath0 =  $fm->getFullPath();
			$fm->customerSpace()->setPath("/".AUSZAHLDIR)->makeDir();   
			$fm->customerSpace()->setPath("/".AUSZAHLDIR."/".$PeriodeDieserMonat);  
			$fm->customerSpace()->setPath("/".AUSZAHLDIR."/".$PeriodeDieserMonat)->makeDir(); 
			$fullPath =  $fm->getFullPath();
			
			//communication_interface::alert("trxNr_USD:".$trxNr_USD."\n"."trxNr_EUR:".$trxNr_EUR."\n"."trxNr_CHF:".$trxNr_CHF."\n");
			
			//Seite füllen, damit Rekap auf neuer Seite erscheint
			if ($trxNr_USD > 0) {
				if ($trxNr_USD % $anzZeilenSeite01 > 16) {
					$anzLinien = 0;
					if ($trxNr_USD < $anzZeilenSeite01) {
						$anzLinien = $anzZeilenSeite01 - $trxNr_USD;
					} else {
						$anzLinien = $anzZeilenSeiteFF - ($trxNr_USD % $anzZeilenSeiteFF);
					}
					for ($index = 0, $max_count = $anzLinien; $index < $anzLinien; $index++) {
						$dtaJournal_USD .= CRLF.CRLF.CRLF;// leere Zeilen um die Seite zu füllen
					}			
				}
				$dtaContent_USD .= $this->getDTATotalRecordTyp890($dtaValutaDate, $trxNr_USD, $GesamtTotal_USD);
				$dtaJournal_USD .= $this->setDTAJournalSeitenTotal("USD",$SeitenTotal_USD);
				$dtaRekap_USD 	 = $this->setDTAJournalRekap("USD", $GesamtTotal_USD, strtoupper($PeriodeDieserMonat), $trxNr_USD, $Personenkreis, $dtaFileName_USD, $emplFilter, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $splitt, $splitTrx, $emplCount);		
				$dtaJournal_USD	.= $dtaRekap_USD;
				
				$fm->setFile($dtaFileName_USD); 
				$fm->putContents($dtaContent_USD); 
				$anzFiles++;
			}
			if ($trxNr_EUR > 0) {
				if ($trxNr_EUR % $anzZeilenSeite01 > 16) {
					$anzLinien = 0;
					if ($trxNr_EUR < $anzZeilenSeite01) {
						$anzLinien = $anzZeilenSeite01 - $trxNr_EUR;
					} else {
						$anzLinien = $anzZeilenSeiteFF - ($trxNr_EUR % $anzZeilenSeiteFF);
					}
					for ($index = 0, $max_count = $anzLinien; $index < $anzLinien; $index++) {
						$dtaJournal_EUR .= CRLF.CRLF.CRLF;// leere Zeilen um die Seite zu füllen
					}			
				}
				$dtaContent_EUR .= $this->getDTATotalRecordTyp890($dtaValutaDate, $trxNr_EUR, $GesamtTotal_EUR);
				$dtaJournal_EUR .= $this->setDTAJournalSeitenTotal("EUR", $SeitenTotal_EUR);
				$dtaRekap_EUR 	 = $this->setDTAJournalRekap("EUR", $GesamtTotal_EUR, strtoupper($PeriodeDieserMonat), $trxNr_EUR, $Personenkreis, $dtaFileName_EUR, $emplFilter, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $splitt, $splitTrx, $emplCount);		
				$dtaJournal_EUR	.= $dtaRekap_EUR;
				
				$fm->setFile($dtaFileName_EUR); 
				$fm->putContents($dtaContent_EUR); 
				$anzFiles++;
			}
			if ($trxNr_CHF > 0) {
				if ($trxNr_CHF % $anzZeilenSeite01 > 16) {
					$anzLinien = 0;
					if ($trxNr_CHF < $anzZeilenSeite01) {
						$anzLinien = $anzZeilenSeite01 - $trxNr_CHF;
					} else {
						$anzLinien = $anzZeilenSeiteFF - ($trxNr_CHF % $anzZeilenSeiteFF);
					}
					for ($index = 0, $max_count = $anzLinien; $index < $anzLinien; $index++) {
						$dtaJournal_CHF .= CRLF.CRLF.CRLF;// leere Zeilen um die Seite zu füllen
					}			
				}
				$dtaContent_CHF .= $this->getDTATotalRecordTyp890($dtaValutaDate, $trxNr_CHF, $GesamtTotal_CHF);
				$dtaJournal_CHF .= $this->setDTAJournalSeitenTotal("CHF",$SeitenTotal_CHF);
				$dtaRekap_CHF 	 = $this->setDTAJournalRekap("CHF", $GesamtTotal_CHF, strtoupper($PeriodeDieserMonat), $trxNr_CHF, $Personenkreis, $dtaFileName_CHF, $emplFilter, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $splitt, $splitTrx, $emplCount);		
				$dtaJournal_CHF .= $dtaRekap_CHF;
				
				$fm->setFile($dtaFileName_CHF); 
				$fm->putContents($dtaContent_CHF); 
				$anzFiles++;
			}
			
			$fm->setFile($dtaRekapFileName_CHF); 
			$fm->putContents($dtaRekap_USD.$dtaRekap_EUR.$dtaRekap_CHF); 
			
			//REKAP
			$rekap = $this->setRekapHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $SeitenNrRekap++);
			$filelist = $fm->listDir(0);
			$y=0;  
			foreach($filelist as $fileNam) {
				if(strtolower(substr($fileNam,-6))==".rekap") {//Liest alle ".rekap"-Zwischenfiles
					$y++;
					if($y % 4 < 1){ 
						$rekap .= CRLF.CRLF.CRLF.CRLF.CRLF.CRLF.CRLF.CRLF.CRLF.CRLF;
						$rekap .= $this->setRekapHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $SeitenNrRekap);
					}
					$fm->setFile($fileNam);
					$rekap .= $fm->getContents($fileNam); 
					$rekap .= CRLF;
				} 
			}
			$fm->fclose(); 
			
			//Erzeugt PDF Dateien
			if ($trxNr_USD > 0) {
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->SetFont('Courier','',9);
				$pdf->MultiCell( 188, 3, $dtaJournal_USD , 0, 'L', 0); 
				$pdf->Output($fullPath.$dtaJournalFileName_USD.'.pdf', 'F');
				$anzFiles++;
			}
			if ($trxNr_EUR > 0) {
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->SetFont('Courier','',9);
				$pdf->MultiCell( 188, 3, $dtaJournal_EUR , 0, 'L', 0); 
				$pdf->Output($fullPath.$dtaJournalFileName_EUR.'.pdf', 'F');
				$anzFiles++;
			}
			if ($trxNr_CHF > 0) {
				$pdf = new FPDF('P','mm','A4');
				$pdf->AddPage();
				$pdf->SetFont('Courier','',9);
				$pdf->MultiCell( 188, 3, $dtaJournal_CHF , 0, 'L', 0); 
				$pdf->Output($fullPath.$dtaJournalFileName_CHF.'.pdf', 'F');
				$anzFiles++;
			}
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$pdf->SetFont('Courier','',9);
			$pdf->MultiCell( 188, 3, $rekap , 0, 'L', 0); 
			$pdf->Output($fullPath.'REKAP.pdf', 'F');
			$anzFiles++;

			$openPaymentsNames = $this->getOpenPaymentNames($PeriodeDieserMonat);
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$pdf->SetFont('Courier','',9);
			$pdf->MultiCell( 188, 3, $openPaymentsNames , 0, 'L', 0); 
			$pdf->Output($fullPath.'openPayments.pdf', 'F');
			$anzFiles++;

			
			system("chmod 666 *");
			
			$retMessage =   "Zahlstelle [z".$ZahlstellenID."]\n".$dtaFileName_CHF."\n".
							"- - - - - - - - - - - - - - - - - - - - - - -\n".
							"Personenkreis [p".$Personenkreis."] ".$nurZahlstelleBeruecksichtigen."\n".
							str_pad( $trxNr_OverAll, 3, " ", STR_PAD_LEFT)." Auftraege (TRX)\n" .
							"($trxNr_CHF in CHF, $trxNr_EUR in EUR, $trxNr_USD in USD)\n" .
							str_pad( $emplCount, 3, " ", STR_PAD_LEFT)." Mitarbeiter\n" .
							str_pad( $trxNr_OverAll-$emplCount, 3, " ", STR_PAD_LEFT)." Zahlungssplitts\n" .
							str_pad( $emplwithoutPayment, 3, " ", STR_PAD_LEFT)." ohne Auszahlung\n".
							str_pad( $anzFiles, 3, " ", STR_PAD_LEFT)." Dateien erzeugt\n\n";
		} else {
			$retMessage =   "no payments: ".$dtaFileName_CHF."\n";
		}//if trxnr < 1
		$ret = array("retMessage"=>$retMessage, "anzFiles"=>$anzFiles);
		return $ret;
	}
	
	function setRekapHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $SeitenNrRekap) {
		$tab1_top= 35;$tab2_top= 30;$tab3_top= 12;$end_top = 18;
									$tab4_top2=14;$end_top2 = 4;
		$tab1_h1 = 35;$tab2_h1 = 20;$tab3_h1 = 25;$end_h1 = 35;
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = "";
		$h .= CRLF.str_pad(substr("PESARIS/COPRONET", 0, $tab1_top-1), $tab1_top)
				  .str_pad(substr("REKAP ZU DTA-AUFTRAEGEN VOM:", 0, $tab2_top-1), $tab2_top)
				  .str_pad($dueDateGUI, $tab3_top, " ", STR_PAD_LEFT)
				  .str_pad(date("d.m.Y/H:i"), $end_top, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad(substr($PeriodeDieserMonat, 0, $tab1_top-1), $tab1_top)
				  .str_pad(substr("Valuta Datum:", 0, $tab2_top-1), $tab2_top)
				  .str_pad($dueDateGUI, $tab3_top, " ", STR_PAD_LEFT)
				  .str_pad("Seite ".$SeitenNrRekap, $end_top, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		return $h;
	}
	
	function setDTAJournalHeader($WHRG, $PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI) {
		$tab1_top= 35;$tab2_top= 30;$tab3_top= 12;$end_top = 18;
									$tab4_top2=14;$end_top2 = 4;
		$tab1_h1 = 35;$tab2_h1 = 20;$tab3_h1 = 25;$end_h1 = 35;

		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = "";
		$h .= CRLF.str_pad(substr("PESARIS/COPRONET", 0, $tab1_top-1), $tab1_top)
				  .str_pad(substr("LISTE ZUM DTA-AUFTRAG VOM:", 0, $tab2_top-1), $tab2_top)
				  .str_pad($dueDateGUI, $tab3_top, " ", STR_PAD_LEFT)
				  .str_pad(date("d.m.Y/H:i"), $end_top, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad(substr($PeriodeDieserMonat, 0, $tab1_top-1), $tab1_top)
				  .str_pad(substr("Valuta Datum:", 0, $tab2_top-1), $tab2_top)
				  .str_pad($dueDateGUI, $tab3_top, " ", STR_PAD_LEFT)
				  .str_pad("Seite", $tab4_top2, " ", STR_PAD_LEFT)
				  .str_pad("1", $end_top2, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL1, 0, $tab1_h1-1), $tab1_h1);
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL2, 0, $tab1_h1-1), $tab1_h1);
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL3, 0, $tab1_h1-1), $tab1_h1);
		$h .= CRLF;
		$h .= CRLF.str_pad("Beauftragte Bank:",$tab1_h1).str_pad(substr($ZahlstelleL1, 0, $tab3_h1-1), $tab3_h1);
		$h .= CRLF.str_pad("",$tab1_h1).str_pad(substr($ZahlstelleL2, 0, $tab3_h1-1), $tab3_h1);
		$h .= CRLF.str_pad("",$tab1_h1).str_pad(substr($ZahlstelleL3, 0, $tab3_h1-1), $tab3_h1)
				  .str_pad(substr("Ab Konto:", 0, $end_h3-1), $end_h3).trim($ZahlstelleKonto);
		$h .= CRLF.str_pad("",$tab1_h1).str_pad(substr($ZahlstelleL4, 0, $tab3_h1-1), $tab3_h1);
		$h .= CRLF.str_pad("F-NR.", $tab1_h3)
				  .str_pad("P-NR.  EMPFAENGER/BEGUENSTIGTER", $tab2_h3)
				  .str_pad("IBAN NUMMER", $tab3_h3)
				  .str_pad("BETRAG ".$WHRG, $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");		
		return $h;
	}
	
	function setDTAJournalFollowHeader($WHRG, $SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleKonto) {
		$tab1_h1 = 35;$tab2_h1 = 20;$end_h1 = 40;
		$tab1_h2 = 35;$tab2_h2 = 20;$tab3_h2 = 31;$tab4_h2 = 6;$end_h2 = 3;
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h = "";
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL1, 0, $tab1_h1-1), $tab1_h1)
				  .str_pad(substr($ZahlstelleL1, 0, $tab2_h1-1), $tab2_h1)
				  .str_pad(date("d.m.Y/H:i"), $end_h1, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad(substr($AuftraggeberFirmaL2, 0, $tab1_h2-1), $tab1_h2)
				  .str_pad(substr($ZahlstelleL2, 0, $tab2_h2-1), $tab2_h2)
				  .str_pad($ZahlstelleKonto, $tab3_h2)
				  .str_pad("Seite", $tab4_h2)
				  .str_pad($SeitenNr, $end_h2, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF.str_pad("F-NR.", $tab1_h3)
				  .str_pad("P-NR.  EMPFAENGER/BEGUENSTIGTER", $tab2_h3)
				  .str_pad("IBAN NUMMER", $tab3_h3)
				  .str_pad("BETRAG ".$WHRG, $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		
		return $h;
	}
	function setDTAJournalSeitenTotal($WHRG, $SeitenTotal) {
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = CRLF.str_pad(" ", $tab1_h3)
				  .str_pad(" ", $tab2_h3)
				  .str_pad("SEITENTOTAL ".$WHRG, $tab3_h3)
				  .str_pad(number_format($SeitenTotal, 2, '.', "'"), $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF."\f".CRLF;
		$h .= CRLF;
		return $h;
	}
	function setDTAJournalRekap($WHRG, $GesamtTotal, $PeriodeDieserMonat, $trxNr, $Personenkreis, $dtaFileName, $emplFilter, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $splitt, $splitTrx, $emplCount) {
		$tab1_h1 = 35;$tab2_h1 = 20;$end_h1 = 40;
		$tab1_h2 = 35;$tab2_h2 = 20;$tab3_h2 = 31;$tab4_h2 = 6;$end_h2 = 3;
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = "";
		$h .= CRLF."REKAP DTA-JOURNAL ".strtoupper($PeriodeDieserMonat)." ".$dtaFileName;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Zahlstelle",$tab2_h3).$ZahlstelleKonto;
		$h .= CRLF;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL1;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL2;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL3;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3).$ZahlstelleL4;
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3)
				  .str_pad("GESAMTTOTAL ".$WHRG, $tab3_h3)
				  .str_pad(number_format($GesamtTotal, 2, '.', "'") , $end_h3, " ", STR_PAD_LEFT);
		if ($WHRG != "CHF") {
			$Kurs = blFunctionCall("payroll.getCurrencyForexRate", $WHRG);
			
			$h .= CRLF.str_pad(" ", $tab1_h3)
				  	  .str_pad("Wechselkurs ".$WHRG."/CHF = 1/".$Kurs, $tab1_h3)
					  .str_pad("(CHF ".number_format($GesamtTotal * $Kurs, 2, '.', "'").")" , $end_h1+18, " ", STR_PAD_LEFT);
		}
		$h .= CRLF;				
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("DTA-Datei", $tab2_h3).$dtaFileName;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Auszahlperiode", $tab2_h3).$PeriodeDieserMonat;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Valuta", $tab2_h3).$dueDateGUI;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Transaktionen", $tab2_h3).$trxNr;
		if ($trxNr > $emplCount) {
			$h .= CRLF.str_pad(" ", $tab1_h3)
					  .str_pad("Empfaenger", $tab2_h3).intval($emplCount);
			$h .= CRLF.str_pad(" ", $tab1_h3)
					  .str_pad("Splitts", $tab2_h3).intval($trxNr-$emplCount);
		}
		if ($Personenkreis != "*"){
			$h .= CRLF;
			$h .= CRLF.str_pad(" ", $tab1_h3)
					  .str_pad("Personenkreis", $tab2_h3)."[p".$Personenkreis."]";
			if (strlen($emplFilter) > $tab2_h3+$tab3_h3+$end_h3-4){
				$h .= CRLF.str_pad("", $tab1_h3)."(".trim(substr($emplFilter, 4, $tab2_h3+$tab3_h3+$end_h3-4));//ohne Anfangs-"AND"				
				$h .= CRLF.str_pad("", $tab1_h3)."(".trim(substr($emplFilter, $tab2_h3+$tab3_h3+$end_h3-4)).")";//ohne Anfangs-"AND"								
			} else {
				$h .= CRLF.str_pad("", $tab1_h3)."(".trim(substr($emplFilter, 4)).")";//ohne Anfangs-"AND"				
			}		
		}
		if ($WHRG=="CHF") {
			if (count($effectedNoPaymentEmployeeList)>0){
				//$effEmplArr = explode(",", $effectedNoPaymentEmployeeList);
				require_once('payroll_auszahlen.php');
				$auszahlen = new auszahlen();
				$h .= CRLF;
				$system_database_manager = system_database_manager::getInstance();
				$result_employeesNoPayment = $system_database_manager->executeQuery("
					SELECT * FROM 
					payroll_employee 
					WHERE id IN (".$effectedNoPaymentEmployeeList.");");
					
					if (count($result_employeesNoPayment)>12) {
						$h .= CRLF."keine Zahlung:".CRLF;
						$i = 0;
						foreach ( $result_employeesNoPayment as $row ){
							    $h .= str_pad($row['EmployeeNumber'], 6,"0", STR_PAD_LEFT);
							    if ($i % 12 == 0) {
							    	$h .= CRLF;
							    } else {
							    	$h .= ", ";
							    }
						}
					} else {
						foreach ( $result_employeesNoPayment as $row ){
							 $h .= CRLF
							    .str_pad("", $tab1_h3)
							    ."keine Zahlung:  "
								.str_pad($row['EmployeeNumber'], 6,"0", STR_PAD_LEFT)
								." ".$auszahlen->replaceUmlaute($row['Firstname']." ".$row['Lastname'].", ".$row['City']);					
						}
					}
			}
		}//end if (CHF)
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		
		return $h;
	}	
	function setDTAJournalZeile($trxNr, $employeeNumber, $ben1, $ben2, $ben3, $ben4, $benIBAN, $benBank1, $benBank2, $benBank3, $payAmount, $payCurrency, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort) {
		$tab1_z1 =  7;$tab2_z1 = 33;$tab3_z1 = 20;
		$tab1_z2 = 20;$tab2_z2 = 24;$tab3_z2 = 16;$tab4_z2 = 22;$end_z2 =  13;
		$l  = "";
		$l .= CRLF.str_pad(str_pad($trxNr,$tab1_z1-2,"0", STR_PAD_LEFT),$tab1_z1)
				  .str_pad(substr( str_pad($employeeNumber,6,"0",STR_PAD_LEFT) ." ".$ben1 , 0, $tab2_z1-1), $tab2_z1)  
				  .str_pad(substr(trim($ben2." ".$ben3), 0, $tab3_z1-1), $tab3_z1). $ben4;
		$l .= CRLF.substr(
				   str_pad(substr(trim($benBank1), 0, $tab1_z2-1), $tab1_z2)
				  .str_pad(substr(trim($benBank2." ".$benBank3), 0, $tab2_z2+$tab3_z2-1), $tab2_z2+$tab3_z2)
				  .str_pad($benIBAN, $tab4_z2), 0, $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2);
				  
		$l .=      str_pad(number_format($payAmount, 2, '.', "'"), $end_z2, " ", STR_PAD_LEFT);
		if ($payCurrency != "CHF") {
			$Kurs = blFunctionCall("payroll.getCurrencyForexRate", $payCurrency);
			$ueberweisung = "Wechselkurs ".$payCurrency."/CHF = 1/".$Kurs.", (CHF ".number_format($payAmount * $Kurs, 2, '.', "'");
			$l .= CRLF.str_pad($ueberweisung, $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2+$end_z2, " ", STR_PAD_LEFT).")";
		}
		if (strlen(trim($endBeguenst1_IBAN)) > 1) {
			$l .= CRLF."Endbeguenstigter: ".CRLF.$endBeguenst1_IBAN." ".$endBeguenst2_Nam." ".$endBeguenst3_Adr." ".$endBeguenst4_Ort;
		}
		$l .= CRLF.str_pad("----", $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2+$end_z2, "-");
		
		return $l;
	}
	
	function setDTAZeile(  $dtaValutaDate, $trxNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort) {
		$ret = "";
		$beneBank = str_replace(" ", "",$beneBank1.$beneBank2.$beneBank3);
		$ret = $this->setDTAZeileTyp836( $dtaValutaDate, $trxNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort);					
		//$ret = $this->setDTAZeileTyp827( $dtaValutaDate, $trxNr, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency);
		return $ret;
	}
	
	function getOpenPaymentNames($PeriodeDieserMonat) {
		$auszahlen = new auszahlen();
		$openPaymentsArr = blFunctionCall('payroll.auszahlen.getUncompleteEmplNamesFromTrackingTable');
		$ret = "";
		if (count($openPaymentsArr) > 0) {
			$ret .= CRLF."Noch nicht ausbezahlte Mitarbeiter      (".$PeriodeDieserMonat.") ".str_pad(date("d.m.Y/H:i"), 22, " ", STR_PAD_LEFT);
			$ret .= CRLF.str_pad("", 80,"=", STR_PAD_RIGHT);
			$sum = 0;
			foreach ( $openPaymentsArr as $openPEmpl ) {
	       		$ret .= CRLF.str_pad($openPEmpl['EmployeeNumber'], 6,"0", STR_PAD_LEFT);
	       		$ret .= "  ".str_pad(substr($auszahlen->replaceUmlaute($openPEmpl['Lastname']),0,17), 18," ", STR_PAD_RIGHT);
	       		$ret .= str_pad(substr($auszahlen->replaceUmlaute($openPEmpl['Firstname']),0,15), 16," ", STR_PAD_RIGHT);
	       		$ret .= str_pad(substr($auszahlen->replaceUmlaute($openPEmpl['City']),0,18), 20," ", STR_PAD_RIGHT);
	       		$ret .= "CHF".str_pad($openPEmpl['amount_available'], 15," ", STR_PAD_LEFT);
	       		$sum += $openPEmpl['amount_available'];
			}
			$ret .= CRLF.str_pad("", 80,"=", STR_PAD_RIGHT);
       		$ret .= CRLF.str_pad("", 62," ", STR_PAD_RIGHT)."CHF".str_pad($sum, 15," ", STR_PAD_LEFT);
		} else {
			$ret .= CRLF."Noch nicht ausbezahlte Mitarbeiter      (".$PeriodeDieserMonat.") ".str_pad(date("d.m.Y/H:i"), 22, " ", STR_PAD_LEFT);
			$ret .= CRLF.str_pad("", 80,"=", STR_PAD_RIGHT);
			$ret .= CRLF.str_pad("Alle ausbezahlt", 80," ", STR_PAD_LEFT);
		}
		return $ret;
	}
	
	function setDTAZeileTyp827(  $dtaValutaDate, $trxNr, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency) {
				$TA827 = "";
				$dtaHeader51stellen = "000000".str_pad(" ", 12)
					."00000"
					.$dtaValutaDate
					.str_pad(" ", 7)
					."COPRO"
					.str_pad($trxNr,5,"0", STR_PAD_LEFT)
					."827"."1"."0";
					     // 1 = Lohn/Salaerzahlung
					
				$TA827.= substr("01"
						.$dtaHeader51stellen
						.str_pad($dtaValutaDate,6)
						.str_pad("DTAID",5)
						.str_pad("TRXNR".str_pad($trxNr,6,"0", STR_PAD_LEFT),11)
						.str_pad($iban, 24) 
						.str_pad($payCurrency, 6, " ", STR_PAD_LEFT)
						.str_pad(number_format($payAmount, 2, ',', "") ,12)
						.str_pad(" ",14)
					, 0, 128);						
				$TA827.= CRLF.substr("02"
						.str_pad($ZahlstelleL1, 20)
						.str_pad($ZahlstelleL2, 20)
						.str_pad($ZahlstelleL3, 20)
						.str_pad($ZahlstelleL4, 20)
						.str_pad(" ",46)
					, 0, 128);						
				$TA827.= CRLF.substr("03"
						."/C/".str_pad($benIBAN,30)
						.str_pad($bn1, 24)
						.str_pad($bn2, 24)
						.str_pad($bn3, 24)
						.str_pad($bn4, 24)
					, 0, 128);						
				$TA827.= CRLF.substr("04"
						.str_pad(strtoupper( "Salaerzahlung" ), 28)
						.str_pad(strtoupper( $PeriodeDieserMonat ), 28)
						.str_pad(strtoupper( "" ), 28)
						.str_pad(strtoupper( "" ), 28)
						.str_pad(strtoupper( "" ), 14)
					, 0, 128);	
		return $TA827.CRLF;					
	}
	
	function setDTAZeileTyp836( $dtaValutaDate, $trxNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $iban, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat, $payAmount, $payCurrency, $spesenregelung, $beneBank1, $beneBank2, $beneBank3, $endBeguenst1_IBAN, $endBeguenst2_Nam, $endBeguenst3_Adr, $endBeguenst4_Ort) {
				$BCNr_kurz = "";
				$Land = substr($iban,0,2);
				if ($Land == "CH" || $Land == "AT") {
					$BankClearingNr = substr($iban, 4,5);
					$BCNr_kurz = trim(intval($BankClearingNr));//die führenden Nullen wegmachen
				}
				$dtaHeader51stellen = "000000".str_pad(" ", 12) 
					."00000"
					.$dtaValutaDate
					.str_pad($BCNr_kurz, 7, " ", STR_PAD_RIGHT)
					."COPRO"
					.str_pad($trxNr,5,"0", STR_PAD_LEFT)
					."836"."1"."0";
					     // 1 = Lohn/Salaerzahlung
					     
				$TA836 = "";
	//Segment 01	
				$TA836.= substr("01"
						.$dtaHeader51stellen
						."TRXNR".str_pad($trxNr,11,"0", STR_PAD_LEFT)
						.str_pad($iban, 24, " ", STR_PAD_RIGHT) 
						.str_pad($dtaValutaDate,6)
						.$payCurrency
						.str_pad(number_format($payAmount, 2, ',', "") ,15, "0", STR_PAD_LEFT)
						.str_pad(" ",11)
					, 0, 128);
	//Segment 02 (Auftraggeber)
// 				$AuftraggeberAdr1 = "";
// 				$AuftraggeberAdr2 = "";					
// 				$AuftraggeberAdr3 = "";					
// 				if (strlen($ZahlstelleL4) > 1) { 
// 					$AuftraggeberAdr3 = substr($ZahlstelleL4,0,34);; 
// 					if (strlen($ZahlstelleL3) > 1) { $AuftraggeberAdr2 = substr($ZahlstelleL3,0,34); }	
// 					if (strlen($ZahlstelleL2) > 1) { $AuftraggeberAdr2 = substr($ZahlstelleL2." ".$AuftraggeberAdr2,0,34); }	
// 				} else {
// 					if (strlen($ZahlstelleL1) > 34) { $AuftraggeberAdr1 = substr($ZahlstelleL1,0,34); }
// 					if (strlen($ZahlstelleL2) > 34) { $AuftraggeberAdr2 = substr($ZahlstelleL2,0,34); }
// 					if (strlen($ZahlstelleL3) > 34) { $AuftraggeberAdr3 = substr($ZahlstelleL3,0,34); }
// 				}	
// 				if (strlen($AuftraggeberAdr2)>34) {
// 					$AuftraggeberAdr2 = substr($AuftraggeberAdr2,0,34);
// 				}
				$umrechnungskurs = " ";					
				$TA836.= CRLF.substr("02"
						.str_pad($umrechnungskurs, 12)
						.str_pad($AuftraggeberFirmaL1, 35)
						.str_pad($AuftraggeberFirmaL2, 35)
						.str_pad($AuftraggeberFirmaL3, 35)
						.str_pad(" ",9)
					, 0, 128);	
					
	//Segment 03 (Bank)					
				$bankBeneficiary1 = $beneBank1;
				$bankBeneficiary2 = $beneBank3;
				$TA836.= CRLF.substr("03"."D"
						.str_pad($bankBeneficiary1,35)
						.str_pad($bankBeneficiary2,35)
						.str_pad($benIBAN,34)
						.str_pad(" ",21)
					, 0, 128);
					
	//Segment 04 (Begünstigter)	
				$BenAdr1 = $bn1;
				$BenAdr2 = $bn2." ".$bn3;					
				$BenAdr3 = $bn4;					
				$benIBAN = $benIBAN;
				if (strlen($endBeguenst1_IBAN)>5) {
					$benIBAN = str_replace(" ", "", $endBeguenst1_IBAN);
					$BenAdr1 = $endBeguenst2_Nam; 
					$BenAdr2 = $endBeguenst3_Adr;
					$BenAdr3 = $endBeguenst4_Ort;
				}
				if (strlen($BenAdr1) > 34) $BenAdr1 = substr($BenAdr1, 0,34);
				if (strlen($BenAdr2) > 34) $BenAdr2 = substr($BenAdr2, 0,34);
				if (strlen($BenAdr3) > 34) $BenAdr3 = substr($BenAdr3, 0,34);
				$TA836.= CRLF.substr("04"
						.str_pad($BenAdr1, 35)
						.str_pad($BenAdr2, 35)
						.str_pad($BenAdr3, 35)
						.str_pad(" ",21)
					, 0, 128);
										
	//Segment 05	
				$spesen = "0";
				if (strlen($spesenregelung) == 1) {
					if (intval($spesenregelung)>0 && intval($spesenregelung)<=2) {
						$spesen = $spesenregelung;
					}
				}						
				$TA836.= CRLF.substr("05"."U"
						.str_pad("SALAERZAHLUNG", 35)
						.str_pad(strtoupper( $PeriodeDieserMonat ), 35)
						.str_pad(" ", 35)
						.$spesen
						.str_pad(" ", 19)
					, 0, 128).CRLF;	
		//communication_interface::alert("DTA_TA836_:".$TA836);				
		return $TA836;					
	} 

	function getDTATotalRecordTyp890(  $dtaValutaDate, $trxNr, $payAmount) {
		$LineNr = $trxNr+1;
				$TA890 = "";
				$dtaHeader51stellen = "000000".str_pad(" ", 12)
					."00000"
					.$dtaValutaDate
					.str_pad(" ", 7)
					."COPRO"
					.str_pad($LineNr,5,"0", STR_PAD_LEFT)
					."890"."1"."0";
					     // 1 = Lohn/Salaerzahlung
					
				$TA890.= substr("01"
						.$dtaHeader51stellen
						.str_pad(number_format($payAmount, 2, ',', "") ,16, "0", STR_PAD_LEFT)
						.str_pad(" ",59)
					, 0, 128);						
		return $TA890.CRLF;					
	}

}
?>
