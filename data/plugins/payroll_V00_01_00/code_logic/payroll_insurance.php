<?php
class insurance {
	
	public function getInsuranceCompanyList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_insurance`", "payroll_getInsuranceCompanyList");

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

	public function getInsuranceCompanyDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			$response["fieldNames"] = array("id");
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_insurance` WHERE id=".$param["id"], "payroll_getInsuranceCompanyList");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}
		return $response;
	}

	public function saveInsuranceCompanyDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			$response["fieldNames"] = array("id");
			return $response;
		}

		if(!preg_match( '/^.{1,45}$/', $param["CompanyName"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid company name";
			$response["fieldNames"] = array("CompanyName");
			return $response;
		}else $param["CompanyName"] = addslashes($param["CompanyName"]);

		if(!preg_match( '/^.{0,15}$/', $param["InsuranceID"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid insurance ID";
			$response["fieldNames"] = array("InsuranceID");
			return $response;
		}else $param["InsuranceID"] = addslashes($param["InsuranceID"]);

		if(!preg_match( '/^.{0,4}$/', $param["SubNumber"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid SubNumber";
			$response["fieldNames"] = array("SubNumber");
			return $response;
		}else $param["SubNumber"] = addslashes($param["SubNumber"]);

		if(!preg_match( '/^[01]{1,1}$/', $param["IsSUVA"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid SUVA flag";
			$response["fieldNames"] = array("id");
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		if($param["id"]>0) $result = $system_database_manager->executeUpdate("UPDATE `payroll_insurance` SET `CompanyName`='".$param["CompanyName"]."',`InsuranceID`='".$param["InsuranceID"]."',`IsSUVA`=".$param["IsSUVA"].",`SubNumber`='".$param["SubNumber"]."' WHERE `id`=".$param["id"], "payroll_saveInsuranceCompanyDetail");
		else $result = $system_database_manager->executeUpdate("INSERT `payroll_insurance`(`CompanyName`,`InsuranceID`,`IsSUVA`,`SubNumber`) VALUES('".$param["CompanyName"]."','".$param["InsuranceID"]."',".$param["IsSUVA"].",'".$param["SubNumber"]."')", "payroll_saveInsuranceCompanyDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteInsuranceCompanyDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_insurance` WHERE id=".$param["id"], "payroll_deleteInsuranceCompanyDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getInsuranceCodeList($param=null) {
		$InsuranceType = 0;
		if(!is_null($param)) {
			if(!preg_match( '/^[1-7]{1,1}$/', $param["InsuranceType"])) {
				$response["success"] = false;
				$response["errCode"] = 10;
				$response["errText"] = "invalid insurance type";
				return $response;
			}
			$InsuranceType = $param["InsuranceType"];
		}
		$sqlCondition = $InsuranceType==0 ? "" : " WHERE cd.`payroll_insurance_type_ID`=".$InsuranceType;
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT cd.*,cmp.CompanyName FROM `payroll_insurance_code` cd INNER JOIN `payroll_insurance` cmp ON cmp.`id`=cd.`payroll_insurance_ID`".$sqlCondition." ORDER BY cd.`InsuranceCode`", "payroll_getInsuranceCodeList");

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

	public function getInsuranceCodeDetail($param) {
		///////////////////////////////////////////////////
		// ID must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_insurance_code` WHERE `id`=".$param["id"], "payroll_getInsuranceCodeDetail");

		$labelres = $system_database_manager->executeQuery("SELECT * FROM `payroll_insurance_cd_label` WHERE `payroll_insurance_code_ID`=".$param["id"], "payroll_getInsuranceCodeDetail");
		foreach($labelres as $row) $result[0]["label_".$row["language"]] = $row["label"];

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}
		return $response;
	}

	public function saveInsuranceCodeDetail($param) {
		$fieldDef = array(
					"id"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>true, "isLabel"=>false),
					"payroll_insurance_type_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>false, "isLabel"=>false),
					"payroll_company_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>false, "isLabel"=>false),
					"payroll_insurance_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>false, "isLabel"=>false),
					"InsuranceCode"=>array("regex"=>"/^[0-9a-zA-Z]{1,10}$/", "isText"=>true, "isID"=>false, "isLabel"=>false),
					"CustomerIdentity"=>array("regex"=>"/^.{0,15}$/", "isText"=>true, "isID"=>false, "isLabel"=>false),
					"ContractIdentity"=>array("regex"=>"/^.{0,15}$/", "isText"=>true, "isID"=>false, "isLabel"=>false)
				);

		require_once('payroll_various_functions.php');
		$variousFunctions = new variousFunctions();
				
		$res = $variousFunctions->getLanguageList("UseForAccounts");
		if($res["success"]) {
			foreach($res["data"] as $row) $fieldDef["label_".$row["core_intl_language_ID"]] = array("regex"=>"/^.{1,45}$/", "isText"=>true, "isID"=>false, "isLabel"=>true, "language"=>$row["core_intl_language_ID"]);
		}

		$testPassed = true;
		$errFields = array();
		foreach($fieldDef as $fieldName=>$fieldParam) {
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

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_saveInsuranceCodeDetail");
		if($param["id"]>0) {
			$id = $param["id"];
			$result = $system_database_manager->executeUpdate("UPDATE `payroll_insurance_code` SET `payroll_insurance_ID`=".$param["payroll_insurance_ID"].", `payroll_insurance_type_ID`=".$param["payroll_insurance_type_ID"].", `payroll_company_ID`=".$param["payroll_company_ID"].", `InsuranceCode`='".addslashes($param["InsuranceCode"])."', `CustomerIdentity`='".addslashes($param["CustomerIdentity"])."', `ContractIdentity`='".addslashes($param["ContractIdentity"])."' WHERE `id`=".$id, "payroll_saveInsuranceCodeDetail");
			$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_insurance_cd_label` WHERE `payroll_insurance_code_ID`=".$id, "payroll_saveInsuranceCodeDetail");
		}else{
			$result = $system_database_manager->executeUpdate("INSERT INTO `payroll_insurance_code`(`payroll_insurance_ID`, `payroll_insurance_type_ID`, `payroll_company_ID`, `InsuranceCode`, `CustomerIdentity`, `ContractIdentity`) VALUES(".$param["payroll_insurance_ID"].", ".$param["payroll_insurance_type_ID"].", ".$param["payroll_company_ID"].", '".addslashes($param["InsuranceCode"])."', '".addslashes($param["CustomerIdentity"])."', '".addslashes($param["ContractIdentity"])."')", "payroll_saveInsuranceCodeDetail");
			$id = $system_database_manager->getLastInsertId();
		}
		foreach($fieldDef as $fieldName=>$fieldParam) {
			if($fieldParam["isLabel"]) {
				$result = $system_database_manager->executeUpdate("INSERT INTO `payroll_insurance_cd_label`(`payroll_insurance_code_ID`,`language`,`label`) VALUES(".$id.",'".$fieldParam["language"]."','".addslashes($param[$fieldName])."')", "payroll_saveInsuranceCodeDetail");
			}
		}
		$system_database_manager->executeUpdate("COMMIT", "payroll_saveInsuranceCodeDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteInsuranceCodeDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$system_database_manager->executeUpdate("BEGIN", "payroll_deleteInsuranceCodeDetail");
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_insurance_code` WHERE `id`=".$param["id"], "payroll_deleteInsuranceCodeDetail");
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_insurance_cd_label` WHERE `payroll_insurance_code_ID`=".$param["id"], "payroll_deleteInsuranceCodeDetail");
		$system_database_manager->executeUpdate("COMMIT", "payroll_deleteInsuranceCodeDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function getInsuranceRateList($param) {
		///////////////////////////////////////////////////
		// InsuranceType must be numeric and non-decimal
		///////////////////////////////////////////////////
		if(preg_match( '/^[1-7]{1,1}$/', $param["InsuranceType"])) {
		    //TRUE
		}else{
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid InsuranceType";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT rt.`id`,cd.`payroll_company_ID`,cd.`InsuranceCode`,ins.`CompanyName`,cd.`CustomerIdentity`,cd.`ContractIdentity`,`payroll_insurance_code_ID`,rt.`payroll_account_ID`,rt.`Description`,rt.`rate`,rt.`Sex`,rt.`AgeFrom`,rt.`AgeTo`,rt.`CodeFrom`,rt.`CodeTo` FROM `payroll_insurance_code` cd INNER JOIN `payroll_insurance_rate` rt ON rt.`payroll_insurance_code_ID`=cd.`id` INNER JOIN `payroll_insurance` ins ON cd.`payroll_insurance_ID`=ins.`id` WHERE cd.`payroll_insurance_type_ID`=".$param["InsuranceType"], "payroll_getInsuranceRateList");

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

	public function getInsuranceRateDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_insurance_rate` WHERE `id`=".$param["id"], "payroll_getInsuranceRateDetail");

		if(count($result) < 1) {
			$response["success"] = false;
			$response["errCode"] = 101;
			$response["errText"] = "no data found";
		}else{
			$response["success"] = true;
			$response["errCode"] = 0;
			$response["data"] = $result[0];
		}
		return $response;
	}

	public function saveInsuranceRateDetail($param) {
		$fieldDef = array(
					"id"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>true),
					"payroll_insurance_code_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>false),
					"payroll_account_ID"=>array("regex"=>"/^[0-9a-zA-Z]{1,5}$/", "isText"=>true, "isID"=>false),
					"Description"=>array("regex"=>"/^.{0,15}$/", "isText"=>true, "isID"=>false),
					"rate"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false),
					"Sex"=>array("regex"=>"/^[FM]{0,1}$/", "isText"=>true, "isID"=>false),
					"AgeFrom"=>array("regex"=>"/^1?[0-9]{1,2}$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"AgeTo"=>array("regex"=>"/^1?[0-9]{1,2}$/", "isText"=>false, "isID"=>false, "defaultValue"=>120),
					"CodeFrom"=>array("regex"=>"/^1?[0-9]{1,2}$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"CodeTo"=>array("regex"=>"/^1?[0-9]{1,2}$/", "isText"=>false, "isID"=>false, "defaultValue"=>0)
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

		$system_database_manager = system_database_manager::getInstance();
		if($param["id"]>0) {
			$result = $system_database_manager->executeUpdate("UPDATE `payroll_insurance_rate` SET `payroll_insurance_code_ID`=".$param["payroll_insurance_code_ID"].",`payroll_account_ID`='".$param["payroll_account_ID"]."',`Description`='".addslashes($param["Description"])."',`rate`=".$param["rate"].",`Sex`='".$param["Sex"]."',`AgeFrom`=".$param["AgeFrom"].",`AgeTo`=".$param["AgeTo"].",`CodeFrom`=".$param["CodeFrom"].",`CodeTo`=".$param["CodeTo"]." WHERE `id`=".$param["id"], "payroll_saveInsuranceRateDetail");
		}else{
			$result = $system_database_manager->executeUpdate("INSERT INTO `payroll_insurance_rate`(`payroll_insurance_code_ID`,`payroll_account_ID`,`Description`,`rate`,`Sex`,`AgeFrom`,`AgeTo`,`CodeFrom`,`CodeTo`) VALUE(".$param["payroll_insurance_code_ID"].",'".$param["payroll_account_ID"]."','".addslashes($param["Description"])."',".$param["rate"].",'".$param["Sex"]."',".$param["AgeFrom"].",".$param["AgeTo"].",".$param["CodeFrom"].",".$param["CodeTo"].")", "payroll_saveInsuranceRateDetail");
		}
		$system_database_manager->executeUpdate("Call payroll_prc_group_accounts()", "payroll_saveInsuranceRateDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteInsuranceRateDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_insurance_rate` WHERE `id`=".$param["id"], "payroll_deleteInsuranceRateDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}
	
}
?>
