<?php
class payslip {

	public function getPayslipCfgList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT `id`,`payslip_name`,`default_payslip`,`pdf_template` FROM `payroll_payslip_cfg` ORDER BY `payslip_name`", "payroll_getPayslipCfgList");

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

	public function getPayslipCfgDetail($param) {
		$system_database_manager = system_database_manager::getInstance();
		$resCfg = array();
		if(!isset($param["listValuesOnly"]) || $param["listValuesOnly"]==false) {
			if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "invalid id";
				return $response;
			}

			$resCfg = $system_database_manager->executeQuery("SELECT * FROM `payroll_payslip_cfg` WHERE `id`=".$param["id"], "payroll_getPayslipCfgDetail");
		}
		//Liste mit Template-Dateien erstellen
		$fm = new file_manager(); //neues Objekt
		$fl = $fm->customerSpace()->setPath("TEMPLATE/")->listDir();
		$arrPDF = array();
		foreach($fl as $row) if(strtolower(substr($row,-4))==".pdf") $arrPDF[] = $row;

		//Liste mit Sprachcodes erstellen
		$arrLanguage = array();
		$resLng = $system_database_manager->executeQuery("SELECT `core_intl_language_ID` FROM `payroll_languages` WHERE `UseForAccounts`=1 ORDER BY `DefaultLanguage` DESC, `core_intl_language_ID`", "payroll_getPayslipCfgDetail");
		foreach($resLng as $row) $arrLanguage[] = $row["core_intl_language_ID"];

		$response["success"] = count($resCfg)!=0 ? true : false;
		$response["errCode"] = 0;
		$response["data"] = $resCfg[0];
		$response["InfoFields"] = count($resCfg)!=0 ? $system_database_manager->executeQuery("SELECT * FROM `payroll_payslip_cfg_info` WHERE `payroll_payslip_cfg_ID`=".$resCfg[0]["id"]." ORDER BY `position`", "payroll_getPayslipCfgDetail") : array();
		$response["AvailablePDF"] = $arrPDF;
		$response["Languages"] = $arrLanguage;
		return $response;
	}

	public function savePayslipCfgDetail($param) {
		if(trim($param["id"])=="" || trim($param["id"])=="0") {
			$updateMode = false;
		}else{
			if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "invalid id";
				return $response;
			}
			$updateMode = true;
		}
		$fieldCfgMain = array(
					"payslip_name"=>array("regex"=>".{1,30}","addQuotes"=>true),
					"pdf_template"=>array("regex"=>"[-._a-zA-Z0-9]{0,30}","addQuotes"=>true),
					"default_payslip"=>array("regex"=>"[01]{1,1}","addQuotes"=>false),
					"info_font_name"=>array("regex"=>"[a-zA-Z]{1,10}","addQuotes"=>true),
					"info_font_size"=>array("regex"=>"7|8|9|10|11|12|13","addQuotes"=>false),
					"info_offset_top"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false),
					"info_offset_left"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false),
					"addr_font_name"=>array("regex"=>"[a-zA-Z]{1,10}","addQuotes"=>true),
					"addr_font_size"=>array("regex"=>"7|8|9|10|11|12|13","addQuotes"=>false),
					"addr_offset_top"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false),
					"addr_offset_left"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false),
					"content_font_name"=>array("regex"=>"[a-zA-Z]{1,10}","addQuotes"=>true),
					"content_font_size"=>array("regex"=>"7|8|9|10|11|12|13","addQuotes"=>false),
					"content_offset_top"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false),
					"content_offset_left"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false),
					"content_width"=>array("regex"=>"[12]{0,1}[0-9]{1,2}","addQuotes"=>false)
				);
		$fieldCfgInfo = array(
					"label"=>array("regex"=>".{1,30}","addQuotes"=>true),
					"language"=>array("regex"=>"[a-z]{2,2}","addQuotes"=>true),
					"field_type"=>array("regex"=>"[0-9]{1,2}","addQuotes"=>false),
					"field_name"=>array("regex"=>"[-_a-zA-Z0-9]{0,20}","addQuotes"=>true)
				);

//communication_interface::alert(print_r($param,true));
		///////////////////////////////////////////////////
		// Mandatory and validity checks (MAIN table fields)
		///////////////////////////////////////////////////
		$errFields = array();
		foreach($fieldCfgMain as $fieldName=>$fieldParam) if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param[$fieldName])) $errFields[] = $fieldName;
		$errFields = array_unique($errFields);
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}
		$errFields = array();
		for($i=0;$i<count($param["InfoFields"]);$i++) {
			foreach($fieldCfgInfo as $fieldName=>$fieldParam) if(!preg_match( '/^'.$fieldParam["regex"].'$/', $param["InfoFields"][$i][$fieldName])) $errFields[] = array("index"=>$i, "FieldName"=>$fieldName);
		}
		if(count($errFields)>0) {
			$response["success"] = false;
			$response["errCode"] = 30;
			$response["errText"] = "invalid field value";
			$response["fieldNames"] = $errFields;
			return $response;
		}

		if($updateMode) {
			$sqlUPDATE = array();
			foreach($fieldCfgMain as $fieldName=>$fieldParam) {
				if($fieldParam["addQuotes"]) $sqlUPDATE[] = "`".$fieldName."`='".addslashes($param[$fieldName])."'";
				else $sqlUPDATE[] = "`".$fieldName."`=".addslashes($param[$fieldName]);
			}
			$sql = "UPDATE `payroll_payslip_cfg` SET ".implode(",",$sqlUPDATE)." WHERE `id`=".$param["id"];
			$parentID = $param["id"];
		}else{
			$sqlFIELDS = array();
			$sqlVALUES = array();
			foreach($fieldCfgMain as $fieldName=>$fieldParam) {
				$sqlFIELDS[] = "`".$fieldName."`";
				if($fieldParam["addQuotes"]) $sqlVALUES[] = "'".addslashes($param[$fieldName])."'";
				else $sqlVALUES[] = addslashes($param[$fieldName]);
			}
			$sql = "INSERT INTO `payroll_payslip_cfg`(".implode(",",$sqlFIELDS).") VALUES(".implode(",",$sqlVALUES).")";
		}
//communication_interface::alert("id:".$param["id"]." / ".$sql);

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_savePayslipCfgDetail");
		$system_database_manager->executeUpdate($sql, "payroll_savePayslipCfgDetail");
		if(!$updateMode) $parentID = $system_database_manager->getLastInsertId();
//communication_interface::alert("sql: ".$sql);
		if($param["default_payslip"]==1) $system_database_manager->executeUpdate("UPDATE `payroll_payslip_cfg` SET `default_payslip`=0 WHERE `id`!=".$parentID, "payroll_savePayslipCfgDetail"); //sicherstellen, dass es nur 1 default template gibt

		$system_database_manager->executeUpdate("DELETE FROM `payroll_payslip_cfg_info` WHERE `payroll_payslip_cfg_ID`=".$parentID, "payroll_savePayslipCfgDetail");
		for($i=0;$i<count($param["InfoFields"]);$i++) {
			$system_database_manager->executeUpdate("INSERT INTO `payroll_payslip_cfg_info`(`payroll_payslip_cfg_ID`,`label`,`language`,`field_type`,`field_name`,`position`) VALUES(".$parentID.",'".addslashes($param["InfoFields"][$i]["label"])."','".$param["InfoFields"][$i]["language"]."',".$param["InfoFields"][$i]["field_type"].",'".$param["InfoFields"][$i]["field_name"]."',".$i.")", "payroll_savePayslipCfgDetail");
		}

		$system_database_manager->executeUpdate("COMMIT", "payroll_savePayslipCfgDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deletePayslipCfgDetail($id) {
		if(!preg_match( '/^[0-9]{1,9}$/', $id)) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_deletePayslipCfgDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_payslip_cfg` WHERE `id`=".$id, "payroll_deletePayslipCfgDetail");
		$system_database_manager->executeUpdate("DELETE FROM `payroll_payslip_cfg_info` WHERE `payroll_payslip_cfg_ID`=".$id, "payroll_deletePayslipCfgDetail");
		$system_database_manager->executeUpdate("COMMIT", "payroll_deletePayslipCfgDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getPayslipNotifications($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["payroll_period_ID"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid payroll_period_ID";
			return $response;
		}
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_payslip_notice` WHERE `payroll_period_ID`=".$param["payroll_period_ID"], "payroll_getPayslipNotifications");

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

	public function savePayslipNotifications($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["payroll_period_ID"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid payroll_period_ID";
			return $response;
		}
		if(count($param["messages"])==0) {
			$response["success"] = false;
			$response["errCode"] = 20;
			$response["errText"] = "no message data submitted";
			return $response;
		}
		$sql = array();
		foreach($param["messages"] as $msg) {
			if(!preg_match( '/^[0-9]{1,9}$/', $msg["payroll_company_ID"]) || !preg_match( '/^[a-z]{2,2}$/', $msg["language"])) {
				$response["success"] = false;
				$response["errCode"] = 30;
				$response["errText"] = "wrong company ID or wrong language code";
				return $response;
			}
			if(trim($msg["notification"])=="") $sql[] = "DELETE FROM `payroll_payslip_notice` WHERE `payroll_period_ID`=".$param["payroll_period_ID"]." AND `payroll_company_ID`=".$msg["payroll_company_ID"]." AND `language`='".$msg["language"]."'";
			else $sql[] = "REPLACE INTO `payroll_payslip_notice`(`payroll_period_ID`,`payroll_company_ID`,`language`,`employee_notification`) VALUES(".$param["payroll_period_ID"].",".$msg["payroll_company_ID"].",'".$msg["language"]."','".addslashes($msg["notification"])."')";
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_savePayslipNotifications");
		foreach($sql as $s) $system_database_manager->executeUpdate($s, "payroll_savePayslipNotifications");
		$system_database_manager->executeUpdate("COMMIT", "payroll_savePayslipNotifications");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	
}
?>
