<?php
class auszahlfiles {
	
	public function setAuszahlFiles($ZahlstellenID, $Personenkreis, $nurZahlstelleBeruecksichtigen, $arrDueDate, $periodeID) {
		require_once(getcwd()."/web/fpdf17/fpdf.php");
        require_once(getcwd()."/kernel/common-functions/configuration.php");
		require_once('payroll_auszahlen.php');
		$auszahlen = new auszahlen();

		$dueDateGUI = $arrDueDate[0].".".$arrDueDate[1].".".$arrDueDate[2];
		$dtaValutaDate = substr($arrDueDate[2], 2).$arrDueDate[1].$arrDueDate[0];

		$fileNamePrefix = "std_";
		if ($nurZahlstelleBeruecksichtigen) {
			$fileNamePrefix = "z".$ZahlstellenID."_";
		}

		$dtaFileName = "";
		$dtaJournalFileName = "";
		$dtaRekapFileName = "";
			
		//Einschraenkung mit dem Personenfilter
		$emplFilter = "";
		$dtaPersKreisFileName = "";
		if (substr($Personenkreis."xx",0,1)!="*") {			
			$dtaPersKreisFileName = "_p".str_replace(",", "p", $Personenkreis);
			$emplFilter = $auszahlen->getEmployeeFilters($Personenkreis);
		}
		$zs = "";

		if($nurZahlstelleBeruecksichtigen) {
			$empArray = $auszahlen->getPeriodZahlstellenMitarbeiterListe($periodeID, $ZahlstellenID, "");
			$empListe = implode(",",$empArray);
			//communication_interface::alert("empListe:".$empListe.print_r($empArray, true));
			$emplFilter = " AND emp.id IN (".$empListe.")";
			$zs = $ZahlstellenID;			
		} else {
			$allEmplWithPayment = $auszahlen->getMitarbeiterZurAuszahlung("8000", "amount > 0.001", "isFullPayed <> 'Y' ",$emplFilter);
			$arrEmpl = array();
			foreach ( $allEmplWithPayment['data'] as $row ){
				$arrEmpl[] = $row['payroll_employee_ID'];
			}
			//communication_interface::alert("count:".$allEmplWithPayment["count"]."\nempl:".print_r($arrEmpl, true));
			//wenn Mehrfach-Nennungen auftauchen, werden diese singularisiert
			$uniqueEmployeeList = implode(",",array_unique($arrEmpl));
			$empArray = $auszahlen->getPeriodZahlstellenMitarbeiterListe($periodeID, -1, $uniqueEmployeeList);
			$empListe = implode(",",$empArray);
		}
//			communication_interface::alert("zs:".$zs
//										."\nZahlstellenID:".$ZahlstellenID
//										."\nemplFilter:".$emplFilter
//										."\nempArray:".print_r($empArray, true)
//										);	return;
//
		$employeeList = $auszahlen->getEmployeeDataCurrentPeriod("8000", $empListe);

		$bankSource = $auszahlen->getZahlstelle($ZahlstellenID);
		$iban = str_replace(" ", "", $bankSource["IBAN"]);		
		$ZahlstelleKonto = $iban;
		$ZahlstelleL1 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line1"] ));
		$ZahlstelleL2 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line2"] ));
		$ZahlstelleL3 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line3"] ));
		$ZahlstelleL4 =  strtoupper($auszahlen->replaceUmlaute( $bankSource["line4"] ));
		$companyID = $bankSource["company"];
		
		//communication_interface::alert("Zahlstelle:".$ZahlstellenID." \nDTAFileName:".$dtaFileName."\nperiodeDieserMonat:".$PeriodeDieserMonat."\nPersonenkreis:".$Personenkreis);
		if (strlen($iban) < 3) {
			$dtaFileName = $fileNamePrefix.date("Y-m-d").$dtaPersKreisFileName.".dta";
			$dtaJournalFileName = $fileNamePrefix.date("Y-m-d").$dtaPersKreisFileName."_dtaJournal";
			$dtaRekapFileName = $fileNamePrefix.date("Y-m-d").$dtaPersKreisFileName.".rekap";;
		} else {
			$dtaFileName = $fileNamePrefix.$iban.$dtaPersKreisFileName.".dta";
			$dtaJournalFileName = $fileNamePrefix.$iban.$dtaPersKreisFileName."_dtaJournal";
			$dtaRekapFileName = $fileNamePrefix.$iban.$dtaPersKreisFileName.".rekap";;
		}
		
		//Die jetztige Periode ist
		$PeriodeDieserMonat = blFunctionCall('payroll.auszahlen.getActualPeriodName');
				
		$company = $auszahlen->getCompany($companyID);
		$AuftraggeberFirmaL1 = $company["name"];						//"Presida Testunternehmung";
		$AuftraggeberFirmaL2 = $company["str"];							//"Mitteldorfstrasse 37";
		$AuftraggeberFirmaL3 = $company["zip"]." ".$company["city"];	//"5033 Buchs";
		
		$dtaContent="";
		$dtaJournal="";
		$trxnr=0;$splitt=0;$splitTrx=0;$emplCount=0;
		$SeitenNr=1;$SeitenTotal=0;$GesamtTotal=0;
		$anzFiles = 0;
		$anzZeilenSeite01 = 24; 
		$anzZeilenSeiteFF = 27;			
  		$RealEffectedEmployees = "";
  		//Iteration über alle Mitarbeiter mit Auszahlung 
		foreach ( $employeeList['data'] as $row ) { 
			$employee_ID = $row['id'];
	  		$availAmt = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
	  		if ($availAmt > 0) {
				$emplCount++;    
				$RealEffectedEmployees .= ",".$employee_ID;
				$employeeNumber = $row['EmployeeNumber'];
				//$amount = $row["amount"];
	
				//communication_interface::alert("amountDTA:".$amountDTA."\namountDTAJournal:".$amount);
				
				//Beneficiary Linien 1-4 (Name & Adresse) werden präventiv gesetzt
				$bn1 = $auszahlen->replaceUmlaute( trim($row['Firstname']). " " . trim($row['Lastname']) );
				$bn2 = $auszahlen->replaceUmlaute( trim($row['Street']) );
				$bn3 = $auszahlen->replaceUmlaute( trim($row['AdditionalAddrLine1']). " " . trim($row['AdditionalAddrLine2']) );
				$bn4 = $auszahlen->replaceUmlaute( trim($row['ZIP-Code']). " " . trim($row['City']) );

				$hasSplit = array();
				if ($nurZahlstelleBeruecksichtigen) {
					$hasSplit = $auszahlen->getPaymentSplit($employee_ID, 0,$ZahlstellenID);			
				} else {
					$hasSplit = $auszahlen->getPaymentSplit($employee_ID, 0,0);			
				}
				if($hasSplit["count"]>0){ //Wenn dieser Mitarbeiter einen Zahlungssplitt hinterlegt hat
					for ( $index = 0, $max_count = sizeof( $hasSplit["data"] ); $index < $max_count; $index++ ) {
	//						communication_interface::alert($employee_ID." hat Splitt "
	//							."\nbank src: ".$hasSplit["data"][$index]["payroll_bank_source_ID"]				
	//							.", nbank dst: ".$hasSplit["data"][$index]["payroll_bank_destination_ID"]				
	//							."\nsplitT: ".$hasSplit["data"][$index]["split_mode"]				
	//							."\namount: ".$hasSplit["data"][$index]["amount"]				
	//							."von Total: ".$amount				
	//						);
				  		$availAmt = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
						$splitTrx++;
						$splitID     	= $hasSplit["data"][$index]["id"];
						$processingOrder= $hasSplit["data"][$index]["processing_order"];
						$account_ID  	= $hasSplit["data"][$index]["payroll_account_ID"];
						$bankDestID  	= $hasSplit["data"][$index]["payroll_bank_destination_ID"];
						$amount		 	= $hasSplit["data"][$index]["amount"];
						$splittWaehrung		= $hasSplit["data"][$index]["payroll_currencyID"];
						$maxCalcAmount	= $amount;
						$splitAmount 	= $auszahlen->calcSplitAmount($hasSplit["data"][$index]["split_mode"]
																  ,$hasSplit["data"][$index]["amount"]
																  ,$availAmt
																  ,$maxCalcAmount
																  ,$employee_ID
																  ,$account_ID);
						$SeitenTotal += $splitAmount;
						$GesamtTotal += $splitAmount;
						$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, $splitID, $processingOrder, $availAmt-$splitAmount);
						
						$bene = $auszahlen->getDestinationBankAccount($employee_ID, $bankDestID, "Y");
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
						
						//DTA bei Splitt
						$trxnr++;			
						
						//DTA Journal
						
						if ($trxnr == 1) {
							$dtaJournal .= $this->setDTAJournalHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
						}
							
						if ($trxnr == $anzZeilenSeite01 || ($trxnr-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
							$SeitenNr++;
							$dtaJournal .= $this->setDTAJournalSeitenTotal($SeitenTotal);
							$dtaJournal .= $this->setDTAJournalFollowHeader($SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
							$SeitenTotal = 0;
						}
						$dtaJournal .= $this->setDTAJournalZeile($trxnr, $employeeNumber, $bn1, $bn2." ".$bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $splitAmount);
						$dtaContent.= $this->getDTAZeileTyp827(  $dtaValutaDate, $trxnr, $iban, $splitAmount, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat);
	
						//Abhaken Mitarbeiter mit Lohnbezug
						$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, $index);
					}
					
					
					//Abhaken Mitarbeiter mit Lohnbezug
					$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, "Y");
					$splitt++;
					
					//Wenn nach dem Splitt jetzt noch was übrig ist
					$availAmt = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
					$splitAmount = $auszahlen->rundungAuf5Rappen($availAmt);
					if ($splitAmount > 0 &&  $nurZahlstelleBeruecksichtigen == false) {
						$trxnr++;			
						$splitTrx++;
						$splitt++;
						
						$bene = $auszahlen->getStandardDestinationBankAccount($employee_ID);
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
						
						//DTA: Uebriges aus Splitt
						$dtaContent.= $this->getDTAZeileTyp827(  $dtaValutaDate, $trxnr, $iban, $splitAmount, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat);
						
						//DTA Journal
						$SeitenTotal += $splitAmount;
						$GesamtTotal += $splitAmount;
						$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, $splitID, $processingOrder, $availAmt-$splitAmount);
						if ($trxnr == 1) {
							$dtaJournal .= $this->setDTAJournalHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
						}
							
						if ($trxnr == $anzZeilenSeite01 || ($trxnr-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
							$SeitenNr++;
							$dtaJournal .= $this->setDTAJournalSeitenTotal($SeitenTotal);
							$dtaJournal .= $this->setDTAJournalFollowHeader($SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
							$SeitenTotal = 0;
						}
						$dtaJournal .= $this->setDTAJournalZeile($trxnr, $employeeNumber, $bn1, $bn2." ".$bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $splitAmount);
						
						//Abhaken Mitarbeiter mit Lohnbezug
						$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, "Y");
					}
				} else { //MA hat kein Splitt	
					$availAmt = $auszahlen->getAmountAvailableFromTrackingTable($periodeID, $employee_ID);
					if ($availAmt > 0.001) {
						$bene = $auszahlen->getStandardDestinationBankAccount($employee_ID);
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
			
						//DTA: wenn kein Splitt
						$trxnr++;			
		
						//DTA Journal
						$SeitenTotal += $availAmt;
						$GesamtTotal += $availAmt;
						$auszahlen->setAmountAvailableTrackingTable($periodeID, $employee_ID, 0, 0, $availAmt-$availAmt);
						if ($trxnr == 1) {
							$dtaJournal .= $this->setDTAJournalHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
						}
							
						if ($trxnr == $anzZeilenSeite01 || ($trxnr-$anzZeilenSeite01) % $anzZeilenSeiteFF == 0) {//zuerst nach X, dann alle Y
							$SeitenNr++;
							$dtaJournal .= $this->setDTAJournalSeitenTotal($SeitenTotal);
							$dtaJournal .= $this->setDTAJournalFollowHeader($SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL3, $ZahlstelleKonto);
							$SeitenTotal = 0;
						}
						$dtaJournal .= $this->setDTAJournalZeile($trxnr, $employeeNumber, $bn1, $bn2." ".$bn3, $bn4, $benIBAN, $beneBank1, $beneBank2, $beneBank3, $availAmt);
						$dtaContent .= $this->getDTAZeileTyp827(  $dtaValutaDate, $trxnr, $iban, $availAmt, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat);
						
						//Abhaken Mitarbeiter mit Lohnbezug
						$auszahlen->updatePeriodenAuszahlFlag($periodeID, "IN", $employee_ID, "Y");				
					}//if ($amount > 0.001) 
				}//hat Split -else	
			}//end if
		}//end foreach

		if (strlen($RealEffectedEmployees)>2) {
			if (substr($RealEffectedEmployees,0,1)==",") {
				$RealEffectedEmployees = substr($RealEffectedEmployees,1);
			}
	
			//Abhaken Mitarbeiter mit Lohnbezug
			if ($trxnr > 0) {
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
			$emplwithoutPayment = count($emplwithoutPaymentList);//Echte Netto-anzahl
		}
		
		$dtaJournal .= $this->setDTAJournalSeitenTotal($SeitenTotal);
		
		//Seite füllen, damit Rekap auf neuer Seite erscheint
		if ($trxnr % $anzZeilenSeite01 > 16) {
			$anzLinien = 0;
			if ($trxnr < $anzZeilenSeite01) {
				$anzLinien = $anzZeilenSeite01 - $trxnr;
			} else {
				$anzLinien = $anzZeilenSeiteFF - ($trxnr % $anzZeilenSeiteFF);
			}
			for ($index = 0, $max_count = $anzLinien; $index < $anzLinien; $index++) {
				$dtaJournal .= CRLF.CRLF.CRLF;// leere Zeilen um die Seite zu füllen
			}			
		}
		$dtaRekap    = $this->setDTAJournalRekap($SeitenNr, $GesamtTotal, strtoupper($PeriodeDieserMonat), $trxnr, $Personenkreis, $dtaFileName, $emplFilter, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $splitt, $splitTrx, $emplCount);
		$dtaJournal .= $dtaRekap;

		if ($trxnr > 1) {
			//Files speichern
			$fm = new file_manager();
			//$fullPath0 =  $fm->getFullPath();
			$fm->customerSpace()->setPath("/".AUSZAHLDIR)->makeDir();   
			$fm->customerSpace()->setPath("/".AUSZAHLDIR."/".$PeriodeDieserMonat);  
			$fm->customerSpace()->setPath("/".AUSZAHLDIR."/".$PeriodeDieserMonat)->makeDir(); 
			$fullPath =  $fm->getFullPath();
			$fm->setFile($dtaFileName); 
			$fm->putContents($dtaContent); 
			$anzFiles++;
			//	$fm->setFile($dtaJournalFileName.".txt"); 
			//	$fm->putContents($dtaJournal); 
			//	$anzFiles++;
			$fm->setFile($dtaRekapFileName); 
			$fm->putContents($dtaRekap); 
			
	
			$rekap = $this->setRekapHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI);
			$filelist = $fm->listDir(0);  
			foreach($filelist as $row) {
				if(strtolower(substr($row,-6))==".rekap") {
					$fm->setFile($row);
					$rekap .= $fm->getContents($row); 
				} 
			}
	
			$fm->fclose(); 
			
			//Erzeugt PDF Dateien
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$pdf->SetFont('Courier','',9);
			$pdf->MultiCell( 188, 3, $dtaJournal , 0, 'L', 0); 
			$pdf->Output($fullPath.$dtaJournalFileName.'.pdf', 'F');
			$anzFiles++;
	
			$pdf = new FPDF('P','mm','A4');
			$pdf->AddPage();
			$pdf->SetFont('Courier','',9);
			$pdf->MultiCell( 188, 3, $rekap , 0, 'L', 0); 
			$pdf->Output($fullPath.'REKAP.pdf', 'F');
			$anzFiles++;
			
			system("chmod 666 *");
			
			$retMessage =   "Zahlstelle [z".$ZahlstellenID."]\n".$dtaFileName."\n".
							"- - - - - - - - - - - - - - - - - - - - - - -\n".
							"Personenkreis [p".$Personenkreis."] ".$nurZahlstelleBeruecksichtigen."\n".
							str_pad( $trxnr, 3, " ", STR_PAD_LEFT)." Auftraege (TRX)\n" .
							str_pad( $emplCount, 3, " ", STR_PAD_LEFT)." Mitarbeiter\n" .
							str_pad( $splitTrx, 3, " ", STR_PAD_LEFT)." Zahlungssplitts\n" .
							str_pad( $emplwithoutPayment, 3, " ", STR_PAD_LEFT)." ohne Auszahlung\n".
							str_pad( $anzFiles, 3, " ", STR_PAD_LEFT)." Dateien erzeugt\n\n";
		} else {
			$retMessage =   "Zahlstelle [z".$ZahlstellenID."]\n".$dtaFileName."\n".
							"- - - - - - - no payments - - - - - -\n";
		}//if trxnr < 1
		$ret = array("retMessage"=>$retMessage, "anzFiles"=>$anzFiles);
		return $ret;
	}
	
	function setRekapHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI) {
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
				  .str_pad("", $tab4_top2+$end_top2, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		return $h;
	}
	
	function setDTAJournalHeader($PeriodeDieserMonat, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $AuftraggeberFirmaL3, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI) {
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
				  .str_pad("BETRAG", $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");		
		return $h;
	}
	
	function setDTAJournalFollowHeader($SeitenNr, $AuftraggeberFirmaL1, $AuftraggeberFirmaL2, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleKonto) {
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
				  .str_pad("BETRAG", $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		
		return $h;
	}
	function setDTAJournalSeitenTotal($SeitenTotal) {
		$tab1_h3 =  7;$tab2_h3 = 53;$tab3_h3 = 20;$end_h3 = 15;
		$h  = CRLF.str_pad(" ", $tab1_h3)
				  .str_pad(" ", $tab2_h3)
				  .str_pad("SEITENTOTAL", $tab3_h3)
				  .str_pad(number_format($SeitenTotal, 2, '.', "'"), $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF."\f".CRLF;
		$h .= CRLF;
		return $h;
	}
	function setDTAJournalRekap($SeitenNr, $GesamtTotal, $PeriodeDieserMonat, $trxnr, $Personenkreis, $dtaFileName, $emplFilter, $effectedNoPaymentEmployeeList, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $ZahlstelleKonto, $dueDateGUI, $splitt, $splitTrx, $emplCount) {
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
				  .str_pad("GESAMTTOTAL", $tab3_h3)
				  .str_pad(number_format($GesamtTotal, 2, '.', "'") , $end_h3, " ", STR_PAD_LEFT);
		$h .= CRLF;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("DTA-Datei", $tab2_h3).$dtaFileName;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Auszahlperiode", $tab2_h3).$PeriodeDieserMonat;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Valuta", $tab2_h3).$dueDateGUI;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Transaktionen", $tab2_h3).$trxnr;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Empfaenger", $tab2_h3).$emplCount;
//		$h .= CRLF.str_pad(" ", $tab1_h3)
//				  .str_pad("Empfaenger mit Splitt", $tab2_h3).$splitt;
		$h .= CRLF.str_pad(" ", $tab1_h3)
				  .str_pad("Splitts", $tab2_h3).$splitTrx;
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
		$h .= CRLF.str_pad("", $tab1_h3+$tab2_h3+$tab3_h3+$end_h3, "-");
		$h .= CRLF;
		
		return $h;
	}	
	function setDTAJournalZeile($trxNr, $employeeNumber, $ben1, $ben2, $ben3, $benIBAN, $benBank1, $benBank2, $benBank3, $betrag) {
		$tab1_z1 =  7;$tab2_z1 = 33;$tab3_z1 = 20;
		$tab1_z2 = 20;$tab2_z2 = 24;$tab3_z2 = 16;$tab4_z2 = 20;$end_z2 =  15;
		$l  = "";
		$l .= CRLF.str_pad(str_pad($trxNr,$tab1_z1-2,"0", STR_PAD_LEFT),$tab1_z1)
				  .str_pad(substr( str_pad($employeeNumber,6,"0",STR_PAD_LEFT) ." ".$ben1, 0, $tab2_z1-1), $tab2_z1)  
				  .str_pad(substr($ben2, 0, $tab3_z1-1), $tab3_z1)
				  .$ben3;
		$l .= CRLF.substr(
				   str_pad(substr(trim($benBank1), 0, $tab1_z2-1), $tab1_z2)
				  .str_pad(substr(trim($benBank2), 0, $tab2_z2-1), $tab2_z2)
				  .str_pad(substr(trim($benBank3), 0, $tab3_z2-1), $tab3_z2)
				  .str_pad($benIBAN, $tab4_z2), 0, $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2);
		$l .=      str_pad(number_format($betrag, 2, '.', "'"), $end_z2, " ", STR_PAD_LEFT);
		$l .= CRLF.str_pad("----", $tab1_z2+$tab2_z2+$tab3_z2+$tab4_z2+$end_z2, "-");
		return $l;
	}
	
	function getDTAZeileTyp827(  $dtaValutaDate, $trxnr, $iban, $amount, $ZahlstelleL1, $ZahlstelleL2, $ZahlstelleL3, $ZahlstelleL4, $benIBAN, $bn1, $bn2, $bn3, $bn4, $PeriodeDieserMonat) {
				$dtaContent = "";
				$dtaHeader51stellen = "000000".str_pad(" ", 12)
					."00000"
					.$dtaValutaDate
					.str_pad(" ", 7)
					."COPRO"
					.str_pad($trxnr,5,"0", STR_PAD_LEFT)
					."827"."1"."0";
					
				$dtaContent.= CRLF.substr("01"
						.$dtaHeader51stellen
						.str_pad($dtaValutaDate,6)
						.str_pad("DTAID",5)
						.str_pad("TRXNR".str_pad($trxnr,6,"0", STR_PAD_LEFT),11)
						.str_pad($iban, 24) 
						.str_pad("CHF", 6, " ", STR_PAD_LEFT)
						.str_pad(number_format($amount, 2, ',', "") ,12)
						.str_pad(" ",14)
					, 0, 128);						
				$dtaContent.= CRLF.substr("02"
						.str_pad($ZahlstelleL1, 20)
						.str_pad($ZahlstelleL2, 20)
						.str_pad($ZahlstelleL3, 20)
						.str_pad($ZahlstelleL4, 20)
						.str_pad(" ",46)
					, 0, 128);						
				$dtaContent.= CRLF.substr("03"
						."/C/".str_pad($benIBAN,30)
						.str_pad($bn1, 24)
						.str_pad($bn2, 24)
						.str_pad($bn3, 24)
						.str_pad($bn4, 24)
					, 0, 128);						
				$dtaContent.= CRLF.substr("04"
						.str_pad(strtoupper( "Salaerzahlung" ), 28)
						.str_pad(strtoupper( $PeriodeDieserMonat ), 28)
						.str_pad(strtoupper( "" ), 28)
						.str_pad(strtoupper( "" ), 28)
						.str_pad(strtoupper( "" ), 14)
					, 0, 128);	
		return $dtaContent;					
	}
	
		
}
?>
