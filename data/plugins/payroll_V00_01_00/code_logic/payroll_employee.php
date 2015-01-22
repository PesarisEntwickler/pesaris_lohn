<?php
class employee {
	
	public function getEmployeeList($param) {
		if(!isset($param["columns"])) $param["columns"] = array("*");
		if(!isset($param["prepend_id"]) || $param["columns"] == "*") $param["prepend_id"] = false;
		if(!isset($param["query_filter"])) $param["query_filter"] = "";
		if(!isset($param["data_source"])) $param["data_source"] = "default";
		if(isset($param["sort"])) {
				$arr = explode(",",$param["sort"]);
				foreach($arr as &$p) $p="payroll_employee.".$p;
				$orderBy = " ORDER BY ".implode(",",$arr);
		}else{
			$orderBy = "";
		}

		if($param["query_filter"] != "") {
			if(!preg_match( '/^[0-9]{1,9}$/', $param["query_filter"])) {
				$response["success"] = false;
				$response["errCode"] = 50;
				$response["errText"] = "invalid filter id";
				return $response;
			}
		}

		foreach($param["columns"] as &$fld) $fld = "payroll_employee.".$fld;
		$columns = implode(",", $param["columns"]);
		if($param["prepend_id"]) $columns = "payroll_employee.id,".$columns;

		$system_database_manager = system_database_manager::getInstance();

		switch($param["data_source"]) {
		case 'current_period':
			$sql = 
"SELECT ".$columns." " .
"FROM payroll_employee " .
"INNER JOIN payroll_period_employee prdemp ON prdemp.payroll_employee_ID=payroll_employee.id " .
"AND prdemp.processing!=0 " .
"INNER JOIN payroll_period prd " .
"WHERE prd.locked=0 AND prd.finalized=0 " .
"AND prd.id=prdemp.payroll_period_ID".$orderBy;
			
			$result = $system_database_manager->executeQuery($sql, "payroll_getEmployeeList");
			break;
		case 'calculation_overview':
			//Kontonummern ermitteln (Brutto-, Netto-Lohn und Auszahlungsbetrag)
			$sql = 
"SELECT `AccountType`,`payroll_account_ID` 
FROM `payroll_account_mapping` 
WHERE `ProcessingMethod`=1 
AND `AccountType` IN (19,20,21)";
			$q = $system_database_manager->executeQuery($sql, "payroll_getEmployeeList");
			if(count($q)<1) {
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "missing account configuration";
				return $response;
			}
			$mapAccount = array("19"=>"", "20"=>"", "21"=>""); //"grossSalary"=>"19", "netSalary"=>"20", "payout"=>"21"
			foreach($q as $row) if(isset($mapAccount[$row["AccountType"]])) $mapAccount[$row["AccountType"]] = $row["payroll_account_ID"];

			if(isset($param["year"]) && isset($param["majorPeriod"])) {
				$result = $system_database_manager->executeQuery("SELECT `id`,`finalized` FROM `payroll_period` WHERE `payroll_year_ID`=".$param["year"]." AND `major_period`=".$param["majorPeriod"]." AND `minor_period`=0", "payroll_getPeriodInformation");
				$payrollPeriodID = $result[0]["id"];
				$periodOpen = $result[0]["finalized"]==1 ? false : true;
			}else{
				//get the id of the current period
				$result = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_period` WHERE `locked`=0 AND `finalized`=0", "payroll_getPeriodInformation");
				$payrollPeriodID = $result[0]["id"];
				$periodOpen = true;
			}

			$sourceTable = $periodOpen ? "payroll_calculation_current" : "payroll_calculation_entry";
			//Als Datenquelle fuer die Beträge je nach Verarbeitungsstatus der Periode entweder `payroll_calculation_entry` oder `payroll_calculation_current` verwenden!
			$result = $system_database_manager->executeQuery("SELECT emp.`id`, emp.`payroll_company_ID`, emp.`EmployeeNumber`, emp.`Firstname`, emp.`Lastname`, emp.`Sex`, IF(prdemp.`core_user_ID_calc`!=0,prdemp.`processing`,0) as processing, IF(prdemp.`core_user_ID_fin_acc`!=0,1,0) as 'fin_acc_status', IF(prdemp.`core_user_ID_mgmt_acc`!=0,1,0) as 'mgmt_acc_status', ROUND(MAX(IF(calc.`payroll_account_ID`='".$mapAccount["19"]."',calc.`amount`,'0.00')),2) as grossSalary, ROUND(MAX(IF(calc.`payroll_account_ID`='".$mapAccount["20"]."',calc.`amount`,'0.00')),2) as netSalary, ROUND(MAX(IF(calc.`payroll_account_ID`='".$mapAccount["21"]."',calc.`amount`,'0.00')),2) as payout FROM `payroll_period_employee` prdemp INNER JOIN `payroll_employee` emp ON emp.`id`=prdemp.`payroll_employee_ID` LEFT JOIN `".$sourceTable."` calc ON calc.`payroll_employee_ID`=prdemp.`payroll_employee_ID` AND calc.`payroll_period_ID`=".$payrollPeriodID." AND calc.`payroll_account_ID` IN ('".$mapAccount["19"]."','".$mapAccount["20"]."','".$mapAccount["21"]."') WHERE prdemp.`payroll_period_ID`=".$payrollPeriodID." AND prdemp.`processing`!=0 GROUP BY emp.`id`", "payroll_getEmployeeList");
			break;
		default:
			if($param["query_filter"]!="") $result = $system_database_manager->executeQuery("SELECT ".$columns." FROM payroll_employee INNER JOIN payroll_empl_filter_cache ON payroll_employee.id=payroll_employee_ID WHERE payroll_empl_filter_ID=".$param["query_filter"].$orderBy, "payroll_getEmployeeList");
			else $result = $system_database_manager->executeQuery("SELECT ".$columns." FROM payroll_employee".$orderBy, "payroll_getEmployeeList");
			break;
		}

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}


	public function getEmployeeFieldDef() {
		$system_database_manager = system_database_manager::getInstance();
		$sql =
"SELECT payroll_employee_field_def.*,payroll_employee_field_label.label
FROM payroll_employee_field_def LEFT JOIN payroll_employee_field_label ON payroll_employee_field_def.fieldName=payroll_employee_field_label.fieldName
AND payroll_employee_field_label.language='".session_control::getSessionInfo("language")."'
";
		
		$sql = 
"SELECT payroll_employee_field_def.*,payroll_employee_field_label.label 
FROM payroll_employee_field_def LEFT JOIN payroll_employee_field_label ON payroll_employee_field_def.fieldName=payroll_employee_field_label.fieldName 
AND payroll_employee_field_label.language='".session_control::getSessionInfo("language")."' 
ORDER BY payroll_employee_field_def.childOf, payroll_employee_field_def.childOrder, payroll_employee_field_label.label";
		$result = $system_database_manager->executeQuery($sql, "payroll_getEmployeeFieldDef");
//communication_interface::alert("getEmployeeFieldDef()   \n".print_r($result,true));
// $s = "getEmployeeFieldDef()   \n";
// $idx = count($result);
// for ($i = 0; $i < $idx; $i++) {
// 	$s .= print_r($result[$i]["fieldName"],true) . "\n";
// }
// communication_interface::alert($s);

		$sql = 
"SELECT payroll_empl_list.id, payroll_empl_list.ListGroup, payroll_empl_list.ListItemToken, 
IF(payroll_empl_list.ListType=1,payroll_empl_list_label.label,
CONCAT(payroll_empl_list.ListItemToken,' - ',payroll_empl_list_label.label)) as label 
FROM payroll_empl_list INNER JOIN payroll_empl_list_label ON payroll_empl_list_label.payroll_empl_list_ID=payroll_empl_list.id 
WHERE payroll_empl_list_label.language='".session_control::getSessionInfo("language")."' 
AND payroll_empl_list.deleted=0 
ORDER BY payroll_empl_list.ListGroup, payroll_empl_list.ListItemOrder, payroll_empl_list_label.label";
		$emplList = $system_database_manager->executeQuery($sql, "payroll_getEmployeeFieldDef");
		foreach($emplList as $row) {
			if(!isset($listOptions[$row["ListGroup"]])) $listOptions[$row["ListGroup"]] = array();
			$listOptions[$row["ListGroup"]][] = array("itemID" => $row["id"], "token" => $row["ListItemToken"], "label" => $row["label"]);
		}
		if(!isset($listOptions)) $listOptions = array();

		$companyList = array();
		$cmpList = $system_database_manager->executeQuery("SELECT id, company_shortname FROM payroll_company ORDER BY company_shortname", "payroll_getEmployeeFieldDef");
		foreach($cmpList as $row) {
			$companyList[] = array("itemID" => $row["id"], "label" => $row["company_shortname"]);
		}

		$countryList = array();
		$cntryList = $system_database_manager->executeQuery("SELECT core_intl_country_ID, country_name FROM core_intl_country_names WHERE country_name_language='".session_control::getSessionInfo("language")."' ORDER BY country_name", "payroll_getEmployeeFieldDef");
		foreach($cntryList as $row) {
			$countryList[] = array("itemID" => $row["core_intl_country_ID"], "label" => $row["country_name"]);
		}

		$payslipList = array();
		$pslpList = $system_database_manager->executeQuery("SELECT `id`, `payslip_name` FROM `payroll_payslip_cfg` ORDER BY `payslip_name`", "payroll_getEmployeeFieldDef");
		foreach($pslpList as $row) {
			$payslipList[] = array("itemID" => $row["id"], "label" => $row["payslip_name"]);
		}

		$insuraceCodeList = array();
		$sql = 
"SELECT payroll_insurance_code.InsuranceCode, payroll_insurance_code.payroll_insurance_type_ID, payroll_insurance_code.payroll_company_ID, payroll_insurance_cd_label.label 
FROM payroll_insurance_code 
LEFT JOIN payroll_insurance_cd_label 
ON payroll_insurance_code.id=payroll_insurance_cd_label.payroll_insurance_code_ID 
AND payroll_insurance_cd_label.language='".session_control::getSessionInfo("language")."' 
ORDER BY payroll_insurance_code.InsuranceCode";
		$inscdList = $system_database_manager->executeQuery($sql, "payroll_getEmployeeFieldDef");
		foreach($inscdList as $row) {
			if(!isset($insuraceCodeList[$row["payroll_insurance_type_ID"]])) $insuraceCodeList[$row["payroll_insurance_type_ID"]] = array();
			$insuraceCodeList[$row["payroll_insurance_type_ID"]][] = array("itemID" => $row["InsuranceCode"], "payroll_company_ID" => $row["payroll_company_ID"], "label" => $row["label"]);
		}

		$languageList = array();
//		$lngList = $system_database_manager->executeQuery("SELECT payroll_languages.*,core_intl_language_names.language_name FROM payroll_languages INNER JOIN core_intl_language_names ON payroll_languages.core_intl_language_ID=core_intl_language_names.core_intl_language_ID WHERE core_intl_language_names.language_name_language='".session_control::getSessionInfo("language")."'".$auxWHERE." ORDER BY payroll_languages.DefaultLanguage DESC, core_intl_language_names.language_name", "payroll_getLanguageList");
		$sql = 
"SELECT payroll_languages.*,core_intl_language_names.language_name 
FROM payroll_languages INNER JOIN core_intl_language_names 
ON payroll_languages.core_intl_language_ID=core_intl_language_names.core_intl_language_ID 
WHERE core_intl_language_names.language_name_language='".session_control::getSessionInfo("language")."' 
ORDER BY payroll_languages.DefaultLanguage DESC, core_intl_language_names.language_name";
		$lngList = $system_database_manager->executeQuery($sql, "payroll_getLanguageList");
		foreach($lngList as $row) {
			$languageList[] = array("itemID" => $row["core_intl_language_ID"], "label" => $row["language_name"]);
		}

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
			$response["listOptions"] = $listOptions;
			$response["listCompanies"] = $companyList;
			$response["listCountries"] = $countryList;
			$response["listLanguages"] = $languageList;
			$response["listInsuranceCodes"] = $insuraceCodeList;
			$response["listPayslip"] = $payslipList;
		}
		return $response;
	}


	public function getEmployeeFieldDetail($param) {
		if(!preg_match( '/^[-_a-zA-Z0-9]{3,25}$/', $param["fieldName"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid fieldName";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$sql = "SELECT * FROM `payroll_employee_field_def` WHERE `fieldName`='".addslashes($param["fieldName"])."'";
		$fieldDef = $system_database_manager->executeQuery($sql, "payroll_getEmployeeFieldDetail");
		if (count($fieldDef)==0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "field not found";
			return $response;
		} else $fieldDef = $fieldDef[0];
		$sql = "SELECT * FROM `payroll_employee_field_label` WHERE `fieldName`='".addslashes($param["fieldName"])."'";
		$fieldLabels = $system_database_manager->executeQuery($sql, "payroll_getEmployeeFieldDetail");

		$listDef = array();
		if($fieldDef["fieldType"]==4 && $fieldDef["dataSource"]=="payroll_empl_list") {
			$list = $system_database_manager->executeQuery("SELECT * FROM payroll_empl_list WHERE ListGroup=".$fieldDef["dataSourceGroup"]." LIMIT 1", "payroll_getEmployeeFieldDetail");
			if (count($list)>0) {
				$listDef["ListGroup"] = $list[0]["ListGroup"];
				$listDef["ListType"] = $list[0]["ListType"];
				$listDef["Items"] = array();
			}

			$listItems = $system_database_manager->executeQuery("SELECT * FROM payroll_empl_list_label lbl INNER JOIN payroll_empl_list lst ON lst.id=lbl.payroll_empl_list_ID AND lst.ListGroup=".$fieldDef["dataSourceGroup"]." AND deleted=0  ORDER BY lst.ListItemOrder", "payroll_getEmployeeFieldDetail");
			foreach($listItems as $listItem) {
				$listDef["Items"][(string)$listItem["id"]]["ListItemToken"] = $listItem["ListItemToken"];
				$listDef["Items"][(string)$listItem["id"]]["ListItemOrder"] = $listItem["ListItemOrder"];
				$listDef["Items"][(string)$listItem["id"]]["labels"][$listItem["language"]] = $listItem["label"];
			}
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["data"] = $fieldDef;
		$response["fieldLabels"] = $fieldLabels;
		$response["listDef"] = $listDef;
		return $response;
	}

	public function saveEmployeeFieldDetail($param) {
		if(!preg_match( '/^[-_a-zA-Z0-9]{3,25}$/', $param["fieldName"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid fieldName";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$fieldDef = $system_database_manager->executeQuery("SELECT * FROM `payroll_employee_field_def` WHERE `fieldName`='".addslashes($param["fieldName"])."'", "payroll_saveEmployeeFieldDetail");
		if(count($fieldDef)==0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "field not found";
			return $response;
		}else $fieldDef = $fieldDef[0];

		if($fieldDef["fieldDefEdit"]==0) {
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "system field must not be changed";
			return $response;
		}


		$param["data"]["fields"]["fieldActive"] = $param["data"]["fields"]["active"];
		$param["data"]["fields"]["dataSourceSort"] = $param["data"]["fields"]["orderBy"];
		$fieldCfgMain = array(
					"fieldActive"=>array("regex"=>"/^[01]{1,1}$/","addQuotes"=>false),
					"mandatory"=>array("regex"=>"/^[01]{1,1}$/","addQuotes"=>false),
					"guiWidth"=>array("regex"=>"/^S|M|L|XL$/","addQuotes"=>true),
					"minVal"=>array("regex"=>$fieldDef["regexPattern"],"addQuotes"=>false),
					"maxVal"=>array("regex"=>$fieldDef["regexPattern"],"addQuotes"=>false),
					"maxLength"=>array("regex"=>"/^[0-9]{1,2}$/","addQuotes"=>false),
					"dataSourceSort"=>array("regex"=>"/^[012]{1,1}$/","addQuotes"=>false)
				);

		$excludeFields = array();
		$withDecimals = strstr($fieldDef["regexPattern"], "\\.")===false ? false : true;
		$changeActiveFlag = ($fieldDef["fieldDefEdit"] & 1)!=0 ? true : false;
		$changeLabels = ($fieldDef["fieldDefEdit"] & 2)!=0 ? true : false;
//communication_interface::alert(print_r($param["data"]["fields"],true));

		$errFields = array();
		$mainLabelCollector = array();
		if($changeLabels) {
			$resLanguage = $system_database_manager->executeQuery("SELECT * FROM payroll_languages WHERE UseForAccounts=1", "payroll_getLanguageList");
			foreach($resLanguage as $row) {
				$mainLabelCollector[$row["core_intl_language_ID"]] = isset($param["data"]["fields"]["label_".$row["core_intl_language_ID"]]) ? trim($param["data"]["fields"]["label_".$row["core_intl_language_ID"]]) : "";
				$ln = strlen($mainLabelCollector[$row["core_intl_language_ID"]]);
				if($ln==0 || $ln>45) $errFields[] = "label_".$row["core_intl_language_ID"];
			}
		}

		switch($fieldDef["fieldType"]) {
		case 1: //TextsaveEmployeeFieldDetail
			$excludeFields = array("minVal", "maxVal", "dataSourceSort");
			//'mandatory':true,'guiWidth':true,'maxLength':true
			//'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,
			break;
		case 2: //Checkbox
			$excludeFields = array("mandatory", "guiWidth", "minVal", "maxVal", "maxLength", "dataSourceSort");
			//'mandatory':false,'guiWidth':false,'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,'maxLength':false
			break;
		case 3: //Zahl
			$excludeFields = array("maxLength", "dataSourceSort");
			//'mandatory':true,'guiWidth':true,'minVal':true,'maxVal':true,
			//'displayCode':false,'orderBy':false,'maxLength':false,
			break;
		case 4: //Liste
			$excludeFields = array("minVal", "maxVal", "maxLength");
			//'mandatory':true,'guiWidth':true,'displayCode':true,'orderBy':true,
			//'minVal':false,'maxVal':false,'maxLength':false,
			$listItemAddRemove = ($fieldDef["fieldDefEdit"] & 8)!=0 ? true : false;
			$listItemEdit = ($fieldDef["fieldDefEdit"] & 16)!=0 ? true : false;
			$listItemChangeLabels = ($fieldDef["fieldDefEdit"] & 32)!=0 ? true : false;
			break;
		case 5: //Datum
			$excludeFields = array("minVal", "maxVal", "maxLength", "dataSourceSort");
			//'mandatory':true,'guiWidth':true,
			//'displayCode':false,'orderBy':false,'minVal':false,'maxVal':false,'maxLength':false
			break;
		}
		if($fieldDef["fieldType"]!=4 && ($fieldDef["fieldDefEdit"] & 60)==0) {
			//keines der Stammfelder darf geändert werden
			$excludeFields = array("mandatory", "guiWidth", "minVal", "maxVal", "maxLength", "dataSourceSort");
		}

		if(!$changeActiveFlag) $excludeFields[] = "fieldActive";

		foreach($excludeFields as $fn) unset($fieldCfgMain[$fn]);

//communication_interface::alert(print_r($fieldCfgMain,true));

		///////////////////////////////////////////////////
		// Mandatory and validity checks (MAIN table fields)
		///////////////////////////////////////////////////
		foreach($fieldCfgMain as $fieldName=>$fieldParam) if(!preg_match($fieldParam["regex"], $param["data"]["fields"][$fieldName])) $errFields[] = $fieldName;
		$errFields = array_unique($errFields);
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}

		$updateCollector = array();
		foreach($fieldCfgMain as $fieldName=>$fieldParam) {
			if($fieldParam["addQuotes"]) {
				$updateCollector[] = "`".$fieldName."`='".addslashes($param["data"]["fields"][$fieldName])."'";
			}else{
				$updateCollector[] = "`".$fieldName."`=".$param["data"]["fields"][$fieldName];
			}
		}

//TODO: Achtung: unbedingt noch Mandatory und validity checks auf Listenwerten ausfaehren!!!!

		$system_database_manager->executeUpdate("BEGIN", "payroll_saveEmployeeFieldDetail");

		///////////////////////////////////////////////////
		// Listenwerte aufbereiten und speichern ([data][listItems])
		///////////////////////////////////////////////////
		$listItemSQL = array();
		if($fieldDef["fieldType"]==4) {
			//bestehende Listenwerte (nur IDs) auslesen um bestimmen zu koennen, welche Listenwerte neu sind, welche geloescht und welche bloss aktualisiert werden muessen
			$dataSourceGroup = $fieldDef["dataSourceGroup"];
			$existingListIDs = array();
			$resLstIDs = $system_database_manager->executeQuery("SELECT `id` FROM `payroll_empl_list` WHERE `ListGroup`=".$dataSourceGroup, "payroll_getLanguageList");
			foreach($resLstIDs as $row) $existingListIDs[] = $row["id"];

			$ListType = $param["data"]["fields"]["displayCode"]==1 ? 2 : 1;
			$orderBy = $param["data"]["fields"]["orderBy"]; // 0:Listentext [ListItemOrder generell auf 0 setzen] / 1:Code [ListItemOrder anhand des Codes ermitteln] / 2:Sortierzahl [nichts unternehmen]

			//sortierung
			if($orderBy==1) {
				$tmpList = array();
				foreach($param["data"]["listItems"] as $itemID=>$itemParam) {
					$tmpList[(string)$itemParam["ListItemToken"]] = (string)$itemID;
				}
				ksort($tmpList);
				$counter = 0;
				$tokenOrder = array();
				foreach($tmpList as $dummy=>$itemID) {
					$tokenOrder[(string)$itemID] = $counter; $counter++; 
				}
				unset($tmpList);
			}

			//loop submitted list items
			$processedItemIDs = array();
			foreach($param["data"]["listItems"] as $itemID=>$itemParam) {
				//zuerst feststellen, ob Item neu oder bestehend ist
				$updateMode = in_array($itemID, $existingListIDs);

				switch($orderBy) {
				case 1:
					$ListItemOrder = $tokenOrder[(string)$itemID];
					break;
				case 2:
					$ListItemOrder = $itemParam["ListItemOrder"];
					break;
				default:
					$ListItemOrder = 0;
					break;
				}
				$ListItemToken = $itemParam["ListItemToken"];

				if($updateMode) {
					//Wird nun Element f�r Element direkt beim Editieren gespeichert --> saveListenWerte($personalstammListenwerte, $data, $fieldName) 
					//$system_database_manager->executeUpdate("UPDATE `payroll_empl_list` SET `ListType`=".$ListType.", `ListItemOrder`=".$ListItemOrder.", `ListItemToken`='".addslashes($ListItemToken)."' WHERE `id`=".$itemID, "payroll_saveEmployeeFieldDetail");
					$itemID4Label = $itemID;
					$processedItemIDs[] = $itemID;
				}else{
					$system_database_manager->executeUpdate("INSERT INTO `payroll_empl_list`(`ListGroup`, `ListType`, `ListItemOrder`, `ListItemToken`, `deleted`, `core_user_ID_delete`, `datetime_delete`) VALUES(".$dataSourceGroup.", ".$ListType.", ".$ListItemOrder.", '".addslashes($ListItemToken)."', 0, 0, '0000-00-00')", "payroll_saveEmployeeFieldDetail");
					$itemID4Label = $system_database_manager->getLastInsertId();
				}

				foreach($itemParam["labels"] as $lblLng=>$lblTxt) {
					$system_database_manager->executeUpdate("REPLACE INTO `payroll_empl_list_label`(`payroll_empl_list_ID`, `language`, `label`, `tokenLabel`) VALUES(".$itemID4Label.", '".$lblLng."', '".addslashes($lblTxt)."', '')", "payroll_saveEmployeeFieldDetail");
				}
			}
			$deleteIDs = array_diff($existingListIDs, $processedItemIDs);
			foreach($deleteIDs as $itemID) {
				$system_database_manager->executeUpdate("DELETE FROM `payroll_empl_list_label` WHERE `payroll_empl_list_ID`=".$itemID, "payroll_saveEmployeeFieldDetail");
				$system_database_manager->executeUpdate("DELETE FROM `payroll_empl_list` WHERE `id`=".$itemID, "payroll_saveEmployeeFieldDetail");
			}
		}

		if(count($updateCollector)!=0) $system_database_manager->executeUpdate("UPDATE `payroll_employee_field_def` SET ".implode(",", $updateCollector)." WHERE `fieldName`='".$param["fieldName"]."'", "payroll_saveEmployeeFieldDetail");

//communication_interface::alert(print_r($mainLabelCollector,true));
		foreach($mainLabelCollector as $lngID=>$lbl) {
			$system_database_manager->executeUpdate("REPLACE INTO `payroll_employee_field_label`(`fieldName`,`language`,`label`) VALUES('".$param["fieldName"]."','".$lngID."','".addslashes($lbl)."')", "payroll_saveEmployeeFieldDetail");
		}
		$system_database_manager->executeUpdate("COMMIT", "payroll_saveEmployeeFieldDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function saveEmployeeForm($param) {
		///////////////////////////////////////////////////
		// layoutID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(preg_match( '/^[0-9]{1,9}$/', $param["layoutID"])) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid layout id";
			return $response;
		}
		///////////////////////////////////////////////////
		// layoutName must have a length of at least 1 character and not more than 30 char
		///////////////////////////////////////////////////
		if(strlen($param["layoutName"])<1 || strlen($param["layoutName"])>30) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid length of layout name";
			return $response;
		}

		$layoutID = $param["layoutID"];
		$layoutName = addslashes($param["layoutName"]);
		$layoutTmp = $param["layoutTmp"]==1 ? 1 : 0;
		$layoutGlob = $param["layoutGlob"]==1 ? 1 : 0;
		$layoutElements = addslashes( json_encode($param["layoutElements"]) );

		$system_database_manager = system_database_manager::getInstance();
		if($layoutID==0) {
			$system_database_manager->executeUpdate("INSERT INTO payroll_empl_form(FormName,FormElements,temporary,global,datetime_created,core_user_id_created) VALUES('".$layoutName."','".$layoutElements."',".$layoutTmp.",".$layoutGlob.",NOW(),".session_control::getSessionInfo("id").")", "payroll_saveEmployeeForm");
		}else{
			$system_database_manager->executeUpdate("UPDATE payroll_empl_form SET FormName='".$layoutName."',FormElements='".$layoutElements."',temporary=".$layoutTmp.",global=".$layoutGlob." WHERE id=$layoutID", "payroll_saveEmployeeForm");
		}

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}


	public function getEmployeeFormList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT id,FormName FROM payroll_empl_form WHERE core_user_id_created=".session_control::getSessionInfo("id")." OR global=1 ORDER BY FormName", "payroll_getEmployeeFormList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}
	
	public function getEmployeeFormDetail($id) {
		///////////////////////////////////////////////////
		// layoutID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(preg_match( '/^[0-9]{1,9}$/', $id)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid layout id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_empl_form WHERE id=".$id, "payroll_getEmployeeFormDetail");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}

	public function deleteEmployeeForm($id) {
		///////////////////////////////////////////////////
		// layoutID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(preg_match( '/^[0-9]{1,9}$/', $id)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid layout id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("DELETE FROM payroll_empl_form WHERE (core_user_id_created=".session_control::getSessionInfo("id")." OR global=1) AND id=".$id, "payroll_deleteEmployeeForm");

		$response["success"] = true;
		$response["errCode"] = 0;

		return $response;
	}
	
	public function getEmployee($id) {
		if(preg_match( '/^[0-9]{1,9}$/', $id)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 19;
			$response["errText"] = "invalid employee id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_employee WHERE id=".$id, "payroll_getEmployee");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}

	public function getEmployeeDetail($id, $queryAuxiliaryTables=false) {
		///////////////////////////////////////////////////
		// layoutID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(preg_match( '/^[0-9]{1,9}$/', $id)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid employee id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM payroll_employee WHERE id=".$id, "payroll_getEmployeeDetail");

		$auxTables = array();
		if($queryAuxiliaryTables && count($result)>=0) {
			$sql0 = "SELECT fieldName, dataSource FROM payroll_employee_field_def WHERE fieldType=110";
			$fldDef = $system_database_manager->executeQuery($sql0, "payroll_getEmployeeDetail");
			foreach($fldDef as $fldDefRow) {
				$sql = "SELECT * FROM ".$fldDefRow["dataSource"].
				      " WHERE payroll_employee_ID=".$result[0]["id"].
					  " ORDER BY DateFrom";
				$tbl = $system_database_manager->executeQuery($sql, "payroll_getEmployeeDetail");
				$auxTables[$fldDefRow["fieldName"]] = $tbl;
				//if (count($tbl) > 0) communication_interface::alert($sql0."\n\n".print_r($fldDef,true)."\n\nfldDefRow[fieldName]=".$fldDefRow["fieldName"]."\n\n".$sql."\n\ngetEmployeeDetail 556: \n".print_r($tbl,true));
			}
		}

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
			$response["auxiliaryTables"] = $auxTables;
		}
		//communication_interface::alert("getEmployeeDetail:".print_r($response,true));
		return $response;
	}

	public function callbackEmployeeDetail($param) {
		///////////////////////////////////////////////////
		// employee id must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!isset($param["rid"]) || !preg_match( '/^[0-9]{1,9}$/', $param["rid"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid employee id";
			return $response;
		}
		$id = $param["rid"];
//communication_interface::alert("callbackEmployeeDetail param[fieldName] = \n".print_r($param,true));
		require_once('chkDate.php');
		$chkDate = new chkDate("1970-01-01", 0, "");
		
		$returnFieldValuePairs = array();
		switch($param["fieldName"]) {
		case 'Sex':
			//there is only a need for returning a value, if the 'DateOfBirth' value was submitted as well (or can be queried via DB)
			$gender = $param["value"]=="F" ? "F" : "M";
			if(isset($param["DateOfBirth"]) && !($param["DateOfBirth"]=="0" && $id==0)) { //if the DateOfBirth was submitted, we need to check it or query the database
				if(strlen($param["DateOfBirth"])>5) {
					
					if(!$chkDate->chkDate($param["DateOfBirth"], 3, $retDate)) {
						$response["success"] = false;
						$response["errCode"] = 40;
						$response["errText"] = "invalid date value";
						$response["fieldNames"] = array("DateOfBirth");
						return $response;
					}
				}else{
					$system_database_manager = system_database_manager::getInstance();
					$res = $system_database_manager->executeQuery("SELECT `name`,`value` FROM `core_registry` WHERE `path`='GLOBAL/SETTINGS/CORE/payroll' AND `name` LIKE 'ahv_m%_age_%'", "payroll_closePeriod");
					foreach($res as $row) $ahvAgeRange[$row["name"]] = $row["value"];
					$result = $system_database_manager->executeQuery("SELECT DateOfBirth FROM payroll_employee WHERE id=".$id, "payroll_getEmployeeFormList");
					if(count($result) < 1) {
						$response["success"] = false;
						$response["errCode"] = 101;
						$response["errText"] = "no data found";
						return $response;
					}
					$dob = explode("-", $result[0]["DateOfBirth"]);
					$retDate["year"] = $dob[0];
					$retDate["month"] = $dob[1];
					$retDate["day"] = $dob[2];
				}
				$returnFieldValuePairs[] = array("RetirementDate",($gender=="F" ? $retDate["year"] + $ahvAgeRange["ahv_max_age_f"] : $retDate["year"] + $ahvAgeRange["ahv_max_age_m"])."-".$retDate["month"]."-".$retDate["day"]);
			}
			break;
		case 'DateOfBirth':
			if($chkDate->chkDate($param["value"], 3, $retDate)) {
				$years = date("Y") - $retDate["year"] - 1;
				if($years<12) {
					$response["success"] = false;
					$response["errCode"] = 30;
					$response["errText"] = "date not far enough in the past";
					$response["fieldNames"] = array("DateOfBirth");
					return $response;
				}

				if(date("m")>$retDate["month"] || (date("m")==$retDate["month"] && date("d")>=$retDate["day"])) { $years++; }
				$returnFieldValuePairs[] = array("Age",$years);
			}else{
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "wrong format";
				$response["fieldNames"] = array("DateOfBirth");
				return $response;
			}
			if(isset($param["Sex"]) && !($param["Sex"]=="0" && $id==0)) { //if the gender was submitted, we calculate the retirement date
				$gender = $param["Sex"];
				if($id>0 && $param["Sex"]==0) {
					//the gender information is not available on the input form but in the database -> get it now
					$system_database_manager = system_database_manager::getInstance();
					$res = $system_database_manager->executeQuery("SELECT `name`,`value` FROM `core_registry` WHERE `path`='GLOBAL/SETTINGS/CORE/payroll' AND `name` LIKE 'ahv_m%_age_%'", "payroll_closePeriod");
					foreach($res as $row) $ahvAgeRange[$row["name"]] = $row["value"];
					$result = $system_database_manager->executeQuery("SELECT Sex FROM payroll_employee WHERE id=".$id, "payroll_getEmployeeFormList");
					if(count($result) < 1) {
						$response["success"] = false;
						$response["errCode"] = 101;
						$response["errText"] = "no data found";
						return $response;
					}
					$gender = $result[0]["Sex"];
				}
				$returnFieldValuePairs[] = array("RetirementDate",($gender=="F" ? $retDate["year"] + $ahvAgeRange["ahv_max_age_f"] : $retDate["year"] + $ahvAgeRange["ahv_max_age_m"])."-".$retDate["month"]."-".$retDate["day"]);
			}
			break;
		case 'SeniorityJoining':
			if($chkDate->chkDate($param["value"], 3, $retDate)) {
				$years = date("Y") - $retDate["year"];
				$months = date("m") - $retDate["month"];
				if($months < 0) { $months += 12; $years--; }
				if($years<0) { $years = 0; $months = 0; }
				$returnFieldValuePairs[] = array("MonthsOfService",$months);
				$returnFieldValuePairs[] = array("YearsOfService",$years);
			}else{
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "wrong format";
				$response["fieldNames"] = array("SeniorityJoining");
				return $response;
			}
			break;
		}
		$response["success"] = true;
		$response["errCode"] = 0;
		$response["newValues"] = $returnFieldValuePairs;
		return $response;
	}
	
	public function saveEmployeeDetail($id, $rawFieldData) {
		///////////////////////////////////////////////////
		// employee id must be numeric and non-decimal
		///////////////////////////////////////////////////
		$response["success"] = true;
		$response["errCode"] = 999;
		$response["fieldNames"] = array();
		$response["tableRows"] = array();
		if(preg_match( '/^[0-9]{1,9}$/', $id)) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid employee id";
			return $response;
		}

//communication_interface::alert("saveEmployeeDetail rawFieldData:\n".print_r($rawFieldData,true));
		
		require_once('chkDate.php');
		$chkDate = new chkDate("1970-01-01", 9);

		$system_database_manager = system_database_manager::getInstance();
		///////////////////////////////////////////////////
		// in case of a new record, all mandatory fields
		// must exist in the set of rawFieldData
		///////////////////////////////////////////////////
		if($id==0) { // && count($rawFieldData)>0
			$arrFieldName = array();
			$result = $system_database_manager->executeQuery(
				"SELECT fieldName FROM payroll_employee_field_def 
				WHERE fieldName!='id' AND mandatory=1 AND `read-only`=0 AND fieldName NOT LIKE 'tbl%'"
					, "payroll_saveEmployeeDetail");
			//communication_interface::alert("saveEmployeeDetail result:\n".print_r($result,true));
				
			foreach($result as $row) {
				if(!isset($rawFieldData[$row["fieldName"]]) || $rawFieldData[$row["fieldName"]]=="") $arrFieldName[] = $row["fieldName"];
			}
			if(count($arrFieldName)>0) {
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "missing mandatory fields in submitted fieldset";
				$response["fieldNames"] = $arrFieldName;
				return $response;
			}
		}

		///////////////////////////////////////////////////
		// get field properties for pending checks
		///////////////////////////////////////////////////
		$arrFormFieldName[] = array();
		$arrTableName[] = array();
		foreach($rawFieldData as $name => $value) {
			if(is_array($value)) {
				if(count($value)>0) $arrTableName[] = $name;
			}else{
				$arrFormFieldName[] = $name;
			}
		}
		$tableset = array();
		$result = $system_database_manager->executeQuery(
			"SELECT * FROM payroll_employee_field_def 
			WHERE `read-only`=0", "payroll_saveEmployeeDetail");
		foreach($result as $row) {
			if(in_array($row["fieldName"], $arrFormFieldName)) {
				$fieldset[$row["fieldName"]]["properties"] = $row;
			}else if($row["fieldType"]==110 && in_array($row["fieldName"], $arrTableName)) {
				$tableset[$row["fieldName"]]["properties"] = $row;
			}else if($row["childOf"]!="" && in_array($row["childOf"], $arrTableName)) {
				$tableset[$row["childOf"]]["fields"][$row["fieldName"]]["properties"] = $row;
			}
		}
//error_log("\n\n".print_r($fieldset,true)."\n\n", 3, "/var/log/copronet-application.log");
//error_log("\n\n".print_r($tableset,true)."\n\n", 3, "/var/log/copronet-application.log");
		///////////////////////////////////////////////////
		// checking all submitted fields if they are mandatory
		///////////////////////////////////////////////////
		$arrFieldName = array();
		foreach($fieldset as $fieldName=>$fieldDetail) {
			if(isset($fieldDetail["properties"]["mandatory"]) && $fieldDetail["properties"]["mandatory"]==1 && trim($rawFieldData[$fieldName])=="") {
				$arrFieldName[] = $fieldName;
			}
		}
		if(count($arrFieldName)>0) {
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "mandatory fields are empty";
			$response["fieldNames"] = $arrFieldName;
			return $response;
		}

		///////////////////////////////////////////////////
		// checking all submitted fields for validity
		// and preparing values for inserting them into the database
		///////////////////////////////////////////////////
		$arrFieldName = array();
		foreach($fieldset as $fieldName=>$fieldDetail) {
			$curRawValue = $rawFieldData[$fieldName];
				switch($fieldDetail["properties"]["fieldType"]) {
				case 1: //Text
					if($curRawValue!= "" && $fieldDetail["properties"]["regexPattern"]!="") {
						if(!preg_match($fieldDetail["properties"]["regexPattern"], $curRawValue)) $arrFieldName[] = $fieldName;
					}
					if(strlen($curRawValue) > $fieldDetail["properties"]["maxLength"]) $arrFieldName[] = $fieldName;
					$fieldset[$fieldName]["value"] = "'".addslashes($curRawValue)."'";
					break;
				case 2: //Checkbox
				case 3: //Number
					if($fieldDetail["properties"]["regexPattern"]!="") $regex = $fieldDetail["properties"]["regexPattern"];
					else $regex = "/^-?[0-9]{1,9}(\.[0-9]{1,6})?$/";

					if(!preg_match($regex, $curRawValue)) $arrFieldName[] = $fieldName;
					$fieldset[$fieldName]["value"] = addslashes($curRawValue);
					break;
				case 4: //Select
					if($fieldDetail["properties"]["regexPattern"]!="") $regex = $fieldDetail["properties"]["regexPattern"];
					else $regex = "/^[0-9]{1,9}$/";

					if(!preg_match($regex, $curRawValue)) {
						$arrFieldName[] = $fieldName;
					}else{
						if(preg_match("/^[0-9]{1,9}$/", $curRawValue)) {
							$fieldset[$fieldName]["value"] = addslashes($curRawValue);
						}else{
							$fieldset[$fieldName]["value"] = "'".addslashes($curRawValue)."'";
						}
					}
					break;
				case 5: //Date
					if($chkDate->chkDate($curRawValue, 1, $retDate)) {
						$fieldset[$fieldName]["value"] = "'".$retDate."'";
					}else $arrFieldName[] = $fieldName;
					break;
				}

/*
		spezielle felder und sonderf�lle:
		-> Tabellen
		-> Berechnete Werte: Alter, Pensionsdatum, Dienstalter, etc. (Werte gar nicht speichern... 
			stattdessen nach INSERT oder UPDATE ein weiteres SQL-Statement absetzen, womit die Werte berechnet werden)
		-> Mutationen archivieren? Hier oder erst bei Abrechnung/Fixierung?
*/
		}
		$arrFieldName = array_unique($arrFieldName); //remove duplicate field names (if any)
		if(count($arrFieldName)>0) {
			$response["success"] = false;
			$response["errCode"] = 41;
			$response["errText"] = "validity check failed";
			$response["fieldNames"] = $arrFieldName;
// 			return $response;
		}
//communication_interface::alert("arrFieldName : ".print_r($arrFieldName, true));
//communication_interface::alert("fieldset : ".print_r($fieldset, true));
//error_log("\n".print_r($fieldset,true)."\n", 3, "/var/log/copronet-application.log");
//error_log("\n".print_r($arrFieldName,true)."\n", 3, "/var/log/copronet-application.log");

		//if the field EmployeeNumber was submitted, then check if there is no duplicate number
		if(isset($rawFieldData["EmployeeNumber"])) {
			$result = $system_database_manager->executeQuery("SELECT id FROM payroll_employee WHERE id!=".$id." AND EmployeeNumber=".addslashes($rawFieldData["EmployeeNumber"]), "payroll_saveEmployeeDetail");
			if(count($result) > 0) {
				$response["success"] = false;
				$response["errCode"] = 50;
				$response["errText"] = "duplicate employee number";
				$response["fieldNames"] = array("EmployeeNumber");
				return $response;
			}
		}

		///////////////////////////////////////////////////
		// check table content [BEGIN]
		///////////////////////////////////////////////////
		$arrMandatoryErr = array();
		$arrValidityErr = array();
		$arrCleanTableRows = array();
		$track = "";
		foreach($tableset as $tableName=>$tableProperties) {	//looping all tables
			$arrCleanTableRows[$tableName] = array();
			foreach($rawFieldData[$tableName] as $tableRow) {	//looping all the submitted table rows
				$currentRow = array();
				$arrIdSplit = array();
				if(!isset($tableRow["id"])) {
					$response["success"] = false;
					$response["errCode"] = 500;
					$response["errText"] = "missing table record id";
					$response["tableRows"] = array($tableName, "");
					return $response;
				}else if(!preg_match('/^(remove_|new)?([0-9]{1,9})$/', $tableRow["id"], $arrIdSplit)) {
					$response["success"] = false;
					$response["errCode"] = 510;
					$response["errText"] = "invalid table record id";
					$response["tableRows"] = array($tableName, $tableRow["id"]);
				}
				switch($arrIdSplit[1]) {
				case 'new':
					$currentRecModification = "insert";
					$currentRecID = 0;
					break;
				case 'remove_':
					$currentRecModification = "delete";
					$currentRecID = $arrIdSplit[2];
					break;
				default:
					$currentRecModification = "update";
					$currentRecID = $arrIdSplit[2];
					break;
				}
				foreach($tableProperties["fields"] as $fieldName=>$fieldProperties) {	
				//looping all fields in the current table row
				
					$curRawValue = $tableRow[$fieldName];
					//remove prefix in order to get the proper field name matching the database field name
					$dbFieldName = str_replace($tableName."_", "", $fieldName); 
					
					// checking all submitted fields if they are mandatory
					if(isset($fieldProperties["properties"]["mandatory"]) && $fieldProperties["properties"]["mandatory"]==1 && trim($curRawValue)=="") {
						$arrMandatoryErr[] = array($tableName, $tableRow["id"]);
						$track .= "\n0";
					} 

					// checking all submitted fields for validity and preparing values for inserting them into the database
					switch($fieldProperties["properties"]["fieldType"]) {
					case 1: //Text
						if($curRawValue!= "" && $fieldProperties["properties"]["regexPattern"]!="") {
							if(!preg_match($fieldProperties["properties"]["regexPattern"], $curRawValue)) {
								$arrValidityErr[] = array($tableName, $tableRow["id"]);
								$track .= "\n1a($tableName)";
							} 
						}
						if(strlen($curRawValue) > $fieldProperties["properties"]["maxLength"]) {
							$arrValidityErr[] = array($tableName, $tableRow["id"]);
									$track .= "\n1(".$tableName.":".$tableRow["id"].")";
						} 
						$currentRow[$dbFieldName] = "'".addslashes($curRawValue)."'";
						break;
					case 2: //Checkbox
					case 3: //Number
						if($fieldProperties["properties"]["regexPattern"]!="") $regex = $fieldProperties["properties"]["regexPattern"];
						else $regex = "/^-?[0-9]{1,9}(\.[0-9]{1,6})?$/";

						if(!preg_match($regex, $curRawValue)) {
							$arrValidityErr[] = array($tableName, $tableRow["id"]);
									$track .= "\n3(".$tableName.":".$tableRow["id"].$curRawValue.")";
						} 
						$currentRow[$dbFieldName] = addslashes($curRawValue);
						break;
					case 4: //Select
						if($fieldProperties["properties"]["regexPattern"]!="") {
							$regex = $fieldProperties["properties"]["regexPattern"];	
						} else {
							$regex = "/^[0-9]{1,9}$/";
						}
						
						if (strlen($curRawValue) > 0) {
							if(!preg_match($regex, $curRawValue)) {
									$arrValidityErr[] = array($tableName, $tableRow["id"]);
									$track .= "\n4(".$tableName.":".$tableRow["id"].")";
							} else {
								if(preg_match("/^[0-9]{1,9}$/", $curRawValue)) {
									$currentRow[$dbFieldName] = addslashes($curRawValue);
								}else{
									$currentRow[$dbFieldName] = "'".addslashes($curRawValue)."'";
								}
							}
						}
						break;
					case 5: //Date
						if(trim($curRawValue)=="") {
							$currentRow[$dbFieldName] = "'0000-00-00'";
						} else {
							if($chkDate->chkDate($curRawValue, 1, $retDate)) {
								$currentRow[$dbFieldName] = "'".$retDate."'";
							} else {
								$arrValidityErr[] = array($tableName, $tableRow["id"]);	
									$track .= "\n5(".$tableName.":".$tableRow["id"]."):".$curRawValue;
							}
						}
						break;
					}
				}
				if(count($currentRow)>0) {
					$currentRow["recModification"] = $currentRecModification;
					$currentRow["recID"] = $currentRecID;
					$arrCleanTableRows[$tableName][] = $currentRow;
					$track .= "\n6".print_r($currentRow, true);
				}
			}
		}
		if(count($arrMandatoryErr)>0) {
//			error_log("\n". date("Ymd-H:i:s arrMandatoryErr ", time())."INFO arrMandatoryErr: ".count($arrMandatoryErr), 3, "__harald.log");
//			error_log("\n". date("Ymd-H:i:s arrMandatoryErr ", time())."INFO ".print_r($arrMandatoryErr,true), 3, "__harald.log");
			$response["success"] = false;
			$response["errCode"] = 530;
			$response["errText"] = "mandatory table fields are empty";
			$response["tableRows"] = $arrMandatoryErr;
			//communication_interface::alert(print_r($arrMandatoryErr,true));
			$response["success"] = false;			
			return $response;
		}
		if(count($arrValidityErr)>0) {
//			error_log("\n". date("Ymd-H:i:s arrValidityErr ", time())."INFO ".count($arrValidityErr), 3, "__harald.log");
//			error_log("\n". date("Ymd-H:i:s arrValidityErr ", time())."INFO ".print_r($arrValidityErr,true), 3, "__harald.log");
//			communication_interface::alert(count($arrValidityErr)."\ntrack: ".$track."\narrValidityErr:".print_r($arrValidityErr,true));
			$response["success"] = false;
			$response["errCode"] = 540;
			$response["errText"] = "validity check failed (table content)";
			$response["tableRows"] = $arrValidityErr;
			$response["success"] = false;
			return $response;
		}
//error_log("\n".print_r($arrCleanTableRows,true)."\n", 3, "/var/log/copronet-application.log");
		///////////////////////////////////////////////////
		// check table content [END]
		///////////////////////////////////////////////////
		

		///////////////////////////////////////////////////
		// all checks were successful
		// now we assemble the sql statements and save the data
		///////////////////////////////////////////////////

		/// BEGIN: Save "normal" fields
		$affectedFields["payroll_employee"] = array();
		$affectedFields["payroll_employee_children"] = array();
		$changeMode = "EDIT";
		if($id>0) {
			$affectedFields["payroll_employee_children"] = $system_database_manager->executeQuery("SELECT * FROM payroll_employee_children WHERE payroll_employee_ID=".$id." ORDER BY id", "payroll_saveEmployeeDetail");
			$sqlFields = array();
			foreach($fieldset as $fieldName=>$fieldDetail){
				if(isset($fieldDetail["value"])) {
					$sqlFields[] = "`".$fieldName."`=".$fieldDetail["value"];
				}
			}
			$sql = "UPDATE payroll_employee SET ".implode(",", $sqlFields)." WHERE id=".$id;
		} else {
			$changeMode = "NEW";
			$sqlFields = array();
			$sqlValues = array();
			foreach($fieldset as $fieldName=>$fieldDetail) {
				if(isset($fieldDetail["value"])) {
					$sqlFields[] = "`".$fieldName."`";
					$value = $fieldDetail["value"];
					if (strlen($value) < 1) {
						$value = "''";
					}
					$sqlValues[] = $value;
					$affectedFields["payroll_employee"][] = $fieldName;
				}
			}
			$sql = "INSERT INTO payroll_employee(".implode(",", $sqlFields).") VALUES(".implode(",", $sqlValues).")";
		}
		$system_database_manager->executeUpdate("BEGIN", "payroll_saveEmployeeDetail");
		if(count($sqlFields)>0) {
			if($changeMode=="EDIT") $snapshotBefore = $system_database_manager->executeQuery(
"SELECT * FROM payroll_employee WHERE id=".$id, "payroll_saveEmployeeDetail");
			$system_database_manager->executeUpdate($sql, "payroll_saveEmployeeDetail");
			if($id==0) $id = $system_database_manager->getLastInsertId();

			$resDate = $system_database_manager->executeQuery(
"SELECT CONCAT(payroll_year_ID,'-',major_period,'-01') as datePeriodStart
, LAST_DAY(CONCAT(payroll_year_ID,'-',major_period,'-01')) as datePeriodEnd 
FROM payroll_period WHERE major_period<13 
ORDER BY payroll_year_ID DESC, major_period DESC LIMIT 1"
, "payroll_saveEmployeeDetail");
			if(count($resDate)<1) {
				$response["success"] = false;
				$response["errCode"] = 666;
				$response["errText"] = "could not get period start and end date";
			}
			$datePeriodStart = $resDate[0]["datePeriodStart"];
			$datePeriodEnd = $resDate[0]["datePeriodEnd"];

			$res = $system_database_manager->executeQuery("SELECT `name`,`value` FROM `core_registry` WHERE `path`='GLOBAL/SETTINGS/CORE/payroll' AND `name` LIKE 'ahv_m%_age_%'", "payroll_closePeriod");
			foreach($res as $row) $ahvAgeRange[$row["name"]] = $row["value"];
			$sql = 
"UPDATE payroll_employee 
SET RetirementDate=IF(Sex='M',DATE_ADD(DateOfBirth, INTERVAL ".$ahvAgeRange["ahv_max_age_m"]." YEAR),
DATE_ADD(DateOfBirth, INTERVAL ".$ahvAgeRange["ahv_max_age_f"]." YEAR)), 
Age=(YEAR(CURDATE())-YEAR(DateOfBirth))-(RIGHT(CURDATE(),5)<RIGHT(DateOfBirth,5)), 
AgeAtPeriodStart=(YEAR('".$datePeriodStart."')-YEAR(DateOfBirth))-(RIGHT('".$datePeriodStart."',5)<RIGHT(DateOfBirth,5)), 
AgeAtPeriodEnd=(YEAR('".$datePeriodEnd."')-YEAR(DateOfBirth))-(RIGHT('".$datePeriodEnd."',5)<RIGHT(DateOfBirth,5)), 
YearsOfService=YEAR(CURDATE())-YEAR(SeniorityJoining), 
MonthsOfService=MONTH(CURDATE())-MONTH(SeniorityJoining) 
WHERE id=".$id;
			$system_database_manager->executeUpdate($sql, "payroll_saveEmployeeDetail");
			$system_database_manager->executeUpdate(
"UPDATE payroll_employee 
SET YearsOfService=YearsOfService-1
, MonthsOfService=MonthsOfService+12 
WHERE id=".$id." AND MonthsOfService<0", "payroll_saveEmployeeDetail");
			$system_database_manager->executeUpdate(
"UPDATE payroll_employee 
SET YearsOfService=0, MonthsOfService=0 
WHERE id=".$id." AND YearsOfService<0", "payroll_saveEmployeeDetail");
			if($changeMode=="EDIT") {
				$snapshotAfter = $system_database_manager->executeQuery(
"SELECT * FROM payroll_employee WHERE id=".$id, "payroll_saveEmployeeDetail");
				$diff = array_diff_assoc($snapshotBefore[0], $snapshotAfter[0]);
				foreach($diff as $diffFieldName=>$diffFieldValue) {
					$affectedFields["payroll_employee"][] = $diffFieldName;
				}
			}
		} else {
			$response["success"] = false;
			$response["errCode"] = 1;
			$response["errText"] = "nothing to save";
		}
		/// END: Save "normal" fields

		/// BEGIN: Save table content
		if(count($arrCleanTableRows)>0) {
			foreach($arrCleanTableRows as $tableName=>$recordCollection) {
				$realTableName = $tableset[$tableName]["properties"]["dataSource"];
				if(!isset($db[$realTableName])) {
					$db[$realTableName]["UPDATE"] = array();
					$db[$realTableName]["DELETE"] = array();
					$db[$realTableName]["INSERT"] = array();
				}
				foreach($recordCollection as $rec) {
					switch($rec["recModification"]) {
					case 'insert':
						$fieldCollector = array();
						foreach($rec as $fieldName=>$fieldValue) if($fieldName!="recID" && $fieldName!="recModification") $fieldCollector[] = $fieldValue;
						if(count($fieldCollector)>0) {
							$fieldCollector[] = $id; //in order to be able to link the new record to the parent employee record we need to provide the employeeID
							$db[$realTableName]["INSERT"][] = "(".implode(",", $fieldCollector).")";
						}
						if(!isset($db[$realTableName]["INSERT_FIELDS"])) {
							$db[$realTableName]["INSERT_FIELDS"] = array();
							foreach($rec as $fieldName=>$fieldValue) if($fieldName!="recID" && $fieldName!="recModification") $db[$realTableName]["INSERT_FIELDS"][] = "`".$fieldName."`";
							if(count($db[$realTableName]["INSERT_FIELDS"])>0) $db[$realTableName]["INSERT_FIELDS"][] = "`payroll_employee_ID`"; //in order to be able to link the new record to the parent employee record we need to provide the employeeID
						}
						break;
					case 'delete':
						$db[$realTableName]["DELETE"][] = $rec["recID"];
						break;
					case 'update':
						$fieldCollector = array();
						foreach($rec as $fieldName=>$fieldValue) if($fieldName!="recID" && $fieldName!="recModification") $fieldCollector[] = "`".$fieldName."`=".$fieldValue;
						if(count($fieldCollector)>0) $db[$realTableName]["UPDATE"][] = array($rec["recID"], implode(",", $fieldCollector));
						break;
					}
				}
			}
			if(isset($db)) {
				$arrSQL = array();
				foreach($db as $tblName=>$tblData) {
					if(count($tblData["DELETE"])>0) {
						$arrSQL[] = "DELETE FROM ".$tblName." WHERE id IN (".implode(",", $tblData["DELETE"]).")";
					}
					if(count($tblData["INSERT"])>0) {
						$arrSQL[] = "INSERT INTO ".$tblName."(".implode(",", $tblData["INSERT_FIELDS"]).") VALUES".implode(",", $tblData["INSERT"]);
					}
					if(count($tblData["UPDATE"])>0) {
						foreach($tblData["UPDATE"] as $rec) $arrSQL[] = "UPDATE ".$tblName." SET ".$rec[1]." WHERE id=".$rec[0];
					}
				}
				//communication_interface::alert(print_r($arrSQL, true));
				foreach($arrSQL as $sql) {
					$system_database_manager->executeUpdate($sql, "payroll_saveEmployeeDetailTbl");
				}
			}
		}
		/// END: Save table content

		$resChldDiff = $system_database_manager->executeQuery(
			"SELECT * FROM payroll_employee_children WHERE payroll_employee_ID=".$id." ORDER BY id", "payroll_saveEmployeeDetail");
		if($affectedFields["payroll_employee_children"]!=$resChldDiff) {
			$chldDiff = $this->array_diff_assoc_recursive($affectedFields["payroll_employee_children"], $resChldDiff);
			if(count($chldDiff)==0) $chldDiff = array("undefined diff");
			$affectedFields["payroll_employee_children"] = $chldDiff;
		} else $affectedFields["payroll_employee_children"] = array();

		require_once('changeManager.php');
		$changeManager = new changeManager("","");				
		$changeManager->changeManager(
				"EmployeeChange"
				, array("payroll_employee_ID" => array($id)
				, "affectedFields" => $affectedFields
				, "activeTransaction"=>true
				, "chageMode"=>$changeMode)); 
		//Bei INSERT+DELETE sind alle Fields affected, nur bei UPDATE muessen wir genau ermitteln, welche Felder geändert wurden

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("COMMIT", "payroll_saveEmployeeDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		$response["fieldNames"] = array();
		$response["tableRows"] = array();
		return $response;
	}

	public function addEmployee2Period($employeeIDs) {
		if(count($employeeIDs)<1) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "array empty";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		// get the id of the current payroll period
		$result = $system_database_manager->executeQuery("SELECT id, payroll_year_ID, major_period_associated FROM payroll_period WHERE locked=0 AND finalized=0", "payroll_addEmployee2Period");
		if(count($result)>0) {
			$payrollPeriodID = $result[0]["id"];
			$periodStartDate = $result[0]["payroll_year_ID"]."-".substr("0".$result[0]["major_period_associated"],-2)."-01";
		}else{
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "no open period found";
			return $response;
		}

		///////////////////////////////////////////////////
		// all employee id's must be numeric and non-decimal
		///////////////////////////////////////////////////
		$v = array();
		$vMng = array();
		$arrEmpID = array();
		$uid = session_control::getSessionInfo("id");
		foreach($employeeIDs as $id) {
			if(!preg_match( '/^[0-9]{1,9}$/', $id)) {
				$response["success"] = false;
				$response["errCode"] = 20;
				$response["errText"] = "invalid employee id";
				return $response;
			}
			$v[] = "(".$payrollPeriodID.",".$id.",1)";
			$vMng[] = "(".$uid.",".$id.",'')";
			$arrEmpID[] = $id;
		}

		// TODO: Mitarbeiter, die bereits verbucht wurden, daerfen nicht durch REPLACE INTO wieder zuraeckgesetzt werden!

		if(count($v)>0) {
			$system_database_manager->executeUpdate("BEGIN", "payroll_addEmployee2Period");
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_id`=".$uid, "payroll_addEmployee2Period");
			//Ausgewählte Mitarbeiter in aktuelle Periode einfuegen
			$system_database_manager->executeUpdate("REPLACE INTO `payroll_period_employee`(`payroll_period_ID`,`payroll_employee_ID`,`processing`) VALUES".implode(",",$v), "payroll_addEmployee2Period");

			//QST-Kanton und QST-Code bei allen Mitarbeitern mit QST-Verarbeitung setzen
			$sql = 
"UPDATE `payroll_employee` emp INNER JOIN `payroll_period_employee` prdemp ON prdemp.`payroll_period_ID`=".$payrollPeriodID." AND prdemp.`payroll_employee_ID`=emp.`id` AND prdemp.`payroll_employee_ID` IN (".implode(",", $arrEmpID).") 
SET prdemp.`DedAtSrcMode`=emp.`DedAtSrcMode`, prdemp.`DedAtSrcCanton`=emp.`DedAtSrcCanton`, prdemp.`DedAtSrcCode`=emp.`DedAtSrcCode` 
WHERE emp.`DedAtSrcMode`!=1";
			$system_database_manager->executeUpdate($sql, "payroll_closePeriod");

			//`payroll_employee`.`EmploymentStatus` fuer eingefaegte Mitarbeiter neu ermitteln
			$sql = 
"UPDATE `payroll_employee` emp INNER JOIN `payroll_employment_period` empprd ON empprd.`payroll_employee_ID`=emp.`id` 
AND empprd.`DateFrom`<=LAST_DAY('".$periodStartDate."') 
AND (empprd.`DateTo`='0000-00-00' OR empprd.`DateTo`>='".$periodStartDate."') 
SET emp.`EmploymentStatus`=2 
WHERE emp.`EmploymentStatus`=1 
AND emp.`id` IN (".implode(",", $arrEmpID).")";
			$system_database_manager->executeUpdate($sql, "payroll_addEmployee2Period");
			$system_database_manager->executeUpdate("INSERT INTO `payroll_tmp_change_mng`(`core_user_id`, `numID`, `alphID`) VALUES".implode(",",$vMng), "payroll_closePeriod");
			//userID INT, internalTransaction TINYINT, wageCodeChange TINYINT, wageBaseChange TINYINT, insuranceChange TINYINT, modifierChange TINYINT, workdaysChange TINYINT, pensiondaysChange TINYINT
			$system_database_manager->executeUpdate("Call payroll_prc_empl_acc(".$uid.", 0, 1, 1, 1, 1, 1, 1)"); 
			$system_database_manager->executeUpdate("DELETE FROM `payroll_tmp_change_mng` WHERE `core_user_id`=".$uid, "payroll_addEmployee2Period");
			$system_database_manager->executeUpdate("COMMIT", "payroll_addEmployee2Period");

			$response["success"] = true;
			$response["errCode"] = 0;
		}else{
			$response["success"] = false;
			$response["errCode"] = 40;
			$response["errText"] = "no data";
		}
		return $response;
	}

	public function getEmployeeFilterList($param) {
		if(isset($param["FilterForEmplOverview"]) && is_bool($param["FilterForEmplOverview"]) === true) $ValidForEmplOverview = $param["FilterForEmplOverview"] ? 1 : 0;
		else $ValidForEmplOverview = 1;
		if(isset($param["FilterForCalculation"]) && is_bool($param["FilterForCalculation"]) === true) $ValidForCalculation = $param["FilterForCalculation"] ? 1 : 0;
		else $ValidForCalculation = 1;

		$system_database_manager = system_database_manager::getInstance();
		$sql = 
"SELECT * FROM `payroll_empl_filter` 
WHERE (`ValidForEmplOverview`=".$ValidForEmplOverview." 
OR `ValidForCalculation`=".$ValidForCalculation.") 
AND (`GlobalFilter`=1 OR `core_user_id_created`=".session_control::getSessionInfo("id").") 
ORDER BY `FilterName`";
		$result = $system_database_manager->executeQuery($sql, "payroll_getEmployeeFilterList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result;
		}
		return $response;
	}

	public function getEmployeeFilterDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_empl_filter` WHERE `id`=".$param["id"], "payroll_getEmployeeFilterDetail");
		$criteria = $system_database_manager->executeQuery("SELECT * FROM `payroll_empl_filter_crit` WHERE `payroll_empl_filter_ID`=".$param["id"]." ORDER BY `SortOrder`", "payroll_getEmployeeFilterDetail");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
			$response["data"]["criteria"] = $criteria;
		}
		return $response;
	}

	public function saveEmployeeFilterDetail($param) {
		if(!isset($param["data"]) || !is_array($param["data"]) || count($param["data"])==0) {
			$response["success"] = false;
			$response["errCode"] = 5;
			$response["errText"] = "missing filter criteria";
			$response["fieldNames"] = array();
			return $response;
		}
		//main record mandatory and validity checks...
		$fieldDef = array(
					"payroll_empl_filter_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>true),
					"FilterName"=>array("regex"=>"/^.{1,45}$/", "isText"=>true, "isID"=>false),
					"ValidForEmplOverview"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"ValidForCalculation"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"GlobalFilter"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"TemporaryFilter"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false)
				);

		$testPassed = true;
		$errFields = array();
		foreach($fieldDef as $fieldName=>$fieldParam) {
			if(isset($fieldParam["defaultValue"]) && (!isset($param[$fieldName]) || $param[$fieldName]=="")) $param[$fieldName]=$fieldParam["defaultValue"];
			if(!preg_match($fieldParam["regex"], $param[$fieldName])) {
				$errFields[] = $fieldName;
				$testPassed = false;
			}
		}
		if(!$testPassed) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}
		$payroll_empl_filter_ID = $param["payroll_empl_filter_ID"]>0 ? $param["payroll_empl_filter_ID"] : 0;

		//sub-records mandatory and validity checks...
		$system_database_manager = system_database_manager::getInstance();
		$dateCheckFields = array("ex_activeempl_refdate");
		$res = $system_database_manager->executeQuery("SELECT `fieldName` FROM `payroll_employee_field_def` WHERE `fieldType`=5 AND `childOf`=''", "payroll_saveEmployeeFilterDetail");
		foreach($res as $row) $dateCheckFields[] = $row["fieldName"];

		$subFieldDef = array(
					"id"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>true),
					"CriteriaType"=>array("regex"=>"/^[1-4]{1,1}$/", "isText"=>false, "isID"=>false),
					"FieldName"=>array("regex"=>"/^.{0,45}$/", "isText"=>true, "isID"=>false),
					"FieldModifier"=>array("regex"=>"/^[0-3]{1,1}$/", "isText"=>false, "isID"=>false),
					"Conjunction"=>array("regex"=>"/^[0-2]{1,1}$/", "isText"=>false, "isID"=>false),
					"Comparison"=>array("regex"=>"/^[0-7]{1,1}$/", "isText"=>false, "isID"=>false),
					"ComparativeValues"=>array("regex"=>"/^.{0,2048}$/", "isText"=>true, "isID"=>false),
					"SortOrder"=>array("regex"=>"/^[0-9]{1,2}$/", "isText"=>false, "isID"=>false)
				);
		$criteriaTypeRegister = array(0,0);
		$invalidCriteriaTypeCombinations = array( array(3,2),array(4,2),array(2,2),array(1,1),array(4,3),array(3,4),array(1,3) );
		$openingBracketCount = 0;
		$closingBracketCount = 0;
		foreach($param["data"] as &$rec) {
			array_shift($criteriaTypeRegister);
			$criteriaTypeRegister[] = $rec["CriteriaType"];
			if($rec["CriteriaType"]==3) $openingBracketCount++;
			else if($rec["CriteriaType"]==4) $closingBracketCount++;
			//checking for invalid CriteriaType combinations
			foreach($invalidCriteriaTypeCombinations as $compRegister) {
				if($criteriaTypeRegister == $compRegister) {
					$testPassed = false;
					$errCode = 200 + $compRegister[0]*10 + $compRegister[1];
					break;
				}
			}
//TODO: etwas speziell -> wenn LISTE, dann muessen Werte zuerst mit explode separiert werden!
			foreach($subFieldDef as $fieldName=>$fieldParam) {
				if(isset($fieldParam["defaultValue"]) && (!isset($rec[$fieldName]) || $rec[$fieldName]=="")) $rec[$fieldName]=$fieldParam["defaultValue"];
				if(!preg_match($fieldParam["regex"], $rec[$fieldName])) {
					$errFields[] = $fieldName;
					$testPassed = false;
					$errCode = 100;
				}
			}
			if($rec["CriteriaType"]==1) {
				if(strlen($rec["FieldName"])<1) {
					$errFields[] = $rec["FieldName"];
					$testPassed = false;
					$errCode = 100;
				}
				if(strlen($rec["ComparativeValues"])<1) {
					$errFields[] = $rec["FieldName"];
					$testPassed = false;
					$errCode = 100;
				}
				if(in_array($rec["FieldName"], $dateCheckFields) && $rec["FieldModifier"]==0) {
					//check date only if there are no special modifiers like DAY(), MONTH(), YEAR()
					if($chkDate->chkDate($rec["ComparativeValues"], 1, $retDate)) {
						$rec["ComparativeValues"] = $retDate;
					}else{
						$errFields[] = $rec["FieldName"];
						$testPassed = false;
						$errCode = 105;
					}
				}
				if($rec["FieldName"] == "ex_activeempl_daterange") {
					$tmparr = explode("-",$rec["ComparativeValues"]);
					if(count($tmparr)==2 && $chkDate->chkDate($tmparr[0], 1, $retDateFrom) && $chkDate->chkDate($tmparr[1], 1, $retDateTo)) {
						$rec["ComparativeValues"] = $retDateFrom."@".$retDateTo;
					}else{
						$errFields[] = "ex_activeempl_daterange";
						$testPassed = false;
						$errCode = 105;
					}
				}
			}
			if(!$testPassed) break;
		}
		unset($rec); // This will fix the issue, thus $rec by ref will be reset for later use by val
		if($testPassed && $openingBracketCount != $closingBracketCount) {
			$testPassed = false;
			$errCode = 300;
			$errFields = array();
		}
		if(!$testPassed) {
			$response["success"] = false;
			$response["errCode"] = $errCode;
			$response["errText"] = "error in criteria collection";
			if(count($errFields)>0) $response["fieldNames"] = $errFields;
			return $response;
		}

		$system_database_manager->executeUpdate("BEGIN", "payroll_saveEmployeeFilterDetail");
		if($payroll_empl_filter_ID>0) {
			$system_database_manager->executeUpdate("UPDATE `payroll_empl_filter` SET `FilterName`='".addslashes($param["FilterName"])."',`GlobalFilter`=".$param["GlobalFilter"].",`TemporaryFilter`=".$param["TemporaryFilter"].",`ValidForEmplOverview`=".$param["ValidForEmplOverview"].",`ValidForCalculation`=".$param["ValidForCalculation"].",`dirtyData`=1,`dirtyCriteria`=1 WHERE `id`=".$payroll_empl_filter_ID, "payroll_saveEmployeeFilterDetail");
		}else{
			$system_database_manager->executeUpdate("INSERT INTO `payroll_empl_filter`(`FilterName`,`FilterPriority`,`FilterCriteria`,`GlobalFilter`,`TemporaryFilter`,`ValidForEmplOverview`,`ValidForCalculation`,`dirtyData`,`dirtyCriteria`,`datetime_created`,`core_user_id_created`) VALUES('".addslashes($param["FilterName"])."',1,'',".$param["GlobalFilter"].",".$param["TemporaryFilter"].",".$param["ValidForEmplOverview"].",".$param["ValidForCalculation"].",1,1,NOW(),".session_control::getSessionInfo("id").")", "payroll_saveEmployeeFilterDetail");
			$payroll_empl_filter_ID = $system_database_manager->getLastInsertId();
		}

		$insertValueCollector = array();
		foreach($param["data"] as $rec) {
			if($rec["id"]!=0) $system_database_manager->executeUpdate("UPDATE `payroll_empl_filter_crit` SET `payroll_empl_filter_ID`=".$payroll_empl_filter_ID.",`CriteriaType`=".$rec["CriteriaType"].",`FieldName`='".addslashes($rec["FieldName"])."',`FieldModifier`=".$rec["FieldModifier"].",`Conjunction`=".$rec["Conjunction"].",`Comparison`=".$rec["Comparison"].",`SortOrder`=".$rec["SortOrder"].",`ComparativeValues`='".addslashes($rec["ComparativeValues"])."' WHERE `id`=".$rec["id"], "payroll_saveEmployeeFilterDetail");
			else $insertValueCollector[] = "(".$payroll_empl_filter_ID.",".$rec["CriteriaType"].",'".addslashes($rec["FieldName"])."',".$rec["FieldModifier"].",".$rec["Conjunction"].",".$rec["Comparison"].",".$rec["SortOrder"].",'".addslashes($rec["ComparativeValues"])."')";
		}
		if(count($insertValueCollector)>0) $system_database_manager->executeUpdate("INSERT INTO `payroll_empl_filter_crit`(`payroll_empl_filter_ID`,`CriteriaType`,`FieldName`,`FieldModifier`,`Conjunction`,`Comparison`,`SortOrder`,`ComparativeValues`) VALUES".implode(", ", $insertValueCollector), "payroll_saveEmployeeFilterDetail");

		$system_database_manager->executeUpdate("COMMIT", "payroll_saveEmployeeFilterDetail");

		$this->employeeFilter2SQL(); //alle Filter mit gesetztem dirty-Flag neu generieren und Cache anlegen

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteEmployeeFilterDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("DELETE FROM `payroll_empl_filter` WHERE `id`=".$param["id"], "payroll_deleteEmployeeFilterDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_empl_filter_crit` WHERE `payroll_empl_filter_ID`=".$param["id"], "payroll_deleteEmployeeFilterDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_empl_filter_cache` WHERE `payroll_empl_filter_ID`=".$param["id"], "payroll_deleteEmployeeFilterDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}


///////////////////////////////////////////////////////
//// Employee Filter -> SQL                        ////
///////////////////////////////////////////////////////
	private function employeeFilter2SQL($filterID=0) {
		///////////////////////////////////////////////////
		// filter id must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $filterID)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid filter id";
			return $response;
		}

		$filterIDs = array();
		$system_database_manager = system_database_manager::getInstance();
		//error_log("\n\n\n", 3, "/var/log/copronet-application.log");

		if($filterID==0) {
			$result = $system_database_manager->executeQuery("SELECT id FROM payroll_empl_filter WHERE dirtyCriteria=1", "payroll_employeeFilter2SQL");
			foreach($result as $row) $filterIDs[] = $row["id"];
		}else $filterIDs[] = $filterID;

		$system_database_manager->executeUpdate("BEGIN", "payroll_employeeFilter2SQL");
		foreach($filterIDs as $filterID) {
			$result = $system_database_manager->executeQuery("SELECT crit.*,fldDef.fieldType,fldDef.dataSourceToken FROM payroll_empl_filter_crit crit LEFT JOIN payroll_employee_field_def fldDef ON crit.FieldName=fldDef.FieldName WHERE crit.payroll_empl_filter_ID=".$filterID." ORDER BY crit.SortOrder", "payroll_employeeFilter2SQL");
			$sqlItems = array();
			foreach($result as $row) {
				switch($row["CriteriaType"]) {
				case 1: //Field name
					if(is_null($row["fieldType"])) {
						//spezialfeld
						//$fieldName = "`".$row["FieldName"]."`";
						switch($row["FieldName"]) {
						case 'ex_activeempl_daterange':
							$tmparr = explode("@",$row["ComparativeValues"]);
							$sqlItems[] = ($row["Comparison"]==4 ? "NOT " : "")."(emprd.`DateFrom`<='".$tmparr[1]."' AND (emprd.`DateTo`>='".$tmparr[0]."' OR emprd.`DateTo`='0000-00-00'))";
							break;
						case 'ex_activeempl_refdate':
							$sqlItems[] = ($row["Comparison"]==4 ? "NOT " : "")."(emprd.`DateFrom`<='".$row["ComparativeValues"]."' AND (emprd.`DateTo`>='".$row["ComparativeValues"]."' OR emprd.`DateTo`='0000-00-00'))";
							break;
						}
					}else{
						//standardfeld aus personalstamm
						switch($row["Comparison"]) {
						case 1: // =
							$comparison = "=";
							break;
						case 2: // <=
							$comparison = "<=";
							break;
						case 3: // >=
							$comparison = ">=";
							break;
						case 4: // <>
							$comparison = "!=";
							break;
						case 5: // >
							$comparison = ">";
							break;
						case 6: // <
							$comparison = "<";
							break;
						case 7: // IN
							$comparison = " IN (".$row["ComparativeValues"].")";
							break;
						}
						$isText = true;
						if($row["fieldType"]==2 || $row["fieldType"]==3 || ($row["fieldType"]==3 && $row["dataSourceToken"]==0) || $row["Conjunction"]==3 || ($row["FieldModifier"]>0 && $row["FieldModifier"]<4)) $isText = false;

						switch($row["FieldModifier"]) {
						case 1: // DAY()
							$fieldName = "DAY(emp.`".$row["FieldName"]."`)";
							break;
						case 2: // MONTH()
							$fieldName = "MONTH(emp.`".$row["FieldName"]."`)";
							break;
						case 3: // YEAR()
							$fieldName = "YEAR(emp.`".$row["FieldName"]."`)";
							break;
						default:
							$fieldName = "emp.`".$row["FieldName"]."`";
							break;
						}

						if($row["Comparison"]<7) {
							if($isText) $sqlItems[] = $fieldName.$comparison."'".$row["ComparativeValues"]."'";
							else $sqlItems[] = $fieldName.$comparison.$row["ComparativeValues"];
						}else $sqlItems[] = $fieldName.$comparison;
					}
					break;
				case 2: //Conjunction
					$sqlItems[] = $row["Conjunction"]==1 ? "AND" : "OR";
					break;
				case 3: //Group start
					$sqlItems[] = "(";
					break;
				case 4: //Group end
					$sqlItems[] = ")";
					break;
				}
			}
			$system_database_manager->executeUpdate("UPDATE payroll_empl_filter SET dirtyData=1, dirtyCriteria=0, FilterCriteria='".addslashes(implode(" ",$sqlItems))."' WHERE id=".$filterID, "payroll_employeeFilter2SQL");
		}

		require_once('changeManager.php');
		$changeManager = new changeManager();				
		$changeManager->changeManager("FilterChange", array("payroll_empl_filter_ID" => $filterIDs, "activeTransaction"=>true));

		$system_database_manager->executeUpdate("COMMIT", "payroll_employeeFilter2SQL");
		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}


	private function array_diff_assoc_recursive($array1, $array2) {
		$difference=array();
		foreach($array1 as $key => $value) {
			if( is_array($value) ) {
				if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
					$difference[$key] = $value;
				} else {
					$new_diff = $this->array_diff_assoc_recursive($value, $array2[$key]);
					if( !empty($new_diff) )
					$difference[$key] = $new_diff;
				}
			} else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
				$difference[$key] = $value;
			}
		}
		return $difference;
	}

	public function getEmployeeLabelListe($language, $fieldNameList) {
		$fieldNames = "'".str_replace(  ","    ,   "','"   , $fieldNameList)."'";//" 'tblprd','tblchld' ";
		$fieldNames = str_replace("''", "'", $fieldNames);
		$sprache = "de";
		if (strlen($language)==2) {
			$sprache = strtolower($language);
		}
		
		$sql = " 
SELECT label AS Labels FROM payroll_employee_field_label 
WHERE language = '".$sprache."'
AND fieldName IN (".$fieldNames."); 		
			   ";
		//communication_interface::alert($sql);
		$system_database_manager = system_database_manager::getInstance();
		$res = $system_database_manager->executeQuery($sql);
		return $res;
	}
}
?>
