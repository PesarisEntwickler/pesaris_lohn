<?php
class payroll_UI {
	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;
		
		//communication_interface::alert("code_ui:".$functionName."\n");
		
		//error_log("\n", 3, "/var/log/copronet-application.log");
		//communication_interface::alert(""); //alert sollte nur zu Debug-Zwecken eingesetzt werden
		switch($functionName) {
	
		case 'payroll.sysLoader':
			communication_interface::cssFileInclude('plugins/payroll_V00_01_00/code_ui/css/payroll.css','all');
			communication_interface::jsFileInclude('plugins/payroll_V00_01_00/code_ui/js/payroll.js','text/javascript','payroll');
			break;
		case 'payroll.bankverbindung_close_check':
			//communication_interface::alert("payroll.bankverbindung_close_check:".print_r($functionParameters[0], true));
			$splitID = $functionParameters[0]["splitID"];
			$bankID = $functionParameters[0]["bankID"];
			$empId = $functionParameters[0]["empId"];
			$dest = blFunctionCall('payroll.auszahlen.getDestinationBankAccount', $empId, $bankID);
			$checkString = $dest["bank_account"].$dest["beneAddress1"].$dest["beneEndbeguenst2"];
			//communication_interface::alert(print_r($dest,true));
			if (strlen($checkString) < 2) {
				communication_interface::alert("Es wurden keine Eingaben gespeichert.\nDer Splitt wird rueckgaengig gemacht.");
				$res = blFunctionCall('payroll.deletePaymentSplitDetail', array("id"=>$splitID));
			} 
			communication_interface::jsExecute(" cb('payroll.paymentSplit', {'empId':".$empId."}); ");
			break;
		case 'payroll.auszahlen.periodenReset':
			$periodID = blFunctionCall('payroll.auszahlen.getActualPeriodID');
			$payroll_period = blFunctionCall('payroll.auszahlen.resetActualPeriodenAuszahlFlags', $periodID);
			$reset = blFunctionCall('payroll.auszahlen.initTrackingTable');
			blFunctionCall('payroll.auszahlen.truncateEffectifPayoutTable');
				
			communication_interface::jsExecute(" cb('payroll.prlCalcOvProcess',{'functionNumber':5}); ");
			
			break; 
		case 'payroll.auszahlen.closewindow':
			//communication_interface::alert("payroll.auszahlen.closewindow");
			//Loeschen aller transferierten Files
			$absWebPath  = $aafwConfig["paths"]["session_control"]["rootPathData"].PAYMENTVIEWDIR;
			foreach (glob($absWebPath.AUSZAHLDIR."*.*") as $filename) {
			   unlink($filename);
			}
			break;
		case 'payroll.auszahlen.GenerateFiles':
			//communication_interface::alert("payroll.auszahlen.GenerateFiles\n1:".print_r($functionParameters[0], true)."\n2:".print_r($functionParameters[1], true)."\nEUR:".$functionParameters[2]."\nUSD:".$functionParameters[3]);
			$param = $functionParameters[0];
			$dueDateFromGUI = $functionParameters[1];
			$EUR = $functionParameters[2];
			$USD = $functionParameters[3]; 
			$res = blFunctionCall('payroll.saveCurrencyForexRate', "EUR", $EUR); 
			if ($res==false) break;
			$res = blFunctionCall('payroll.saveCurrencyForexRate', "USD", $USD); 
			if ($res==false) break;

			$paramArray = explode("##", $param);
			$zahlstelle = $paramArray[1];
			$personengruppenIDListe = "**";
			$personengruppen = substr($param, 0, 13);
			if ("SELECTED_EMPL" == $personengruppen) {
				$personengruppenIDListe = "";
				for ( $index = 1, $max_count = sizeof( $paramArray ); $index < $max_count; $index++ ) {
					$selectZeile = $paramArray[$index];
					$tokenPos    = strpos( $selectZeile, "[p" );
					if ( $tokenPos > 0 ) {
						$pArray = explode("]", $selectZeile);	
						$personengruppenID =  substr($pArray[0], $tokenPos + strlen("[p"));		
						$personengruppenIDListe .= "," . $personengruppenID;		
					}
				}		
			}
			$personengruppenIDListe = substr($personengruppenIDListe,1);
			$tokenPos    = strpos( $zahlstelle, "[z" );
			$zArray = explode("]", $zahlstelle);	
			$zahlstellenID =  substr($zArray[0], $tokenPos + strlen("[z"));	
			
//			communication_interface::alert("payroll.auszahlen.GenerateFiles" .
//					"\nzahlstellenID=".$zahlstellenID .
//					"\npersonengruppenIDListe=".$personengruppenIDListe);

			$hatAuszahlFiles = blFunctionCall('payroll.auszahlen.GenerateDataFiles', $zahlstellenID, $personengruppenIDListe, $dueDateFromGUI);

			if ($hatAuszahlFiles > 0) {
				//communication_interface::alert($hatAuszahlFiles." Datei erzeugt fuer Zahlstelle ".$zahlstellenID." und Personenkreis p".$personengruppenIDListe);// für ZahlstellenID:$zahlstellenID, Personengruppen:$personengruppenIDListe");				
			} else {
				//communication_interface::alert("Fehler beim Erzeugen der Zahlungs-Dateien");				
				break;
			}
			//Hier kein break, was bewirkt, dass dann nach der Erzeugung der Files 
			//gleich das History-Window aufgeht, was gewünscht ist
			//break;
		case 'payroll.auszahlen.openHistoryWindow':
			$PeriodeDieserMonat = blFunctionCall('payroll.auszahlen.getActualPeriodName');
			//communication_interface::alert($PeriodeDieserMonat);
			if ( isset($functionParameters[0]) && substr($functionParameters[0], 0, strlen(PERIODENPREFIX))==PERIODENPREFIX ) {
				$PeriodeDieserMonat = substr($functionParameters[0], 0, strlen(PERIODENPREFIX)+strlen('YYYY-MM'));
				//communication_interface::alert("isset:".$PeriodeDieserMonat);
			}
			$data["period"] = $PeriodeDieserMonat;
			$aktuellePeriode4ListingDir = "/".AUSZAHLDIR."/".$PeriodeDieserMonat;
			$data["aktuellePeriode4ListingDir"] = $aktuellePeriode4ListingDir;

			$fm = new file_manager();
			$fm->customerSpace()->setPath("/".AUSZAHLDIR)->makeDir();   
		 
			//Bestehende Periodendirectories lesen
			$dirlist = $fm->customerSpace()->setPath(AUSZAHLDIR)->listDir(1);  
			$data["DirectoriesLoop"] = array();
			$id = 1;
			foreach($dirlist as $dirrow) {
				$selected="0";
				if (strcasecmp ( $dirrow , $PeriodeDieserMonat ) == 0 ) {
					$selected="1";
				}
				$data["DirectoriesLoop"][] = array("id"=>$id,"bezeichnung"=>$dirrow,"selected"=>$selected);
				$id++;				
			}
			
			$db_name = session_control::getSessionInfo("db_name");
			
			//Auslisten der Files aus dem Directory
			$fm->customerSpace()->setPath($aktuellePeriode4ListingDir);
			$filelist = $fm->listDir(0);  

			$absWebPath  = $aafwConfig["paths"]["session_control"]["rootPathData"].PAYMENTVIEWDIR;
			$absDataPath = $aafwConfig["paths"]["plugin"]["customerDir"].$db_name."/".$aktuellePeriode4ListingDir."/";
			$data["PeriodenFiles"] = array();
			if (count($filelist)>1) {
				foreach($filelist as $row) {
					if(strtolower(substr($row,-4))==".dta"  ||  strtolower(substr($row,-4))==".pdf"  ||  strtolower(substr($row,-4))==".txt") {
						$fnArr = explode(".", $row);
						$technFilename = $db_name."_".$PeriodeDieserMonat."_".$row;
						$data["PeriodenFiles"][] = array("fileName"=>$row, "fileEndg"=>$fnArr[1], "technFilename"=>"/".PAYMENTVIEWDIR.$technFilename);
						copy($absDataPath.$row, $absWebPath.$technFilename);
					}
				}
			}
			$objWindow = new wgui_window("payroll", "wndIDAuszahlenPeriodenwahl"); // aufrufendes Plugins, als HTML "id" damit ist das Fenster per JS, resp. jQuery ansprechbar
			$objWindow->windowTitle($objWindow->getText("txtTitelAuszahlenHistory"));
			$objWindow->windowIcon("auszahlen32.png");
			$objWindow->windowWidth(650);
			$objWindow->windowHeight(300); 
			$objWindow->modal(true);	
			$objWindow->loadContent("auszahlen",$data,"wguiBlockAuszahlenHistoryWindow"); //Template-Datei, zu uebergebende Daten , Template-Blocks
			$objWindow->showWindow();			
			communication_interface::jsExecute("prlAuszahlenHistoryWindowInit();");
			break;
		case 'payroll.prlPsoEmployeeOverview':

			$data = array();
			$objWindow = new wgui_window("payroll", "employeeOverview");
			$objWindow->windowTitle("Personalstamm"); //sprintf($objWindow->getText("payrollfin_title_main"), $curFiscalyear)
			$objWindow->windowIcon("employees32.png");
			$objWindow->windowWidth(850); //750
			$objWindow->windowHeight(550); //450
			$objWindow->dockable(true);
			$objWindow->fullscreen(true);
			$objWindow->loadContent("employees",$data,"employeeOverview");
			$objWindow->addEventFunction_onResize("$('#gridEmplOv').height( $('#employeeOverview .o').height() - 60 );");
			$objWindow->addEventFunction_onResize("$('#gridEmplOv').width( $('#employeeOverview .o').width() - 42 );");
			$objWindow->addEventFunction_onResize("prlPsoGrid.resizeCanvas();"); //prlPsoGrid.render();
			$objWindow->showWindow();

			$this->emplOverviewPopulateTable(array("updateTable"=>false));

			communication_interface::jsExecute("prlPsoInit();");

			$settings = session_control::getSessionSettings("payroll", "psoSettings");
			if($settings=="") {
				$settings["quickFilterEnabled"] = false;
			}else $settings = unserialize($settings);
			$settings = json_encode($settings);
			communication_interface::jsExecute("prlPsoSetSettings(".$settings.");");
			break;
		case 'payroll.psoSetDBFilter':
			if(isset($functionParameters[0]["dbFilter"])) {
				session_control::setSessionSettings("payroll", "psoDbFilter", $functionParameters[0]["dbFilter"], false); //true = save permanently
				$this->emplOverviewPopulateTable(array("updateTable"=>true));
				communication_interface::jsExecute("$('#modalContainer').mb_close();");
			}else{
				$formList = blFunctionCall('payroll.getEmployeeFilterList',array("FilterForEmplOverview"=>true, "FilterForCalculation"=>false));
				if($formList["success"]) {
					$data["filter_list"] = $formList["data"];
				}else{
					$data["filter_list"] = array();
				}
				$objWindow = new wgui_window("payroll", "selectDBFilterForm");
				$objWindow->windowTitle($objWindow->getText("prlPsoBtnDFilter"));
				$objWindow->windowIcon("employee-edit32.png");
				$objWindow->windowWidth(530);
				$objWindow->windowHeight(150);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("employees",$data,"selectDBFilterForm");
				$objWindow->showWindow();
				communication_interface::jsExecute("$('#prlPsoDFltBtnOK').bind('click', function(e) { cb('payroll.psoSetDBFilter',{'dbFilter':$('#prlPsoDFilter').val()}); });");
				communication_interface::jsExecute("$('#prlPsoDFltBtnCancel').bind('click', function(e) { $('#modalContainer').mb_close(); });");
			}
			break;
		case 'payroll.psoSaveSettings':
			$psoDbFilter = session_control::getSessionSettings("payroll", "psoDbFilter");
			session_control::setSessionSettings("payroll", "psoDbFilter", $psoDbFilter, true); //true = save permanently

			session_control::setSessionSettings("payroll", "psoSettings", serialize($functionParameters[0]), true); //true = save permanently
			$objWindow = new wgui_window("payroll", "infoBox");
			$objWindow->windowTitle($objWindow->getText("txtEinstellungen"));
			$objWindow->setContent("<br/>".$objWindow->getText("prlPsoSettingsSaved")."<br/><br/><button class='PesarisButton' onclick='$(\"#modalContainer\").mb_close();'>".$objWindow->getText("btnOK")."</button>");
			$objWindow->showInfo();
			break;
		case 'payroll.prlVlOpenForm':
			$currentFormId = session_control::getSessionSettings("payroll", "psoCurrentEmplForm");
			if($currentFormId!="") {
				$formDetail = blFunctionCall('payroll.getEmployeeFormDetail',$currentFormId);
			}
			$txtNochkeineBankverbindung = "";
			if($functionParameters[0]["wndStatus"]==0) {
				//nur ausfuehren, wenn Window noch nicht geoeffnet ist
				$data = array();
				$data["modus"] = "mitarbeiterdatenBearbeiten";
				$objWindow = new wgui_window("payroll", "employeeForm");
				$title = $objWindow->getText("txtMitarbeiterdatenBearbeiten");
				$objWindow->windowTitle($title); 
				$objWindow->windowIcon("employee-edit32.png");
				$objWindow->windowWidth(830);
				$objWindow->windowHeight(550);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->loadContent("employees",$data,"employeeForm");
				$objWindow->showWindow();
				$txtNochkeineBankverbindung = $objWindow->getText("txtNochKeineBankverbindung");

				communication_interface::jsExecute("prlVlDirty = false;");
				communication_interface::jsExecute("prlVlDesignMode = false;");
				if($functionParameters[0]["fldDefFP"]=="") { //TODO: Dieser Check muss in Zukunft noch etwas "rafinierter" gemacht werden
					communication_interface::jsExecute($this->getEmplFieldDef());
					communication_interface::jsExecute("prlVlFldDefFP = 'X';"); //TODO: in Zukunft nicht nur 'X', sondern echten Fingerprint uebermitteln
				}
				communication_interface::jsExecute("prlVlInit();");

				if($formDetail["success"]) {
					communication_interface::jsExecute('prlVlCreateForm('.$formDetail["data"][0]["FormElements"].');');
				}
				communication_interface::jsExecute("$('#prlVlTabContainer').height( $('#employeeForm .o').height() - $('#prlVlTabs .o').height() - 40 );");
				communication_interface::jsExecute("$('#employeeForm .n').html('".$title." (<span id=\"prlVlTitle\"></span>)');");
			}
			$MAinfo = "";
			$employeeID = "";
			$bankdestinationID = "";
			if(isset($functionParameters[0]["id"]) && $functionParameters[0]["id"]!="" && $functionParameters[0]["id"]!=0) {
				$employeeID = $functionParameters[0]["id"];
				$employeeData = blFunctionCall('payroll.getEmployeeDetail',$employeeID,true);
				if($employeeData["success"]) {
					$MAinfo = addslashes( $employeeData["data"][0]["EmployeeNumber"]." ".$employeeData["data"][0]["Lastname"].", ".$employeeData["data"][0]["Firstname"] );
					$bankDestArr = blFunctionCall('payroll.auszahlen.getDestinationBankAccount',$employeeID,"");
					if(strlen($bankDestArr["bank_id"]) > 0) {
						$bankdestinationID = $bankDestArr["bank_id"];
					} else {
						communication_interface::alert($txtNochkeineBankverbindung);
						$bankdestinationID = "0";
						communication_interface::jsExecute("$('#btnBankverbindungUndSplitt').css('border-color', 'orange');");
					}
					//Determine which fields are used in the current layout (only the corresponding values will be submitted)
					$fieldsUsed = array();
					$layoutMap = json_decode($formDetail["data"][0]["FormElements"], true);
					foreach($layoutMap as $curTab) foreach($curTab["elements"] as $curFieldName) $fieldsUsed[] = $curFieldName;

					$fieldCollector = "";
					$fieldDefRecs = blFunctionCall('payroll.getEmployeeFieldDef');
					if($fieldDefRecs["success"]) {
						foreach($fieldDefRecs["data"] as $row) {
							if($row["childOf"]=="" && in_array($row["fieldName"], $fieldsUsed) ) {
								switch($row["fieldType"]) {
								case 1: //Text
								case 3: //Number
									$fieldCollector .= ($fieldCollector=="" ? "" : ",")."['".$row["fieldName"]."','".str_replace("'","\\'",$employeeData["data"][0][$row["fieldName"]])."']";
									break;
								case 2: //Checkbox
								case 4: //Select
									$fieldCollector .= ($fieldCollector=="" ? "" : ",")."['".$row["fieldName"]."','".$employeeData["data"][0][$row["fieldName"]]."']";
									break;
								case 5: //Date
									$fieldCollector .= ($fieldCollector=="" ? "" : ",")."['".$row["fieldName"]."','".$this->convertMySQL2Date($employeeData["data"][0][$row["fieldName"]])."']";
									break;
								case 100: //Zip/City
									$fieldCollector .= ($fieldCollector=="" ? "" : ",")."['".$row["fieldName"]."', {";
									foreach($fieldDefRecs["data"] as $childRow) {
										if($childRow["childOf"]==$row["fieldName"]) {
											if($childRow["childOrder"]==1) $fieldCollector .= "'zip':'".str_replace("'","\\'",$employeeData["data"][0][$childRow["fieldName"]])."', ";
											else $fieldCollector .= "'city':'".str_replace("'","\\'",$employeeData["data"][0][$childRow["fieldName"]])."'";
										}
									}
									$fieldCollector .= "}]";
									break;
								case 110: //Auxiliary tables
									$arrFieldsOfInterest = array();
									$greatRowCollector = array();
									$curAuxField = 0;
									$arrFieldsOfInterest[] = "id";
									foreach($fieldDefRecs["data"] as $childRow) {
										if($childRow["childOf"]==$row["fieldName"]) {
											$fldn = str_replace($row["fieldName"]."_", "", $childRow["fieldName"]);
											$arrFieldsOfInterest[] = $fldn;
											$arrDateFields[$fldn] = $childRow["fieldType"]==5 ? true : false;
										}
									}
									foreach($employeeData["auxiliaryTables"][$row["fieldName"]] as $auxTblRow) {
										$singleRowCollector = array();
										foreach($arrFieldsOfInterest as $curAuxField) {
											if(isset($arrDateFields[$curAuxField])) {											
												$singleRowCollector[] = "'".$this->convertMySQL2Date($auxTblRow[$curAuxField])."'";
											}else{
												$singleRowCollector[] = "'".str_replace("'","\\'",$auxTblRow[$curAuxField])."'";
											}
										}
										$greatRowCollector[] = "[".implode(",", $singleRowCollector)."]";
									}
									$fieldCollector .= ($fieldCollector=="" ? "" : ",")."['".$row["fieldName"]."',[".implode(",", $greatRowCollector)."]]";
									break;
								}
							}
						}
					}
					if($fieldCollector != "") communication_interface::jsExecute("prlVlFill( [".$fieldCollector."] );");
					communication_interface::jsExecute("$('#prlVlTitle').html('".$MAinfo."');");
				}
			}else{
				communication_interface::jsExecute("$('#prlVlTitle').html('* NEUERFASSUNG *');");
			}
			communication_interface::jsExecute("prlVlRid = ".$employeeID.";"); //set record ID of current employee (on client side)  
			communication_interface::jsExecute("$('#btnBankverbindungUndSplitt').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_UebersichtZahlungssplit', 'empId':".$employeeID.", 'bankID':".$bankdestinationID." }); });");
			break;
		case 'payroll.prlVlLoadFormData':
			//wenn Form bereits geladen ist und nur die Daten geaendert werden sollen
			break;
		case 'payroll.prlVlSaveFormData':
			$s = json_encode($functionParameters[0]["data"]);
			//communication_interface::alert("1*** \nrid:".$functionParameters[0]["rid"]."\ndata:".str_replace(",", "\n", $s).""); //TODO: remove!
			$ret = blFunctionCall('payroll.saveEmployeeDetail', $functionParameters[0]["rid"], $functionParameters[0]["data"]);
			if($ret["success"]) {

				//HIER WERDEN DATEN NEU IN UEBERSICHTSTABELLE GELADEN. 
				//DAS IST NOCH NICHT OPTIMAL GELOEST, DA *ALLE* DATEN 
				//NACH JEDER AENDERUNG AN DEN CLIENT GESCHICKT WERDEN!!
				$queryOption["query_filter"] = "";
				$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
				$tblData = "prlPsoData = [";
				$firstPass = true;
				if($employeeList["success"]) {
					foreach($employeeList["data"] as $row) {
						$tblData .= $firstPass ? "{" : ", {";
						$tblRow = "";
						foreach($row as $fieldName=>$fieldValue) {
							if($fieldName=="Sex") $fieldValue = $fieldValue=="F" ? "w" : "m"; //TODO: Werte dynamisch ersetzen!
							$tblRow .= ($tblRow == "" ? "" : ", ")."'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
						}
						$tblData .= $tblRow."}";
						$firstPass = false;
					}
				}
				$tblData .= "];";
				communication_interface::jsExecute($tblData);
				communication_interface::jsExecute("prlPsoDataView.beginUpdate();");
				communication_interface::jsExecute("prlPsoDataView.setItems(prlPsoData);");
				communication_interface::jsExecute("prlPsoDataView.endUpdate();");
				communication_interface::jsExecute("prlPsoDataView.reSort();");
				communication_interface::jsExecute("prlPsoGrid.invalidate();");
				
				communication_interface::jsExecute("  $('#employeeForm').mb_close();  ");  

			}else{
				//communication_interface::alert("count f:".count($ret['fieldNames']).", count t:".count($ret['tableRows'])."\n".print_r($ret,true));

				$flds = $ret['fieldNames'];
				$tblr = $ret['tableRows'];

				$felder = "";
				$werte = "";
				if (count($flds)>0) {
					foreach($flds as $fName) {
						$felder .= ",'".$fName."'";
					} 
				}
				if (count($tblr)>0) {
					foreach($tblr as $tableRow) {
						$felder .= ",'".$tableRow[0]."'";
						$werte  .= ",".$tableRow[1];
					} 
				}
				//communication_interface::alert($felder."\nw:".$werte);
				
				$employeeLabels = blFunctionCall('payroll.getEmployeeLabelListe', "de", substr($felder,1) );
				//communication_interface::alert($felder."\nw:".$werte."\temployeeLabels".print_r($employeeLabels, true));

				$labelListe = "";				
				foreach ( $employeeLabels as $lbl ) {
       				$labelListe .= "- ".$lbl["Labels"]."<br/>";
				}
				
				$content = "<br/>Die Daten konnten nicht gespeichert werden." .
						"<br/><br/>".$labelListe.
						"<br/><div align='center'><button class='PesarisButton' onclick='$(\"#modalContainer\").mb_close();'>OK</button></div><br/>".
						"<br/> [".$ret['errText']." (".$ret['errCode'].")]"
						;
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->setContent($content);
				$objWindow->windowTitle("Speichern fehlgeschlagen");
				$objWindow->windowWidth(360);
				$objWindow->windowHeight(250);
				$objWindow->showAlert();
				communication_interface::jsExecute("$('#employeeForm input').css('background-color','#fff');");
				communication_interface::jsExecute("$('#employeeForm select').css('background-color','#fff');");
				if (count($tblr)>0) {
					foreach($tblr as $rowName) communication_interface::jsExecute("$('#employeeForm #".$rowName[0]."').css('background-color','#f88');");
				}
				if (count($flds)>0) {
					foreach($flds as $fieldName) communication_interface::jsExecute("$('#employeeForm #".$fieldName."').css('background-color','#f88');");
				}

			}
			break;
		case 'payroll.prlVlSaveFormLayout':
			$ret = blFunctionCall('payroll.saveEmployeeForm', $functionParameters[0]);
			if($ret["success"]) {
				communication_interface::jsExecute("$('#employeeForm').mb_close();");
			}else{
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle("Speichern fehlgeschlagen");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(220);
				$objWindow->setContent("<br/>Das Layout konnte nicht gespeichert werden.<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
				$objWindow->showAlert();
			}
			break;
		case 'payroll.prlVlSelectForm':
			if($functionParameters[0]>0) {
				session_control::setSessionSettings("payroll", "psoCurrentEmplForm", $functionParameters[0], true); //true = save permanently
				communication_interface::jsExecute("$('#modalContainer').mb_close();");
			}else{
				$formList = blFunctionCall('payroll.getEmployeeFormList');
				if($formList["success"]) {
					$data["layout_list"] = $formList["data"];
				}else{
					$data["layout_list"] = array();
				}
				$objWindow = new wgui_window("payroll", "selectEmployeeForm");
				$objWindow->windowTitle($objWindow->getText("prlVlSelectFormTitle"));
				$objWindow->windowIcon("employee-edit32.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(160);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("employees",$data,"selectEmployeeForm");
				$objWindow->showWindow();
			}
			break;
		case 'payroll.prlVlEditForm':
			if(isset($functionParameters[0]["f"])) {
				switch($functionParameters[0]["f"]) {
				case 'dmAlert':
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle($objWindow->getText("prlVlAlertCloseWnd_Title"));
					$objWindow->windowWidth(550);
					$objWindow->windowHeight(250);
					$objWindow->setContent("<br/>".$objWindow->getText("prlVlAlertCloseWnd_Msg")."<br/><br/><button onclick='$(\"#employeeForm\").mb_close();cb(\"payroll.prlVlEditForm\",{\"f\":\"layoutOv\"});'>".$objWindow->getText("txtY")."</button><button onclick='$(\"#modalContainer\").mb_close();'>".$objWindow->getText("txtN")."</button>");
					$objWindow->showQuestion();
					break;
				case 'layoutOv':
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle($objWindow->getText("txtLayoutBearbeiten"));
					$objWindow->windowWidth(500);
					$objWindow->windowHeight(220);

					$formList = blFunctionCall('payroll.getEmployeeFormList');
					if($formList["success"]) {
						$data["layout_list"] = $formList["data"];
					}else{
						$data["layout_list"] = array();
					}
					$data["modus"] = "layoutbearbeiten";
					$objWindow->loadContent("employees",$data,"employeeEditFormDlg1");
					$objWindow->showInfo();
					communication_interface::jsExecute('document.getElementById("btnBankverbindung").enabled=false;');
					break;
				case 'takeAction':
					if(isset($functionParameters[0]["w"])) {
						switch($functionParameters[0]["w"]) {
						case 'edit':
						case 'new':
							$data["id"] = isset($functionParameters[0]["id"]) ? $functionParameters[0]["id"] : 0;
							if($data["id"]>0) {
								$formDetail = blFunctionCall('payroll.getEmployeeFormDetail',$data["id"]);
								if($formDetail["success"]) {
									$data["LayoutName"] = $formDetail["data"][0]["FormName"];
									$data["tempChecked"] = $formDetail["data"][0]["temporary"]==1 ? " checked": "";
									$data["globalChecked"] = $formDetail["data"][0]["global"]==1 ? " checked": "";
								}
							}else{
								$data["LayoutName"] = "";
								$data["tempChecked"] = "";
								$data["globalChecked"] = "";
							}
							$objWindow = new wgui_window("payroll", "infoBox");
							$objWindow->windowTitle($objWindow->getText("txtLayoutBearbeiten")." - ".$data["LayoutName"]);
							$objWindow->windowWidth(500);
							$objWindow->windowHeight(225);
							$objWindow->loadContent("employees",$data,"employeeEditFormDlg2");
							$objWindow->showInfo();
							break;
						case 'delete':
							if($functionParameters[0]["commit"]==1) {
								$res = blFunctionCall('payroll.deleteEmployeeForm',$functionParameters[0]["id"]);
								communication_interface::jsExecute("$('#modalContainer').mb_close();");
							}else{
								$formDetail = blFunctionCall('payroll.getEmployeeFormDetail',$functionParameters[0]["id"]);
								if($formDetail["success"]) {
									$objWindow = new wgui_window("payroll", "infoBox");
									$objWindow->windowTitle("Layout loeschen");
									$objWindow->windowWidth(380);
									$objWindow->windowHeight(160);
									$data["FormId"] = $functionParameters[0]["id"];
									$data["FormName"] = $formDetail["data"][0]["FormName"];
									$objWindow->loadContent("employees",$data,"deleteEmployeeForm");
									$objWindow->showQuestion();
								}
							}
							break;
						case 'showEditor':
							$data = array();
							$objWindow = new wgui_window("payroll", "employeeForm");
							$objWindow->windowTitle($objWindow->getText("txtLayoutBearbeiten"));// - ".$data["LayoutName"]); //<-- hier eventuell noch zusaetzlich den Layoutnamen einblenden
							$objWindow->windowIcon("employee-edit32.png");
							$objWindow->windowWidth(820); //710
							$objWindow->windowHeight(550); //470
							$objWindow->dockable(false);
							$objWindow->buttonMaximize(false);
							$objWindow->resizable(false);
							$objWindow->fullscreen(false);
							$objWindow->loadContent("employees",$data,"employeeForm");
							$objWindow->showWindow();

							communication_interface::jsExecute("prlVlDirty = false;");
							communication_interface::jsExecute("prlVlDesignMode = true;");
							communication_interface::jsExecute("prlVlDesignParam = {'lid':".$functionParameters[0]["id"].",'lname':'".$functionParameters[0]["name"]."','tmp':'".$functionParameters[0]["temp"]."','glob':'".$functionParameters[0]["global"]."'};");
							communication_interface::jsExecute($this->getEmplFieldDef());
							if($functionParameters[0]["id"]>0) {
								$formDetail = blFunctionCall('payroll.getEmployeeFormDetail',$functionParameters[0]["id"]);
								if($formDetail["success"]) {
									communication_interface::jsExecute('prlVlCreateForm('.$formDetail["data"][0]["FormElements"].');');
								}
							}
							communication_interface::jsExecute("prlVlInit();");
							communication_interface::jsExecute("$('#prlVlTabContainer').height( $('#employeeForm .o').height() - $('#prlVlTabs .o').height() - 40 );");
							communication_interface::jsExecute("$('#modalContainer').mb_close();");
							break;
						}
					}
					break;
				}
			}
			break;
		case 'payroll.prlVlCallback':
			if(!isset($functionParameters[0]["confirm"])) {
				if($functionParameters[0]["fieldName"]=="DateOfBirth") { //Exception: DateOfBirth (the field 'Sex' is mandatory as well for retirement date calculation)
					communication_interface::jsExecute("cb('payroll.prlVlCallback',{'fieldName':'DateOfBirth', 'value':'".$functionParameters[0]["value"]."', 'rid':'".$functionParameters[0]["rid"]."', 'confirm':1, 'Sex':($('#Sex').length > 0 ? $('#Sex').val() : 0)});");
					break;
				}else if($functionParameters[0]["fieldName"]=="Sex") { //Exception: Sex (the field 'DateOfBirth' is mandatory as well for retirement date calculation)
					communication_interface::jsExecute("cb('payroll.prlVlCallback',{'fieldName':'Sex', 'value':'".$functionParameters[0]["value"]."', 'rid':'".$functionParameters[0]["rid"]."', 'confirm':1, 'DateOfBirth':($('#DateOfBirth').length > 0 ? $('#DateOfBirth').val() : 0)});");
					break;
				}
			}

			$cb = blFunctionCall('payroll.callbackEmployeeDetail',$functionParameters[0]);
			if($cb["success"]) {
				foreach($cb["newValues"] as $fieldValuePair) {
					if($fieldValuePair[0] == "RetirementDate") $fieldValuePair[1] = $this->convertMySQL2Date($fieldValuePair[1]);
					communication_interface::jsExecute('if($("#'.$fieldValuePair[0].'").length > 0) $("#'.$fieldValuePair[0].'").val("'.$fieldValuePair[1].'");');
				}
			} else {
				foreach($cb["fieldNames"] as $fieldName) communication_interface::jsExecute("$('#employeeForm #".$fieldName."').css('background-color','#f88');");
			}
			break;
		case 'payroll.prlVlFieldCfg':
			//communication_interface::alert("payroll.prlVlFieldCfg: ".print_r($functionParameters,true));
			if(!isset($functionParameters[0]["action"])) $functionParameters[0]["action"] = "default";
			switch($functionParameters[0]["action"]) {
			case 'save'://'payroll.prlVlFieldCfg'
				unset($functionParameters[0]["data"]["toggleSettings"]);
				$fieldDef = blFunctionCall('payroll.saveEmployeeFieldDetail', $functionParameters[0]["data"]);
				if($fieldDef["success"]) {
					communication_interface::jsExecute($this->getEmplFieldDef());
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				} else {
					if($fieldDef["errCode"]==20) {
						communication_interface::jsExecute("$('input[id^=\"prlVlFldCfg_\"], select[id^=\"prlVlFldCfg_\"]').css('background-color','');");
						foreach($fieldDef["fieldNames"] as $fieldName) communication_interface::jsExecute("$('#prlVlFldCfg_".$fieldName."').css('background-color','#f88');");
					} else {
						communication_interface::alert("Speichern fehlgeschlagen [".$fieldDef["errText"].", Code: ".$fieldDef["errCode"]."]");
					}
				}
				break;
			case 'edit'://'payroll.prlVlFieldCfg'
				//communication_interface::alert("edit: ".print_r($functionParameters,true));
				//fieldName auslesen
				$fieldName = isset($functionParameters[0]["fieldName"]) ? $functionParameters[0]["fieldName"] : "";
				$loadData = isset($functionParameters[0]["loadData"]) && $functionParameters[0]["loadData"]=="false" ? false : true;

				//Get language list
				$languageList = blFunctionCall('payroll.getLanguageList','UseForAccounts');
				$lngArr = array();
				$labelLanguagesTotal = array();
				$labelLanguagesUsed = array();
				if($languageList["success"]) foreach($languageList["data"] as $lngRow) {
					$lngArr[] = array("LanguageCode"=>$lngRow["core_intl_language_ID"], "LanguageName"=>$lngRow["language_name"]);
					$labelLanguagesTotal[] = $lngRow["core_intl_language_ID"];
				}
				$data["languageList"] = $lngArr;

				//alle Infos des Fields als JSON zusammenstellen
				$curFieldDef = array();
				$curFieldLabels = array();
				$curListItems = array();
				$displayCode = 0;
				if($loadData) {
					$fieldDef = blFunctionCall('payroll.getEmployeeFieldDetail', array("fieldName"=>$fieldName));
					if($fieldDef["success"]) {
						$curFieldDef = $fieldDef["data"];
						foreach($fieldDef["fieldLabels"] as $row) {
							$curFieldLabels[] = "'label_".$row["language"]."':'".str_replace("'","\\'",$row["label"])."'";
							$labelLanguagesUsed[] = $row["language"];
						}
						$labelLanguagesDiff = array_diff($labelLanguagesTotal, $labelLanguagesUsed);
						foreach($labelLanguagesDiff as $row) $curFieldLabels[] = "'label_".$row."':''";

						if(isset($fieldDef["listDef"]["Items"])) {
							if($fieldDef["listDef"]["ListType"]==2) $displayCode = 1;
							foreach($fieldDef["listDef"]["Items"] as $itemID=>$row) {
								$tmpLabels = array();
								foreach($row["labels"] as $language=>$label) $tmpLabels[] = "'".$language."':'".str_replace("'","\\'",$label)."'";
								$curListItems[] = "'".$itemID."':{'ListItemOrder':'".$row["ListItemOrder"]."','ListItemToken':'".$row["ListItemToken"]."', 'labels':{".implode(",",$tmpLabels)."} }";
							}
						}
					}
				}

				$objWindow = new wgui_window("payroll", "prlVlFldCfgEditMain");
				$objWindow->windowTitle($objWindow->getText("txtPersonalstammfeldverwalten"));
				$objWindow->windowIcon("file_mann32.gif");
				$objWindow->windowWidth(590);
				$objWindow->windowHeight(550);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlVlFldCfgEditMain");
				$objWindow->showWindow();

				communication_interface::jsExecute(" $('#prlVlFldCfg_listID').bind('change', function() {
					var vl = $('#prlVlFldCfg_listID').val();
					$('#ListenwerteFldCfgContainer').html('<table><tr><td>'+vl+'... <img src=\"web/img/working.gif\" /></td></tr></table>');
						cb('payroll.getPersonalstammListenwerte',{'PersonalstammListenwert':vl, 'fieldName':'".$fieldName."'});
					});
				");
					
				communication_interface::jsExecute(" $('#prlVlFldCfg_BtnAdd').bind('click', function() {
					$('#ListenwerteFldCfgContainer').html('<table><tr><td>id NEW</td></tr></table>');
						cb('payroll.getPersonalstammListenwerte',{'PersonalstammListenwert':'NEW', 'fieldName':'".$fieldName."'});
					});
				");					

				if($loadData) {
					$roundParam = 0;
					$withDecimals = strstr($curFieldDef["regexPattern"], "\\.")===false ? false : true;
					if($withDecimals) $roundParam = strstr($curFieldDef["regexPattern"], "{1,2}")===false ? 5 : 2;
//communication_interface::alert("withDecimals:".($withDecimals?"true":"false")." / roundParam:".$roundParam);

					$changeActiveFlag = ($curFieldDef["fieldDefEdit"] & 1)!=0 ? "true" : "false";
					$changeLabels = ($curFieldDef["fieldDefEdit"] & 2)!=0 ? "true" : "false";
					switch($curFieldDef["fieldType"]) {
					case 1: $fieldTypeLabel="Text"; $toggleSettings="{'mandatory':true,'guiWidth':true,'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,'maxLength':true,'noSettings':false,'hr1':false,'BtnAdd':false,'hr2':false,'listID':false,'BtnEdit':false,'BtnDel':false}"; break;
					case 2: $fieldTypeLabel="Ja/Nein Feld (Checkbox)"; $toggleSettings="{'mandatory':false,'guiWidth':false,'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,'maxLength':false,'noSettings':true,'hr1':false,'BtnAdd':false,'hr2':false,'listID':false,'BtnEdit':false,'BtnDel':false}"; break;
					case 3: $fieldTypeLabel="Zahl ".($withDecimals ? "mit" : "ohne")." Nachkommastellen"; $toggleSettings="{'mandatory':true,'guiWidth':true,'displayCode':false,'orderBy':false,'minVal':true,'maxVal':true,'maxLength':false,'noSettings':false,'hr1':false,'BtnAdd':false,'hr2':false,'listID':false,'BtnEdit':false,'BtnDel':false}"; break;
					case 4:
						$fieldTypeLabel="Liste";
						$hr1 = ($curFieldDef["fieldDefEdit"] & 8)!=0 ? "true" : "false";
						$BtnAdd = ($curFieldDef["fieldDefEdit"] & 8)!=0 ? "true" : "false";
						$BtnDel = ($curFieldDef["fieldDefEdit"] & 8)!=0 ? "true" : "false";
						$hr2 = ($curFieldDef["fieldDefEdit"] & 8)!=0 || ($curFieldDef["fieldDefEdit"] & 16)!=0 ? "true" : "false";
						$listID = ($curFieldDef["fieldDefEdit"] & 8)!=0 || ($curFieldDef["fieldDefEdit"] & 16)!=0 ? "true" : "false";
						$BtnEdit = ($curFieldDef["fieldDefEdit"] & 16)!=0 ? "true" : "false";
						$toggleSettings="{'mandatory':true,'guiWidth':true,'displayCode':true,'orderBy':true,'minVal':false,'maxVal':false,'maxLength':false,'noSettings':false,'hr1':".$hr1.",'BtnAdd':".$BtnAdd.",'hr2':".$hr2.",'listID':".$listID.",'BtnEdit':".$BtnEdit.",'BtnDel':".$BtnDel."}";
						break;
					case 5: $fieldTypeLabel="Datum"; $toggleSettings="{'mandatory':true,'guiWidth':true,'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,'maxLength':false,'noSettings':false,'hr1':false,'BtnAdd':false,'hr2':false,'listID':false,'BtnEdit':false,'BtnDel':false}"; break;
					}
					if($curFieldDef["fieldType"]!=4 && ($curFieldDef["fieldDefEdit"] & 60)==0) {
						$toggleSettings="{'mandatory':false,'guiWidth':false,'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,'maxLength':false,'noSettings':true,'hr1':false,'BtnAdd':false,'hr2':false,'listID':false,'BtnEdit':false,'BtnDel':false}";
					}
					communication_interface::jsExecute("prlVlFldCfg = {'fieldName':'".$curFieldDef["fieldName"]."', 'fieldType':'".$fieldTypeLabel."', 'userLanguage':'".session_control::getSessionInfo("language")."', 'enableActiveFlag':".$changeActiveFlag.", 'enableLables':".$changeLabels.", 'toggleSettings':".$toggleSettings.", 'data':{'fields':{'active':'".$curFieldDef["fieldActive"]."','mandatory':'".$curFieldDef["mandatory"]."','guiWidth':'".$curFieldDef["guiWidth"]."','displayCode':'".$displayCode."','orderBy':'".$curFieldDef["dataSourceSort"]."','minVal':'".($withDecimals ? number_format($curFieldDef["minVal"], $roundParam, ".", "") : round($curFieldDef["minVal"]))."','maxVal':'".($withDecimals ? number_format($curFieldDef["maxVal"], $roundParam, ".", "") : round($curFieldDef["maxVal"]))."','maxLength':'".$curFieldDef["maxLength"]."','listID':''".(count($curFieldLabels)!=0 ? ",":"").implode(",",$curFieldLabels)."}, 'listItems':{ ".implode(",",$curListItems)." } } };");
					communication_interface::jsExecute("prlVlFldCfg = {'fieldName':'".$curFieldDef["fieldName"]."', 'fieldType':'".$fieldTypeLabel."', 'userLanguage':'".session_control::getSessionInfo("language")."', 'enableActiveFlag':".$changeActiveFlag.", 'enableLables':".$changeLabels.", 'toggleSettings':".$toggleSettings.", 'data':{'fields':{'active':'".$curFieldDef["fieldActive"]."','mandatory':'".$curFieldDef["mandatory"]."','guiWidth':'".$curFieldDef["guiWidth"]."','displayCode':'".$displayCode."','orderBy':'".$curFieldDef["dataSourceSort"]."','minVal':'".($withDecimals ? number_format($curFieldDef["minVal"], $roundParam, ".", "") : round($curFieldDef["minVal"]))."','maxVal':'".($withDecimals ? number_format($curFieldDef["maxVal"], $roundParam, ".", "") : round($curFieldDef["maxVal"]))."','maxLength':'".$curFieldDef["maxLength"]."','listID':''".(count($curFieldLabels)!=0 ? ",":"").implode(",",$curFieldLabels)."}, 'listItems':{ ".implode(",",$curListItems)." } } };");
				}
				communication_interface::jsExecute("prlVlFldCfgInit();");
				break;
			case 'ListItemAdd':
			case 'ListItemEdit':
				$editMode = $functionParameters[0]["action"]=="ListItemEdit" ? true : false;

				//Get language list
				$languageList = blFunctionCall('payroll.getLanguageList','UseForAccounts');
				$lngArr = array();
				if($languageList["success"]) {
					foreach($languageList["data"] as $lngRow) {
						$lngArr[] = array("LanguageCode"=>$lngRow["core_intl_language_ID"], "LanguageName"=>$lngRow["language_name"]);
					}
				}
				$data["languageList"] = $lngArr;

				$objWindow = new wgui_window("payroll", "prlVlFldCfgEditMain");
				$objWindow->windowTitle("Personalstamm-Feld: Listenwert bearbeiten");
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(450);
				$objWindow->windowHeight(310);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlVlFldCfgLstItemEdit");
				$objWindow->showWindow();
				//communication_interface::alert(print_r($data,true));
				//communication_interface::alert("Personalstamm-Feld: Listenwert bearbeiten\n".print_r($functionParameters[0],true));
				communication_interface::jsExecute("prlVlFldCfgLoadItem('".($editMode ? $functionParameters[0]["ItemID"] : "")."');");
				communication_interface::jsExecute("$('#prlVlFldCfgLISave').bind('click', function(e) { prlVlFldCfgChangeItem('".($editMode ? $functionParameters[0]["ItemID"] : "")."'); });");
				communication_interface::jsExecute("$('#prlVlFldCfgLICancel').bind('click', function(e) { cb('payroll.prlVlFieldCfg',{'action':'edit','loadData':'false'}); });");
				break;
			case 'ListItemDel':
				//communication_interface::alert("ListItemDel:".print_r($functionParameters,true));
				if(isset($functionParameters[0]["commit"])) {
					communication_interface::jsExecute("delete prlVlFldCfg.data.listItems['".$functionParameters[0]["ItemID"]."'];");
					communication_interface::jsExecute("cb('payroll.prlVlFieldCfg',{'action':'edit','loadData':'false'});");
				}else{
					$data["ItemName"] = $functionParameters[0]["ItemText"];
					$data["ItemID"] = $functionParameters[0]["ItemID"];
					$data["technFeldname"] = $functionParameters[0]["technFeldname"];
					
					$ret = blFunctionCall('payroll.personalstammfelder.check_If_Used',$data);
					
					//communication_interface::alert("ListItemDel:".$ret);
					if ($ret > 0 ){
						$objWindow = new wgui_window("payroll", "infoBox");
						$displayText = $objWindow->getText("txtListenwertKannNichtGeloeschtWerden");
						$displayText = str_replace("#1#", $functionParameters[0]["ItemText"], $displayText);
						$displayText = str_replace("#2#", $functionParameters[0]["technFeldname"], $displayText);
						$displayText = str_replace("#3#", $ret, $displayText);
						$data["displayText"] = $displayText;
						$objWindow->windowTitle($objWindow->getText("txtKeinLoeschen"));
						$objWindow->windowWidth(380);
						$objWindow->windowHeight(160);
						$objWindow->loadContent("configuration",$data,"prlVlFldCfgLstItemDel_Not");
						$objWindow->showAlert();
					} else {
						$objWindow = new wgui_window("payroll", "infoBox"); 
						$displayText = $objWindow->getText("txtListenwertWirklichLoeschen");
						$displayText = str_replace("#1#", $functionParameters[0]["ItemText"], $displayText);
						$displayText = str_replace("#2#", $functionParameters[0]["technFeldname"], $displayText);
						$data["displayText"] = $displayText;
						$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
						$objWindow->windowWidth(380);
						$objWindow->windowHeight(170);
						$objWindow->loadContent("configuration",$data,"prlVlFldCfgLstItemDel");
						$objWindow->showQuestion();
					}
					
				}
				break;
			default:
				$data["field_list"] = array();
				$fieldDefRecs = blFunctionCall('payroll.getEmployeeFieldDef');
				if($fieldDefRecs["success"]) {
					foreach($fieldDefRecs["data"] as $row) {
						if($row["fieldDefEdit"]!=0) $data["field_list"][] = array("fieldName"=>$row["fieldName"], "label"=>$row["label"]);
					}
				}

				$objWindow = new wgui_window("payroll", "prlVlFldCfgSelect");
				$objWindow->windowTitle($objWindow->getText("txtPersonalstammfelderverwalten"));
				$objWindow->windowIcon("file_mann32.gif");
				$objWindow->windowWidth(340);
				$objWindow->windowHeight(500);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlVlFldCfgSelect");
				$objWindow->showWindow();

//				communication_interface::jsExecute("$('#prlVlFldCfg_field').html($('option', $('#prlVlFldCfg_field')).sort(function(a, b) { return a.text == b.text ? 0 : a.text < b.text ? -1 : 1 }));");
				communication_interface::jsExecute("$('#modalContainer button').eq(0).bind('click', function(e) { $('#modalContainer').mb_close(); });");
				communication_interface::jsExecute("$('#modalContainer button').eq(1).bind('click', function(e) { cb('payroll.prlVlFieldCfg', {'action':'edit', 'fieldName':$('#prlVlFldCfg_field').val()}); });");
				break;
			}
			break;
		case 'payroll.getPersonalstammListenwerte';
			$personalstammListenwert = $functionParameters[0]["PersonalstammListenwert"];
			$fieldName = $functionParameters[0]["fieldName"];
			$code = "";
			$sort = "";
			$txt_de = "";
			$txt_fr = "";
			if ($personalstammListenwert != "NEW") {
				$listWerte  = blFunctionCall('payroll.personalstammfelder.getListWerte', $personalstammListenwert);
				$code = $listWerte["Code"];
				$sort = $listWerte["Sortierzahl"];
				if(isset($listWerte["language_de"])) {$txt_de = $listWerte["language_de"]; }
				if(isset($listWerte["language_fr"])) {$txt_fr = $listWerte["language_fr"]; }
			}
			$listInhalt  = "<table class=\'listenWerte\' ><tr><th>Code</th><th>Sortierzahl</th><th>Listentext de</th><th>Listentext fr</th><th>&nbsp;</th></tr>";
			$listInhalt .= "<tr>  <input type=\'hidden\' id=\'listenWerte_id\' name=\'listenWerte_id\' value=\'".$personalstammListenwert."\'  />";
			$listInhalt .= "<td><input type=\'text\' id=\'listenWerte_code\' name=\'listenWerte_code\' value=\'".$code."\' style=\'width:40px;text-align: left;\' /></td>";
			$listInhalt .= "<td><input type=\'text\' id=\'listenWerte_sort\' name=\'listenWerte_sort\' value=\'".$sort."\' style=\'width:40px;text-align: left;\' /></td>";
			$listInhalt .= "<td><input type=\'text\' id=\'listenWerte_txt_de\' name=\'listenWerte_txt_de\' value=\'".$txt_de."\' style=\'width:130px;text-align: left;\' /></td>";
			$listInhalt .= "<td><input type=\'text\' id=\'listenWerte_txt_fr\' name=\'listenWerte_txt_fr\' value=\'".$txt_fr."\' style=\'width:130px;text-align: left;\' /></td>";
			$listInhalt .= "<td id=\"okFeld\"><button class=\"pesarisButton\"  id=\"listenWerte_BtnSave\" style=\"width: 30px; height: 22px; border-style: none; \">OK</button></td>";
			$listInhalt .= "</tr>";
			$listInhalt .= "</table>";
			communication_interface::jsExecute(" $('#ListenwerteFldCfgContainer').html( '$listInhalt' ); ");
			communication_interface::jsExecute(" $('#listenWerte_BtnSave').bind('click', function(e) { personalstammListenwertSave('".$personalstammListenwert."', '".$fieldName."'); });");
			communication_interface::jsExecute(" $('#okFeld').css('background-color',''); ");
			communication_interface::jsExecute(" $('#statusIcon').css('background-image','url(web/img/leer.png)'); ");
			break;
		case 'payroll.personalstammfelder_Save_ListenWerte':
			communication_interface::jsExecute(" $('#okFeld').css('background-color',''); ");
			communication_interface::jsExecute(" $('#listenWerte_code').css('background-color',''); ");
			communication_interface::jsExecute(" $('#listenWerte_sort').css('background-color',''); ");
			communication_interface::jsExecute(" $('#listenWerte_txt_de').css('background-color',''); ");
			communication_interface::jsExecute(" $('#listenWerte_txt_fr').css('background-color',''); ");
				
			//communication_interface::alert('payroll.personalstammfelder_Save_ListenWerte: '.print_r($functionParameters[0],true));
			$goInto = true;
			if ( strlen($functionParameters[0]["data"]["code"]) < 1) {communication_interface::jsExecute(" $('#listenWerte_code').css('background-color','#f88'); "); $goInto = false;}
			if ( strlen($functionParameters[0]["data"]["sort"]) < 1) {communication_interface::jsExecute(" $('#listenWerte_sort').css('background-color','#f88'); "); $goInto = false;}
			if ( strlen($functionParameters[0]["data"]["txt_de"]) < 1) {communication_interface::jsExecute(" $('#listenWerte_txt_de').css('background-color','#f88'); "); $goInto = false;}
			if ( strlen($functionParameters[0]["data"]["txt_fr"]) < 1) {communication_interface::jsExecute(" $('#listenWerte_txt_fr').css('background-color','#f88'); "); $goInto = false;}				
			
			$fieldName = $functionParameters[0]["fieldName"];
			if ( $goInto == true) {
				$saved = blFunctionCall('payroll.personalstammfelder.saveListenWerte', $functionParameters[0]["id"], $functionParameters[0]["data"], $fieldName);
				if ($saved == true) {
					communication_interface::jsExecute(" $('#okFeld').css('background-color','lightgreen'); ");
					communication_interface::jsExecute("  cb('payroll.prlVlFieldCfg', {'action':'edit', 'fieldName':'".$fieldName."'  });  ");
					communication_interface::jsExecute(" $('#statusIcon').css('background-image','url(web/img/isOK24x24.png)'); ");
					communication_interface::jsExecute(" $('#statusIcon').css('background-repeat','no-repeat'); ");
				} else {
					communication_interface::jsExecute(" $('#okFeld').css('background-color','#f88'); ");
					communication_interface::jsExecute(" $('#statusIcon').css('background-image','url(web/img/bullet_red16x16.png)'); ");
					communication_interface::jsExecute(" $('#statusIcon').css('background-repeat','no-repeat'); ");
				}
			}
			break;
		case 'payroll.OpenConfigMain':
			$data = array();
			$objWindow = new wgui_window("payroll", "prlConfigMain");
			$objWindow->windowTitle("Stammdatenverwaltung"); //sprintf($objWindow->getText("payrollfin_title_main"), $curFiscalyear)
			$objWindow->windowIcon("config32.png");
			$objWindow->windowWidth(850); //750
			$objWindow->windowHeight(550); //450
			$objWindow->dockable(true);
			$objWindow->fullscreen(true);
			$objWindow->loadContent("configuration",$data,"configurationMain");
			$objWindow->addEventFunction_onResize("$('#gridCfgCmpc, #gridCfgLoac, #gridCfgInsc, #gridCfgSyac, #gridCfgDasc').height( $('#prlConfigMain .o').height() - 125 );");
			$objWindow->addEventFunction_onResize("$('#gridCfgCmpc, #gridCfgLoac, #gridCfgInsc, #gridCfgSyac, #gridCfgDasc').width( $('#prlConfigMain .o').width() - 89 );");
			$objWindow->addEventFunction_onResize("$('#prlCfgCmpcTab,#prlCfgLoacTab,#prlCfgInscTab,#prlCfgAcacTab,#prlCfgSyacTab,#prlCfgDascTab').height( $('#prlConfigMain .o').height() - 85 );");
//			$objWindow->addEventFunction_onResize("prlCfgGrid.resizeCanvas();"); //prlPsoGrid.render();
			$objWindow->showWindow();

//			$defaultTblColumns = array("id","label","sign");

//			communication_interface::jsExecute("$('#prlCfgTabs').tabs();");
//			communication_interface::jsExecute('prlCfgColumns = [{id: "id", name: "LOA-Nr.", field: "id", sortable: true, resizable: true, width: 80},{id: "label", name: "Lohnart", field: "label", sortable: true, resizable: true},{id: "sign", name: "auf-/abbauend", field: "sign", sortable: true, resizable: true, cssClass: "txtCenter"}];');
			communication_interface::jsExecute('prlCfg.CfgCmpc.grid.columns = [{id: "id", name: "Firma Nr.", field: "id", sortable: true, resizable: true, width: 85},{id: "company_shortname", name: "Name", field: "company_shortname", sortable: true, resizable: true, width: 120},{id: "Street", name: "Strasse", field: "Street", sortable: true, resizable: true, width: 200},{id: "ZIP-Code", name: "PLZ", field: "ZIP-Code", sortable: true, resizable: true, width: 60},{id: "City", name: "Ort", field: "City", sortable: true, resizable: true, width: 200}];');
			communication_interface::jsExecute('prlCfg.CfgLoac.grid.columns = [{id: "id", name: "LOA-Nr.", field: "id", sortable: true, resizable: true, width: 80},{id: "label", name: "Lohnart", field: "label", sortable: true, resizable: true, width: 200},{id: "sign", name: "auf-/abbauend", field: "sign", sortable: true, resizable: true, width: 130, cssClass: "txtCenter"}];');
			communication_interface::jsExecute('prlCfg.CfgInsc.grid.columns = [{id: "InsuranceCode", name: "V.Cd", field: "InsuranceCode", sortable: true, resizable: true, width: 40},{id: "CompanyName", name: "Versicherer", field: "CompanyName", sortable: true, resizable: true, width: 200},{id: "CustomerIdentity", name: "Kunden-Nr.", field: "CustomerIdentity", sortable: true, resizable: true, width: 110},{id: "ContractIdentity", name: "Vertrag Nr.", field: "ContractIdentity", sortable: true, resizable: true, width: 100},{id: "Description", name: "Praemie", field: "Description", sortable: true, resizable: true, width: 80},{id: "payroll_account_ID", name: "LOA-Nr.", field: "payroll_account_ID", sortable: true, resizable: true, width: 75},{id: "rate", name: "Ansatz", field: "rate", sortable: true, resizable: true, width: 75},{id: "Sex", name: "m/w", field: "Sex", sortable: true, resizable: true, width: 55},{id: "AgeFrom", name: "von Alter", field: "AgeFrom", sortable: true, resizable: true, width: 70},{id: "AgeTo", name: "bis Alter", field: "AgeTo", sortable: true, resizable: true, width: 70},{id: "CodeFrom", name: "von Cd", field: "CodeFrom", sortable: true, resizable: true, width: 70},{id: "CodeTo", name: "bis Cd", field: "CodeTo", sortable: true, resizable: true, width: 70}];');
			communication_interface::jsExecute('prlCfg.CfgSyac.grid.columns = [{id: "payroll_account_ID", name: "LOA-Nr.", field: "payroll_account_ID", sortable: true, resizable: true, width: 80},{id: "Description", name: "Systemlohnart (Bezeichnung)", field: "Description", sortable: true, resizable: true, width: 300}];');
			communication_interface::jsExecute('prlCfg.CfgDasc.grid.columns = [{id: "DedAtSrcCanton", name: "Kanton", field: "DedAtSrcCanton", sortable: true, resizable: true, width: 80},{id: "AnnualSettlementMode", name: "Jahresausgleich", field: "AnnualSettlementMode", sortable: true, resizable: true, width: 200},{id: "DaysPerMonth", name: "Anrechenb. Arbeitstage", field: "DaysPerMonth", sortable: true, resizable: true, width: 200},{id: "commission", name: "Provision", field: "commission", sortable: true, resizable: true, width: 120},{id: "dasacc1", name: "QST-Basis f. JAG", field: "dasacc1", sortable: true, resizable: true, width: 150},{id: "dasacc2", name: "QST-Basis satzbest.", field: "dasacc2", sortable: true, resizable: true, width: 150}];');
/*
* Alle Tabellen in prlCfgInit() initialisieren, aber Daten in einer separaten Funktion laden... (z.B. mit nachfolgender prlCfgLoacPopulateTable ==> TRUE)
* Fuer jede Section in der Configuration muss es eine separate "PopulateTable"-Funktion geben
*/
			$this->prlCfgCmpcPopulateTable(array("updateTable"=>false));
			communication_interface::jsExecute("prlCfgInit();");
			communication_interface::jsExecute("prlCfg.CfgCmpc.grid.gridObj.resizeCanvas();");

//			session_control::setSessionSettings("payroll", "loacSettings", serialize($functionParameters[0]), true); //true = save permanently
			$assignments = array("CfgCmpc" => "cmpcSettings","CfgLoac" => "loacSettings","CfgInsc" => "inscSettings","CfgSyac" => "syacSettings","CfgDasc" => "dascSettings");
			foreach($assignments as $key=>$value) {
				$settings = session_control::getSessionSettings("payroll", $value);
				if($settings=="") {
					$settings["quickFilterEnabled"] = false;
				} else {
					if (isset($settings) && strlen($settings)>1) {
//						$settings = unserialize($settings);	
//						if (!is_array($settings)) {
//							$settings = array();
//						}
					}
				};
				$settings = json_encode($settings);
				communication_interface::jsExecute("prlCfgSetSettings(".$settings.",'".$key."');");
			}
			break;
		case 'payroll.ConfigUpdateTable':
			switch($functionParameters[0]["section"]) {
			case 'CfgCmpc':
				$this->prlCfgCmpcPopulateTable(array("updateTable"=>true));
				break;
			case 'CfgLoac':
				$this->prlCfgLoacPopulateTable(array("updateTable"=>true));
				break;
			case 'CfgInsc':
				$this->prlCfgInscPopulateTable(array("updateTable"=>true, "InsuranceType"=>(isset($functionParameters[0]["InsuranceType"]) ? $functionParameters[0]["InsuranceType"] : 1) ));
				break;
			case 'CfgSyac':
				$this->prlCfgSyacPopulateTable(array("updateTable"=>true));
				break;
			case 'CfgDasc':
				$this->prlCfgDascPopulateTable(array("updateTable"=>true));
				break;
			}
			break;
		case 'payroll.FormulaEditor':
//'payroll.FormulaEditor',{'action':'save'}
			if(!isset($functionParameters[0]["action"])) $functionParameters[0]["action"] = "";
			switch($functionParameters[0]["action"]) {
			case 'save':
//				$functionParameters[0]["data"]
//communication_interface::alert(print_r($functionParameters[0],true));
				$ret = blFunctionCall("payroll.saveFormula", $functionParameters[0]["data"]);
				if($ret["success"]) {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");

					//nach dem Speichern, wird SELECT im LOA-Editor aktualisiert
					$objWindow = new wgui_window("payroll", "prlFormulaOverview");
					$formulaList = blFunctionCall('payroll.getFormulaList');
					$formulaArr = array();
					if($formulaList["success"]) foreach($formulaList["data"] as $formulaRow) $formulaArr[] = "['".$formulaRow["id"]."','".str_replace(array("*","/","+","-","factor","surcharge","quantity","rate","amount"),array(" &times; "," &#247; "," + "," - ",$objWindow->getText("loacFieldFACTOR"),$objWindow->getText("loacFieldSURCHARGE"),$objWindow->getText("loacFieldQUANTITY"),$objWindow->getText("loacFieldRATE"),$objWindow->getText("loacFieldAMOUNT")),$formulaRow["formula"])."']";
					communication_interface::jsExecute("prlLoaFeRefreshSelect([".implode(",",$formulaArr)."]);");
				}else{
					communication_interface::alert("Speichern fehlgeschlagen [".$ret["errText"].", Code: ".$ret["errCode"]."]");
				}
				break;
			case 'editor':
				$formulaList = blFunctionCall('payroll.getFormulaList');
				$data = array();
				$formulaItems = array();
				if($formulaList["success"]) foreach($formulaList["data"] as $formulaRow) {
					if($functionParameters[0]["id"]==$formulaRow["id"]) {
						$arr = array();
						preg_match_all('/-?[0-9]{1,8}(\.[0-9]{1,5})?|\+|-|\*|\/|\(|\)|factor|surcharge|quantity|rate|amount/s', $formulaRow["formula"], $arr);
						foreach($arr[0] as $item) $formulaItems[] = "'".$item."'";
						break;
					}
				}

				$objWindow = new wgui_window("payroll", "prlFormulaEditor");
				$objWindow->windowTitle($objWindow->getText("prlLoaFeTitle"));
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(850);
				$objWindow->windowHeight(330);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlFormulaEditor");
				$objWindow->showWindow();

				communication_interface::jsExecute("prlLoaFe = {'id':".$functionParameters[0]["id"].", 'data':[".implode(",",$formulaItems)."], 'valuePattern':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/, 'labels':{'factor':'FAKTOR','surcharge':'ZUSCHLAG','quantity':'MENGE','rate':'ANSATZ','amount':'BETRAG','+':'+','-':'&#150;','*':'&times;','/':'&#247;','(':'(',')':')','errValuePattern':'Fehlerhafter Konstanten-Wert.','errBracket':'Unterschiedliche Anzahl oeffnender und schliessender Klammern.'} };");
				communication_interface::jsExecute("prlLoaFeInit();");
				break;
			case 'delete':
//communication_interface::alert(print_r($functionParameters[0],true));
				if(!isset($functionParameters[0]["commit"])) {
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
					$objWindow->windowWidth(370);
					$objWindow->windowHeight(155);
					$objWindow->setContent("<br/>Bitte bestaetigen Sie die Loeschung.<br/><br/><button onclick='cb(\"payroll.FormulaEditor\",{\"action\":\"delete\", \"id\":\"".$functionParameters[0]["id"]."\", \"commit\":\"yes\"});'>Loeschen</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
					$objWindow->showQuestion();
				}else{
					$ret = blFunctionCall("payroll.deleteFormula", array("id"=>$functionParameters[0]["id"]));
					if($ret["success"]) {
						communication_interface::jsExecute("$('#modalContainer').mb_close();");

						//nach dem Speichern, wird SELECT im LOA-Editor aktualisiert
						$objWindow = new wgui_window("payroll", "prlFormulaOverview");
						$formulaList = blFunctionCall('payroll.getFormulaList');
						$formulaArr = array();
						if($formulaList["success"]) foreach($formulaList["data"] as $formulaRow) $formulaArr[] = "['".$formulaRow["id"]."','".str_replace(array("*","/","+","-","factor","surcharge","quantity","rate","amount"),array(" &times; "," &#247; "," + "," - ",$objWindow->getText("loacFieldFACTOR"),$objWindow->getText("loacFieldSURCHARGE"),$objWindow->getText("loacFieldQUANTITY"),$objWindow->getText("loacFieldRATE"),$objWindow->getText("loacFieldAMOUNT")),$formulaRow["formula"])."']";
						communication_interface::jsExecute("prlLoaFeRefreshSelect([".implode(",",$formulaArr)."]);");
					}else{
						communication_interface::alert("Loeschen fehlgeschlagen [".$ret["errText"].", Code: ".$ret["errCode"]."]");
					}
				}
				break;
			default: //overview
				$objWindow = new wgui_window("payroll", "prlFormulaOverview");

				$formulaList = blFunctionCall('payroll.getFormulaList');
				$formulaArr = array();
				if($formulaList["success"]) foreach($formulaList["data"] as $formulaRow) {
					$formulaArr[] = array("id"=>$formulaRow["id"], "formula"=>str_replace(array("*","/","+","-","factor","surcharge","quantity","rate","amount"),array(" &times; "," &#247; "," + "," - ",$objWindow->getText("loacFieldFACTOR"),$objWindow->getText("loacFieldSURCHARGE"),$objWindow->getText("loacFieldQUANTITY"),$objWindow->getText("loacFieldRATE"),$objWindow->getText("loacFieldAMOUNT")),$formulaRow["formula"]));
				}
				$data["formula_list"] = $formulaArr;

				$objWindow->windowTitle($objWindow->getText("prlLoaFeTitle"));
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(250);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlFormulaOverview");
				$objWindow->showWindow();
				break;
			}
			break;
		case 'payroll.ConfigFldModOverview': //prefix: prlFldMod
			$data = array();
			//$data["filter_list"] = ;
			$objWindow = new wgui_window("payroll", "payrollFieldModifierOv");
			$objWindow->windowTitle($objWindow->getText("prlCfgLoacBtnFldMod"));
			$objWindow->windowIcon("config32.png");
			$objWindow->windowWidth(850);
			$objWindow->windowHeight(450);
			$objWindow->dockable(false);
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->modal(true);
			$objWindow->loadContent("configuration",$data,"payrollFieldModifierOv");
			$objWindow->showWindow();

			$prlFldModData = array();
			$accConfig = blFunctionCall('payroll.getFieldModifierList');
			if($accConfig["success"]) {
				foreach($accConfig["data"] as $rec) {
					if($rec["ModifierType"]==1) {
						$FldModType = "Feld: ";
						switch($rec["TargetField"]) {
						case 3:
							$FldModType .= "MENGE";
							break;
						case 4:
							$FldModType .= "ANSATZ";
							break;
						case 5:
							$FldModType .= "BETRAG";
							break;
						}
						$RValues = $rec["FieldName"]!="" ? "[".$rec["FieldNameUlLabel"]."]" : $rec["TargetValue"];
					}else{
						$FldModType = "LIMITEN";
						$RValues = $rec["max_limit"]." | ".$rec["deduction"]." | ".$rec["min_limit"];
					}
					$prlFldModData[] = "{'id':'".$rec["id"]."','payroll_account_ID':'".$rec["payroll_account_ID"]."','FldModType':'".$FldModType."','FilterName':'".str_replace("'","\\'",$rec["FilterName"])."','processing_order':'".$rec["processing_order"]."','RValues':'".$RValues."'}";
				}
			}

			communication_interface::jsExecute('prlFldModColumns = [{id: "payroll_account_ID", name: "LOA", field: "payroll_account_ID", sortable: true, resizable: true, width: 60},{id: "FldModType", name: "Typ", field: "FldModType", sortable: true, resizable: true, width: 120},{id: "FilterName", name: "Personalfilter", field: "FilterName", sortable: true, resizable: true, width: 225},{id: "processing_order", name: "Prio.", field: "processing_order", sortable: true, resizable: true, width: 60, cssClass: "txtCenter"},{id: "RValues", name: "Ersatzwert(e)", field: "RValues", sortable: true, resizable: true, width: 270}];');
			communication_interface::jsExecute("prlFldModData = [".implode(",",$prlFldModData)."];");
			communication_interface::jsExecute("prlFldModInit();");
			break;
		case 'payroll.ConfigEditFormOpen':
			$sectionCfg = array(
				"CfgCmpc"=>array("windowTitle"=>"Firma/Company"
								, "windowWidth"=>720
								, "windowHeight"=>370
								, "dataSourceFnc"=>"payroll.getCompanyDetail"
								, "fieldNameTransl"=>array("HR-RC-Name"=>"HR_RC_Name"
														 ,"ZIP-Code"=>"ZIP_Code"
														 ,"UID-EHRA"=>"UID_EHRA"
														 ,"BUR-REE-Number"=>"BUR_REE_Number"))
								,
				"CfgSyac"=>array("windowTitle"=>"Systemlohnart"
								, "windowWidth"=>525
								, "windowHeight"=>160
								, "dataSourceFnc"=>"payroll.getPayrollAccountMappingDetail"
								, "fieldNameTransl"=>array())
								,
				"CfgInscInsr"=>array("windowTitle"=>"Versicherer bearbeiten"
								, "windowWidth"=>550
								, "windowHeight"=>225
								, "dataSourceFnc"=>"payroll.getInsuranceCompanyList"
								, "fieldNameTransl"=>array())
								,
				"CfgInscInsrEdt"=>array("windowTitle"=>"Versicherer bearbeiten"
								, "windowWidth"=>550
								, "windowHeight"=>175
								, "dataSourceFnc"=>"payroll.getInsuranceCompanyDetail"
								, "fieldNameTransl"=>array())
								,
				"CfgInscCode"=>array("windowTitle"=>"Versicherungscodes bearbeiten"
								, "windowWidth"=>550
								, "windowHeight"=>225
								, "dataSourceFnc"=>"payroll.getInsuranceCodeList"
								, "fieldNameTransl"=>array())
								,
				"CfgInscCodeEdt"=>array("windowTitle"=>"Versicherungscodes bearbeiten"
								, "windowWidth"=>550
								, "windowHeight"=>250
								, "dataSourceFnc"=>"payroll.getInsuranceCodeDetail"
								, "fieldNameTransl"=>array())
								,
				"CfgInsc"=>array("windowTitle"=>"Versicherungspraemien bearbeiten"
								, "windowWidth"=>650
								, "windowHeight"=>275
								, "dataSourceFnc"=>"payroll.getInsuranceRateDetail"
								, "fieldNameTransl"=>array())
								,
				"CfgDasc"=>array("windowTitle"=>"QST: Kantonale Einstellungen"
								, "windowWidth"=>510
								, "windowHeight"=>410
								, "dataSourceFnc"=>"payroll.getDedAtSrcCantonDetail"
								, "fieldNameTransl"=>array())
								,
				"CfgDascGlob"=>array("windowTitle"=>"QST: Globale Einstellungen"
								, "windowWidth"=>850
								, "windowHeight"=>330
								, "dataSourceFnc"=>"payroll.getDedAtSrcGlobalSettings"
								, "fieldNameTransl"=>array())
								,
				"CfgFldMod"=>array("windowTitle"=>"Feld-Modifikator bearbeiten"
								, "windowWidth"=>650
								, "windowHeight"=>360
								, "dataSourceFnc"=>"payroll.getFieldModifierDetail"
								, "fieldNameTransl"=>array()
								)
				);

//communication_interface::alert("section:".$functionParameters[0]["section"]." / id:".$functionParameters[0]["id"]);
			$sectionID = $functionParameters[0]["section"];
			if(!isset($sectionCfg[$sectionID])) {
				communication_interface::alert("Diese Funktion ist noch nicht aktiv");
				break;
			}
			$recID = isset($functionParameters[0]["id"]) ? $functionParameters[0]["id"] : 0;
			$editMode = $recID==0 ? false : true;
			if($sectionID=="CfgDasc" && trim($recID)!="" && strlen(trim($recID))==2) $editMode = true;
			$data = array();
//communication_interface::alert("recID:".$recID." / editMode:".($editMode ? "TRUE" : "FALSE"));
			$nextCompanyId = $recID;
				//gewisse Masken muessen mit SELECT-Daten gefuellt werden
				switch($sectionID) {
				case "CfgCmpc":
					$zahlstellenDaten = blFunctionCall('payroll.auszahlen.getZahlstellenListe', $recID);
					if($zahlstellenDaten["success"]) $data["zahlstellen_list"] = $zahlstellenDaten["data"];
					if ($recID == 0) {
						$nextCompanyId = blFunctionCall('payroll.getNextCompanyId');
						//communication_interface::alert("neu? ".$nextCompanyId);	
						$data["id"] = $nextCompanyId;				
						$data["rid"] = $nextCompanyId;				
						$data["prlFormCfg_id"] = $nextCompanyId;				
						$data["prlFormCfg_rid"] = $nextCompanyId;				
					}
					break;
				case "CfgInscInsr":
					$ret = blFunctionCall($sectionCfg[$sectionID]["dataSourceFnc"]);
					if($ret["success"]) {
						foreach($ret["data"] as &$rec) {
							if($rec["InsuranceID"]=="") $rec["InsuranceID"]="--";
							if($rec["SubNumber"]=="") $rec["SubNumber"]="--";
						}
						$data["insurance_list"] = $ret["data"];
					}
					$editMode = false;
					break;
				case "CfgInscCode":
					$ret = blFunctionCall($sectionCfg[$sectionID]["dataSourceFnc"],array("InsuranceType"=>$functionParameters[0]["InsuranceType"]));
					if($ret["success"]) $data["insuranceCode_list"] = $ret["data"];
					$data["InsuranceType"] = $functionParameters[0]["InsuranceType"];
					$editMode = false;
					break;
				case "CfgInscCodeEdt":
					$data["InsuranceType"] = $functionParameters[0]["InsuranceType"];
					$companyList = blFunctionCall('payroll.getCompanyList');
					if($companyList["success"]) $data["company_list"] = $companyList["data"];
					$ret = blFunctionCall("payroll.getInsuranceCompanyList");
					if($ret["success"]) {
						foreach($ret["data"] as &$rec) {
							if($rec["InsuranceID"]=="") $rec["InsuranceID"]="--";
							if($rec["SubNumber"]=="") $rec["SubNumber"]="--";
						}
						$data["insurance_list"] = $ret["data"];
					}
					$ret = blFunctionCall("payroll.getLanguageList","UseForAccounts");
					if($ret["success"]) $data["label_list"] = $ret["data"];
					break;
				case "CfgInsc":
					$data["InsuranceType"] = $functionParameters[0]["InsuranceType"];
					$codeList = blFunctionCall('payroll.getInsuranceCodeList',array("InsuranceType"=>$functionParameters[0]["InsuranceType"]));
					if($codeList["success"]) {
						foreach($codeList["data"] as &$row) {
							$codeLabel = blFunctionCall('payroll.getInsuranceCodeDetail',array("id"=>$row["id"]));
							if($codeLabel["success"]) $row["label"] = $codeLabel["data"]["label_".session_control::getSessionInfo("language")];
						}
						$data["insurance_list"] = $codeList["data"];
					}
					break;
				case "CfgFldMod":
					$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>true, "FilterForCalculation"=>true));
					if($ret["success"]) $data["filter_list"] = $ret["data"];
					break;
				case "CfgDascGlob":
					$editMode = true;
					break;
				}
			$objWindow = new wgui_window("payroll", "prlForm".$sectionID);
			$objWindow->windowTitle($sectionCfg[$sectionID]["windowTitle"]);
			$objWindow->windowIcon("config32.png");
			$objWindow->windowWidth($sectionCfg[$sectionID]["windowWidth"]);
			$objWindow->windowHeight($sectionCfg[$sectionID]["windowHeight"]);
			$objWindow->dockable(false);
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->modal(true);
			$objWindow->loadContent("configuration",$data,"prlForm".$sectionID);
			$objWindow->showWindow();

			if($sectionID=="CfgFldMod") {
				communication_interface::jsExecute($this->getEmplFieldDef());

				//Get payroll account list
				$accountList = blFunctionCall('payroll.getPayrollAccountList'); //id,payroll_year_ID,label,sign
				$arrAccCollector = array();
				if($accountList["success"]) {
					foreach($accountList["data"] as $row) $arrAccCollector[] = "['".$row["id"]."', '".str_replace("'","\\'",$row["label"])."', ".$row["var_fields"]."]";
				}
				communication_interface::jsExecute("prlCalcDataLOA = [".implode(", ",$arrAccCollector)."];");
				communication_interface::jsExecute("prlCfgFldModInit({'labels':{'dateFnc':[ [':D',' (Tag)'], [':M',' (Monat)'], [':Y',' (Jahr)'] ]}});");
			}

			if($editMode) {
				$ret = blFunctionCall($sectionCfg[$sectionID]["dataSourceFnc"], array("id"=>$recID));
				if($sectionID=="CfgFldMod") {
					$ret["data"]["FldModType"] = $ret["data"]["ModifierType"]==1 ? $ret["data"]["TargetField"] : 9;
					$ret["data"]["TargetType0"] = $ret["data"]["FieldName"]=="" ? 1 : 0;
					$ret["data"]["TargetType1"] = $ret["data"]["FieldName"]=="" ? 0 : 1;
				}
				$arrData = array();
				if($ret["success"]) {
					foreach($ret["data"] as $fieldName=>$fieldValue) {
						$fieldName = isset($sectionCfg[$sectionID]["fieldNameTransl"][$fieldName]) ? $sectionCfg[$sectionID]["fieldNameTransl"][$fieldName] : $fieldName;
						$arrData[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
					}
				}
//communication_interface::alert(print_r($arrData,true));
				communication_interface::jsExecute("prlCfgEditFormInit('".$sectionID."',{".implode(",",$arrData)."});");
			}

			communication_interface::jsExecute("$('#prlFormCfgSave').bind('click', function(e) { prlCfgEditFormSave('".$sectionID."'); });");
			communication_interface::jsExecute("$('#prlFormCfgCancel').bind('click', function(e) { ".($sectionID=="CfgFldMod" ? "cb('payroll.ConfigFldModOverview');" : "$('#modalContainer').mb_close();")." });");
			if($sectionID=="CfgInsc") {
				communication_interface::jsExecute("$('#prlFormCfg_payroll_insurance_code_ID').bind('change', function(e) { var a=['insr','custno','contrno','descr']; var o=$(this).find(':selected'); $.each(a, function() { $('#prlFormCfg'+this.toString()).val(o.attr(this.toString())); }); });");
				communication_interface::jsExecute("$('#prlFormCfg_payroll_insurance_code_ID').change();");
//				communication_interface::jsExecute("$.each(['insr','custno','contrno','descr'], function() { $('#prlFormCfg'+this.toString()).val($('#prlFormCfg_payroll_insurance_code_ID').find(':selected').attr(this.toString())); });");
			} else if($sectionID=="CfgFldMod") communication_interface::jsExecute("prlCfgFldModToggle();");
			if($sectionID=="CfgCmpc") {
				//communication_interface::alert("recID=".$recID.", nextCompanyId=".$nextCompanyId);
				communication_interface::jsExecute("$('#prlFormCfg_id').val('".$nextCompanyId."')");			
				communication_interface::jsExecute("$('#selZahlstelle').bind('click',    function(e) {cb('payroll.paymentSplit',{'action':'GUI_bank_source_edit','bankId':this.value,'company_ID':'".$recID."'}) } )");			
				communication_interface::jsExecute("$('#neueZahlstelle').bind('click', function(e) {cb('payroll.paymentSplit',{'action':'GUI_bank_source_edit','bankId':'0',       'company_ID':'".$recID."'}); })");			
			}
			break;
		case 'payroll.ConfigEditFormSave':
			$sectionCfg = array(
						"CfgCmpc"=>array("saveFnc"=>"payroll.saveCompanyDetail", "fieldNameTransl"=>array("HR-RC-Name"=>"HR_RC_Name","ZIP-Code"=>"ZIP_Code","UID-EHRA"=>"UID_EHRA","BUR-REE-Number"=>"BUR_REE_Number")),
						"CfgSyac"=>array("saveFnc"=>"payroll.savePayrollAccountMappingDetail", "fieldNameTransl"=>array()),
						"CfgInscInsrEdt"=>array("saveFnc"=>"payroll.saveInsuranceCompanyDetail", "fieldNameTransl"=>array()),
						"CfgInscCodeEdt"=>array("saveFnc"=>"payroll.saveInsuranceCodeDetail", "fieldNameTransl"=>array()),
						"CfgInsc"=>array("saveFnc"=>"payroll.saveInsuranceRateDetail", "fieldNameTransl"=>array()),
						"CfgFldMod"=>array("saveFnc"=>"payroll.saveFieldModifierDetail", "fieldNameTransl"=>array()),
						"CfgDasc"=>array("saveFnc"=>"payroll.saveDedAtSrcCantonDetail", "fieldNameTransl"=>array()),
						"CfgDascGlob"=>array("saveFnc"=>"payroll.saveDedAtSrcGlobalSettings", "fieldNameTransl"=>array())
					);

			$sectionID = $functionParameters[0]["section"];
			if(!isset($sectionCfg[$sectionID])) {
				communication_interface::alert("Diese Funktion ist noch nicht aktiv\n\npayroll.ConfigEditFormSave ".print_r($functionParameters[0],true));
				break;
			}

			$data = $functionParameters[0]["data"];
			if(count($sectionCfg[$sectionID]["fieldNameTransl"])>0) {
				foreach($sectionCfg[$sectionID]["fieldNameTransl"] as $newKey=>$oldKey) {
					if(isset($data[$oldKey])) {
						$data[$newKey] = $data[$oldKey];
						unset($data[$oldKey]);
					}
				}
			}

//communication_interface::alert("1190_".print_r($data,true));
//communication_interface::alert("1191_".print_r($functionParameters[0],true));
			if($sectionID=="CfgFldMod") {
				$data["ModifierType"] = $data["FldModType"]==9 ? 2 : 1;
				$data["TargetField"] = $data["FldModType"]==9 ? 0 : $data["FldModType"];
				if($data["TargetType0"]==1) $data["FieldName"] = "";
			}

//communication_interface::alert("sectionID: ".$sectionID);
//communication_interface::alert("blFunctionCall: ".$sectionCfg[$sectionID]["saveFnc"]);
			$ret = blFunctionCall($sectionCfg[$sectionID]["saveFnc"], $data);
			if($ret["success"]) {
				if($sectionID=="CfgFldMod") {
					communication_interface::jsExecute("cb('payroll.ConfigFldModOverview');");
				} else {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
					switch($sectionID) {
					case 'CfgCmpc':
						$this->prlCfgCmpcPopulateTable(array("updateTable"=>true));
						break;
					case 'CfgInsc':
						$InsuranceType = 1;
						if(isset($data["InsuranceType"])) $InsuranceType = $data["InsuranceType"];
						if(isset($functionParameters[0]["InsuranceType"])) $InsuranceType = $functionParameters[0]["InsuranceType"];
						$this->prlCfgInscPopulateTable(array("updateTable"=>true, "InsuranceType"=>$InsuranceType ));
						break;
					case 'CfgSyac':
						$this->prlCfgSyacPopulateTable(array("updateTable"=>true));
						break;
					case 'CfgDasc':
						$this->prlCfgDascPopulateTable(array("updateTable"=>true));
						break;
					}
				}
			} else {
				//communication_interface::alert("[1219] Error: ".$ret["errText"]." ");
				communication_interface::jsExecute("$('input[id^=\"prlFormCfg_\"], select[id^=\"prlFormCfg_\"]').css('background-color','');");
				foreach($ret["fieldNames"] as $fieldName) {
					$fieldName = isset($sectionCfg[$sectionID]["fieldNameTransl"][$fieldName]) ? $sectionCfg[$sectionID]["fieldNameTransl"][$fieldName] : $fieldName;
					communication_interface::jsExecute("$('#prlFormCfg_".$fieldName."').css('background-color','#f88');");
				}
			}
			break;
		case 'payroll.ConfigEditFormDelete':
			if(!isset($functionParameters[0]["commit"])) {
				$InsuranceType = isset($functionParameters[0]["InsuranceType"]) ? $functionParameters[0]["InsuranceType"] : 0;
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
				$objWindow->windowWidth(370);
				$objWindow->windowHeight(155);
				$objWindow->setContent("<br/>Bitte bestaetigen Sie die Loeschung.<br/><br/><button onclick='cb(\"payroll.ConfigEditFormDelete\",{\"section\":\"".$functionParameters[0]["section"]."\", \"id\":\"".$functionParameters[0]["id"]."\", \"InsuranceType\":\"".$InsuranceType."\", \"commit\":\"yes\"});'>Loeschen</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
				$objWindow->showQuestion();
			}else{
				$sectionCfg = array(
							"CfgCmpc"=>array("deleteFnc"=>"payroll.deleteCompanyDetail"),
							"CfgInscInsr"=>array("deleteFnc"=>"payroll.deleteInsuranceCompanyDetail"),
							"CfgInscCode"=>array("deleteFnc"=>"payroll.deleteInsuranceCodeDetail"),
							"CfgInsc"=>array("deleteFnc"=>"payroll.deleteInsuranceRateDetail"),
							"CfgFldMod"=>array("deleteFnc"=>"payroll.deleteFieldModifierDetail"),
							"CfgDasc"=>array("deleteFnc"=>"payroll.deleteDedAtSrcCantonDetail")
						);

				$sectionID = $functionParameters[0]["section"];
				if(!isset($sectionCfg[$sectionID])) {
					communication_interface::alert("Diese Funktion ist noch nicht aktiv");
					break;
				}
				$ret = blFunctionCall($sectionCfg[$sectionID]["deleteFnc"], array("id"=>$functionParameters[0]["id"]));
				if($ret["success"]) {
					if($sectionID=="CfgFldMod") {
						communication_interface::jsExecute("cb('payroll.ConfigFldModOverview');");
					}else{
						communication_interface::jsExecute("$('#modalContainer').mb_close();");
						switch($sectionID) {
						case 'CfgCmpc':
							$this->prlCfgCmpcPopulateTable(array("updateTable"=>true));
							break;
						case 'CfgInsc':
							$this->prlCfgInscPopulateTable(array("updateTable"=>true, "InsuranceType"=>(isset($functionParameters[0]["InsuranceType"]) ? $functionParameters[0]["InsuranceType"] : 1) ));
							break;
						case 'CfgDasc':
							$this->prlCfgDascPopulateTable(array("updateTable"=>true));
							break;
						}
					}
				}else{
					communication_interface::alert("Loeschen fehlgeschlagen");
				}
			}
			break;
		case 'payroll.ConfigImportDasRates':
			$step = isset($functionParameters[0]["step"]) ? $functionParameters[0]["step"] : 1;
			switch($step) {
			case 1: //form anzeigen
				$data["MAX_FILE_SIZE"] = "9000000";
				$data["param"] = openssl_encrypt( serialize( array("cb_function"=>"payroll.ConfigImportDasRates","data"=>array("step"=>"2") ) ), "aes128", "pw_rcvfile_pw");
				$objWindow = new wgui_window("payroll", "prlCfgDascUplRt");
				$objWindow->windowTitle("QST: Tarifdaten importieren");
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(200);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlCfgDascUplRt");
				$objWindow->showWindow();

				communication_interface::jsExecute("$('input[type=file][name=rcvfile]').css('margin-bottom','10px');"); //.css('opacity','0.6').hide()
				communication_interface::jsExecute("$('#prlCfgDascUplRtWait').css('position','absolute').css('top',60).css('left',210).hide();"); //.css('opacity','0.6').hide()	.css('background-color','#fff')
				communication_interface::jsExecute("$('#prlFormCfgSave').bind('click', function(e) { $('#prlCfgDascUplRtWait').show(); $(this).attr('disabled','disabled'); $('#prlFormCfgCancel').attr('disabled','disabled'); $('#modalContainer div.ui-tabs').css('opacity','0.5'); $('#prlCfgDascUplRtFrm').submit(); });");
				communication_interface::jsExecute("$('#prlFormCfgCancel').bind('click', function(e) { $('#modalContainer').mb_close(); });");
				break;
			case 2: //dateiupload abgeschlossen... Tarifdaten importieren
//				communication_interface::alert(print_r($functionParameters[0],true));
				if($functionParameters[0]["success"]) {
					$fm = new file_manager();
					if( $fm->setTmpDir($functionParameters[0]["tmpDirToken"]) ) {
//						communication_interface::alert("path: ".$fm->getFullPath());
//						communication_interface::alert("file: ".$functionParameters[0]["fileName"]);

						$arr = explode(".",$functionParameters[0]["fileName"]);
						if(strtolower($arr[count($arr)-1])!="txt") {
							$fm->deleteDir(); //weil falsche Dateierweiterung, kann TmpDir gleich wieder geloescht werden
							$objWindow = new wgui_window("payroll", "infoBox");
							$objWindow->windowTitle("Falsche Dateierweiterung");
							$objWindow->windowWidth(450);
							$objWindow->windowHeight(200);
							$objWindow->setContent("<div class=\"ui-tabs ui-widget-content ui-corner-all PesarisWGUI center\"><br/>Falsche Dateierweiterung: Erwartet wurde '.txt', uebermittelt wurde eine Datei mit der Endung '.".strtolower($arr[count($arr)-1])."'.<br/><br/></div><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
							$objWindow->showAlert();
						}else{
							//TODO: soweit ist alles OK -> wir rufen jetzt die BL-Funktion fuer den Import der Tarifdatei auf
							$ret = blFunctionCall('payroll.importDedAtSrcRates',array("tmpDirToken"=>$functionParameters[0]["tmpDirToken"], "fileName"=>$functionParameters[0]["fileName"]));
							if($ret["success"]) {
								$objWindow = new wgui_window("payroll", "infoBox");
								$objWindow->windowTitle("Daten-Import erfolgreich");
								$objWindow->windowWidth(450);
								$objWindow->windowHeight(150);
								$objWindow->setContent("<div class=\"ui-tabs ui-widget-content ui-corner-all PesarisWGUI center\"><br/>Der Daten-Import fuer den Kanton ".$ret["canton"]." war erfolgreich.<br/><br/></div><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
								$objWindow->showInfo();
							}else{
								$objWindow = new wgui_window("payroll", "infoBox");
								$objWindow->windowTitle("Daten-Import fehlgeschlagen");
								$objWindow->windowWidth(420);
								$objWindow->windowHeight(170);
								$objWindow->setContent("<div class=\"ui-tabs ui-widget-content ui-corner-all PesarisWGUI center\"><br/>Fehlermeldung: ".$ret["errText"]."<br/><br/></div><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
								$objWindow->showAlert();
							}
						}
					}else{
						$objWindow = new wgui_window("payroll", "infoBox");
						$objWindow->windowTitle("Dateiuebermittlung fehlgeschlagen");
						$objWindow->windowWidth(420);
						$objWindow->windowHeight(170);
						$objWindow->setContent("<div class=\"ui-tabs ui-widget-content ui-corner-all PesarisWGUI center\"><br/>Datei konnte nicht gelesen werden.<br/><br/></div><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
						$objWindow->showAlert();
					}
				}else{
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle("Dateiuebermittlung fehlgeschlagen");
					$objWindow->windowWidth(420);
					$objWindow->windowHeight(150);
					$objWindow->setContent("<div class=\"ui-tabs ui-widget-content ui-corner-all PesarisWGUI center\"><br/>Es wurde keine Datei empfangen.<br/><br/></div><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
					$objWindow->showAlert();
				}
				break;
			default:
				communication_interface::alert("default:".print_r($functionParameters[0],true));
				break;
			}
			break;
		case 'payroll.OpenPayrollAccountForm':
/*
	$functionParameters[0]["id"]		<--- account ID
	$functionParameters[0]["dirty"]
	$functionParameters[0]["wndStatus"]
*/
//			$currentFormId = session_control::getSessionSettings("payroll", "psoCurrentEmplForm");
//			if($currentFormId!="") {
//				$formDetail = blFunctionCall('payroll.getEmployeeFormDetail',$currentFormId);
//			}

			if($functionParameters[0]["wndStatus"]==0) {
				$objWindow = new wgui_window("payroll", "payrollAccountForm");

				//nur ausfuehren, wenn Window noch nicht geoeffnet ist
				$data = array();

				$languageList = blFunctionCall('payroll.getLanguageList','UseForAccounts');
				$lngArr = array();
				if($languageList["success"]) foreach($languageList["data"] as $lngRow) $lngArr[] = array("LanguageCode"=>$lngRow["core_intl_language_ID"], "LanguageName"=>$lngRow["language_name"]);
				$data["language_list"] = $lngArr;

				$formulaList = blFunctionCall('payroll.getFormulaList');
				$formulaArr = array();
				if($formulaList["success"]) foreach($formulaList["data"] as $formulaRow) {
					$formulaArr[] = array("id"=>$formulaRow["id"], "formula"=>str_replace(array("*","/","+","-","factor","surcharge","quantity","rate","amount"),array(" &times; "," &#247; "," + "," - ",$objWindow->getText("loacFieldFACTOR"),$objWindow->getText("loacFieldSURCHARGE"),$objWindow->getText("loacFieldQUANTITY"),$objWindow->getText("loacFieldRATE"),$objWindow->getText("loacFieldAMOUNT")),$formulaRow["formula"]));
				}
				$data["formula_list"] = $formulaArr;

//				$objWindow->windowTitle("Mitarbeiterdaten bearbeiten (<span id="#prlVlTitle"></span>)"); //Mitarbeiterdaten erfassen / aendern		 id='#prlVlTitle'
				$objWindow->windowTitle($objWindow->getText("txtLohnartBearbeiten") ); 
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(870); //710  
				$objWindow->windowHeight(560);     
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->loadContent("configuration",$data,"payrollAccountForm");
				$objWindow->showWindow();

				communication_interface::jsExecute("prlLoacDirty = false;");
				$definitions = "";
				$definitions .= "prlLoacLabels = {'fwdField0':'".$objWindow->getText("loacFieldLIMITS")."', 'fwdField1':'".$objWindow->getText("loacFieldFACTOR")."', 'fwdField2':'".$objWindow->getText("loacFieldSURCHARGE")."', 'fwdField3':'".$objWindow->getText("loacFieldQUANTITY")."', 'fwdField4':'".$objWindow->getText("loacFieldRATE")."', 'fwdField5':'".$objWindow->getText("loacFieldAMOUNT")."', 'fwdNegVal':'".$objWindow->getText("fwdNegVal")."', 'invertVal':'".$objWindow->getText("invertVal")."'};\n";
				$definitions .= "prlLoacFieldDef = {'id':{'maxlength':5, 'mandatory':true,'rgx':/^[0-9a-zA-Z]{1,5}$/}, 'label_de':{'maxlength':45, 'mandatory':true,'rgx':''}, 'label_fr':{'maxlength':45, 'mandatory':true,'rgx':''}, 'label_it':{'maxlength':45, 'mandatory':true,'rgx':''}, 'max_limit':{'maxlength':9, 'mandatory':true,'rgx':/^[0-9]{1,6}(\.[0-9]{1,2})?$/}, 'deduction':{'maxlength':9, 'mandatory':true,'rgx':/^[0-9]{1,6}(\.[0-9]{1,2})?$/}, 'min_limit':{'maxlength':9, 'mandatory':true,'rgx':/^[0-9]{1,6}(\.[0-9]{1,2})?$/}, 'factor':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/}, 'surcharge':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/}, 'amount':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/}, 'rate':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/}, 'quantity':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/}, 'round_param':{'maxlength':7, 'mandatory':true,'rgx':/^[0-9]{1,2}(\.[0-9]{1,4})?$/}, 'quantity_conversion':{'maxlength':10, 'mandatory':true, 'rgx':/^[0-9]{1,5}(\.[0-9]{1,4})?$/}, 'quantity_unit_de':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'quantity_unit_fr':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'quantity_unit_it':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'rate_conversion':{'maxlength':10, 'mandatory':true, 'rgx':/^[0-9]{1,5}(\.[0-9]{1,4})?$/}, 'rate_unit_de':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'rate_unit_fr':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'rate_unit_it':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'amount_conversion':{'maxlength':10, 'mandatory':true, 'rgx':/^[0-9]{1,5}(\.[0-9]{1,4})?$/}};";
				communication_interface::jsExecute($definitions);
				communication_interface::jsExecute("prlLoacInit();");
/*
prlLoacLoaAll = {'87':{'number':'123456', 'label':'Monatslohn'},'61':{'number':'654321', 'label':'Stundenlohn'},'12':{'number':'456512', 'label':'AHV'},'103':{'number':'789432', 'label':'UVGZ'},'35':{'number':'355645', 'label':'Ãœberstundenzuschlag'},'109':{'number':'852514', 'label':'Super-LOA'}};
prlLoacLoaInExcl = []; //array mit IDs, deren LOA nicht zur Auswahl stehen
prlLoacLoaOutExcl = []; //array mit IDs, deren LOA nicht zur Auswahl stehen
prlLoacFieldDef = {'account_number':{'maxlength':5, 'mandatory':true,'rgx':/^[0-9a-zA-Z]{1,5}$/}, 'label_de':{'maxlength':45, 'mandatory':true,'rgx':''}, 'label_fr':{'maxlength':45, 'mandatory':true,'rgx':''}, 'label_it':{'maxlength':45, 'mandatory':true,'rgx':''}, 'max_limit':{'maxlength':9, 'mandatory':true,'rgx':/^[0-9]{1,6}(\.[0-9]{1,2})?$/}, 'deduction':{'maxlength':9, 'mandatory':true,'rgx':/^[0-9]{1,6}(\.[0-9]{1,2})?$/}, 'min_limit':{'maxlength':9, 'mandatory':true,'rgx':/^[0-9]{1,6}(\.[0-9]{1,2})?$/}, 'factor':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/}, 'surcharge':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/}, 'amount':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/}, 'rate':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/}, 'quantity':{'maxlength':13, 'mandatory':true,'rgx':/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/}, 'round_param':{'maxlength':7, 'mandatory':true,'rgx':/^[0-9]{1,2}(\.[0-9]{1,4})?$/}, 'quantity_conversion':{'maxlength':10, 'mandatory':true, 'rgx':/^[0-9]{1,5}(\.[0-9]{1,4})?$/}, 'quantity_unit_de':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'quantity_unit_fr':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'quantity_unit_it':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'rate_conversion':{'maxlength':10, 'mandatory':true, 'rgx':/^[0-9]{1,5}(\.[0-9]{1,4})?$/}, 'rate_unit_de':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'rate_unit_fr':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'rate_unit_it':{'maxlength':10, 'mandatory':false, 'rgx':''}, 'amount_conversion':{'maxlength':10, 'mandatory':true, 'rgx':/^[0-9]{1,5}(\.[0-9]{1,4})?$/}};

prlLoacLoadData({'account_number':'4456', 'label_de':'AHV', 'label_fr':'Lohnarde de l\'AHV', 'label_it':'Lohnardo del Ahavauo', 'sign':'1', 'print_account':'1', 'var_fields':'2', 'accIn':[ {'id':87, 'fwdField':3, 'fwdNegVal':true},{'id':103, 'fwdField':3, 'fwdNegVal':true} ], 'accOut': [ {'id':103, 'fwdField':5, 'fwdNegVal':false} ], 'having_limits':true, 'max_limit':'126000.00', 'deduction':'0.000', 'min_limit':'0.000', 'limits_calc_mode':'1', 'limits_aux_account_ID':'1', 'input_assignment':'3', 'factor':'0.0000', 'surcharge':'0.0000', 'amount':'0.0000', 'rate':'0.0000', 'quantity':'0.0000', 'having_calculation':true, 'calculation_formula':1, 'having_rounding':false, 'round_param':'0.0000', 'output_assignment':'5', 'quantity_conversion':'1.0000', 'quantity_decimal':'10', 'quantity_print':true, 'quantity_unit_de':'', 'quantity_unit_fr':'', 'quantity_unit_it':'', 'rate_conversion':'1.0000', 'rate_decimal':'2', 'rate_print':true, 'rate_unit_de':'%', 'rate_unit_fr':'%', 'rate_unit_it':'%', 'amount_conversion':'1.0000', 'amount_decimal':'10', 'amount_print':true});
*/

//				communication_interface::jsExecute("$('#prlVlTabContainer').height( $('#employeeForm .o').height() - $('#prlVlTabs .o').height() - 40 );");
				communication_interface::jsExecute("$('#payrollAccountForm .n').html('Lohnart bearbeiten (<span id=\"prlLoacTitle\"></span>)');");
			}

			$accountList = blFunctionCall('payroll.getPayrollAccountList');
			if($accountList["success"]) {
				$arrRowCollector = array();
				foreach($accountList["data"] as $row) {
					$arrRowCollector[] = "'".$row["id"]."':{'number':'".$row["id"]."', 'label':'".str_replace("'","\\'",$row["label"])."'}";
				}
				communication_interface::jsExecute("prlLoacLoaAll = {".implode(",", $arrRowCollector)."};");
			}
			communication_interface::jsExecute("prlLoacFillAuxLoaSelection();");


			if(isset($functionParameters[0]["id"]) && $functionParameters[0]["id"]!="" && $functionParameters[0]["id"]!="0") {
//communication_interface::alert("id: ".$functionParameters[0]["id"]);
				$booleanFields = array('having_limits','having_calculation','having_rounding','quantity_print','rate_print','amount_print');
				$prlAccData = blFunctionCall('payroll.getPayrollAccountDetail',$functionParameters[0]["id"]);
				if($prlAccData["success"]) {
					$arrOut = array();
					foreach($prlAccData["data"][0] as $fieldName=>$fieldValue) {
						if(in_array($fieldName, $booleanFields)) $arrOut[] = "'".$fieldName."':".($fieldValue==1 ? "true" : "false");
						else $arrOut[] = "'".$fieldName."':'".$fieldValue."'";
					}
					$accIn = array();
					foreach($prlAccData["incomingAccounts"] as $accInRow) {
						$accIn[] = "{'id':'".$accInRow["id"]."', 'fwdField':".$accInRow["field_assignment"].", 'fwdNegVal':".($accInRow["fwd_neg_values"]==1 ? "true" : "false").", 'fwdTarget':".$accInRow["child_account_field"].", 'fwdInvVal':".($accInRow["invert_value"]==1 ? "true" : "false")."}";
					}
					$accOut = array();
					foreach($prlAccData["targetAccounts"] as $accOutRow) {
						$accOut[] = "{'id':'".$accOutRow["id"]."', 'fwdField':".$accOutRow["field_assignment"].", 'fwdNegVal':".($accOutRow["fwd_neg_values"]==1 ? "true" : "false").", 'fwdTarget':".$accOutRow["child_account_field"].", 'fwdInvVal':".($accOutRow["invert_value"]==1 ? "true" : "false")."}";
					}
					$arrOut[] = "'accIn':[".implode(",", $accIn)."]";
					$arrOut[] = "'accOut':[".implode(",", $accOut)."]";
					communication_interface::jsExecute("prlLoacLoadData({".implode(",", $arrOut)."});");
//error_log("\n\nprlLoacLoadData({".implode(",", $arrOut)."});\n\n", 3, "/var/log/copronet-application.log");
					communication_interface::jsExecute("$('#prlLoacTitle').html('".$prlAccData["data"][0]["id"]." - ".$prlAccData["data"][0]["label_".session_control::getSessionInfo("language")]."');");
/*
, 'accIn':[ {'id':'87', 'fwdField':3, 'fwdNegVal':true},{'id':103, 'fwdField':3, 'fwdNegVal':true} ], 'accOut': [ {'id':'103, 'fwdField':5, 'fwdNegVal':false} ]
*/
				}
			}else{
				communication_interface::jsExecute("$('#payrollAccountForm input[id^=\"label_\"],#payrollAccountForm input[id^=\"quantity_unit_\"],#payrollAccountForm input[id^=\"rate_unit_\"]').val();"); //clear all language relevant input fields
//				communication_interface::jsExecute("prlLoacLoadData({'id':'', 'sign':'1', 'print_account':'1', 'var_fields':'2, 'having_limits':false, 'max_limit':'0.00', 'deduction':'0.00', 'min_limit':'0.00', 'limits_calc_mode':'1', 'limits_aux_account_ID':'0', 'input_assignment':'3', 'factor':'0.0000', 'surcharge':'0.0000', 'amount':'0.0000', 'rate':'0.0000', 'quantity':'0.0000', 'having_calculation':true, 'payroll_formula_ID':0, 'having_rounding':false, 'round_param':'0.0000', 'output_assignment':'5', 'quantity_conversion':'1.0000', 'quantity_decimal':'10', 'quantity_print':true, 'rate_conversion':'1.0000', 'rate_decimal':'10', 'rate_print':true, 'amount_conversion':'1.0000', 'amount_decimal':'10', 'amount_print':true});");
//				communication_interface::jsExecute("prlLoacLoadData({'id':'','processing_order':'0','sign':'0','print_account':'0','var_fields':'0','input_assignment':'5','output_assignment':'3','having_limits':false,'having_calculation':true,'having_rounding':false,'payroll_formula_ID':'8','surcharge':'0.00000','factor':'0.00000','quantity':'0.00000','rate':'0.00000','amount':'0.00000','round_param':'0.0000','limits_aux_account_ID':'0','limits_calc_mode':'0','max_limit':'0.00','min_limit':'0.00','deduction':'0.00','quantity_conversion':'1.0000','quantity_decimal':'10','quantity_print':true,'rate_conversion':'1.0000','rate_decimal':'10','rate_print':true,'amount_conversion':'1.0000','amount_decimal':'10','amount_print':true});");
				communication_interface::jsExecute("prlLoacLoadData({'id':'','processing_order':'0','sign':'0','print_account':'0','var_fields':'0','input_assignment':'3','output_assignment':'5','having_limits':false,'having_calculation':true,'having_rounding':false,'payroll_formula_ID':'0','surcharge':'0.00000','factor':'0.00000','quantity':'0.00000','rate':'0.00000','amount':'0.00000','round_param':'0.0000','limits_aux_account_ID':'0','limits_calc_mode':'0','max_limit':'0.00','min_limit':'0.00','deduction':'0.00','quantity_conversion':'1.0000','quantity_decimal':'10','quantity_print':true,'rate_conversion':'1.0000','rate_decimal':'10','rate_print':true,'amount_conversion':'1.0000','amount_decimal':'10','amount_print':true});");

				//eventually select first tab and set focus to first field
				communication_interface::jsExecute("$('#prlLoacTitle').html('* NEUERFASSUNG *');");
			}

			$definitions = "";
			$definitions .= "prlLoacLoaInExcl = ['".$functionParameters[0]["id"]."'];\n";
			$definitions .= "prlLoacLoaOutExcl = ['".$functionParameters[0]["id"]."'];";
			communication_interface::jsExecute($definitions);

			communication_interface::jsExecute("prlLoacRID = '".$functionParameters[0]["id"]."';"); //set record ID of current employee (on client side)
			break;
		case 'payroll.prlLoacDelete':
			if(isset($functionParameters[0]["commit"])) {
//				communication_interface::alert("LOA mit ID ".$functionParameters[0]["id"]." wird geloescht.");
				$ret = blFunctionCall('payroll.deletePayrollAccount', $functionParameters[0]);
				if($ret["success"]) {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
					$this->prlCfgLoacPopulateTable(array("updateTable"=>true));
				}else{
					communication_interface::alert("Fehler. Die LOA mit der ID ".$functionParameters[0]["id"]." konnte nicht geloescht werden.");
				}
			}else{
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
				$objWindow->windowWidth(380);
				$objWindow->windowHeight(155);
				$objWindow->setContent("<br/>Sind Sie sicher, dass Sie die Lohnart '".$functionParameters[0]["id"]."' loeschen moechten?<br/><br/><button onclick='cb(\"payroll.prlLoacDelete\",{\"id\":\"".$functionParameters[0]["id"]."\", \"commit\":\"yes\"});'>Loeschen</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
				$objWindow->showQuestion();
			}
			break;
		case 'payroll.prlLoacSave':
			//communication_interface::alert("payroll.prlLoacSave: ".print_r($functionParameters,true));
 			$ret = blFunctionCall('payroll.savePayrollAccount', $functionParameters[0]["rid"], $functionParameters[0]["data"]);
			
			//communication_interface::alert('payroll.prlLoacSave::'.print_r($ret, true));
			
 			if($ret["success"]) {
				communication_interface::jsExecute("$('#payrollAccountForm').mb_close();");
				$this->prlCfgLoacPopulateTable(array("updateTable"=>true));

				//TODO: HIER WERDEN DATEN NEU IN UEBERSICHTSTABELLE GELADEN. DAS IST NOCH NICHT OPTIMAL GELOEST, DA *ALLE* DATEN NACH JEDER AENDERUNG AN DEN CLIENT GESCHICKT WERDEN!!
/*
				$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
				$tblData = "prlPsoData = [";
				$firstPass = true;
				if($employeeList["success"]) {
					foreach($employeeList["data"] as $row) {
						$tblData .= $firstPass ? "{" : ", {";
						$tblRow = "";
						foreach($row as $fieldName=>$fieldValue) {
							if($fieldName=="Sex") $fieldValue = $fieldValue=="F" ? "w" : "m"; //TODO: Werte dynamisch ersetzen!
							$tblRow .= ($tblRow == "" ? "" : ", ")."'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
						}
						$tblData .= $tblRow."}";
						$firstPass = false;
					}
				}
				$tblData .= "];";
				communication_interface::jsExecute($tblData);
				communication_interface::jsExecute("prlPsoGrid.invalidate();");
				communication_interface::jsExecute("prlPsoDataView.setItems(prlPsoData);");
				communication_interface::jsExecute("prlPsoDataView.refresh();");
				communication_interface::jsExecute("prlPsoGrid.updateRowCount();");
				communication_interface::jsExecute("prlPsoGrid.render();");
*/
 			}else{
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle("Speichern fehlgeschlagen");
				$objWindow->windowWidth(420);
				$objWindow->windowHeight(170);
				$objWindow->setContent("<br/>Die Daten konnten nicht gespeichert werden. (err: ".$ret["errCode"].")<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
				$objWindow->showAlert();
				communication_interface::jsExecute("$('#payrollAccountForm input').css('background-color','');");
				communication_interface::jsExecute("$('#payrollAccountForm select').css('background-color','');");
				foreach($ret["fieldNames"] as $fieldName) communication_interface::jsExecute("$('#payrollAccountForm #loac_".$fieldName."').css('background-color','#f88');");
 			}
			break;
		case 'payroll.prlCfgSaveSettings':
//communication_interface::alert(serialize($functionParameters[0]));
			$assignments = array("CfgCmpc" => "cmpcSettings","CfgLoac" => "loacSettings","CfgInsc" => "inscSettings","CfgSyac" => "syacSettings","CfgDasc" => "dascSettings");
			if(isset($assignments[$functionParameters[0]["section"]])) {
				session_control::setSessionSettings("payroll", $assignments[$functionParameters[0]["section"]], serialize($functionParameters[0]["settings"]), true); //true = save permanently
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtEinstellungen"));
				$objWindow->setContent("<br/>".$objWindow->getText("prlPsoSettingsSaved")."<br/><br/><button class='PesarisButton' onclick='$(\"#modalContainer\").mb_close();'>".$objWindow->getText("btnOK")."</button>");
				$objWindow->showInfo();
			}else communication_interface::alert("wrong section id");
			break;
		case 'payroll.prlCalcOverview':
			$data = array();
			$objWindow = new wgui_window("payroll", "prlCalcOverview");
			$objWindow->windowTitle($objWindow->getText("txtLohnBearbeiten")); //sprintf($objWindow->getText("payrollfin_title_main"), $curFiscalyear)
			$objWindow->windowIcon("calculator32.png");
			$objWindow->windowWidth(850); 
			$objWindow->windowHeight(550); 
			$objWindow->dockable(true);
			$objWindow->fullscreen(true);
			$objWindow->loadContent("calculation",$data,"calculationOverview");
			$objWindow->addEventFunction_onResize("$('#gridCalcOv').height( $('#prlCalcOverview .o').height() - 90 );");
			$objWindow->addEventFunction_onResize("$('#gridCalcOv').width( $('#prlCalcOverview .o').width() - 40 );");
			$objWindow->addEventFunction_onResize("prlCalcOvGrid.resizeCanvas();");
			$objWindow->showWindow();
//error_log("*1*\n", 3, "/var/log/daniel.log");

			$prdInfo = $this->prlCalcOvFillYearPeriodCmb();
//error_log("prdInfo:\n".print_r($prdInfo,true)."\n", 3, "/var/log/daniel.log");

			communication_interface::jsExecute('prlCalcOvCfg = {statusLabel:["","berechnet","fixiert/ausbezahlt","verbucht"], finAccLabel:" (FIBU)", mgmtAccLabel:" (BEBU)"};');
			communication_interface::jsExecute('prlCalcOvColumns = [ {id: "EmployeeNumber", name: "Pers.Nr.", field: "EmployeeNumber", sortable: true, resizable: true, width: 100},' .
																	'{id: "Lastname", name: "Nachname", field: "Lastname", sortable: true, resizable: true},' .
																	'{id: "Firstname", name: "Vorname", field: "Firstname", sortable: true, resizable: true},' .
																	'{id: "Sex", name: "m/w", field: "Sex", sortable: true, width: 50, cssClass: "txtCenter"},' .
																	'{id: "ProcStatus", name: "Status", field: "ProcStatus", sortable: true, resizable: true},' .
																	'{id: "grossSalary", name: "Bruttolohn", field: "grossSalary", sortable: false, resizable: true, cssClass: "txtRight", formatter: ' .
																			'function (row, cell, value, columnDef, dataContext) { return $.aafwFormatNumber(value,{aSep: "\'",aDec: "."}); }},' .
																	'{id: "netSalary", name: "Nettolohn", field: "netSalary", sortable: false, resizable: true, cssClass: "txtRight", formatter: ' .
																			'function (row, cell, value, columnDef, dataContext) { return $.aafwFormatNumber(value,{aSep: "\'",aDec: "."}); }},' .
																	'{id: "payout", name: "Auszahlung", field: "payout", sortable: true, resizable: true, cssClass: "txtRight", ' .
																	'formatter: function (row, cell, value, columnDef, dataContext) { return $.aafwFormatNumber(value,{aSep: "\'",aDec: "."}); }}];'); 
																	//, formatter: function (row, cell, value, columnDef, dataContext) { return $.aafwFormatNumber(value, {aSep: "\\'", aDec: ".", vMin: "-999999999.99"}); }
			//Get employee list and prepare data in order to fill the client-side table
			$this->prlCalcOvPopulateTable(array("year"=>$prdInfo["currentYear"], "majorPeriod"=>$prdInfo["currentMajorPeriod"], "updateTable"=>false));
			communication_interface::jsExecute("prlCalcOvInit();");

			$settings = session_control::getSessionSettings("payroll", "calcOvSettings");
			if($settings=="") {
				$settings["quickFilterEnabled"] = false;
			}else $settings = unserialize($settings);
			$settings = json_encode($settings);
			communication_interface::jsExecute("prlCalcOvSetSettings(".$settings.");");
			break;
		case 'payroll.calcOvSaveSettings':
			session_control::setSessionSettings("payroll", "calcOvSettings", serialize($functionParameters[0]), true); //true = save permanently
			$objWindow = new wgui_window("payroll", "infoBox");
			$objWindow->windowTitle($objWindow->getText("txtEinstellungen"));
			$objWindow->setContent("<br/>".$objWindow->getText("prlPsoSettingsSaved")."<br/><br/><button class='PesarisButton' onclick='$(\"#modalContainer\").mb_close();'>".$objWindow->getText("btnOK")."</button>");
			$objWindow->showInfo();
			break;
		case 'payroll.prlCalcOvFnc':
			switch($functionParameters[0]["functionNumber"]) {
			case 1: //year dropdown change event
				$prdInfo = $this->prlCalcOvFillYearPeriodCmb(array("year"=>$functionParameters[0]["year"]));
				$this->prlCalcOvPopulateTable(array("year"=>$prdInfo["currentYear"], "majorPeriod"=>$prdInfo["currentMajorPeriod"], "updateTable"=>true));
				break;
			case 2: //major period dropdown change event
//				communication_interface::alert("major_period geaendert: ".$functionParameters[0]["year"]." / ".$functionParameters[0]["majorPeriod"]);
				$this->prlCalcOvPopulateTable(array("year"=>$functionParameters[0]["year"], "majorPeriod"=>$functionParameters[0]["majorPeriod"], "updateTable"=>true));
				break;
			}
			break;
		case 'payroll.prlCalcOvProcess':
			switch($functionParameters[0]["functionNumber"]) {
			case 5: //Auszahlung erstellen...
				//communication_interface::alert("GUI Auszahldaten erzeugen");
				$data = array();
				$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>true, "FilterForCalculation"=>true));
				if($ret["success"]) $data["employeeFilter_list"] = $ret["data"];

				$ret = blFunctionCall("payroll.auszahlen.getZahlstellenListe", 0);
				if($ret["success"]) $data["zahlstellen_list"] = $ret["data"];

				$objWindow = new wgui_window("payroll", "wndIDAuszahlenGenerate"); // aufrufendes Plugins, als HTML "id" damit ist das Fenster per JS, resp. jQuery ansprechbar
				$data["btnBerechnen"] = $objWindow->getText("btnNeuBerechnen");
				
				//Die jetztige Periode ist				
				$PeriodeDieserMonat = blFunctionCall('payroll.auszahlen.getActualPeriodName');
				$data["period"] = $PeriodeDieserMonat;
								
				$data["wechselkursEUR"] = blFunctionCall('payroll.getCurrencyForexRate', "EUR" );
				$data["wechselkursUSD"] = blFunctionCall('payroll.getCurrencyForexRate', "USD" );
												
				$data["nochNichtAusbezahlteMA"] = blFunctionCall('payroll.auszahlen.getAuszahlMitarbeiteranzahl'); 
								
				$uebermorgen = strtotime ( '+2 day' , strtotime ( date("d.m.Y") ) ) ;
				//$data["valutaDatum"] = $uebermorgen;				
				$data["valutaDatum"] = date ( 'd.m.Y' , $uebermorgen );				
				$objWindow->windowTitle($objWindow->getText("txtAuszahlungErstellen").": ".$PeriodeDieserMonat);
				$objWindow->windowIcon("auszahlen32.png");
				$objWindow->windowWidth(800); 
				$objWindow->windowHeight(410); 
				$objWindow->modal(true);	
				$objWindow->loadContent("auszahlen",$data,"wguiBlockAuszahlenGenerateWindow"); //Template-Datei, zu uebergebende Daten , Template-Blocks
				$objWindow->showWindow();
				communication_interface::jsExecute("prlAuszahlenGenerateWindowInit();");
				break;
			case 1: //Lohn berechnen
				blFunctionCall('payroll.calculate');
				blFunctionCall('payroll.auszahlen.truncateEffectifPayoutTable');
				$this->prlCalcOvPopulateTable(array("updateTable"=>true));
				break;
			case 4: //Fixieren
				if(!isset($functionParameters[0]["subFunction"])) {
					$prdValidity = blFunctionCall('payroll.checkPeriodValidity');
					if($prdValidity["processedCount"] == $prdValidity["payoutCount"]) {
						$objWindow = new wgui_window("payroll", "infoBox");
						$objWindow->windowTitle($objWindow->getText("prlPayoutTitle"));
						$objWindow->windowWidth(420);
						$objWindow->windowHeight(170);
						$objWindow->setContent("<br/>".$objWindow->getText("prlPayout_all")."<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
						$objWindow->showInfo();
						break;
					}

					$data = array();
					//Get payroll company list
					$companyList = blFunctionCall('payroll.getCompanyList');
					if($companyList["success"]) $data["companyList"] = $companyList["data"];
					$objWindow = new wgui_window("payroll", "CalculationFinalizeForm");
					$objWindow->windowTitle($objWindow->getText("prlPayoutTitle"));
					$objWindow->windowIcon("calculator32.png");
					$objWindow->windowWidth(380);
					$objWindow->windowHeight(200);
					$objWindow->dockable(false);
					$objWindow->buttonMaximize(false);
					$objWindow->resizable(false);
					$objWindow->fullscreen(false);
					$objWindow->modal(true);
					$objWindow->loadContent("calculation",$data,"processPayment");
					$objWindow->showWindow();

					communication_interface::jsExecute("prlPayoutInit();");
				}else{
					switch($functionParameters[0]["subFunction"]) {
					case 1: //select specific employees
						if($functionParameters[0]["data"]["filter_mode"]==2) {
							//show employee selection window
							$objWindow = new wgui_window("payroll", "EmployeeSelector");
							$data["dbFilterList"] = array();
							$data["dbFilterList"][] = array("id"=>0, "FilterName"=>$objWindow->getText("prlPsSelAllEmployees"));
							$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>false, "FilterForCalculation"=>true));
							if($ret["success"]) foreach($ret["data"] as $row) $data["dbFilterList"][] = $row;

							$objWindow->windowTitle($objWindow->getText("prlPayoutTitleSelection"));
							$objWindow->windowIcon("employee-edit32.png");
							$objWindow->windowWidth(700);
							$objWindow->windowHeight(320);
							$objWindow->dockable(false);
							$objWindow->buttonMaximize(false);
							$objWindow->resizable(false);
							$objWindow->fullscreen(false);
							$objWindow->modal(true);
							$objWindow->loadContent("utilities",$data,"EmployeeSelector");
							$objWindow->showWindow();

							//TODO: als "data_source" nur die Mitarbeiter der aktuellen Periode, die bereits berechnet aber noch nicht ausbezahlt wurden
							//Get employee list and prepare data in order to fill the client-side table
							$queryOption["columns"] = array("EmployeeNumber", "Firstname", "Lastname");
							$queryOption["prepend_id"] = true;
							$queryOption["query_filter"] = "";
							$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
							$emplData = array();
							if($employeeList["success"]) {
								foreach($employeeList["data"] as $row) $emplData[] = "[".$row["id"].",'".$row["EmployeeNumber"]."','".$row["Firstname"]."','".$row["Lastname"]."']";
							}

							//TODO: exclude eventuell gar nicht noetig!
							//Get employee id's to exclude
							$queryOption["columns"] = array("id");
							$queryOption["prepend_id"] = false;
							$queryOption["query_filter"] = "";
							$queryOption["data_source"] = "current_period";
							$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
							$exclData = array();
							if($employeeList["success"]) {
								foreach($employeeList["data"] as $row) $exclData[] = $row["id"];
							}

							communication_interface::jsExecute("prlPsSelALL = [ ".implode(",",$emplData)." ];");
							communication_interface::jsExecute("prlPsSelEXCL = [".implode(",",$exclData)."];");
							communication_interface::jsExecute("prlPsSelCFG = {'saveCB':function(){alert('Mitarbeiter-Auswahlfunktion ist momentan noch nicht aktiv.');}, 'cancelCB':'', 'errNoSelection':'".$objWindow->getText("prlPsSelErrMissingSelection")."'};");
							communication_interface::jsExecute("prlPsSelInit();");
							communication_interface::jsExecute("prlPsSelUpdate();");
						}else{
							//all information available -> process payment
//							communication_interface::alert(print_r($functionParameters[0],true));
							$ret = blFunctionCall('payroll.processPayment',$functionParameters[0]["data"]);
							if(!$ret["success"]) {
								if(isset($ret["errField"])) {
									communication_interface::jsExecute("$('#prlPayout_".$ret["errField"]."').css('background-color','#f88');");
//									communication_interface::alert("Ungueltiger Wert im Feld ".$ret["errField"]);
									break;
								}else{
									communication_interface::alert("Fehler [Code ".$ret["errCode"]."]");
									break;
								}
							}
							$fileName = $this->doDbBackup("F");
							communication_interface::jsExecute("$('#modalContainer').mb_close();");
							$this->prlCalcOvPopulateTable(array("updateTable"=>true));
						}
						break;
					}
				}
				//meldung, falls noch bei keinen MA eine Berechnung gemacht wurde... ohne Berechnung ist naemlich keine Verbuchung moeglich
//				communication_interface::alert("auszahlen...");
//				blFunctionCall('payroll.calculate');
				break;
			case 2: //Verbuchen
				if(!isset($functionParameters[0]["subFunction"])) {
					$prdValidity = blFunctionCall('payroll.checkPeriodValidity');
					if(($prdValidity["finaccEntryCount"] != 0 || $prdValidity["mgmtaccEntryCount"] != 0) && !isset($functionParameters[0]["ignoreWarning"])) {
						$objWindow = new wgui_window("payroll", "infoBox");
						$objWindow->windowTitle("Hinweis");
						$objWindow->windowWidth(420);
						$objWindow->windowHeight(170);
						$objWindow->setContent("<br/>Bitte beachten Sie, dass in dieser Abrechnungsperiode bereits Buchungen existieren.<br/><br/><button onclick='cb(\"payroll.prlCalcOvProcess\", {\"functionNumber\":2, \"ignoreWarning\":1});'>Fortfahren</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
						$objWindow->showInfo();
						break;
					}

					$data = array();
					//Get payroll company list
					$companyList = blFunctionCall('payroll.getCompanyList');
					if($companyList["success"]) $data["companyList"] = $companyList["data"];
					$objWindow = new wgui_window("payroll", "AccountingFinalizeForm");
					$objWindow->windowTitle($objWindow->getText("prlAccEtrTitle"));
					$objWindow->windowIcon("calculator32.png");
					$objWindow->windowWidth(400);
					$objWindow->windowHeight(310);
					$objWindow->dockable(false);
					$objWindow->buttonMaximize(false);
					$objWindow->resizable(false);
					$objWindow->fullscreen(false);
					$objWindow->modal(true);
					$objWindow->loadContent("calculation",$data,"enterAccountingData");
					$objWindow->showWindow();

					communication_interface::jsExecute("prlAccEtrInit();");
					communication_interface::jsExecute("prlAccEtrToggleFieldStatus();");
				}else{
					switch($functionParameters[0]["subFunction"]) {
					case 1:
						if($functionParameters[0]["data"]["filter_mode"]==2) {
							//show employee selection window
							$objWindow = new wgui_window("payroll", "EmployeeSelector");
							$data["dbFilterList"] = array();
							$data["dbFilterList"][] = array("id"=>0, "FilterName"=>$objWindow->getText("prlPsSelAllEmployees"));
							$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>false, "FilterForCalculation"=>true));
							if($ret["success"]) foreach($ret["data"] as $row) $data["dbFilterList"][] = $row;

							$objWindow->windowTitle($objWindow->getText("prlAccEtrTitleSelection"));
							$objWindow->windowIcon("employee-edit32.png");
							$objWindow->windowWidth(700);
							$objWindow->windowHeight(320);
							$objWindow->dockable(false);
							$objWindow->buttonMaximize(false);
							$objWindow->resizable(false);
							$objWindow->fullscreen(false);
							$objWindow->modal(true);
							$objWindow->loadContent("utilities",$data,"EmployeeSelector");
							$objWindow->showWindow();

							//TODO: als "data_source" nur die Mitarbeiter der aktuellen Periode, die bereits berechnet+ausbezahlt aber noch nicht verbucht wurden
							//Get employee list and prepare data in order to fill the client-side table
							$queryOption["columns"] = array("EmployeeNumber", "Firstname", "Lastname");
							$queryOption["prepend_id"] = true;
							$queryOption["query_filter"] = "";
							$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
							$emplData = array();
							if($employeeList["success"]) {
								foreach($employeeList["data"] as $row) $emplData[] = "[".$row["id"].",'".$row["EmployeeNumber"]."','".$row["Firstname"]."','".$row["Lastname"]."']";
							}

							//TODO: exclude eventuell gar nicht noetig!
							//Get employee id's to exclude
							$queryOption["columns"] = array("id");
							$queryOption["prepend_id"] = false;
							$queryOption["query_filter"] = "";
							$queryOption["data_source"] = "current_period";
							$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
							$exclData = array();
							if($employeeList["success"]) {
								foreach($employeeList["data"] as $row) $exclData[] = $row["id"];
							}

							communication_interface::jsExecute("prlPsSelALL = [ ".implode(",",$emplData)." ];");
							communication_interface::jsExecute("prlPsSelEXCL = [".implode(",",$exclData)."];");
							communication_interface::jsExecute("prlPsSelCFG = {'saveCB':function(){alert('Mitarbeiter-Auswahlfunktion ist momentan noch nicht aktiv.');}, 'cancelCB':'', 'errNoSelection':'".$objWindow->getText("prlPsSelErrMissingSelection")."'};");
							communication_interface::jsExecute("prlPsSelInit();");
							communication_interface::jsExecute("prlPsSelUpdate();");
						}else{
							//all information available -> process payment
							//communication_interface::alert(print_r($functionParameters[0],true));
							$ret = blFunctionCall('payroll.processFinMgmtAccountingEntry',$functionParameters[0]["data"]);
							if(!$ret["success"]) {
								if(isset($ret["errField"])) {
									communication_interface::jsExecute("$('#prlAccEtr_".$ret["errField"]."').css('background-color','#f88');");
//									communication_interface::alert("Ungueltiger Wert im Feld ".$ret["errField"]);
									break;
								}else{
									communication_interface::alert("Fehler [Code ".$ret["errCode"]."]");
									break;
								}
							}
							communication_interface::jsExecute("$('#modalContainer').mb_close();");
							$this->prlCalcOvPopulateTable(array("updateTable"=>true));
						}
						break;
					}
				}
				blFunctionCall('payroll.auszahlen.initTrackingTable');
				break;
			case 3: //Periode fixieren
				if(isset($functionParameters[0]["commit"]) && $functionParameters[0]["commit"]==1) {
					//progress dialog
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle("Periode abschliessen");
					$objWindow->windowWidth(380);
					$objWindow->windowHeight(200);
					$objWindow->setContent("<br/>Die Periode wird abgeschlossen.<br/><br/>Bitte warten... <img src=\"/web/img/working.gif\"><br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
					$objWindow->showInfo();
					communication_interface::jsExecute("cb(\"payroll.prlCalcOvProcess\", {\"functionNumber\":3, \"go\":1, \"formData\":".json_encode($functionParameters[0]["formData"])."});");
				}else if(isset($functionParameters[0]["go"]) && $functionParameters[0]["go"]==1) {
					//communication_interface::alert(print_r($functionParameters[0],true));
					$fileName = $this->doDbBackup("X");
					$ret = blFunctionCall('payroll.closePeriod',$functionParameters[0]["formData"]);
					if(!$ret["success"]) communication_interface::alert("Fehler Code ".$ret["errCode"]);

					session_control::setSessionSettings("payroll", "finalizeEqualDates", $functionParameters[0]["formData"]["equalDates"], true); //true = save permanently

					$this->prlCalcOvFillYearPeriodCmb();

					communication_interface::jsExecute("$('#modalContainer').mb_close();");
					$this->prlCalcOvPopulateTable(array("updateTable"=>true));
				}else{
					$ret = blFunctionCall('payroll.getPeriodInformation');
					if($ret["success"]) {
						$prdValidity = blFunctionCall('payroll.checkPeriodValidity');
//						if(!isset($ret["data"]["currentPeriod"]["processingStatus"]["payout"]) || $ret["data"]["currentPeriod"]["processingStatus"]["payout"]==0) {
						if(isset($prdValidity["error"])) {
							//kein Mitarbeiter wurde bisher ausbezahlt/fixiert -> abbrechen
							$objWindow = new wgui_window("payroll", "infoBox");
							$objWindow->windowTitle("Fehler");
							$objWindow->windowWidth(420);
							$objWindow->windowHeight(170);
							if(isset($prdValidity["error"]["calculationIncomplete"])) $objWindow->setContent("<br/>Die Periode kann nicht abgeschlossen werden, da nicht alle Mitarbeiter berechnet wurden.<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
							else $objWindow->setContent("<br/>Die Periode kann nicht abgeschlossen werden, da nicht alle Mitarbeiter ausbezahlt/fixiert wurden.<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
							$objWindow->showAlert();
							break;
						}
						if(isset($prdValidity["warning"]) && !isset($functionParameters[0]["ignoreWarning"])) {
							$data["warningList"] = array();
							if(isset($prdValidity["warning"]["financialAccountingIncomplete"])) $data["warningList"][] = array("msg"=>"FIBU-Buchungen: Fehlen bei einzelnen oder allen Mitarbeitern.");
							if(isset($prdValidity["warning"]["managementAccountingIncomplete"])) $data["warningList"][] = array("msg"=>"BEBU-Buchungen: Fehlen bei einzelnen oder allen Mitarbeitern.");
							if(isset($prdValidity["warning"]["employeeSetIncomplete"])) {
								$data["warningList"][] = array("msg"=>"Folgende Mitarbeiter wurden in dieser Periode nicht beruecksichtigt:");
								foreach($prdValidity["unprocessedList"] as $row) $data["warningList"][] = array("msg"=>"&#149; ".$row["Lastname"].", ".$row["FirstName"]);

							}
							$objWindow = new wgui_window("payroll", "infoBox");
							$objWindow->windowTitle("Warnung");
							$objWindow->windowWidth(575);
							$objWindow->windowHeight(205);
							$objWindow->loadContent("calculation",$data,"calculationFinalizeWarning");
							$objWindow->showInfo();
							communication_interface::jsExecute("$('#prlCalcFinWarnList').css('font-size','11px').css('width','500px').css('margin-top','5px').css('margin-bottom','15px');");
							communication_interface::jsExecute("$('#prlCalcFinProceed').bind('click', function(e) { cb('payroll.prlCalcOvProcess', {'functionNumber':3, 'ignoreWarning':1}); });");
							communication_interface::jsExecute("$('#prlCalcFinCancel').bind('click', function(e) { $('#modalContainer').mb_close(); });");
							break;
						}
						$currentYear = $ret["data"]["info"]["year"];
						$majorPeriod = $ret["data"]["currentPeriod"]["major_period"];
						$minorPeriod = $ret["data"]["currentPeriod"]["minor_period"];
//						$openPeriodFound = false;

						$objWindow = new wgui_window("payroll", "CalculationFinalizeForm");

						if($majorPeriod>0 && $majorPeriod<10) $txt = " (".$objWindow->getText("prlCalcOvMonth0".$majorPeriod).")";
						else if($majorPeriod>9 && $majorPeriod<13) $txt = " (".$objWindow->getText("prlCalcOvMonth".$majorPeriod).")";
						else if($majorPeriod>14) $txt = " (".$objWindow->getText("prlCalcOvBonus").")";
						else $txt = "";
						$data["currentYear"] = $currentYear;
						$data["currentMajorPeriod"] = substr("0".$majorPeriod,-2).$txt;

						$objWindow->windowTitle($objWindow->getText("prlCalcFinFormTitle"));
						$objWindow->windowIcon("calculator32.png");
						$objWindow->windowWidth(670);
						$objWindow->windowHeight(430);
						$objWindow->dockable(false);
						$objWindow->buttonMaximize(false);
						$objWindow->resizable(false);
						$objWindow->fullscreen(false);
						$objWindow->modal(true);
						$objWindow->loadContent("calculation",$data,"calculationFinalize");
						$objWindow->showWindow();

						$yearList = array();
						foreach($ret["data"]["nextPeriod"]["year"] as $yr=>$dummy) $yearList[] = "['".$yr."','".$yr."',".($yr==$currentYear ? 1 : 0)."]";
						$prdList = array();
						foreach($ret["data"]["nextPeriod"]["year"][$currentYear] as $prdNum) {
							if($prdNum>0 && $prdNum<10) $txt = " (".$objWindow->getText("prlCalcOvMonth0".$prdNum).")";
							else if($prdNum>9 && $prdNum<13) $txt = " (".$objWindow->getText("prlCalcOvMonth".$prdNum).")";
							else if($prdNum>14) $txt = " (".$objWindow->getText("prlCalcOvBonus").")";
							else $txt = "";
							
							$prdList[] = "['".$prdNum."','".substr("0".$prdNum,-2).$txt."',".(count($prdList)==0 ? 1 : 0)."]";
						}
						$dateList = array();
						foreach($ret["data"]["nextPeriod"]["proposedDates"] as $dateType=>$dateVal) $dateList[] = "'".$dateType."':'".$this->convertMySQL2Date($dateVal)."'";

						communication_interface::jsExecute("prlCalcFinDATA = {'payroll_year_ID':[ ".implode(",",$yearList)." ],'major_period':[ ".implode(",",$prdList)." ],".implode(",",$dateList).",'FwdEmpl':1,'FwdData':1,'equalDates':".(session_control::getSessionSettings("payroll", "finalizeEqualDates")!="" ? session_control::getSessionSettings("payroll", "finalizeEqualDates") : 0)."};");
						communication_interface::jsExecute("prlCalcFinCFG = {'chkDate':".$this->getDateRegexPattern()."};");
						communication_interface::jsExecute("prlCalcFinInit();");
					}//else 
				}
				break;
			case 1003: //Hilfsfunktion zu 'case 3': Jahres-Wechsel
				$ret = blFunctionCall('payroll.getPeriodInformation');
				if(!isset($ret["data"]["nextPeriod"]["year"][$functionParameters[0]["year"]])) break;

				$objWindow = new wgui_window("payroll", "CalculationFinalizeForm");
				$prdList = array();
				foreach($ret["data"]["nextPeriod"]["year"][$functionParameters[0]["year"]] as $prdNum) {
					if($prdNum>0 && $prdNum<10) $txt = " (".$objWindow->getText("prlCalcOvMonth0".$prdNum).")";
					else if($prdNum>9 && $prdNum<13) $txt = " (".$objWindow->getText("prlCalcOvMonth".$prdNum).")";
					else if($prdNum>14) $txt = " (".$objWindow->getText("prlCalcOvBonus").")";
					else $txt = "";
					
					$prdList[] = "['".$prdNum."','".substr("0".$prdNum,-2).$txt."',".(count($prdList)==0 ? 1 : 0)."]";
				}

				communication_interface::jsExecute("prlCalcFinDATA.major_period = [ ".implode(",",$prdList)." ];");
				communication_interface::jsExecute("prlCalcFinUpdatePrd(prlCalcFinDATA.major_period);");
				break;
			default:
				communication_interface::alert("coming soon...");
//				communication_interface::alert("Processing Data... fnc:".$functionParameters[0]["functionNumber"].", y:".$functionParameters[0]["year"].", majorP:".$functionParameters[0]["majorPeriod"].", minorP:".$functionParameters[0]["minorPeriod"]);
				break;
			}
			break;
		case 'payroll.prlCalcOvSettings':
			if(!isset($functionParameters[0]["action"])) $functionParameters[0]["action"] = "default";
			switch($functionParameters[0]["action"]) {
			case 'save':
				//Attended Time
				$attendedTimeHours = array();
				foreach($functionParameters[0] as $k=>$v) {
					if(preg_match('/^TmCd([0-9]{1,2})$/', $k, $matches)) {
						$attendedTimeHours[] = array("attended_time_code"=>$matches[1], "attended_time"=>$v);
						unset($functionParameters[0][$k]);
					}
				}
				if(count($attendedTimeHours)!=0) {
					$attimeList = blFunctionCall('payroll.saveAttendedTimeHours', array("data"=>$attendedTimeHours));
					if(!$attimeList["success"]) {
						if($attimeList["errCode"]==20) {
							communication_interface::jsExecute("$('input[id*=prlPrdSet_TmCd]').css('background-color','');");
							foreach($attimeList["attended_time_codes"] as $attended_time_code) communication_interface::jsExecute("$('#prlPrdSet_TmCd".$attended_time_code."').css('background-color','#f88');");
						}else{
							communication_interface::alert("payroll.prlCalcOvSettings ".$attimeList["errText"]." (error code: ".$attimeList["errCode"].")");
						}
						break;
					}
				}

				//Notifications
				if(count($functionParameters[0]["messages"])!=0) {
					$noticeList = blFunctionCall('payroll.savePayslipNotifications', array("payroll_period_ID"=>$functionParameters[0]["payroll_period_ID"], "messages"=>$functionParameters[0]["messages"]));
					if(!$noticeList["success"]) {
						communication_interface::alert("payroll.prlCalcOvSettings not if ".$attimeList["errText"]." (error code: ".$attimeList["errCode"].")");
						break;
					}
				}
				unset($functionParameters[0]["messages"]);
				unset($functionParameters[0]["action"]);

				//Datumswerte speichern
				$retDates = blFunctionCall('payroll.savePeriodDates', $functionParameters[0]);
				if(!$retDates["success"]) {
					if($retDates["errCode"]==30) {
						communication_interface::jsExecute("$('input[id*=prlPrdSet_]').css('background-color','');");
						foreach($retDates["errFields"] as $fld) communication_interface::jsExecute("$('#prlPrdSet_".$fld."').css('background-color','#f88');");
					}else{
						communication_interface::alert("payroll.prlCalcOvSettings save: ".$retDates["errText"]." (error code: ".$retDates["errCode"].")");
					}
					break;
				}
				communication_interface::jsExecute("$('#modalContainer').mb_close();");
				break;
			default:
				//Get period dates and id
				$selectedPeriod = array();
				$prdInfo = blFunctionCall('payroll.getPeriodInformation',array("year"=>$functionParameters[0]["year"]));
				if($prdInfo["success"]) $selectedPeriod = $prdInfo["data"]["major_period"][$functionParameters[0]["majorPeriod"]]["info"];
				$periodID = $selectedPeriod["id"];
//				communication_interface::alert(print_r($selectedPeriod,true));
//				communication_interface::alert(print_r($prdInfo["data"]["currentPeriod"],true));
				$isActivePeriod = $prdInfo["data"]["info"]["status"]==0 || $prdInfo["data"]["currentPeriod"]["major_period"]!=$functionParameters[0]["majorPeriod"] ? false : true;
//major_period koennte im Titel integriert werden
//aktuelle periode? wenn nein $('#prlPrdSetTabs input,#prlPrdSetSave').
				$data["HourlyWage_DateFrom"] = $this->convertMySQL2Date($selectedPeriod["HourlyWage_DateFrom"]);
				$data["Wage_DateFrom"] = $this->convertMySQL2Date($selectedPeriod["Wage_DateFrom"]);
				$data["Salary_DateFrom"] = $this->convertMySQL2Date($selectedPeriod["Salary_DateFrom"]);
				$data["HourlyWage_DateTo"] = $this->convertMySQL2Date($selectedPeriod["HourlyWage_DateTo"]);
				$data["Wage_DateTo"] = $this->convertMySQL2Date($selectedPeriod["Wage_DateTo"]);
				$data["Salary_DateTo"] = $this->convertMySQL2Date($selectedPeriod["Salary_DateTo"]);

				//Get language list
				$languageList = blFunctionCall('payroll.getLanguageList','UseForAccounts');
				$lngArr = array();
				if($languageList["success"]) foreach($languageList["data"] as $lngRow) $lngArr[] = array("LanguageCode"=>$lngRow["core_intl_language_ID"], "LanguageName"=>$lngRow["language_name"]);
				$data["languageList"] = $lngArr;

				//Get company list
				$companyList = blFunctionCall('payroll.getCompanyList');
				if($companyList["success"]) $data["companyList"] = $companyList["data"];
				else $data["companyList"] = array();

				//Get notifications
				$notifications = array();
				$noticeList = blFunctionCall('payroll.getPayslipNotifications', array("payroll_period_ID"=>$periodID));
				if($noticeList["success"]) foreach($noticeList["data"] as $row) $notifications[] = "{'payroll_company_ID':'".$row["payroll_company_ID"]."', 'notification':'".str_replace("\n","\\n",str_replace("'","\\'",$row["employee_notification"]))."', 'language':'".$row["language"]."'}";

				//Get attended time information
				$attimeList = blFunctionCall('payroll.getAttendedTimeList');
				if($attimeList["success"]) {
					$tabindex=7;
					foreach($attimeList["data"] as &$row) { $row["tabindex"]=$tabindex; $tabindex++; }
					$data["attimeList"] = $attimeList["data"];
					unset($row);
				}

				$objWindow = new wgui_window("payroll", "prlCalcOvSettings");
				$objWindow->windowTitle($objWindow->getText("prlCalcOvSetFormTitle").$functionParameters[0]["majorPeriod"].", ".$functionParameters[0]["year"]);
				$objWindow->windowIcon("calculator32.png");
				$objWindow->windowWidth(660);
				$objWindow->windowHeight(480);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("calculation",$data,"prlCalcOvSettings");
				$objWindow->showWindow();

				communication_interface::jsExecute("prlPrdSet = {'payroll_period_ID':'".$periodID."', 'messages':[".implode(",", $notifications)."] };");
//		communication_interface::alert("prlPrdSet = {'payroll_period_ID':'".$periodID."', 'messages':[".implode(",", $notifications)."] };");
				communication_interface::jsExecute("prlPrdSetInit();");
				if(!$isActivePeriod) communication_interface::jsExecute("$('#prlPrdSetTabs input,#prlPrdSetSave').attr('disabled', 'disabled');");
//communication_interface::jsExecute("alert(prlPrdSet.toSource());");
				break;
			}
			break;
		case 'payroll.prlCalcOvOutput':
			$fireScripts = true;
			switch($functionParameters[0]["functionNumber"]) {
			case 1: //Lohnabrechnungen drucken
				$docPathID = blFunctionCall('payroll.calculationReport','payslip',array("year"=>$functionParameters[0]["year"],"majorPeriod"=>$functionParameters[0]["majorPeriod"],"minorPeriod"=>$functionParameters[0]["minorPeriod"]));
//				$fireScripts = false;
				break;
			case 2: //Bewegungsjournal drucken
//$now = microtime(true); //TODO: PROFILING START
				$docPathID = blFunctionCall('payroll.calculationReport','calculationJournal',array("year"=>$functionParameters[0]["year"],"majorPeriod"=>$functionParameters[0]["majorPeriod"],"minorPeriod"=>$functionParameters[0]["minorPeriod"]));
//communication_interface::alert(microtime(true) - $now); //TODO: PROFILING STOP
				break;
			case 3: //FIBU-Kontierungen drucken
                if(isset($functionParameters[0]["selectedReportType"])) {
                    $docPathID = blFunctionCall('payroll.calculationReport','finAccJournal',array("year"=>$functionParameters[0]["year"],"majorPeriod"=>$functionParameters[0]["majorPeriod"],"minorPeriod"=>$functionParameters[0]["minorPeriod"], "selectedReportType"=>$functionParameters[0]["selectedReportType"], "company"=>$functionParameters[0]["company"], "cost_center"=>$functionParameters[0]["cost_center"]));
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				}else{
                    $fireScripts = false;

                    //show report selection window
                    $objWindow = new wgui_window("payroll", "ReportSelector");
                    $objWindow->windowTitle("FIBU-Journale: Report Auswahl");
                    $objWindow->windowIcon("calculator20.png");
                    $objWindow->windowWidth(480);
                    $objWindow->windowHeight(200);
                    $objWindow->dockable(false);
                    $objWindow->buttonMaximize(false);
                    $objWindow->resizable(false);
                    $objWindow->fullscreen(false);
                    $objWindow->modal(true);
                    $objWindow->loadContent("calculation",$data,"prlCalcReportSelector");
                    $objWindow->showWindow();
                    
                    communication_interface::jsExecute("
                    prlRepSelCFG = {
                        'saveCB':function(){
                            cb('payroll.prlCalcOvOutput', {
                                'functionNumber':3, 
                                'year':".$functionParameters[0]["year"].", 
                                'majorPeriod':".$functionParameters[0]["majorPeriod"].",
                                'minorPeriod':".$functionParameters[0]["minorPeriod"].", 
                                'selectedReportType':$('#prlRepSelReportType').val(), 
                                'company':$('#prlRepSelFilterFirma').val(), 
                                'cost_center':$('#prlRepSelFilterKst').val()
                                });
                            $('#prlRepSelSave').prop('disabled', true);
                         }, 
                         'cancelCB':'', 
                         'errWrongCompanyFormat':'".$objWindow->getText("prlRepSelErrWrongCompanyFormat")."', 
                         'errWrongKstFormat':'".$objWindow->getText("prlRepSelErrWrongKstFormat")."'
                       };");
                    communication_interface::jsExecute("prlRepSelInit();");
                }
				break;
			case 4: //BEBU-Kontierungen drucken
                if(isset($functionParameters[0]["selectedReportType"])) {
					$docPathID = blFunctionCall('payroll.calculationReport','mgmtAccJournal',array("year"=>$functionParameters[0]["year"],"majorPeriod"=>$functionParameters[0]["majorPeriod"],"minorPeriod"=>$functionParameters[0]["minorPeriod"], "selectedReportType"=>$functionParameters[0]["selectedReportType"], "company"=>$functionParameters[0]["company"], "cost_center"=>$functionParameters[0]["cost_center"]));
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				}else{
                    $fireScripts = false;

                    //show report selection window
                    $objWindow = new wgui_window("payroll", "ReportSelector");
                    $objWindow->windowTitle("BEBU-Journale: Report Auswahl");
                    $objWindow->windowIcon("calculator20.png");
                    $objWindow->windowWidth(480);
                    $objWindow->windowHeight(200);
                    $objWindow->dockable(false);
                    $objWindow->buttonMaximize(false);
                    $objWindow->resizable(false);
                    $objWindow->fullscreen(false);
                    $objWindow->modal(true);
                    $objWindow->loadContent("calculation",$data,"prlCalcReportSelector");
                    $objWindow->showWindow();
                
                    communication_interface::jsExecute("
                    prlRepSelCFG = {
                        'saveCB':function(){
                            cb('payroll.prlCalcOvOutput', {
                                'functionNumber':4, 
                                'year':".$functionParameters[0]["year"].", 
                                'majorPeriod':".$functionParameters[0]["majorPeriod"].",
                                'minorPeriod':".$functionParameters[0]["minorPeriod"].", 
                                'selectedReportType':$('#prlRepSelReportType').val(), 
                                'company':$('#prlRepSelFilterFirma').val(), 
                                'cost_center':$('#prlRepSelFilterKst').val()
                                });
                            $('#prlRepSelSave').prop('disabled', true);
                         }, 
                         'cancelCB':'', 
                         'errWrongCompanyFormat':'".$objWindow->getText("prlRepSelErrWrongCompanyFormat")."', 
                         'errWrongKstFormat':'".$objWindow->getText("prlRepSelErrWrongKstFormat")."'
                       };");
                    communication_interface::jsExecute("prlRepSelInit();");
                }
				break;
			case 5: //Lohnkonto drucken
				if(isset($functionParameters[0]["employees"])) {
//					communication_interface::alert("PDF-Output... ".print_r($functionParameters[0],true));
					$docPathID = blFunctionCall('payroll.calculationReport','payrollAccountJournal',array("year"=>$functionParameters[0]["year"],"employees"=>$functionParameters[0]["employees"]));
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				}else{
					$fireScripts = false;

					//show employee selection window
					$objWindow = new wgui_window("payroll", "EmployeeSelector");
					$data["dbFilterList"] = array();
					$data["dbFilterList"][] = array("id"=>0, "FilterName"=>$objWindow->getText("prlPsSelAllEmployees"));
					$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>false, "FilterForCalculation"=>true));
					if($ret["success"]) foreach($ret["data"] as $row) $data["dbFilterList"][] = $row;

					$objWindow->windowTitle($objWindow->getText("txtCalcMenupunkt_Lohnkonto").": ".$objWindow->getText("txtMitarbeiterauswahl"));
					$objWindow->windowIcon("employee-edit32.png");
					$objWindow->windowWidth(700);
					$objWindow->windowHeight(320);
					$objWindow->dockable(false);
					$objWindow->buttonMaximize(false);
					$objWindow->resizable(false);
					$objWindow->fullscreen(false);
					$objWindow->modal(true);
					$objWindow->loadContent("utilities",$data,"EmployeeSelector");
					$objWindow->showWindow();

					//TODO: als "data_source" nur die Mitarbeiter der aktuellen Periode, die bereits berechnet aber noch nicht ausbezahlt wurden
					//Get employee list and prepare data in order to fill the client-side table
					$queryOption["columns"] = array("EmployeeNumber", "Firstname", "Lastname");
					$queryOption["prepend_id"] = true;
					$queryOption["query_filter"] = "";
					$queryOption["sort"] = "Lastname,Firstname";
					$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
					$emplData = array();
					if($employeeList["success"]) {
						foreach($employeeList["data"] as $row) $emplData[] = "[".$row["id"].",'".$row["EmployeeNumber"]."','".str_replace("'","\\'",$row["Firstname"])."','".str_replace("'","\\'",$row["Lastname"])."']";
					}
					communication_interface::jsExecute("prlPsSelALL = [ ".implode(",",$emplData)." ];");
					communication_interface::jsExecute("prlPsSelEXCL = [];"); //exclude ist hier nicht noetig
					communication_interface::jsExecute("prlPsSelCFG = {'saveCB':function(){cb('payroll.prlCalcOvOutput', {'functionNumber':5, 'year':".$functionParameters[0]["year"].", 'employees':$('#prlPsSelLst').val()}); $('#prlPsSelSave').prop('disabled', true);}, 'cancelCB':'', 'errNoSelection':'".$objWindow->getText("prlPsSelErrMissingSelection")."'};");
					communication_interface::jsExecute("prlPsSelInit();");
					communication_interface::jsExecute("prlPsSelUpdate();");
				}
				break;
			default:
				$fireScripts = false;
				communication_interface::alert("coming soon...");
//				communication_interface::alert("PDF-Output... fnc:".$functionParameters[0]["functionNumber"].", y:".$functionParameters[0]["year"].", majorP:".$functionParameters[0]["majorPeriod"].", minorP:".$functionParameters[0]["minorPeriod"]);
				break;
			}
			if($fireScripts) {
				communication_interface::jsExecute("$('#dlForm input[name=param]').val('".str_replace("'","\\'",serialize(array("tmpPathID"=>$docPathID)))."');");
				communication_interface::jsExecute("$('#dlForm').attr('action','getfile.php');");
				communication_interface::jsExecute("$('#dlForm').attr('target','dlFrame');");
				communication_interface::jsExecute("$('#dlForm').submit();");
			}
			break;
		case 'payroll.prlCalcDataEditor':
			$data = array();

			$objWindow = new wgui_window("payroll", "prlCalcDataEditor");
			$objWindow->windowTitle("Lohndaten bearbeiten");
			$objWindow->windowIcon("calculator32.png");
			$objWindow->windowWidth(900);
			$objWindow->windowHeight(510);
			$objWindow->dockable(false);
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->loadContent("calculation",$data,"calculationDataEditor");
			$objWindow->showWindow();

			//Get employee list and prepare data in order to fill the client-side table
			$queryOption["columns"] = array("EmployeeNumber", "Lastname", "Firstname");
			$queryOption["prepend_id"] = true;
			$queryOption["query_filter"] = "";
			$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
			$prlCalcDataEmplArr = array();
			if($employeeList["success"])
				$sortkey = array();
				foreach ($employeeList["data"] as $key => $row) $sortkey[$key] = $row["Lastname"];
				array_multisort($sortkey, SORT_ASC, $employeeList["data"]);

				foreach($employeeList["data"] as $row)
					$prlCalcDataEmplArr[] = "['".$row["id"]."', '".$row["EmployeeNumber"]."', '".str_replace("'","\\'",$row["Lastname"])."', '".str_replace("'","\\'",$row["Firstname"])."']";

			//Get payroll account list
			$accountList = blFunctionCall('payroll.getPayrollAccountList'); //id,payroll_year_ID,label,sign
			$arrAccCollector = array();
			if($accountList["success"]) {
				foreach($accountList["data"] as $row) {
					if($row["var_fields"]>0) {
						$arrAccCollector[] = "['".$row["id"]."', '".str_replace("'","\\'",$row["label"])."', ".$row["var_fields"]."]";
					}
				}
			}
	/*
LOA-Konfig-Maske und ggf. Werte in DB korrigieren/anpassen:
	000	0	--NADA--
	001	1	Quantity			1000
	010	2	Rate				1010
	011	3	Quantity + Rate			1210
	100	4	Amount				1223
	101	5	Amount + Quantity
	110	6	Amount + Rate
	111	7	Amount + Rate + Quantity	1234

	000	0	--NADA--
	001	1	Quantity			1000
	010	2	Rate				1010
	100	4	Amount				1223
	011	3	Quantity + Rate			1210
	101	5	Quantity + Amount
	110	6	Rate + Amount
	111	7	Quantity + Rate + Amount	1234

KONWOERSCHN:
-----------
<label for="loac_var_fields"><wgui:text id="loacVarFields"/></label>
<select id="loac_var_fields">
	<option value="0"><wgui:text id="loacVarFieldsNone"/></option>			--->	Bleibt 0 !
	<option value="1"><wgui:text id="loacVarFieldsRate"/></option>			--->	aus 1 wird 2 !
	<option value="2"><wgui:text id="loacVarFieldsQuantity"/></option>		--->	aus 2 wird 1 !
	<option value="3"><wgui:text id="loacVarFieldsRateAndQuantity"/></option>	--->	Bleibt 3 !
</select><br/>



$textResourceMap["loacVarFieldsNone"] = "keine";					0
$textResourceMap["loacVarFieldsQuantity"] = "MENGE";					1
$textResourceMap["loacVarFieldsRate"] = "ANSATZ";					2
$textResourceMap["loacVarFieldsAmount"] = "BETRAG";					4
$textResourceMap["loacVarFieldsAmountRate"] = "ANSATZ + BETRAG";			6
$textResourceMap["loacVarFieldsRateQuantity"] = "MENGE + ANSATZ";			3
$textResourceMap["loacVarFieldsAmountQuantity"] = "MENGE + BETRAG";			5
$textResourceMap["loacVarFieldsAmountRateQuantity"] = "MENGE + ANSATZ + BETRAG";	7

<label for="loac_var_fields"><wgui:text id="loacVarFields"/></label>
<select id="loac_var_fields">
	<option value="0"><wgui:text id="loacVarFieldsNone"/></option>
	<option value="1"><wgui:text id="loacVarFieldsQuantity"/></option>
	<option value="2"><wgui:text id="loacVarFieldsRate"/></option>
	<option value="4"><wgui:text id="loacVarFieldsAmount"/></option>
	<option value="6"><wgui:text id="loacVarFieldsAmountRate"/></option>
	<option value="3"><wgui:text id="loacVarFieldsRateQuantity"/></option>
	<option value="5"><wgui:text id="loacVarFieldsAmountQuantity"/></option>
	<option value="7"><wgui:text id="loacVarFieldsAmountRateQuantity"/></option>
</select><br/>
	*/

			communication_interface::jsExecute("$('#prlCalcDataCmbEmpl').data('suspendEvents',true);");
			$definitions = "prlCalcDataFldDef = {'prlCalcDataQuantity':{'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/,'mandatory':true}, 'prlCalcDataRate':{'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/,'mandatory':true}, 'prlCalcDataAmount':{'rgx':/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/,'mandatory':true}, 'prlCalcDataFrom':{'rgx':/^[0-9]{1,2}\.[0-9]{1,2}\.([0-9]{2,2}|(19|20)[0-9]{2,2})$/,'mandatory':true}, 'prlCalcDataTo':{'rgx':/^[0-9]{1,2}\.[0-9]{1,2}\.([0-9]{2,2}|(19|20)[0-9]{2,2})$/,'mandatory':true}};\n";
			$definitions .= "prlCalcDataStorage = {};\n";
			$definitions .= "prlCalcDataTblData = [];\n";
			$definitions .= "prlCalcDataLOA = [".implode(", ",$arrAccCollector)."];\n"; //['1000', 'Monatslohn', 1], ['1010', 'Gehalt', 2], ['1210', 'Ãœberzeitzulage', 3], ['1223', 'Baby-Bonus', 4], ['1234', 'Zulage', 7]
			$definitions .= "prlCalcDataEmpl = [".implode(", ",$prlCalcDataEmplArr)."];\n"; //['1', '12345', 'Hans', 'Muster'], ['2', '78945', 'Daniel', 'Mueller'], ['3', '78456', 'Arthur', 'Ruebenzahl'], ['4', '43247', 'Hans', 'Meier']
			$definitions .= "prlCalcDataLbl = {'db':'Gespeicherter Eintrag', 'new':'Neuer Eintrag', 'del':'Geloeschter Eintrag', 'edit':'Geaenderter Eintrag', 'once':'einmalig', 'perm':'permanent', 'limt':'befristet'};\n";
			communication_interface::jsExecute($definitions);
			communication_interface::jsExecute("prlCalcDataInit();");
			communication_interface::jsExecute("$('#prlCalcDataCmbEmpl').data('suspendEvents',false);");
			break;
		case 'payroll.prlCalcDataSave':
			$res = blFunctionCall('payroll.calculationDataSave',$functionParameters[0]);
			if($res["success"]) {
				communication_interface::jsExecute("$('#prlCalcDataEditor').mb_close();");
			}else{
				communication_interface::alert("Fehler ".$res["errCode"]." / ".print_r($res["errFields"],true));
			}
//			communication_interface::alert("id:".print_r($functionParameters[0],true));
			break;
		case 'payroll.prlCalcDataGetEmplRecs':
			$queryOption["columns"] = array("payroll_employee_ID", "payroll_account_ID", "account_text", "PayrollDataType", "quantity", "rate", "amount", "CostCenter", "DateFrom", "DateTo");
			$queryOption["prepend_id"] = true;
			$queryOption["query_filter"] = array(array("payroll_employee_ID",$functionParameters[0]));

			$employeeData = blFunctionCall('payroll.getCalculationData', $queryOption);
			$prlCalcDataEmplArr = array();
			if($employeeData["success"])
				foreach($employeeData["data"] as $row)
					if($row["PayrollDataType"]==1 || $row["PayrollDataType"]==3 || $row["PayrollDataType"]==4)
						$prlCalcDataEmplArr[] = "{'emplId':'".$row["payroll_employee_ID"]."', 'rid':".$row["id"].", 'accNo':'".$row["payroll_account_ID"]."', 'accTxt':'".str_replace("'","\\'",$row["account_text"])."', 'PayrollDataType':'".$row["PayrollDataType"]."', 'quantity':'".$row["quantity"]."', 'rate':'".$row["rate"]."', 'amount':'".$row["amount"]."', 'cc':'".$row["CostCenter"]."', 'dateFrom':'".$this->convertMySQL2Date($row["DateFrom"])."', 'dateTo':'".$this->convertMySQL2Date($row["DateTo"])."'}";
/*
* DB + LOA-Konfig bereinigen (wegen neuer var_fields Codes...)
TEILW.ERLEDIGT* Neue Funktion: Auslesen der Employee-Var-Daten (Wichtig: Nur bestimmte Recs anzeigen -> FILTERN! [nach Typ und "alle od. nur einzelnen Employee")	PayrollDataType=1, 3 od. 4
TEILW.ERLEDIGT* Neue Funktion: Speichern der Employee-Var-Daten
*/
//			communication_interface::alert("id:".$functionParameters[0]);
			communication_interface::jsExecute("prlCalcDataCurEmpl = [".implode(",", $prlCalcDataEmplArr)."];");
//			communication_interface::alert($functionParameters[0]." / prlCalcDataCurEmpl = [".implode(",", $prlCalcDataEmplArr)."];");
			communication_interface::jsExecute("prlCalcDataLoadTbl();");
			break;
		case 'payroll.EmployeeSelectorOpen': //employee selection form: Open form
			$objWindow = new wgui_window("payroll", "EmployeeSelector");
			$data["dbFilterList"] = array();
			$data["dbFilterList"][] = array("id"=>0, "FilterName"=>$objWindow->getText("prlPsSelAllEmployees"));
			$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>false, "FilterForCalculation"=>true));
			if($ret["success"]) foreach($ret["data"] as $row) $data["dbFilterList"][] = $row;

			$objWindow->windowTitle($objWindow->getText("prlPsSelFormTitle"));
			$objWindow->windowIcon("employee-edit32.png");
			$objWindow->windowWidth(700);
			$objWindow->windowHeight(320);
			$objWindow->dockable(false);
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->modal(true);
			$objWindow->loadContent("utilities",$data,"EmployeeSelector");
			$objWindow->showWindow();


			//Get employee list and prepare data in order to fill the client-side table
			$queryOption["columns"] = array("EmployeeNumber", "Firstname", "Lastname");
			$queryOption["prepend_id"] = true;
			$queryOption["query_filter"] = "";
			$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
			$emplData = array();
			if($employeeList["success"]) {
				foreach($employeeList["data"] as $row) $emplData[] = "[".$row["id"].",'".$row["EmployeeNumber"]."','".str_replace("'","\\'",$row["Firstname"])."','".str_replace("'","\\'",$row["Lastname"])."']";
			}

			//Get employee id's to exclude
			$queryOption["columns"] = array("id");
			$queryOption["prepend_id"] = false;
			$queryOption["query_filter"] = "";
			$queryOption["data_source"] = "current_period";
			$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
			$exclData = array();
			if($employeeList["success"]) {
				foreach($employeeList["data"] as $row) $exclData[] = $row["id"];
			}

			communication_interface::jsExecute("prlPsSelALL = [ ".implode(",",$emplData)." ];");
			communication_interface::jsExecute("prlPsSelEXCL = [".implode(",",$exclData)."];");
			communication_interface::jsExecute("prlPsSelCFG = {'saveCB':'payroll.EmployeeSelectorSave', 'cancelCB':'', 'errNoSelection':'".$objWindow->getText("prlPsSelErrMissingSelection")."'};");
			communication_interface::jsExecute("prlPsSelInit();");
			communication_interface::jsExecute("prlPsSelUpdate();");
			break;
		case 'payroll.EmployeeSelectorFilter': //employee selection form: get employee list according to selected db filter
			$queryOption["columns"] = array("EmployeeNumber", "Firstname", "Lastname");
			$queryOption["prepend_id"] = true;
			$queryOption["query_filter"] = $functionParameters[0]==0 ? "" : $functionParameters[0];
			$queryOption["sort"] = "Lastname,Firstname";
			$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
			$emplData = array();
			if($employeeList["success"]) {
				foreach($employeeList["data"] as $row) $emplData[] = "[".$row["id"].",'".$row["EmployeeNumber"]."','".str_replace("'","\\'",$row["Firstname"])."','".str_replace("'","\\'",$row["Lastname"])."']";
			}
			communication_interface::jsExecute("prlPsSelALL = [ ".implode(",",$emplData)." ];");
			communication_interface::jsExecute("prlPsSelUpdate();");
			break;
		case 'payroll.EmployeeSelectorSave': //employee selection form: save data
//			communication_interface::alert("x:".print_r($functionParameters[0],true));
			$res = blFunctionCall('payroll.addEmployee2Period', $functionParameters[0]);
			communication_interface::jsExecute("$('#modalContainer').mb_close();");
			$this->prlCalcOvPopulateTable(array("updateTable"=>true));
			break;
		case 'payroll.FinMgmtAccOpen':
			//Get employee list and prepare data in order to fill the client-side employee SELECT
			$queryOption["columns"] = array("Firstname", "Lastname");
			$queryOption["prepend_id"] = true;
			$queryOption["query_filter"] = "";
			$queryOption["sort"] = "Lastname,Firstname";
			$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);
			$emplData = array("'0':'---'");
			if($employeeList["success"]) {
				foreach($employeeList["data"] as $row) $emplData[] = "'".$row["id"]."':'".str_replace("'","\\'",$row["Lastname"].", ".$row["Firstname"])."'";
			}
//communication_interface::alert(implode(",",$emplData));

			//Get payroll account list
			$accountList = blFunctionCall('payroll.getPayrollAccountList'); //id,payroll_year_ID,label,sign
			$accData = array();
			if($accountList["success"]) {
				foreach($accountList["data"] as $row) $accData[] = "'".$row["id"]."':'".str_replace("'","\\'",$row["label"])."'";
			}

			//Get company list
			$companyList = blFunctionCall('payroll.getCompanyList');
			$cpnyData = array("'0':'---'");
			if($companyList["success"]) {
				foreach($companyList["data"] as $row) $cpnyData[] = "'".$row["id"]."':'".str_replace("'","\\'",$row["company_shortname"])."'";
			}

			$data = array();
			$objWindow = new wgui_window("payroll", "payrollFinMgmtAccounting");
			$objWindow->windowTitle($objWindow->getText("prlAccAsgTitle"));
			$objWindow->windowIcon("config32.png");
			$objWindow->windowWidth(870);
			$objWindow->windowHeight(580);
			$objWindow->dockable(false); 
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->modal(true);
			$objWindow->loadContent("configuration",$data,"payrollFinMgmtAccounting");
			$objWindow->showWindow();

			communication_interface::jsExecute("prlAccAsgParam = {'savemode':1, 'deleteObj':{}, 'yes':'Ja', 'no':'Nein', 'debit':'S', 'credit':'H', 'companies':{".implode(",",$cpnyData)."}, 'employees':{".implode(",",$emplData)."}, 'loa':{".implode(",",$accData)."}, 'validity':[ ['Wert in Feld \\'','\\' ist ungueltig.'], [ ['cc1', /^.{0,15}$/],['aid1', /^[0-9a-zA-Z]{1,5}$/], ['acc1',/^.{1,15}$/], ['cacc1',/^.{0,15}$/], ['et1',/^.{1,50}$/] ],[ ['cc2',/^.{1,15}$/],['aid2',/^[0-9a-zA-Z]{1,5}$/],['amt2',/^[0-9]{1,3}(\.[0-9]{0,2})?$/] ],[ ['cc3',/^.{0,15}$/],['aid3',/^[0-9a-zA-Z]{1,5}$/],['acc3',/^.{1,15}$/],['cacc3',/^.{0,15}$/],['et3',/^.{1,50}$/] ] ] };");
			//	prlAccAsgParam = {'savemode':2,'employeeID':123,'companyID':432};

			//Get financial and management accounting information (configuration info)
			$accConfig = blFunctionCall('payroll.getFinMgmtAccountingInfo');
			$accFinAssign = array();
			$accMgmtAssign = array();
			$accMgmtSplit = array();
			if($accConfig["success"]) {
				$fieldsOfInterest = array("id"=>"id","payroll_company_ID"=>"cid","payroll_employee_ID"=>"eid","payroll_account_ID"=>"aid","cost_center"=>"cc","account_no"=>"acc","counter_account_no"=>"cacc","debitcredit"=>"dc","entry_text"=>"et","invert_value"=>"inv");
				foreach($accConfig["dataFinAccAssign"] as $row) {
					$arrTmp = array();
					foreach($fieldsOfInterest as $curSrcField=>$curDestField) {
						if($curDestField=="et") $arrTmp[] = "'".$curDestField."':'".str_replace("'","\\'",$row[$curSrcField])."'";
						else $arrTmp[] = "'".$curDestField."':'".$row[$curSrcField]."'";
					}
					$accFinAssign[] = "{".implode(",",$arrTmp)."}";
				}
				foreach($accConfig["dataMgmtAccAssign"] as $row) {
					$arrTmp = array();
					foreach($fieldsOfInterest as $curSrcField=>$curDestField) {
						if($curDestField=="et") $arrTmp[] = "'".$curDestField."':'".str_replace("'","\\'",$row[$curSrcField])."'";
						else $arrTmp[] = "'".$curDestField."':'".$row[$curSrcField]."'";
					}
					$accMgmtAssign[] = "{".implode(",",$arrTmp)."}";
				}
				$fieldsOfInterest = array("id"=>"id","payroll_company_ID"=>"cid","payroll_employee_ID"=>"eid","payroll_account_ID"=>"aid","cost_center"=>"cc","amount"=>"amt","invert_value"=>"inv","remainder"=>"rem");
				foreach($accConfig["dataMgmtAccSplit"] as $row) {
					$arrTmp = array();
					foreach($fieldsOfInterest as $curSrcField=>$curDestField) $arrTmp[] = "'".$curDestField."':'".$row[$curSrcField]."'";
					$accMgmtSplit[] = "{".implode(",",$arrTmp)."}";
				}
			}
			if(count($accFinAssign)>0) communication_interface::jsExecute("prlAccAsgData[1] = [".implode(",",$accFinAssign)."];");
			if(count($accMgmtAssign)>0) communication_interface::jsExecute("prlAccAsgData[3] = [".implode(",",$accMgmtAssign)."];");
			if(count($accMgmtSplit)>0) communication_interface::jsExecute("prlAccAsgData[2] = [".implode(",",$accMgmtSplit)."];");
			communication_interface::jsExecute("prlAccAsgInit();");
			break;
		case 'payroll.saveAccountAssingment':
//			communication_interface::alert(print_r($functionParameters[0],true));
			//editFinMgmtAccountingConfig
			$ret = blFunctionCall('payroll.editFinMgmtAccountingConfig',$functionParameters[0]);
			if($ret["success"]) {
				$fieldMapper = array('id'=>'id', 'payroll_company_ID'=>'cid', 'payroll_employee_ID'=>'eid', 'cost_center'=>'cc', 'payroll_account_ID'=>'aid', 'account_no'=>'acc', 'counter_account_no'=>'cacc', 'debitcredit'=>'dc', 'invert_value'=>'inv', 'entry_text'=>'et', 'amount'=>'amt', 'remainder'=>'rem');
				$arrRetObj = array();

				foreach($functionParameters[0] as $k=>$v) {
					if($k!='section' && $k!='mode' && isset($fieldMapper[$k])) $arrRetObj[] = "'".$fieldMapper[$k]."':'".str_replace("'","\\'",$v)."'";
				}
				if($functionParameters[0]["mode"]=='add') $arrRetObj[] = "'id':'".$ret["id"]."'";

				communication_interface::jsExecute("prlAccAsgRec('".$functionParameters[0]["section"]."', '".$functionParameters[0]["mode"]."', {".implode(",",$arrRetObj)."});");
//				communication_interface::alert("prlAccAsgRec('".$functionParameters[0]["section"]."', '".$functionParameters[0]["mode"]."', {".implode(",",$arrRetObj)."});");
			}else{
				communication_interface::alert("Fehler: ".$ret["errText"]." [Code ".$ret["errCode"]."], Feld:".$ret["fieldName"]);
			}

			break;
		case 'payroll.EmployeeFilter':
			switch($functionParameters[0]["action"]) {
			case 'editor':
				$data = array();
				$objWindow = new wgui_window("payroll", "EmployeeFilterEditor");
				$objWindow->windowTitle($objWindow->getText("prlUtlEfcTitle"));
				$objWindow->windowIcon("employee-edit32.png");
				$objWindow->windowWidth(850);
				$objWindow->windowHeight(420);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("utilities",$data,"EmployeeFilterEditor");
				$objWindow->showWindow();

//communication_interface::alert("id:".$functionParameters[0]["id"]);
				if( isset($functionParameters[0]["id"]) && $functionParameters[0]["id"]>0 ) {
					$FilterName = "";
					$ValidForEmplOverview = 1;
					$ValidForCalculation = 1;
					$GlobalFilter = 1;
					$TemporaryFilter = 1;
					$payroll_empl_filter_ID = 0;
					$GlobalFilter = 1;
					$TemporaryFilter = 0;
					$critCollector = array();
					$ret = blFunctionCall('payroll.getEmployeeFilterDetail',array("id"=>$functionParameters[0]["id"]));
					if($ret["success"]) {
//communication_interface::alert( print_r($ret["data"],true) );
						$FilterName = str_replace("'","\\'",$ret["data"]["FilterName"]);
						$ValidForEmplOverview = $ret["data"]["ValidForEmplOverview"];
						$ValidForCalculation = $ret["data"]["ValidForCalculation"];
						$payroll_empl_filter_ID = $ret["data"]["id"];

						foreach($ret["data"]["criteria"] as $row) {
							if(preg_match( '/^(19|20)[0-9]{2,2}-[0-9]{2,2}-[0-9]{2,2}$/', $row["ComparativeValues"])) {
								$row["ComparativeValues"] = $this->convertMySQL2Date($row["ComparativeValues"]);
							}else if(preg_match( '/^(19|20)[0-9]{2,2}-[0-9]{2,2}-[0-9]{2,2}@(19|20)[0-9]{2,2}-[0-9]{2,2}-[0-9]{2,2}$/', $row["ComparativeValues"])) {
								$tmparr = explode("@",$row["ComparativeValues"]);
								$row["ComparativeValues"] = $this->convertMySQL2Date($tmparr[0])."-".$this->convertMySQL2Date($tmparr[1]);
							}
							$critCollector[] = "{'id':'".$row["id"]."', 'CriteriaType':'".$row["CriteriaType"]."', 'FieldName':'".$row["FieldName"]."', 'FieldModifier':'".$row["FieldModifier"]."', 'Conjunction':'".$row["Conjunction"]."', 'Comparison':'".$row["Comparison"]."', 'SortOrder':'".$row["SortOrder"]."', 'ComparativeValues':'".str_replace("'","\\'",$row["ComparativeValues"])."'}";
						}
					}
					$jsData = "prlUtlEfc = {'FilterName':'".$FilterName."', 'ValidForEmplOverview':'".$ValidForEmplOverview."', 'ValidForCalculation':'".$ValidForCalculation."', 'GlobalFilter':'".$GlobalFilter."', 'TemporaryFilter':'".$TemporaryFilter."', 'payroll_empl_filter_ID':'".$payroll_empl_filter_ID."', 'data':[".implode(", ", $critCollector)."], ";
				}else{
					$jsData = "prlUtlEfc = {'FilterName':'', 'ValidForEmplOverview':'1', 'ValidForCalculation':'1', 'GlobalFilter':'1', 'TemporaryFilter':'0', 'payroll_empl_filter_ID':'0', 'data':[], ";
				}
				$jsData .= "'labels':{'Conjunction':['','".$objWindow->getText("prlUtlEfcEdtConjunctionAND")."','".$objWindow->getText("prlUtlEfcEdtConjunctionOR")."'], 'Comparison':['', '=', '&lt;=', '&gt;=', '&lt;&gt;', '&gt;', '&lt;', 'IN'], 'FieldModifier':['', '".mb_strtoupper($objWindow->getText("prlUtlEfcEdtFieldModifierDAY"))."', '".mb_strtoupper($objWindow->getText("prlUtlEfcEdtFieldModifierMONTH"))."', '".mb_strtoupper($objWindow->getText("prlUtlEfcEdtFieldModifierYEAR"))."'], 'Checkbox':['".$objWindow->getText("txtY")."', '".$objWindow->getText("txtN")."']}, 'auxFields':{'ex_activeempl_refdate': {'label': 'Aktive Personen (Stichtag)','type': 91, 'len': 10, 'vMin': 0.0, 'vMax': 0.0, 'disabled': false, 'validate': false, 'mandatory': false, 'rgx': ".$this->getDateRegexPattern().", 'callback': false, 'guiWidth': 'XL', 'inUse': false}, 'ex_activeempl_daterange': {'label': 'Aktive Personen (von/bis)','type': 92, 'len': 10, 'vMin': 0.0, 'vMax': 0.0, 'disabled': false, 'validate': false, 'mandatory': false, 'rgx': ".$this->getDateRegexPattern().", 'callback': false, 'guiWidth': 'XL', 'inUse': false}} };";
//communication_interface::alert($functionParameters[0]["loadFieldDef"]);
				if( !isset($functionParameters[0]["loadFieldDef"]) || $functionParameters[0]["loadFieldDef"]==1 ) communication_interface::jsExecute($this->getEmplFieldDef());
				communication_interface::jsExecute($jsData);
				communication_interface::jsExecute("prlUtlEfcInit();");
				break;
			case 'save':
//communication_interface::alert( print_r($functionParameters[0],true) );
//communication_interface::jsExecute("$('#prlUtlEfcErr').text('test');");
				$ret = blFunctionCall("payroll.saveEmployeeFilterDetail", $functionParameters[0]["data"]);
				if($ret["success"]) {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
					$psoDbFilter = session_control::getSessionSettings("payroll", "psoDbFilter");
					if($functionParameters[0]["data"]["payroll_empl_filter_ID"] == $psoDbFilter) $this->emplOverviewPopulateTable(array("updateTable"=>true));
				}else{
//					communication_interface::alert( print_r($ret,true) );
//					communication_interface::alert( print_r($functionParameters[0]["data"],true) );
					communication_interface::alert("Speichern fehlgeschlagen [".$ret["errText"].", Code: ".$ret["errCode"]."]");
				}
				break;
			case 'delete':
				if(!isset($functionParameters[0]["commit"])) {
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
					$objWindow->windowWidth(370);
					$objWindow->windowHeight(155);
					$objWindow->setContent("<br/>Bitte bestaetigen Sie die Loeschung.<br/><br/><button onclick='cb(\"payroll.EmployeeFilter\",{\"action\":\"delete\", \"id\":\"".$functionParameters[0]["id"]."\", \"commit\":\"yes\"});'>Loeschen</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
					$objWindow->showQuestion();
				}else{
					$ret = blFunctionCall("payroll.deleteEmployeeFilterDetail", array("id"=>$functionParameters[0]["id"]));
					if($ret["success"]) {
						communication_interface::jsExecute("$('#modalContainer').mb_close();");
					}
				}
				break;
			default: //show overview
				$data = array();

				$ret = blFunctionCall("payroll.getEmployeeFilterList",array("FilterForEmplOverview"=>true, "FilterForCalculation"=>true));
				if($ret["success"]) $data["employeeFilter_list"] = $ret["data"];

				$objWindow = new wgui_window("payroll", "EmployeeFilterOverview");
				$objWindow->windowTitle($objWindow->getText("prlUtlEfcTitle"));
				$objWindow->windowIcon("employee-edit32.png");
				$objWindow->windowWidth(570);
				$objWindow->windowHeight(230);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("utilities",$data,"EmployeeFilterOverview");
				$objWindow->showWindow();

				communication_interface::jsExecute("$('#prlUtlEfcBtnNew').bind('click', function(e) { cb('payroll.EmployeeFilter',{'action':'editor', 'id':0, 'loadFieldDef':(jQuery.isEmptyObject(prlVlFieldDef)?1:0)}); });");
				communication_interface::jsExecute("$('#prlUtlEfcBtnEdit').bind('click', function(e) { if($('#prlUtlEfc_id').val()>0) cb('payroll.EmployeeFilter',{'action':'editor', 'id':$('#prlUtlEfc_id').val(), 'loadFieldDef':(jQuery.isEmptyObject(prlVlFieldDef)?1:0)}); });");
				communication_interface::jsExecute("$('#prlUtlEfcBtnDelete').bind('click', function(e) { if($('#prlUtlEfc_id').val()>0) cb('payroll.EmployeeFilter',{'action':'delete', 'id':$('#prlUtlEfc_id').val()}); });");
				communication_interface::jsExecute("$('#prlUtlEfcBtnCancel').bind('click', function(e) { $('#modalContainer').mb_close(); });");
				break;
			}
			break;
		case 'payroll.dbBackup':
			if(!isset($functionParameters[0]["start"])) {
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtBackupAusfuehren"));
				$objWindow->windowWidth(470);
				$objWindow->windowHeight(200);
				$objWindow->setContent("<br/>Der Backup-Prozess kann, je nach Datenmenge, mehrere Sekunden oder sogar Minuten in Anspruch nehmen. Unterbrechen Sie bitte den Vorgang nicht, nachdem Sie das Backup gestartet haben.<br/><br/><button onclick='cb(\"payroll.dbBackup\", {\"start\":\"yes\"}); $(this).prop(\"disabled\", true);'>Backup starten</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
				$objWindow->showInfo();
			}else{
				$fileName = $this->doDbBackup("M");

				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtBackupAbgeschlossen"));
				$objWindow->windowWidth(470);
				$objWindow->windowHeight(200);
				$objWindow->setContent("<br/>Das Backup mit der Bezeichnung '".$fileName."' wurde abgeschlossen. Bitte merken Sie sich diese Bezeichung, damit Sie bei Bedarf die Datenbank auf den richtigen Zeitpunkt zuruecksetzen koennen.<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
				$objWindow->showInfo();
			}
			break;
		case 'payroll.dbRestore':
			if(!isset($functionParameters[0]["start"])) {
				$customerDbName = session_control::getSessionInfo("db_name");
				$tmpBaseDir = $aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName."/backup/";
				$files = scandir($tmpBaseDir);
				$fileList = "";
				foreach($files as $fn) {
					if(substr($fn, -4)==".sql") {
						$sfn = str_replace(".sql","",$fn);
						$fileList .= "<option value=\"".$sfn."\">".$sfn."</option>";
					}
				}
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtDatenWiederherstellen"));
				$objWindow->windowWidth(570);
				$objWindow->windowHeight(240);
				$objWindow->setContent("<br/>Die Daten aus folgendem Backup wiederherstellen:<br/><select id=\"dbrstbkp\">".$fileList."</select><br/><br/> Die Wiederherstellung kann, je nach Datenmenge, mehrere Sekunden oder sogar Minuten in Anspruch nehmen. Unterbrechen Sie bitte den Vorgang nicht, nachdem Sie die Wiederherstellung gestartet haben.<br/><br/><button onclick='cb(\"payroll.dbRestore\", {\"start\":\"yes\", \"backup\":$(\"#dbrstbkp\").val()}); $(this).prop(\"disabled\", true);'>Wiederherstellung starten</button><button onclick='$(\"#modalContainer\").mb_close();'>Abbrechen</button>");
				$objWindow->showInfo();
			}else{
				$customerDbName = session_control::getSessionInfo("db_name");
				$tmpBaseDir = $aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName."/backup/";
				if(!file_exists($tmpBaseDir.$functionParameters[0]["backup"].".sql")) {
					communication_interface::alert("Error: File does not exist!");
					break;
				}
//communication_interface::alert("restore... ".$functionParameters[0]["backup"]);
//TODO: ACHTUNG nur fuer SRV2 gueltig!!!           
				exec($aafwConfig["paths"]["utilities"]["mysql"]." --comments -u backup -p63i7E24ce ".$customerDbName." < ".$tmpBaseDir.$functionParameters[0]["backup"].".sql"); 
//TODO: ACHTUNG nur fuer SRV1 gueltig!!!
//				exec($aafwConfig["paths"]["utilities"]["mysql"]." --comments -u backup -p63i7E24ce btest < ".$tmpBaseDir.$functionParameters[0]["backup"].".sql"); 

				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtWiederherstellungAbgeschlossen"));
				$objWindow->windowWidth(470);
				$objWindow->windowHeight(200);
				$objWindow->setContent("<br/>Die Wiederherstellung ist abgeschlossen. Um sicher zu gehen, dass saemtliche Daten neu geladen werden, melden Sie sich bitte von System ab und anschliessend loggen Sie sich neu ein.<br/><br/><button onclick='cb(\"core.logout\");'>Logout</button>"); //$(\"#modalContainer\").mb_close();
				$objWindow->showInfo();
			}
			break;
		case 'payroll.prepareCalculation':
			$stage = isset($functionParameters[0]["stage"]) ? $functionParameters[0]["stage"] : 2;
			$sp = array();
			$sp["wageCodeChange"] = isset($functionParameters[0]["wageCodeChange"]) && $functionParameters[0]["wageCodeChange"]==0 ? "0" : "1";
			$sp["wageBaseChange"] = isset($functionParameters[0]["wageBaseChange"]) && $functionParameters[0]["wageBaseChange"]==0 ? "0" : "1";
			$sp["insuranceChange"] = isset($functionParameters[0]["insuranceChange"]) && $functionParameters[0]["insuranceChange"]==0  ? "0" : "1";
			$sp["modifierChange"] = isset($functionParameters[0]["modifierChange"]) && $functionParameters[0]["modifierChange"]==0 ? "0" : "1";
			$sp["workdaysChange"] = isset($functionParameters[0]["workdaysChange"]) && $functionParameters[0]["workdaysChange"]==0 ? "0" : "1";
			$sp["pensiondaysChange"] = isset($functionParameters[0]["pensiondaysChange"]) && $functionParameters[0]["pensiondaysChange"]==0 ? "0" : "1";

			switch($stage) {
			case 1:
				//question dialog
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtBerechnungsdatenVorbereiten"));
				$objWindow->windowWidth(480);
				$objWindow->windowHeight(200);
				$objWindow->setContent("<br/>Ihre letzte Aenderung koennte Einfluss auch die Berechnungsdaten haben. Moechten Sie die Berechnungsdaten neu vorbereiten lassen?<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
				$objWindow->showQuestion();
				break;
			case 2:
				//progress dialog
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtBerechnungsdatenVorbereiten"));
				$objWindow->windowWidth(480);
				$objWindow->windowHeight(200);
				$objWindow->setContent("<br/>Das Bearbeiten der Berechnung laeuft.<br/><br/>Bitte warten... <img src=\"/web/img/working.gif\"><br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>Schliessen</button>");
				$objWindow->showInfo();
				communication_interface::jsExecute("cb('payroll.prepareCalculation',{'stage':3,'wageCodeChange':".$sp["wageCodeChange"].",'wageCodeChange':".$sp["wageCodeChange"].",'wageBaseChange':".$sp["wageBaseChange"].",'insuranceChange':".$sp["insuranceChange"].",'modifierChange':".$sp["modifierChange"].",'workdaysChange':".$sp["workdaysChange"].",'pensiondaysChange':".$sp["pensiondaysChange"]."});");
				break;
			case 3:
				//execute "prepare calculation"
				blFunctionCall('payroll.prepareCalculation', $sp);
				communication_interface::jsExecute("$('#modalContainer').mb_close();");
				break;
			}
			break;
		case 'payroll.payslipConfig':
			if(isset($functionParameters[0]["f"])) {
				switch($functionParameters[0]["f"]) {
				case 'templateOv':
					$objWindow = new wgui_window("payroll", "infoBox");
					$objWindow->windowTitle($objWindow->getText("txtLohnabrechnungsVorlagen"));
					$objWindow->windowWidth(500);
					$objWindow->windowHeight(240);

					$templateList = blFunctionCall('payroll.getPayslipCfgList');
					if($templateList["success"]) {
						$data["template_list"] = $templateList["data"];
					}else{
						$data["template_list"] = array();
					}
					$objWindow->loadContent("configuration",$data,"selectPayslipCfg");
					$objWindow->showInfo();
					break;
				case 'takeAction':
					if(isset($functionParameters[0]["w"])) {
						switch($functionParameters[0]["w"]) {
						case 'edit':
							if($functionParameters[0]["id"]!=0) $formList = blFunctionCall('payroll.getPayslipCfgDetail', array("id"=>$functionParameters[0]["id"]));
							else $formList = blFunctionCall('payroll.getPayslipCfgDetail', array("listValuesOnly"=>true));

							$data["template_list"] = array();
							foreach($formList["AvailablePDF"] as $row) $data["template_list"][] = array("template"=>$row);

							$objWindow = new wgui_window("payroll", "editPayslipCfg");
							$objWindow->windowTitle($objWindow->getText("txtLayoutBearbeiten")); //<-- hier eventuell noch zusaetzlich den Layoutnamen einblenden
							$objWindow->windowIcon("config32.png");
							$objWindow->windowWidth(850);
							$objWindow->windowHeight(480);
							$objWindow->dockable(false);
							$objWindow->buttonMaximize(false);
							$objWindow->resizable(false);
							$objWindow->fullscreen(false);
							$objWindow->modal(true);
							$objWindow->loadContent("configuration",$data,"editPayslipCfg");
							$objWindow->showWindow();

							$arrLanguages = array();
							foreach($formList["Languages"] as $row) $arrLanguages[] = "'".$row."'";

							if($functionParameters[0]["FieldDef"]!=1) communication_interface::jsExecute($this->getEmplFieldDef());
							if($functionParameters[0]["id"]!=0) {
							//communication_interface::alert(print_r($formList,true));
								$retFld = array();
								$infoFld = array();
								foreach($formList["data"] as $fld=>$val) $retFld[] = "'".$fld."':'".str_replace("'","\\'",$val)."'";
								foreach($formList["InfoFields"] as $row) $infoFld[] = "{'label':'".str_replace("'","\\'",$row["label"])."','language':'".$row["language"]."','field_type':'".$row["field_type"]."','field_name':'".$row["field_name"]."'}";
								communication_interface::jsExecute("prlPslpCfg = {'SystemInfoFields':[{'val':'1', 'lbl':'Auszahlungsdatum'}, {'val':'2', 'lbl':'Zeitraum Periode'}, {'val':'3', 'lbl':'Bezeichnung Periode'}], 'data':{".implode(",",$retFld).", 'InfoFields':[".implode(",",$infoFld)."]}, 'Languages':[".implode(",",$arrLanguages)."] };");
							}else communication_interface::jsExecute("prlPslpCfg = {'SystemInfoFields':[{'val':'1', 'lbl':'Auszahlungsdatum'}, {'val':'2', 'lbl':'Zeitraum Periode'}, {'val':'3', 'lbl':'Bezeichnung Periode'}], 'data':{'id':0, 'payslip_name':'', 'pdf_template':'', 'default_payslip':0, 'info_font_name':'phv', 'info_font_size':'10', 'info_offset_top':'70', 'info_offset_left':'20', 'addr_font_name':'phv', 'addr_font_size':'10', 'addr_offset_top':'45', 'addr_offset_left':'130', 'content_font_name':'phv', 'content_font_size':'10', 'content_offset_top':'110', 'content_offset_left':'20', 'content_width':'170', 'InfoFields':[]}, 'Languages':[".implode(",",$arrLanguages)."] };");

							communication_interface::jsExecute("prlPslpCfgInit();");
							break;
						case 'delete':
							if($functionParameters[0]["commit"]==1) {
								$res = blFunctionCall('payroll.deletePayslipCfgDetail',$functionParameters[0]["id"]);
								communication_interface::jsExecute("$('#modalContainer').mb_close();");
							}else{
								$templateList = blFunctionCall('payroll.getPayslipCfgList');
								if($templateList["success"]) {
									foreach($templateList["data"] as $row) if($row["id"]==$functionParameters[0]["id"]) $data["TemplateName"] = $row["payslip_name"]; 
									$objWindow = new wgui_window("payroll", "infoBox");
									$objWindow->windowTitle($objWindow->getText("txtVorlageLoeschen"));
									$objWindow->windowWidth(370);
									$objWindow->windowHeight(180);
									$data["TemplateId"] = $functionParameters[0]["id"];
									$objWindow->loadContent("configuration",$data,"deletePayslipCfg");
									$objWindow->showQuestion();
								}
							}
							break;
						case 'save':
							$ret = blFunctionCall('payroll.savePayslipCfgDetail', $functionParameters[0]["data"]);
							if($ret["success"]) {
								communication_interface::jsExecute("$('#modalContainer').mb_close();");
							}else{
								communication_interface::jsExecute("$('#modalContainer input').css('background-color','');");
								communication_interface::jsExecute("$('#modalContainer select').css('background-color','');");
								//communication_interface::alert(print_r($ret,true));
								if($ret["errCode"]==30) {
									foreach($ret["fieldNames"] as $fld) {
										if($fld["FieldName"]=="label") communication_interface::jsExecute("$('.prlPslpCfgLstI li').eq(".$fld["index"].").find('input').eq(0).css('background-color','#f88');");
									}
								}else{
									foreach($ret["fieldNames"] as $fieldName) communication_interface::jsExecute("$('#modalContainer #prlPslpCfg_".$fieldName."').css('background-color','#f88');");
								}
							}
						}
					}
					break;
				}
			}
			break;
		case 'payroll.currencyConfig':
			if(!isset($functionParameters[0]["action"])) $functionParameters[0]["action"] = "edit";

			switch($functionParameters[0]["action"]) {
			case 'edit':
				$tmpCncyAssigned = array();
				$data["currency_table"] = array();
				$res = blFunctionCall('payroll.getCurrencyList', array("type"=>"assigned"));
				if($res["success"]) {
					foreach($res["data"] as $row) {
						if($row["default_currency"]==1) $data["default_currency"] = $row["core_intl_currency_ID"];
						else $data["currency_table"][] = $row;
						$tmpCncyAssigned[] = $row["core_intl_currency_ID"];
					}
				}

				$data["currency_list"] = array();
				$res = blFunctionCall('payroll.getCurrencyList', array("type"=>"complete"));
				if($res["success"]) foreach($res["data"] as $row) if(!in_array($row["core_intl_currency_ID"], $tmpCncyAssigned)) $data["currency_list"][] = $row;

				$objWindow = new wgui_window("payroll", "prlCurrencyEdit");
				$objWindow->windowTitle($objWindow->getText("txtWaehrungenBearbeiten"));
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(320);
				$objWindow->windowHeight(230);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("configuration",$data,"prlCurrencyEdit");
				$objWindow->showWindow();

				communication_interface::jsExecute("$('#prlCfgCncySave').bind('click', function(e) { var r = {}; $('.prlCfgCncy select, .prlCfgCncy input').each(function(index) { r[$(this).attr('id').substr(11)] = $(this).val(); }); cb('payroll.currencyConfig',{'action':'save', 'data':r}); });");
				communication_interface::jsExecute("$('#prlCfgCncyCancel').bind('click', function(e) { $('#modalContainer').mb_close(); });");
				communication_interface::jsExecute("$('.prlCfgCncy tr div').bind('click', function(e) { $(this).parent().parent().remove(); });");
				break;
			case 'save':
				$currencyData = array();
				foreach($functionParameters[0]["data"] as $k=>$v) {
					if(preg_match('/^forexRate_([A-Z]{3,3})$/', $k, $matches)) $currencyData[$matches[1]] = $v;
				}
				if($functionParameters[0]["data"]["currency"]!="") $currencyData[$functionParameters[0]["data"]["currency"]] = $functionParameters[0]["data"]["forexRate"];

				$res = blFunctionCall('payroll.saveCurrencyList', array("mode"=>"replace", "data"=>$currencyData));
				if($res["success"]) {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				}else{
					communication_interface::alert("error payroll.saveCurrencyList");
				}
				break;
			}

			break;
		case 'payroll.copyPayrollAccount':
			if(isset($functionParameters[0]["srcLOA"])) {
				$res = blFunctionCall('payroll.copyPayrollAccount', array("src"=>$functionParameters[0]["srcLOA"], "dest"=>$functionParameters[0]["destLOA"]));
				if($res["success"]) {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				}else{
					communication_interface::alert("payroll.copyPayrollAccount:".$res["errText"]);
				}
			}else{
				$objWindow = new wgui_window("payroll", "infoBox");
				$objWindow->windowTitle($objWindow->getText("txtLohnartkopieren"));
				$objWindow->windowWidth(300);
				$objWindow->windowHeight(150);
				$objWindow->setContent('<div class="ui-tabs ui-widget-content ui-corner-all"><label for="prlFormCfg_srcAcc">von:</label><input type="text" id="prlFormCfg_srcAcc"/><br/><label for="prlFormCfg_destAcc">nach:</label><input type="text" id="prlFormCfg_destAcc"/><br/></div><button id="prlFormCfgSave">Lohnart kopieren</button><button id="prlFormCfgCancel">Abbrechen</button><br/>');
				$objWindow->showInfo();
				communication_interface::jsExecute("$('#prlFormCfgSave').bind('click', function(e) { cb('payroll.copyPayrollAccount',{'srcLOA':$('#prlFormCfg_srcAcc').val(), 'destLOA':$('#prlFormCfg_destAcc').val()}); });");
				communication_interface::jsExecute("$('#prlFormCfgCancel').bind('click', function(e) { $('#modalContainer').mb_close(); });");
				communication_interface::jsExecute("$('label[for=prlFormCfg_srcAcc]').css('width','60px').css('display','inline-block');");
				communication_interface::jsExecute("$('label[for=prlFormCfg_destAcc]').css('width','60px').css('display','inline-block');");
				communication_interface::jsExecute("$('#prlFormCfg_srcAcc').css('width','60px');");
				communication_interface::jsExecute("$('#prlFormCfg_destAcc').css('width','60px');");
			}
			break;


/* **** P A Y M E N T   S P L I T T  *******************************************************************  */
/* **** start                        *******************************************************************  */
					
		case 'payroll.paymentSplit':
			$employee = array();
			$company_ID = 0;
			$payrollEmployeeID = 0;
			$MAInfo = "";
			$companyShort= ""; 
			$functionParameterAction = "paymentSplitAction_UebersichtZahlungssplit";
			if(isset( $functionParameters[0]["action"])) {
				$functionParameterAction = $functionParameters[0]["action"];
			}			
			
			switch($functionParameterAction) {
			case 'BankverbindungBearbeiten_BankdataFill':
			case 'GUI_bank_source_Overview':
			case 'GUI_bank_source_edit':
			case 'GUI_bank_source_save':
			case 'GUI_bank_source_del':
				if (isset($functionParameters[0]["company_ID"])) {
					$company_ID = $functionParameters[0]["company_ID"];
				}
				//nix machen
				break;
			default:
				$payrollEmployeeID = isset($functionParameters[0]["empId"]) ? $functionParameters[0]["empId"] : 0;
				if($payrollEmployeeID < 1) {
					//communication_interface::alert("ERROR\npayroll.paymentSplit payrollEmployeeID:".$payrollEmployeeID."\n action:".$functionParameterAction);	
					break;			
				} else {
					$employee = blFunctionCall('payroll.getEmployee', $payrollEmployeeID);
					$company_ID = $employee["data"][0]["payroll_company_ID"];
					$MAInfo = " - ".addslashes( $employee["data"][0]["EmployeeNumber"]." ".$employee["data"][0]["Lastname"].", ".$employee["data"][0]["Firstname"]);
				}
				break;
			}
			
			$company = array();
			if ($company_ID > 0) {
				$company = blFunctionCall('payroll.auszahlen.getCompany', $company_ID);
				$companyShort= $company["short"];
			}
			
			switch($functionParameterAction) {
			case 'paymentSplitAction_BankverbindungBearbeiten': //case 'payroll.paymentSplit' action:'paymentSplitAction_BankverbindungBearbeiten'
				//communication_interface::alert("case 'paymentSplitAction_BankverbindungBearbeiten':".print_r($functionParameters[0], true));
				$loadFromJSON = isset($functionParameters[0]["loadFromJSON"]) ? true : false;
				$splitID = 0;
				$bankID = 0;
				$zahlstellenID = 0;				
				//$splitList = blFunctionCall('payroll.getPaymentSplitList_and_splitRecId', $payrollEmployeeID, $functionParameters[0]['rid']);
				//communication_interface::alert("case 'paymentSplitAction_BankverbindungBearbeiten'\n splitList:".print_r($splitList, true));
				if(isset($functionParameters[0]["splitID"])){
					$splitID = $functionParameters[0]["splitID"];
				} else {
					if(isset($functionParameters[0]["rid"])){
						$splitID = $functionParameters[0]["rid"];
					}
				}

				if (isset($functionParameters[0]["bankID"])) {
					$bankID = intval($functionParameters[0]["bankID"]);
				}

				$windHeight = 560;
				if($splitID>0) {
					//$windHeight = 570;
					//Wenn diesem Mitarbeiter einen Zahlungssplitt hinterlegt ist
					$splitDetail = blFunctionCall('payroll.getPaymentSplitDetail', array("id"=>$splitID, "empId"=>$payrollEmployeeID));
					$data["bank_source_list"] = $splitDetail["dbview_payroll_bank_source"];
					$data["bank_destination_list"] = $splitDetail["bankDestination"];
					$zahlstellenID = $splitDetail["data"]["payroll_bank_source_ID"];
					$bankID = $splitDetail["data"]["payroll_bank_destination_ID"];					
				}					
							
				if(isset($functionParameters[0]["empId"])){
					$payrollEmployeeID = $functionParameters[0]["empId"];
				}
				
				if (!intval($payrollEmployeeID)>0){
					//communication_interface::alert("Error in 'paymentSplitAction_BankverbindungBearbeiten':  Employee-Id missing ".$payrollEmployeeID);
					break;
				}

				$employeeData = blFunctionCall('payroll.getEmployeeDetail',$payrollEmployeeID,true);

				//laden der Zahlstellenbanken 
				$selectedCompany = 0;//wenn 0, dann alle sonst : $selectedCompany = $companyID;
				$zahlstellenDaten = blFunctionCall("payroll.auszahlen.getZahlstellenListe", $selectedCompany);
				if($zahlstellenDaten["success"]) $data["zahlstellen_list"] = $zahlstellenDaten["data"];
								
				//laden aller Länder
				$countryList = blFunctionCall('payroll.getCountryList');
				$data["country_list"] = $countryList["data"];
				
				if ($bankID < 1) {
					//Versuche eine Bankverbindung zu finden in der Tabelle payroll_bank_destination
					$dest = blFunctionCall('payroll.auszahlen.getDestinationBankAccount', $payrollEmployeeID, 0);
					if($dest["success"]) {
						$bankID = $dest["bank_id"];
						$data["id"] = 0;
						$data["payroll_employee_ID"] = $payrollEmployeeID;
						$data["description"] = "";
						$data["bank_account"] = "";
						$data["postfinance_account"] = "";
						$data["bank_swift"] = "";
						$data["beneficiary1_line1"] = $employeeData["data"][0]["Firstname"]." ".$employeeData["data"][0]["Lastname"];
						$adr = $employeeData["data"][0]["AdditionalAddrLine1"]." ".$employeeData["data"][0]["AdditionalAddrLine2"];
						if (strlen($adr) > 32) {$adr = $employeeData["data"][0]["AdditionalAddrLine1"];}
						$data["beneficiary1_line2"] = $adr;
						$data["beneficiary1_line3"] = $employeeData["data"][0]["Street"];
						$data["beneficiary1_line4"] = $employeeData["data"][0]["ZIP-Code"]." ".$employeeData["data"][0]["City"]." ".$employeeData["data"][0]["ResidenceCanton"];
						$data["beneficiary2_line1"] = "";
						$data["beneficiary2_line2"] = "";
						$data["beneficiary2_line3"] = "";
						$data["beneficiary2_line4"] = "";
						$data["beneficiary2_line5"] = "";
						$data["beneficiary_bank_line1"] = "";
						$data["beneficiary_bank_line2"] = "";
						$data["beneficiary_bank_line3"] = "";
						$data["beneficiary_bank_line4"] = "";
						$data["notice_line1"] = "";
						$data["notice_line2"] = "";
						$data["destination_type"] = "";
						$data["core_intl_country_ID"] = "CH";
						$data["expense"] = "";
						$data["notice_line1"] = "";
						$data["notice_line2"] = "";
						$data["notice_line3"] = "";
						$data["notice_line4"] = "";
						$data["currency"] = "";
					}
				} 
				$bankDestDetail = blFunctionCall('payroll.getDestBankDetail', $bankID);					
				foreach($bankDestDetail["data"] as $fldName => $fldVal) {
					$data[$fldName] = $fldVal;
					if($fldName=="beneficiary1_line1" && trim($fldVal)=="") $data["beneficiary1_line1"] =  $employeeData["data"][0]["Firstname"]." ".$employeeData["data"][0]["Lastname"];
					if($fldName=="beneficiary1_line2" && trim($fldVal)=="") $data["beneficiary1_line2"] =  $employeeData["data"][0]["AdditionalAddrLine1"]." ".$employeeData["data"][0]["AdditionalAddrLine2"];
					if($fldName=="beneficiary1_line3" && trim($fldVal)=="") $data["beneficiary1_line3"] =  $employeeData["data"][0]["Street"];
					if($fldName=="beneficiary1_line4" && trim($fldVal)=="") $data["beneficiary1_line4"] =  $employeeData["data"][0]["ZIP-Code"]." ".$employeeData["data"][0]["City"]." ".$employeeData["data"][0]["ResidenceCanton"];
					//communication_interface::alert("field:".$fldName."=".$fldVal);
				}
				$is_standard_bank=$bankDestDetail["data"]["is_standard_bank"];
				if ($is_standard_bank == "Y") {
					$MAInfo .= " [standard bank]";
					$zahlstellenID = $bankDestDetail["data"]["nonstandard_banksourcezahlstelle"];
				} else {
					$MAInfo .= " [Bank-ID: ".$bankID."]";
				}
				

				$data["splitID"] = $splitID;
				$data["bankID"] = $bankID;

				$editMode = false;
				if ($bankID > 0) {
					$editMode = true;
				}
				$objWindow = new wgui_window("payroll", "destinationBankEdit");
				$objWindow->windowTitle($objWindow->getText("txtBankverbindungBearbeiten").$MAInfo); 
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(950);
				$objWindow->windowHeight($windHeight);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(true);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("payment",$data,"destinationBankEdit");
				$objWindow->showWindow();

				if(!$editMode) {
					communication_interface::jsExecute("$('#prlPmtSplt_core_intl_country_ID').val('CH');");
				} else {
					communication_interface::jsExecute("$('#prlPmtSplt_destination_type').val('".$data["destination_type"]."');");
					communication_interface::jsExecute("$('#prlPmtSplt_core_intl_country_ID').val('".$data["core_intl_country_ID"]."');"); 
					communication_interface::jsExecute("$('#prlPmtSplt_expense').val('".$data["expense"]."');");
				}
												
				//Payment-Split-Daten
				if($splitID > 0){					
					//communication_interface::alert("paymentSplitAction_editSplit\npayrollEmployeeID:".$payrollEmployeeID."\nHat Splitt  ".$splitID." \nbankID:".$bankID);
					if(!$loadFromJSON) {
						$arrFld = array();
						if($splitDetail["success"]) {
							$periodFlags = ($splitDetail["data"]["major_period"]==0 ? 0 : 1) + ($splitDetail["data"]["minor_period"]==0 ? 0 : 2) + ($splitDetail["data"]["major_period_bonus"]==0 ? 0 : 4);
							$specificPeriod = ($splitDetail["data"]["major_period_num"]+$splitDetail["data"]["minor_period_num"]+$splitDetail["data"]["major_period_bonus_num"])==0 ? false : true;
							if($periodFlags==7) $splitDetail["data"]["period"] = "";
							else if(!$specificPeriod && $periodFlags==1) $splitDetail["data"]["period"] = "MAP";
							else if(!$specificPeriod && $periodFlags==2) $splitDetail["data"]["period"] = "MIP";
							else if(!$specificPeriod && $periodFlags==4) $splitDetail["data"]["period"] = "MPB";
							else if($splitDetail["data"]["major_period_bonus_num"]!=0) $splitDetail["data"]["period"] = $splitDetail["data"]["major_period_bonus_num"];
							else if($splitDetail["data"]["major_period_num"]!=0) $splitDetail["data"]["period"] = $splitDetail["data"]["major_period_num"];
	
							foreach($splitDetail["data"] as $fieldName=>$fieldValue) $arrFld[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
						}
						communication_interface::jsExecute("prlPmtSplt = {'empId':'".$payrollEmployeeID."', 'editSplt':{".implode(",", $arrFld)."}};");
					}
					communication_interface::jsExecute("$('#btnBankverbindungClose').bind('click', function(e) {    cb('payroll.bankverbindung_close_check', {'splitID':".$splitID.", 'bankID':".$bankID." , 'empId':".$payrollEmployeeID."});	  	});   ");					
					communication_interface::jsExecute("$('#btnInitZahlungssplitt').hide();");
					$bdCurr = "CHF";
					if (isset($bankDestDetail["data"]["bank_dest_currency"])) {
						$bdCurr = $bankDestDetail["data"]["bank_dest_currency"];
					}
					communication_interface::jsExecute("$('#prlPmtSplt_anzeigewaehrung').val('".$bdCurr."');");
					//communication_interface::jsExecute("$('#destBankContainer').css('height', '440px');");
				} else {
					//communication_interface::jsExecute("$('#destBankContainer').css('height', '440px');");
					//communication_interface::alert("paymentSplitAction_editSplit\npayrollEmployeeID:".$payrollEmployeeID."\nNO Splitt  ".$splitID." \nbankID:".$bankID);
					if ($is_standard_bank != "Y") {
						communication_interface::jsExecute("$('#btnInitZahlungssplitt').hide();");
					}				
					communication_interface::jsExecute("$('#fieldsetZahlungssplitt').hide();");
					communication_interface::jsExecute("$('#btnBankverbindungClose').bind('click', function(e) { $('#modalContainer').mb_close(); });");
					communication_interface::jsExecute("$('#btnInitZahlungssplitt').bind('click', function(e) { cb('payroll.paymentSplit', {'action': 'paymentSplitAction_initZahlungssplitt', 'empId':$payrollEmployeeID, 'zahlstelle':0, 'bankID':$bankID});   $('#modalContainer').mb_close();	});   ");	    
				}
				communication_interface::jsExecute("prlPmtSpltEditInit();");
				communication_interface::jsExecute("prlPmtSpltEditJSON2Form();"); 
				communication_interface::jsExecute("$('#prlPmtSplt_selectedZahlstelle').val('".$zahlstellenID."');");                                        
				communication_interface::jsExecute("$('#btnSaveBankDestinationUndSplit').bind('click', function(e) {    jsSaveBankDestinationUndSplit(".$payrollEmployeeID.");     });");//der ruft im JS dann dies auf: 'paymentSplitAction_saveBankDestinationUndSplit'
				communication_interface::jsExecute("$('#prlPmtSplt_bank_account').bind('blur', function(e) { cb('payroll.paymentSplit', {'action': 'BankverbindungBearbeiten_BankdataFill', 'iban': document.getElementById('prlPmtSplt_bank_account').value , 'bankLine3': document.getElementById('prlPmtSplt_beneficiary_bank_line3').value });  	});   ");
				communication_interface::jsExecute("$('#prlPmtSplt_beneficiary2_line1').bind('blur', function(e) { cb('payroll.paymentSplit', {'action': 'BankverbindungBearbeiten_Beneficiary2Line1', 'iban': document.getElementById('prlPmtSplt_beneficiary2_line1').value  });  	});   ");

				$iban = strtoupper(  trim($data["bank_account"])  );
				if (strlen($iban)> 2) {
					if (substr($iban,0,2)=="CH") {
						communication_interface::jsExecute("$('#prlPmtSplt_bank_swift').attr('disabled',true);");                                        
						communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line1').attr('disabled',true);");
						communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line2').attr('disabled',true);");                                   
						communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line3').attr('disabled',true);");
					}
				}
				break;

			case 'BankverbindungBearbeiten_Beneficiary2Line1':
				$IBAN = "";
				communication_interface::jsExecute("$('#prlPmtSplt_beneficiary2_line1').css('backgroundColor','#fff');   ");
				if (isset($functionParameters[0]["iban"])) {
					$IBAN = trim($functionParameters[0]["iban"]);
					$IBAN = str_replace(" ", "", $IBAN);
				}
				if (strlen($IBAN) >= 19) {
					$land = substr($IBAN,0,2);
					if (intval($land) > 0) {//Beginnt mit einer Zahl
						communication_interface::jsExecute("$('#prlPmtSplt_beneficiary2_line1').css('backgroundColor','#ffacac');   ");
						communication_interface::alert("IBAN inkorrekt [CH12 2345 3456 4567 5678 9]");
					}
				} else {
					if (strlen($IBAN) > 0)  {
						communication_interface::jsExecute("$('#prlPmtSplt_beneficiary2_line1').css('backgroundColor','#ffacac');   ");
						communication_interface::alert("IBAN inkorrekt? (< 19 Stellen)");
					}
				}
				break;

			case 'BankverbindungBearbeiten_BankdataFill':
				$IBAN = "";
				communication_interface::jsExecute("$('#prlPmtSplt_bank_account').css('backgroundColor','#fff');   ");
				if (isset($functionParameters[0]["iban"])) {
					$IBAN = strtoupper( trim($functionParameters[0]["iban"]) );
					$IBAN = str_replace(" ", "", $IBAN);
				}
				if (strlen($IBAN) >= 19) {
					$land = substr($IBAN,0,2);
					if (intval($land) > 0) {//Beginnt mit einer Zahl
						communication_interface::jsExecute("$('#prlPmtSplt_bank_account').css('backgroundColor','#ffacac');   ");
						communication_interface::alert("IBAN inkorrekt [CH12 2345 3456 4567 5678 9]");
					} else {
						if (strlen(trim($functionParameters[0]["bankLine3"])) < 1) {
							if ($land == "CH") {
								$clearingBank = blFunctionCall('payroll.getClearingBank', $IBAN);
								$adr = $clearingBank["Domizil"].", ".$clearingBank["Postadr"];
								if (strlen($adr)>32) {
									$adr = $clearingBank["Domizil"];
								}
								if (strlen(trim($clearingBank["Domizil"])) < 2) {
									$adr = $clearingBank["Postadr"];
								}
								if (strlen(trim($clearingBank["Postadr"])) < 2) {
									$adr = $clearingBank["Domizil"];
								}
								if (strlen($adr) < 2) {
									communication_interface::alert("Keine Bank gefunden.\nIst die IBAN korrekt?");
								} else {
									communication_interface::jsExecute("$('#prlPmtSplt_description').val('".$clearingBank["Kurzbez"]."');");                                        
	
									communication_interface::jsExecute("$('#prlPmtSplt_bank_swift').css('backgroundColor','#eee');");                                        
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line1').css('backgroundColor','#eee');");
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line2').css('backgroundColor','#eee');");                                   
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line3').css('backgroundColor','#eee');");
	
									communication_interface::jsExecute("$('#prlPmtSplt_bank_swift').val('".$clearingBank["SWIFT"]."');");                                        
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line1').val('".substr($clearingBank["BankName"],0,32)."');");                                        
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line2').val('".$adr."');");                                        
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line3').val('".substr($clearingBank["PLZ"]." ".$clearingBank["Ort"],0,32)."');");                                        						
	
									communication_interface::jsExecute("$('#prlPmtSplt_bank_swift').attr('disabled',true);");                                        
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line1').attr('disabled',true);");
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line2').attr('disabled',true);");                                   
									communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line3').attr('disabled',true);");
								}
							}
						}
					}
				} else {
					communication_interface::jsExecute("$('#prlPmtSplt_bank_swift').val('');");                                        
					communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line1').val('');");
					communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line2').val('');");                                   
					communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line3').val('');");

					communication_interface::jsExecute("$('#prlPmtSplt_bank_swift').attr('disabled',false);");                                        
					communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line1').attr('disabled',false);");
					communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line2').attr('disabled',false);");                                   
					communication_interface::jsExecute("$('#prlPmtSplt_beneficiary_bank_line3').attr('disabled',false);");

					communication_interface::jsExecute("$('#prlPmtSplt_bank_account').css('backgroundColor','#ffacac');   ");
					communication_interface::alert("IBAN inkorrekt (< 19 Stellen)");
				}
				break;

			case 'paymentSplitAction_UebersichtZahlungssplit': 
				//communication_interface::alert("Form load paymentSplitAction_UebersichtZahlungssplit\nemp:".$payrollEmployeeID);
				$data["paymentSplitList"] = array();
				$splitList = blFunctionCall('payroll.getPaymentSplitList', $payrollEmployeeID);
				$bankdestinationID = 0;
				if(!$splitList["success"]) {
					//Ohne Splitt gehts direkt zum Banken-Fenster
					//if($splitList["errCode"] != 101) communication_interface::alert("Error Code ".$splitList["errCode"]);
					communication_interface::jsExecute("cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':".$payrollEmployeeID.", 'bankID':".$bankdestinationID.", 'company_ID':".$company_ID."});");
					break;
				}
				
				//Mitarbeiter hat Zahlungssplit					
				foreach($splitList["data"] as $row) {
					if ($bankdestinationID < 1) {//die erste Bank gemäss Processing Order
						$bankdestinationID = $row["payroll_bank_destination_ID"];
					}
					switch($row["split_mode"]) {
					case 1: $row["split_mode"]="Lohnart"; break;
					case 2: $row["split_mode"]="%"; break;
					case 3: $row["split_mode"]="Betrag ".$row["payroll_currency_ID"]; break;
					}//end switch
					
					$periodFlags = ($row["major_period"]==0 ? 0 : 1) + ($row["minor_period"]==0 ? 0 : 2) + ($row["major_period_bonus"]==0 ? 0 : 4);
					switch($periodFlags) {
					case 1: //Hauptzahltag
						$prd = array("alle HZ", "Januar", "Februar", "Maerz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember", "13. Monatslohn", "14. Monatslohn");
						$row["period"] = $prd[$row["major_period_num"]];
						break;
					case 2: //Zwischenzahltag
						$row["period"] = "alle ZZ";
						break;
					case 4: //Grati
						$prd = array("alle HZ", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, "Gratifikation 1", "Gratifikation 2");
						$row["period"] = $prd[$row["major_period_bonus_num"]];
						break;
					case 7: //alle Perioden
						$row["period"] = "alle";
						break;
					}//end switch
					$row["src_bank_label"] = $row["src_bank_label"]=="" ? "std" : "z".$row["src_bank_label"];
					$row["dest_bank_label"] = $row["dest_bank_label"]=="" ? "-- Standard --" : $row["dest_bank_label"]."  [".$row["payroll_bank_destination_ID"]."]";
					if ($row["destination_type"] == 3) {
						$row["dest_bank_label"] = "CASH [".$row["payroll_bank_destination_ID"]."]";
					}
					//communication_interface::alert("row:".print_r($row, true));
					$data["paymentSplitList"][] = $row;
				}//end foreach
				
				$standardBank = blFunctionCall('payroll.auszahlen.getStandardDestinationBankAccount', $payrollEmployeeID);				
				$objWindow = new wgui_window("payroll", "GUI_paymentSplitOverview");
				$objWindow->windowTitle($objWindow->getText("txtUebersichtZahlungssplit").$MAInfo);
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(710);
				$objWindow->windowHeight(330);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("payment",$data,"GUI_paymentSplitOverview");
				$objWindow->showWindow();

				$stdBankDescr = $standardBank['bankpostcash']." ".$standardBank['beneBank1']." ".$standardBank['beneBank3'];

				communication_interface::jsExecute("document.getElementById('stdBankDescr').value = '".$stdBankDescr."'; ");
				communication_interface::jsExecute("document.getElementById('stdBankAccount').value = '".$standardBank['bank_account']." [". $standardBank['bank_id']."]'; ");
				
				communication_interface::jsExecute("$('#btnStandardBankverbindung').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':".$payrollEmployeeID.", 'bankID':".$standardBank['bank_id']."}); });");
				
				communication_interface::jsExecute("prlPmtSplt = {'empId':'".$payrollEmployeeID."'};");
				communication_interface::jsExecute("prlPmtSpltMainInit();");
				if(isset($splitList["errCode"]) && $splitList["errCode"] == 101) communication_interface::jsExecute("$('#prlPmtSpltSaveOrder').attr('disabled', 'disabled');");
				break;

			case 'paymentSplitAction_saveBankDestinationUndSplit':
				$functionParameters[0]["data"]["empId"] = $payrollEmployeeID;
				$res = blFunctionCall('payroll.saveBankDestinationUndSplit', $functionParameters[0]["data"]);
				//communication_interface::alert("paymentSplitAction_saveBankDestinationUndSplit ".$payrollEmployeeID."\n".print_r($functionParameters,true)."\nresult:\n".print_r($res,true));
				break;
								
			case 'paymentSplitAction_saveSplitOrder': //case 'payroll.paymentSplit' action:'paymentSplitAction_saveSplitOrder'
				$res = blFunctionCall('payroll.savePaymentSplitOrder', array("data"=>$functionParameters[0]["data"]));
				break;
								
			case 'paymentSplitAction_deleteSplit': //case 'payroll.paymentSplit' action:'paymentSplitAction_deleteSplit'
				//communication_interface::alert('paymentSplitAction_deleteSplit: '.print_r($functionParameters[0], TRUE));
				if(isset($functionParameters[0]["commit"])) {
					$res = blFunctionCall('payroll.deletePaymentSplitDetail', array("id"=>$functionParameters[0]["splitID"]));
					communication_interface::jsExecute("cb('payroll.paymentSplit', {'empId':prlPmtSplt.empId});");
				}else{
					$splitID = $functionParameters[0]["rid"];
					//communication_interface::alert("case paymentSplitAction_deleteSplit loeschen splitID=".$splitID.print_r($functionParameters[0], true));
					$data[] = array();
					$data["splitID"] = $splitID;
					$objWindow = new wgui_window("payroll", "paymentSplitDelete");
					$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
					$objWindow->windowWidth(350);
					$objWindow->windowHeight(155);
					$objWindow->loadContent("payment",$data,"paymentSplitDelete");
					$objWindow->showQuestion();

					communication_interface::jsExecute("prlPmtSplt = {'empId':'".$payrollEmployeeID."', 'psId':'".$functionParameters[0]["rid"]."'};");//rid
					communication_interface::jsExecute("$('#prlPmtSpltYes').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_deleteSplit', 'commit':1, 'empId':prlPmtSplt.empId, 'splitID':prlPmtSplt.psId}); });");
					communication_interface::jsExecute("$('#prlPmtSpltNo').bind('click', function(e) { cb('payroll.paymentSplit', {'empId':prlPmtSplt.empId}); });");
				}
				break;

			case 'paymentSplitAction_BankverbindungAuswaehlen': 
				$res = blFunctionCall('payroll.getPaymentSplitDetail', array("id"=>0, "empId"=>$payrollEmployeeID));
				$data["bank_list"] = $res["bankDestination"];

				$objWindow = new wgui_window("payroll", "destinationBankOverview");
				$objWindow->windowTitle($objWindow->getText("txtBankverbindungAuswaehlen").$MAInfo);
				$objWindow->windowWidth(570);
				$objWindow->windowHeight(260);
				$objWindow->loadContent("payment",$data,"destinationBankOverview");
				$objWindow->showInfo();

				communication_interface::jsExecute("$('#prlBankDNew').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'bankID':0}); });");
				communication_interface::jsExecute("$('#prlBankDEdit').bind('click', function(e) { if($('#prlFormCfg_id').val()==0) return false; cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'bankID':$('#prlFormCfg_id').val()}); });");
				communication_interface::jsExecute("$('#prlBankDDelete').bind('click', function(e) { if($('#prlFormCfg_id').val()==0) return false; cb('payroll.paymentSplit', {'action':'paymentSplitAction_deleteBankDestination', 'empId':prlPmtSplt.empId, 'bankId':$('#prlFormCfg_id').val()}); });");
				communication_interface::jsExecute("$('#prlBankDCancel').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'loadFromJSON':1}); });");//vorher zu 'action':'paymentSplitAction_editSplit'
				break;
								
			case 'paymentSplitAction_initZahlungssplitt';
				$res = blFunctionCall('payroll.initZahlungssplitt', $functionParameters[0]["empId"], $functionParameters[0]["zahlstelle"], $functionParameters[0]["bankID"]);
				break;			
				
			case 'paymentSplitAction_deleteBankDestination': //case 'payroll.paymentSplit' action:'paymentSplitAction_deleteBankDestination'
				if(isset($functionParameters[0]["commit"])) {
					$res = blFunctionCall('payroll.deleteDestBankDetail', array("id"=>$functionParameters[0]["bankId"]));
					communication_interface::jsExecute("cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'loadFromJSON':1});");//vorher zu 'action':'paymentSplitAction_editSplit'
				} else {
					$objWindow = new wgui_window("payroll", "destinationBankDelete");
					//$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen")." id=".$functionParameters[0]["bankId"]);
					$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
					$objWindow->windowWidth(420);
					$objWindow->windowHeight(155);
					$objWindow->loadContent("payment",$data,"destinationBankDelete");
					$objWindow->showQuestion();

					communication_interface::jsExecute("$('#prlPmtSpltYes').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_deleteBankDestination', 'commit':1, 'empId':prlPmtSplt.empId, 'bankId':'".$functionParameters[0]["bankId"]."'}); });");
					communication_interface::jsExecute("$('#prlPmtSpltNo').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'loadFromJSON':1}); });");//vorher zu 'action':'paymentSplitAction_editSplit'
				}
				break;

			case 'GUI_bank_source_Overview': //case 'payroll.paymentSplit' action:'GUI_bank_source_Overview'
				//communication_interface::alert("GUI_bank_source_Overview");
			
				$res = blFunctionCall('payroll.getPaymentSplitDetail', array("id"=>0, "empId"=>$payrollEmployeeID));
				$data["list_of_zahlstellen"] = $res["dbview_payroll_bank_source"];
				$data["list_of_zahlstellen_waehrungen"] = $res["dbview_payroll_bank_sourcewaehrungen"];

				$objWindow = new wgui_window("payroll", "GUItemplate_bank_source_Overview");
				$objWindow->windowTitle($objWindow->getText("txtZahlstelleAuswaehlen"));
				$objWindow->windowWidth(570);
				$objWindow->windowHeight(250);
				$objWindow->loadContent("payment",$data,"GUItemplate_bank_source_Overview");
				$objWindow->showInfo();

				communication_interface::jsExecute("$('#prlBankSourceNew').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'GUI_bank_source_edit'}); });");
				communication_interface::jsExecute("$('#prlBankSourceEdit').bind('click', function(e) { if($('#prlFormCfg_id').val()==0) return false; cb('payroll.paymentSplit', {'action':'GUI_bank_source_edit', 'empId':prlPmtSplt.empId, 'bankId':$('#prlFormCfg_id').val()}); });");
				communication_interface::jsExecute("$('#prlBankSourceDelete').bind('click', function(e) { if($('#prlFormCfg_id').val()==0) return false; cb('payroll.paymentSplit', {'action':'GUI_bank_source_del', 'empId':prlPmtSplt.empId, 'bankId':$('#prlFormCfg_id').val()}); });");
				communication_interface::jsExecute("$('#prlBankSourceCancel').bind('click', function(e) { cb('payroll.paymentSplit', {'action':'paymentSplitAction_BankverbindungBearbeiten', 'empId':prlPmtSplt.empId, 'loadFromJSON':1}); });");//vorher zu 'action':'paymentSplitAction_editSplit'
				break;
				
			case 'GUI_bank_source_edit': //case 'payroll.paymentSplit' action:'GUI_bank_source_edit'
				//communication_interface::alert("GUI_bank_source_edit, Ba=".$functionParameters[0]["bankId"]);
				$editMode = !isset($functionParameters[0]["bankId"]) || $functionParameters[0]["bankId"]=="0" || $functionParameters[0]["bankId"]=="" ? false : true;

				if($editMode) {
					$res = blFunctionCall('payroll.getBankSourceDetail', array("id"=>$functionParameters[0]["bankId"]));
					$display = " Data: ";
					if($res["success"]) {
						foreach($res["data"] as $fldName => $fldVal) {							
							$data[$fldName] = $fldVal;
							$display .= $fldVal.", ";
						}
					}
					//communication_interface::alert("->GUI_bank_source_edit  -- editmode --". $display);
				} else {
					$data["id"] = 0;
					//$data["payroll_employee_ID"] = $payrollEmployeeID;
					$data["payroll_company_ID"] = $company_ID;
					$data["description"] = "";
					$data["source_type"] = "";
					$data["bank_source_carrier"] = "";
					$data["bank_source_currency_code"] = "";
					$data["bank_source_IBAN"] = "";
					$data["bank_source_desc1"] = "";
					$data["bank_source_desc2"] = "";
					$data["bank_source_desc3"] = "";
					$data["bank_source_desc4"] = "";
				}
				
				$objWindow = new wgui_window("payroll", "GUI_bank_source_Edit");
				$objWindow->windowTitle($companyShort." ".$objWindow->getText("txtZahlstelleBearbeiten"));
				$objWindow->windowIcon("config32.png");
				$objWindow->windowWidth(480);
				$objWindow->windowHeight(395);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(true);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("payment", $data, "GUI_bank_source_Edit");
				$objWindow->showWindow();

				if ($editMode) {
					//communication_interface::jsExecute("$('#prlPmtSplt_BankSourceEdit_btnDelete').bind('click', function(e) { if($('#prlFormCfg_id').val()==0) return false; cb('payroll.paymentSplit', {'action':'GUI_bank_source_del', 'company_ID': '".$company_ID."' , 'bankId':$('#prlPmtSplt_payroll_bankId').val() });        cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});    });");
					communication_interface::jsExecute("$('#prlPmtSplt_BankSourceEdit_btnDelete').bind('click', function(e) {  cb('payroll.paymentSplit', {'action':'GUI_bank_source_del', 'company_ID': '".$company_ID."' , 'bankId':$('#prlPmtSplt_payroll_bankId').val()});           });");
				} else {
					communication_interface::jsExecute("$('#prlPmtSplt_BankSourceEdit_btnDelete').css('background-color','#eee')");
				}
				communication_interface::jsExecute("$('#prlPmtSplt_BankSourceEdit_btnCancel').bind('click', function(e) {    cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});     });");
				//communication_interface::jsExecute("$('#prlPmtSplt_BankSourceEdit_btnSave').bind('click', function(e) { prl_BankSourceEdit_btnSave();     cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});   });");
				communication_interface::jsExecute("$('#prlPmtSplt_BankSourceEdit_btnSave').bind('click', function(e) { prl_BankSourceEdit_btnSave();    });");
				break;
			case 'GUI_bank_source_save': //case 'payroll.paymentSplit' action:'GUI_bank_source_save'
				//communication_interface::alert("GUI_bank_source_save > data:".print_r($functionParameters[0]["data"],true));
				communication_interface::jsExecute("$('#modalContainer input').css('background-color','');");
				communication_interface::jsExecute("$('#modalContainer select').css('background-color','');");
				$res = blFunctionCall("payroll.saveBankSourceDetail", $functionParameters[0]["data"]);
				if($res["success"]) {
					$company_ID = $functionParameters[0]["data"]["payroll_company_ID"];
					$err = false;
					//communication_interface::alert("GUI_bank_source_save success:".print_r($functionParameters[0]["data"],true));
					if ($functionParameters[0]["data"]["source_type"] < 3) {//Bei 1=Bank und 2=Post
						if (strlen($functionParameters[0]["data"]["description"]) < 2) {       communication_interface::jsExecute("$('#prlPmtSplt_description').css('background-color','#f88');");  $err=true;}
						if (strlen($functionParameters[0]["data"]["bank_source_IBAN"]) < 20) { communication_interface::jsExecute("$('#prlPmtSplt_bank_source_IBAN').css('background-color','#f88');");  $err=true;}
						if (strlen($functionParameters[0]["data"]["bank_source_desc1"]) < 3) { communication_interface::jsExecute("$('#prlPmtSplt_bank_source_desc1').css('background-color','#f88');");  $err=true;}
						if (strlen($functionParameters[0]["data"]["bank_source_desc2"]) < 3) { communication_interface::jsExecute("$('#prlPmtSplt_bank_source_desc2').css('background-color','#f88');");  $err=true;}
					}
					if ($err == false) {
						 communication_interface::jsExecute("cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});   ");
					}
				} else {
					//communication_interface::alert("GUI_bank_source_save success NOT:",true);
					foreach($res["fieldNames"] as $fieldName) communication_interface::jsExecute("$('#prlPmtSplt_".$fieldName."').css('background-color','#f88');");
					communication_interface::alert("error code ".$res["errCode"]."\n".$res["errText"]."\n".print_r($res["fieldNames"]));
				}
				break;
			case 'GUI_bank_source_del': //case 'payroll.paymentSplit' action:'GUI_bank_source_del'
				$bankId = $functionParameters[0]["bankId"];
				//communication_interface::alert("GUI_bank_source_del 3372\n  company_ID: ".$company_ID.", bankId: ".$bankId.print_r($functionParameters[0], true));
				if(isset($functionParameters[0]["todo"])) {
					//communication_interface::alert("GUI_bank_source_del If");
					$res = blFunctionCall('payroll.deleteBankSourceDetail', array("id"=>$bankId));
					communication_interface::jsExecute("cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});");
					break;
				} else {
					//communication_interface::alert("GUI_bank_source_del Else");
					$data["id"] = $bankId;
					$data["company_ID"] = $company_ID;
					$objWindow = new wgui_window("payroll", "GUItemplate_bank_source_Delete");
					$objWindow->windowTitle($objWindow->getText("txtLoeschungBestaetigen"));
					$objWindow->windowWidth(420);
					$objWindow->windowHeight(155);
					$objWindow->loadContent("payment",$data,"GUItemplate_bank_source_Delete");
					$objWindow->showQuestion();
					communication_interface::jsExecute("$('#btn_bank_source_Delete_NO').bind('click', function(e) {     cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});         });");
					communication_interface::jsExecute("$('#btn_bank_source_Delete_YES').bind('click', function(e) {    cb('payroll.paymentSplit', {'action':'GUI_bank_source_del', 'company_ID': '".$company_ID."' , 'bankId':'".$bankId."', 'todo':'delete'});  " .
																												/*	"	alert('Bank deleted');    " .			*/
																													"	$('#modalContainer').mb_close();    " .
																												/*  "	cb('payroll.ConfigEditFormOpen',{'section':'CfgCmpc','id':'".$company_ID."'});  " . */
																													"   });");
				}
				break;
			case 'paymentSplitAction_editSplit': //case 'payroll.paymentSplit' action:'paymentSplitAction_editSplit'
				communication_interface::alert("paymentSplitAction_editSplit --> obsolet");
				break;

			default: //case 'payroll.paymentSplit' action:'?'
				communication_interface::alert("case payroll.paymentSplit\n Unterfunktion ?\nAction=[".$functionParameters[0]["action"]."]");
				break;
			}	
		default:
			return "Funktion unbekannt";
			break;
		}
	}
	


	public function eventListener($eventName, $eventParameters) {
		global $aafwConfig;

		switch($eventName) {
		case 'core.bootLoadMenu':
			uiFunctionCall('baseLayout.appMenuAddSection','payroll','Lohnbuchhaltung'); 
			uiFunctionCall('baseLayout.appMenuAddItem','payroll','menupaymngr','Lohn bearbeiten',       'plugins/payroll_V00_01_00/code_ui/media/icons/calculator20.png', 'prlCalcOvOpen();return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','payroll','menuAuszahlen','Auszahldaten',        'plugins/payroll_V00_01_00/code_ui/media/icons/auszahlen20.png',  'cb(\'payroll.auszahlen.openHistoryWindow\');return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','payroll','menupayrempl','Personalstamm',        'plugins/payroll_V00_01_00/code_ui/media/icons/employees20.png',  'prlPsoOpenEmployeeOverview();return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','payroll','menupayrcnf','Stammdatenverwaltung',  'plugins/payroll_V00_01_00/code_ui/media/icons/config20.png',     'prlCfgOpenMainWindow();return false;');
			break;
		case 'core.bootComplete':
			blFunctionCall('payroll.onBootComplete');
			break;
		}
	}

	private function getEmplFieldDef() {
		$fieldDefRecs = blFunctionCall('payroll.getEmployeeFieldDef');

		$fieldDef = "";
		$dateRegexPattern = $this->getDateRegexPattern();
		if($fieldDefRecs["success"]) {
			foreach($fieldDefRecs["data"] as $row) {
				if($row["childOf"]=="" && $row["fieldName"]!="id" && $row["fieldActive"]==1) {
					switch($row["fieldType"]) {
					case 5: //Date
						$row["regexPattern"]=$dateRegexPattern;
					case 1: //Text
					case 2: //Checkbox
					case 3: //Number
//vMin/vMax ohne Nachkommastellen bei TEXT und DATE
						if($row["minVal"]!=0 || $row["maxVal"]!=0 || $row["regexPattern"]!="") $validate = "true";
						else $validate = "false";
						$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': ".$row["fieldType"].", 'len': ".$row["maxLength"].", 'vMin': ".$row["minVal"].", 'vMax': ".$row["maxVal"].", 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': ".$validate.", 'mandatory': ".($row["mandatory"] ? "true" : "false").", 'rgx': ".($row["regexPattern"]=="" ? "''" : $row["regexPattern"]).", 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false}";
						break;
					case 4: //Select
//	prlVlCreateForm([ {'tabName':'Personalien', 'tabID':'tb1', 'tabRow':1, 'elements': ['FirstName','LastName','ZipCity']}, {'tabName':'Diverses', 'tabID':'tb2', 'tabRow':1, 'elements': ['SV-AS-Number','DateOfBirth','Age']}, {'tabName':'Ausbildung', 'tabID':'tb3', 'tabRow':2, 'elements': ['tbl_education']} ]);
//$listOptions[$row["ListGroup"]][] = array("itemID" => $row["ListGroup"], "token" => $row["ListItemToken"], "label" => $row["label"]);
						if($row["dataSource"]=="payroll_empl_list") {
							$useTokenAsID = $row["dataSourceToken"]==1 ? true : false;
							$arrItemCollector = array();
							if(!$row["mandatory"]) {
								switch($row["dataSourceGroup"]) {
								case 3:
									$arrItemCollector[] = "['', '']";
									break;
								default:
									$arrItemCollector[] = "['0', '']";
									break;
								}
							}

							foreach($fieldDefRecs["listOptions"][$row["dataSourceGroup"]] as $optionItem) {
								if($row["dataSourceGroup"]!=3 || ($row["fieldName"]!="ResidenceCanton" && $optionItem["token"]!="EX") || $row["fieldName"]=="ResidenceCanton")
									$arrItemCollector[] = "['".($useTokenAsID ? $optionItem["token"] : $optionItem["itemID"])."', '".str_replace("'","\\'",$optionItem["label"])."']";
							}
							$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
						}else if($row["dataSource"]=="payroll_company") {
							$arrItemCollector = array();
							foreach($fieldDefRecs["listCompanies"] as $optionItem) {
								$arrItemCollector[] = "['".$optionItem["itemID"]."', '".str_replace("'","\\'",$optionItem["label"])."']";
							}
							$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
						}else if($row["dataSource"]=="payroll_payslip_cfg") {
							$arrItemCollector = array();
							$arrItemCollector[] = "['0', '']";
							foreach($fieldDefRecs["listPayslip"] as $optionItem) {
								$arrItemCollector[] = "['".$optionItem["itemID"]."', '".str_replace("'","\\'",$optionItem["label"])."']";
							}
							$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
						}else if($row["dataSource"]=="core_intl_country_names") {
							$arrItemCollector = array();
							foreach($fieldDefRecs["listCountries"] as $optionItem) {
								$arrItemCollector[] = "['".$optionItem["itemID"]."', '".str_replace("'","\\'",$optionItem["label"])."']";
							}
							$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
						}else if($row["dataSource"]=="payroll_insurance_code") {
							$arrItemCollector = array();
							if(!$row["mandatory"]) $arrItemCollector[] = "['', '']";
							foreach($fieldDefRecs["listInsuranceCodes"][$row["dataSourceGroup"]] as $optionItem) {
								$arrItemCollector[] = "['".$optionItem["itemID"]."', '".str_replace("'","\\'",$optionItem["itemID"].($optionItem["label"]!="" ? " - ".$optionItem["label"] : ""))."', ".$optionItem["payroll_company_ID"]."]";
			//TODO: payroll_company_ID reicht hier alleine nicht aus... im JavaScript muss eine entsprechende Filterfunktion vorgesehen werden!
							}
							$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
						}else if($row["dataSource"]=="payroll_languages") {
							$arrItemCollector = array();
							foreach($fieldDefRecs["listLanguages"] as $optionItem) {
								$arrItemCollector[] = "['".$optionItem["itemID"]."', '".str_replace("'","\\'",$optionItem["label"])."']";
							}
							$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
						}
						break;
					case 100: //Zip/City
						$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 100, 'disabled': false, 'validate': false, 'mandatory': false, 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'fieldset': {";
						foreach($fieldDefRecs["data"] as $childRow) {
							if($childRow["childOf"]==$row["fieldName"]) {
								if($childRow["childOrder"]==1) $fieldDef .= "'ZIP': {'id':'".$childRow["fieldName"]."', 'len': ".$childRow["maxLength"].", 'vMin': 0, 'vMax': 0, 'validate': false, 'mandatory': ".($childRow["mandatory"] ? "true" : "false").", 'rgx': '', 'guiWidth': '".$childRow["guiWidth"]."'},";
								else $fieldDef .= "'City': {'id':'".$childRow["fieldName"]."', 'len': ".$childRow["maxLength"].", 'vMin': 0, 'vMax': 0, 'validate': false, 'mandatory': ".($childRow["mandatory"] ? "true" : "false").", 'rgx': '', 'guiWidth': '".$childRow["guiWidth"]."'}";
							}
						}
						$fieldDef .= "}}";
						break;
					case 110: //Table element
						$arrFieldset = array();
						$arrTablecols = array();
						foreach($fieldDefRecs["data"] as $childRow) {
							if($childRow["childOf"]==$row["fieldName"]) {
								$label = str_replace("'","\\'",$childRow["label"]);
								$width = $childRow["guiWidth"]."0";

								switch($childRow["fieldType"]) {
								case 5: //Date
									$childRow["regexPattern"]=$dateRegexPattern;
								case 1: //Text
								case 2: //Checkbox
								case 3: //Number
									if($childRow["minVal"]!=0 || $childRow["maxVal"]!=0 || $childRow["regexPattern"]!="") $validate = "true";
									else $validate = "false";
									$arrFieldset[] = "'".$childRow["fieldName"]."': {'label': '".$label."','type': ".$childRow["fieldType"].", 'len': ".$childRow["maxLength"].", 'vMin': ".$childRow["minVal"].", 'vMax': ".$childRow["maxVal"].", 'disabled': ".($childRow["read-only"] ? "true" : "false").", 'validate': ".$validate.", 'mandatory': ".($childRow["mandatory"] ? "true" : "false").", 'rgx': ".($childRow["regexPattern"]=="" ? "''" : $childRow["regexPattern"]).", 'callback': ".($childRow["callback"] ? "true" : "false").", 'width': '".$width."', 'inUse': false}";
									break;
								case 4: //Select
									if($childRow["dataSource"]=="payroll_empl_list") {
										$useTokenAsID = $childRow["dataSourceToken"]==1 ? true : false;
										$arrItemCollector = array();
										if(!$childRow["mandatory"]) $arrItemCollector[] = "['0', '']";
										foreach($fieldDefRecs["listOptions"][$childRow["dataSourceGroup"]] as $optionItem) {
											$arrItemCollector[] = "['".($useTokenAsID ? $optionItem["token"] : $optionItem["itemID"])."', '".str_replace("'","\\'",$optionItem["label"])."']";
										}
										$arrFieldset[] = "'".$childRow["fieldName"]."': {'label': '".$label."','type': 4, 'len': ".$childRow["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($childRow["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($childRow["callback"] ? "true" : "false").", 'width': '".$width."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
									}else if($childRow["dataSource"]=="payroll_insurance_code") {
										$arrItemCollector = array();
										if(!$childRow["mandatory"]) $arrItemCollector[] = "['', '']";
										foreach($fieldDefRecs["listInsuranceCodes"][$childRow["dataSourceGroup"]] as $optionItem) {
											$arrItemCollector[] = "['".$optionItem["itemID"]."', '".str_replace("'","\\'",$optionItem["itemID"].($optionItem["label"]!="" ? " - ".$optionItem["label"] : ""))."', ".$optionItem["payroll_company_ID"]."]";
			//TODO: payroll_company_ID reicht hier alleine nicht aus... im JavaScript muss eine entsprechende Filterfunktion vorgesehen werden!
										}
//										$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 4, 'len': ".$row["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($row["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($row["callback"] ? "true" : "false").", 'guiWidth': '".$row["guiWidth"]."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
										$arrFieldset[] = "'".$childRow["fieldName"]."': {'label': '".$label."','type': 4, 'len': ".$childRow["maxLength"].", 'vMin': 0, 'vMax': 0, 'disabled': ".($childRow["read-only"] ? "true" : "false").", 'validate': false, 'mandatory': false, 'rgx': '', 'callback': ".($childRow["callback"] ? "true" : "false").", 'width': '".$width."', 'inUse': false, 'options': [".implode(",", $arrItemCollector)."] }";
									}
									break;
								}

								$arrTablecols[] = "{'label':'".$label."', 'colID':'".$childRow["fieldName"]."', 'width':'".$width."'}";
							}
						}
						$fieldDef .= ($fieldDef=="" ? "" : ",")."'".$row["fieldName"]."': {'label': '".str_replace("'","\\'",$row["label"])."','type': 110, 'disabled': false, 'validate': true, 'mandatory': false, 'inUse': false, 'fieldset':{".implode(", ", $arrFieldset)."}, 'tablecols':[".implode(", ", $arrTablecols)."] }";
						break;
					}
				}
			}
		}
//error_log("\n".$fieldDef."\n", 3, "/var/log/copronet-application.log");
		return "prlVlFieldDef = {".$fieldDef."};";
	}

	private function prlCalcOvFillYearPeriodCmb($param=null) {
		$prdInfo = !is_null($param) && isset($param["year"]) ? blFunctionCall('payroll.getPeriodInformation',array("year"=>$param["year"])) : blFunctionCall('payroll.getPeriodInformation');
		//fill year SELECT
		communication_interface::jsExecute("$('#prlCalcOvYear').find('option').remove();");
		communication_interface::jsExecute("$.each([".implode(",",$prdInfo["data"]["years"])."], function() { $('#prlCalcOvYear').append( $('<option></option>').val(this.toString()).html(this.toString()) ) });");
		//set current year
		communication_interface::jsExecute("$('#prlCalcOvYear').val(".$prdInfo["data"]["info"]["year"].");");
		//disable all periods
		communication_interface::jsExecute("$('#prlCalcOvMajorPeriod').find('option').removeAttr('disabled');");
		//disable all unused periods
		$arrPrdALL = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);
		$arrPrdUsed = array();
		foreach($prdInfo["data"]["major_period"] as $prdNum=>$prdData) $arrPrdUsed[] = $prdNum;
		$arrPrdUnused = array_diff($arrPrdALL,$arrPrdUsed);
		communication_interface::jsExecute("$.each([".implode(",",$arrPrdUnused)."], function() { $('#prlCalcOvMajorPeriod option[value='+this.toString()+']').attr('disabled','disabled') });");
		//select current period
		$currentMajorPeriod = isset($prdInfo["data"]["currentPeriod"]) ? $prdInfo["data"]["currentPeriod"]["major_period"] : 1;
		communication_interface::jsExecute("$('#prlCalcOvMajorPeriod').val(".$currentMajorPeriod.");");
		return array("currentYear"=>$prdInfo["data"]["info"]["year"], "currentMajorPeriod"=>$currentMajorPeriod);
	}

	private function prlCalcOvPopulateTable($param=null) {
//		$this->prlCalcOvPopulateTable(array("year"=>$prdInfo["currentYear"], "majorPeriod"=>$prdInfo["currentMajorPeriod"])); $param["updateTable"]
//		$defaultTblColumns = array("EmployeeNumber", "Lastname", "Firstname", "Sex"); //Fehlen noch: status, bruttolohn, nettolohn, auszahlung
//		$queryOption["columns"] = $defaultTblColumns;
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;
		$queryOption["prepend_id"] = true;
		$queryOption["query_filter"] = "";
		$queryOption["data_source"] = "calculation_overview";
		if(isset($param["year"])) $queryOption["year"] = $param["year"];
		if(isset($param["majorPeriod"])) $queryOption["majorPeriod"] = $param["majorPeriod"];
		$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);

		$tblData = "prlCalcOvData = [";
		$firstPass = true;
		if($employeeList["success"]) {
			foreach($employeeList["data"] as $row) {
				$tblData .= $firstPass ? "{" : ", {";
				$tblRow = "";
				foreach($row as $fieldName=>$fieldValue) {
					if($fieldName=="Sex") $fieldValue = $fieldValue=="F" ? "w" : "m"; //TODO: Werte dynamisch ersetzen!
					$tblRow .= ($tblRow == "" ? "" : ", ")."'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
				}
				$tblData .= $tblRow."}";
				$firstPass = false;
			}
		}
		$tblData .= "];";
		communication_interface::jsExecute($tblData);
//error_log(date("c")."\n".$tblData."\nUpdate Table = ".($updateTable?"true":"false")."\n\n", 3, "/var/log/daniel.log");
		communication_interface::jsExecute('prlCalcOvSetData('.($updateTable?'true':'false').');');
//communication_interface::jsExecute("prlCalcOvGrid.invalidate();");
	}

	private function prlCfgCmpcPopulateTable($param=null) {
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;

		$accountList = blFunctionCall('payroll.getCompanyList');

		$arrRowCollector = array();
		if($accountList["success"]) {
			foreach($accountList["data"] as $row) {
				$arrFieldCollector = array();
				foreach($row as $fieldName=>$fieldValue) $arrFieldCollector[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
				$arrRowCollector[] = "{".implode(",", $arrFieldCollector)."}";
			}
		}

		communication_interface::jsExecute("prlCfg.CfgCmpc.grid.data = [".implode(",", $arrRowCollector)."];");
		if($updateTable) {
			communication_interface::jsExecute("prlCfg.CfgCmpc.grid.dataView.beginUpdate();");
			communication_interface::jsExecute("prlCfg.CfgCmpc.grid.dataView.setItems(prlCfg.CfgCmpc.grid.data);");
			communication_interface::jsExecute("prlCfg.CfgCmpc.grid.dataView.endUpdate();");
			communication_interface::jsExecute("prlCfg.CfgCmpc.grid.dataView.reSort();");
			communication_interface::jsExecute("prlCfg.CfgCmpc.grid.gridObj.invalidate();");
		}
	}

	private function prlCfgLoacPopulateTable($param=null) {
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;

		$accountList = blFunctionCall('payroll.getPayrollAccountList');

		$arrRowCollector = array();
		if($accountList["success"]) {
			foreach($accountList["data"] as $row) {
				$arrFieldCollector = array();
				$row["sign"] = $row["sign"]==0 ? "+" : "-";
				foreach($row as $fieldName=>$fieldValue) {
					$arrFieldCollector[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
				}
				$arrRowCollector[] = "{".implode(",", $arrFieldCollector)."}";
			}
		}

		communication_interface::jsExecute("prlCfg.CfgLoac.grid.data = [".implode(",", $arrRowCollector)."];");
		if($updateTable) {
			communication_interface::jsExecute("prlCfg.CfgLoac.grid.dataView.beginUpdate();");
			communication_interface::jsExecute("prlCfg.CfgLoac.grid.dataView.setItems(prlCfg.CfgLoac.grid.data);");
			communication_interface::jsExecute("prlCfg.CfgLoac.grid.dataView.endUpdate();");
			communication_interface::jsExecute("prlCfg.CfgLoac.grid.dataView.reSort();");
			communication_interface::jsExecute("prlCfg.CfgLoac.grid.gridObj.invalidate();");
		}
/*
		communication_interface::jsExecute("prlCfgData = [".implode(",", $arrRowCollector)."];");
		if($updateTable) {
			communication_interface::jsExecute("prlCfgDataView.beginUpdate();");
			communication_interface::jsExecute("prlCfgDataView.setItems(prlCfgData);");
			communication_interface::jsExecute("prlCfgDataView.endUpdate();");
			communication_interface::jsExecute("prlCfgDataView.reSort();");
		}
*/
	}

	private function prlCfgInscPopulateTable($param=null) {
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;

		$accountList = blFunctionCall('payroll.getInsuranceRateList',array("InsuranceType"=>$param["InsuranceType"]));

		$arrRowCollector = array();
		if($accountList["success"]) {
			foreach($accountList["data"] as $row) {
				$arrFieldCollector = array();
				foreach($row as $fieldName=>$fieldValue) $arrFieldCollector[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
				$arrRowCollector[] = "{".implode(",", $arrFieldCollector)."}";
			}
		}

		communication_interface::jsExecute("prlCfg.CfgInsc.grid.data = [".implode(",", $arrRowCollector)."];");
		if($updateTable) {
			communication_interface::jsExecute("prlCfg.CfgInsc.grid.dataView.beginUpdate();");
			communication_interface::jsExecute("prlCfg.CfgInsc.grid.dataView.setItems(prlCfg.CfgInsc.grid.data);");
			communication_interface::jsExecute("prlCfg.CfgInsc.grid.dataView.endUpdate();");
			communication_interface::jsExecute("prlCfg.CfgInsc.grid.dataView.reSort();");
			communication_interface::jsExecute("prlCfg.CfgInsc.grid.gridObj.invalidate();");
		}
	}

	private function prlCfgSyacPopulateTable($param=null) {
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;

		$accountList = blFunctionCall('payroll.getPayrollAccountMappingList');

		$arrRowCollector = array();
		if($accountList["success"]) {
			foreach($accountList["data"] as $row) {
				$arrFieldCollector = array();
				foreach($row as $fieldName=>$fieldValue) $arrFieldCollector[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
				$arrRowCollector[] = "{".implode(",", $arrFieldCollector)."}";
			}
		}

		communication_interface::jsExecute("prlCfg.CfgSyac.grid.data = [".implode(",", $arrRowCollector)."];");
		if($updateTable) {
			communication_interface::jsExecute("prlCfg.CfgSyac.grid.dataView.beginUpdate();");
			communication_interface::jsExecute("prlCfg.CfgSyac.grid.dataView.setItems(prlCfg.CfgSyac.grid.data);");
			communication_interface::jsExecute("prlCfg.CfgSyac.grid.dataView.endUpdate();");
			communication_interface::jsExecute("prlCfg.CfgSyac.grid.dataView.reSort();");
			communication_interface::jsExecute("prlCfg.CfgSyac.grid.gridObj.invalidate();");
		}
	}

	private function prlCfgDascPopulateTable($param=null) {
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;

		$accountList = blFunctionCall('payroll.getDedAtSrcCantonList');

		$arrRowCollector = array();
		if($accountList["success"]) {
			foreach($accountList["data"] as $row) {
				$arrFieldCollector = array();
				foreach($row as $fieldName=>$fieldValue) {
					switch($fieldName) {
					case 'DaysPerMonth':
						$fieldValue = $fieldValue==0 ? "30-Tage-Regel" : "effektive Tage";
						break;
					case 'AnnualSettlementMode':
						$fieldValue = $fieldValue==0 ? "Nein" : "Ja";
						break;
					}
					$arrFieldCollector[] = "'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";

				}
				$arrRowCollector[] = "{".implode(",", $arrFieldCollector)."}";
			}
		}

		communication_interface::jsExecute("prlCfg.CfgDasc.grid.data = [".implode(",", $arrRowCollector)."];");
//		communication_interface::jsExecute("alert(prlCfg.CfgDasc.grid.data.toSource());");
		if($updateTable) {
			communication_interface::jsExecute("prlCfg.CfgDasc.grid.dataView.beginUpdate();");
			communication_interface::jsExecute("prlCfg.CfgDasc.grid.dataView.setItems(prlCfg.CfgDasc.grid.data);");
			communication_interface::jsExecute("prlCfg.CfgDasc.grid.dataView.endUpdate();");
			communication_interface::jsExecute("prlCfg.CfgDasc.grid.dataView.reSort();");
			communication_interface::jsExecute("prlCfg.CfgDasc.grid.gridObj.invalidate();");
		}
	}

	private function emplOverviewPopulateTable($param=null) {
		$updateTable = isset($param["updateTable"]) && !$param["updateTable"] ? false : true;

		$psoDbFilter = session_control::getSessionSettings("payroll", "psoDbFilter");
		//Get employee list and prepare data in order to fill the client-side table
		$defaultTblColumns = array("EmployeeNumber", "Lastname", "Firstname", "Street", "`ZIP-Code`", "City", "Sex");
		$queryOption["columns"] = $defaultTblColumns;
		$queryOption["prepend_id"] = true;
		$queryOption["query_filter"] = $psoDbFilter;
		$employeeList = blFunctionCall('payroll.getEmployeeList', $queryOption);

		$tblData = "prlPsoData = [";
		$firstPass = true;
		if($employeeList["success"]) {
			foreach($employeeList["data"] as $row) {
				$tblData .= $firstPass ? "{" : ", {";
				$tblRow = "";
				foreach($row as $fieldName=>$fieldValue) {
					if($fieldName=="Sex") $fieldValue = $fieldValue=="F" ? "w" : "m"; //TODO: Werte dynamisch ersetzen!
					$tblRow .= ($tblRow == "" ? "" : ", ")."'".$fieldName."':'".str_replace("'","\\'",$fieldValue)."'";
				}
				$tblData .= $tblRow."}";
				$firstPass = false;
			}
		}
		$tblData .= "];";

		communication_interface::jsExecute('prlPsoColumns = [{id: "EmployeeNumber", name: "Pers.Nr.", field: "EmployeeNumber", sortable: true, resizable: true, width: 60},{id: "Lastname", name: "Nachname", field: "Lastname", sortable: true, resizable: true},{id: "Firstname", name: "Vorname", field: "Firstname", sortable: true, resizable: true},{id: "Street", name: "Strasse", field: "Street", sortable: true, resizable: true, width: 150},{id: "ZIP-Code", name: "PLZ", field: "ZIP-Code", sortable: true, resizable: true, width: 50},{id: "City", name: "Ort", field: "City", sortable: true, resizable: true},{id: "Sex", name: "m/w", field: "Sex", sortable: true, width: 40, cssClass: "txtCenter"}];');
		communication_interface::jsExecute($tblData);

		if($updateTable) {
			communication_interface::jsExecute("prlPsoDataView.beginUpdate();");
			communication_interface::jsExecute("prlPsoDataView.setItems(prlPsoData);");
			communication_interface::jsExecute("prlPsoDataView.endUpdate();");
			communication_interface::jsExecute("prlPsoDataView.reSort();");
			communication_interface::jsExecute("prlPsoGrid.invalidate();");
		}
	}

	private function convertMySQL2Date($mysqlDate) {
		if($mysqlDate=="0000-00-00") return "";
		$newDateArr = array("","","");
		$fragments = explode("-", $mysqlDate);
		if (count($fragments) < 2){//Kein Datum der Form yyyy-mm-dd
			return $mysqlDate;
		}
		$dateformat_medium = session_control::getSessionSettings("CORE", "dateformat_medium");
		switch(preg_replace("/[-\/.]+/", "", str_replace("%", "", strtoupper( $dateformat_medium )))) {
		case 'YMD':
			$newDateArr=$fragments;
			break;
		case 'MDY':
			$newDateArr[0] = $fragments[1];
			$newDateArr[1] = $fragments[2];
			$newDateArr[2] = $fragments[0];
			break;
		case 'DMY':
		default:
			if (!isset($fragments[2])) {
				return "";
			}
			$newDateArr[0] = $fragments[2];
			$newDateArr[1] = $fragments[1];
			$newDateArr[2] = $fragments[0];
			break;
		}
		if(strpos($dateformat_medium, ".") !== false) return implode(".",$newDateArr);
		else if(strpos($dateformat_medium, "/") !== false) return implode("/",$newDateArr);
		
		$ret = implode("-",$newDateArr);
		//if (strlen($ret) == 0) $ret=$mysqlDate;
		return $ret;
	}

	private function getDateRegexPattern() {
		$pattern = array("","");
		$dateformat_medium = session_control::getSessionSettings("CORE", "dateformat_medium");
		switch(preg_replace("/[-\/.]+/", "", str_replace("%", "", strtoupper( $dateformat_medium )))) {
		case 'YMD':
			$pattern[0] = "(19|20)?\d\d";
			$pattern[1] = "(0?2{1,1}[-/.]([0-2]?\d?)|(0?1|0?3|0?5|0?7|0?8|10|12)[-/.]([0-2]?\d?|30|31)|(0?4|0?6|0?9|11)[-/.]([0-2]?\d?|30))";
			break;
		case 'MDY':
			$pattern[0] = "(0?2{1,1}[-/.]([0-2]?\d?)|(0?1|0?3|0?5|0?7|0?8|10|12)[-/.]([0-2]?\d?|30|31)|(0?4|0?6|0?9|11)[-/.]([0-2]?\d?|30))";
			$pattern[1] = "(19|20)?\d\d";
			break;
		case 'DMY':
		default:
			$pattern[0] = "(([0-2]?\d?)[-/.]0?2{1,1}|([0-2]?\d?|30|31)[-/.](0?1|0?3|0?5|0?7|0?8|10|12)|([0-2]?\d?|30)[-/.](0?4|0?6|0?9|11))";
			$pattern[1] = "(19|20)?\d\d";
			break;
		}

		return "/^".implode("[-/.]",$pattern)."$/";
	}

	private function doDbBackup($suffix="") {
        global $aafwConfig;
		$fileName = date("Ymd-His");
		$ret = blFunctionCall("payroll.getPeriodInformation");
		if($ret["success"]) {
			$fileName .= "-".$ret["data"]["info"]["year"]."P".$ret["data"]["currentPeriod"]["major_period"].$suffix;
		}

		//Pruefen, ob Mandanten-Verzeichnis existiert -> falls nicht -> anlegen
		$customerDbName = session_control::getSessionInfo("db_name");
		if(!file_exists($aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName)) mkdir($aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName, 0777);
		//Pruefen, ob tmp-Verzeichnis im Mandanten-Verzeichnis existiert -> falls nicht -> anlegen
		if(!file_exists($aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName."/backup")) mkdir($aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName."/backup", 0777);
		//Pruefen, ob User-Verzeichnis im tmp-Verzeichnis existiert -> falls nicht -> anlegen
		$tmpBaseDir = $aafwConfig["paths"]["plugin"]["customerDir"].$customerDbName."/backup/";

		exec($aafwConfig["paths"]["plugin"]["mysqldump"]." --opt -u backup -p63i7E24ce ".$customerDbName." > ".$tmpBaseDir.$fileName.".sql");

		return $fileName;
	}
}

$SYS_PLUGIN["ui"]["payroll"] = new payroll_UI();

?>
