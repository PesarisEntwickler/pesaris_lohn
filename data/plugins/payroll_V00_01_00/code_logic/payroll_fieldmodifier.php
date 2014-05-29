<?php
class fieldmodifier {
	
	public function getFieldModifierList() {
		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT modif.`id`,modif.`payroll_account_ID`,modif.`payroll_empl_filter_ID`,flt.`FilterName`,modif.`processing_order`,modif.`ModifierType`,modif.`TargetField`,modif.`FieldName`,fldlbl.`label` as FieldNameUlLabel,modif.`TargetValue`,modif.`max_limit`,modif.`deduction`,modif.`min_limit` FROM `payroll_calculation_modifier` modif INNER JOIN `payroll_empl_filter` flt ON flt.`id`=modif.`payroll_empl_filter_ID` LEFT JOIN `payroll_employee_field_label` fldlbl ON modif.`FieldName`=fldlbl.`fieldName` AND fldlbl.`language`='".session_control::getSessionInfo("language")."' ORDER BY modif.`payroll_account_ID`,modif.`ModifierType`,modif.`TargetField`,modif.`processing_order`", "payroll_getFieldModifierList");

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

	public function getFieldModifierDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery("SELECT * FROM `payroll_calculation_modifier` WHERE `id`=".$param["id"], "payroll_getFieldModifierDetail");

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

	public function saveFieldModifierDetail($param) {
		$fieldDef = array(
					"id"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>true),
					"payroll_account_ID"=>array("regex"=>"/^[0-9a-zA-Z]{1,5}$/", "isText"=>true, "isID"=>false),
					"payroll_empl_filter_ID"=>array("regex"=>"/^[0-9]{1,9}$/", "isText"=>false, "isID"=>false),
					"processing_order"=>array("regex"=>"/^[0-9]{1,2}$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"ModifierType"=>array("regex"=>"/^[12]{1,1}$/", "isText"=>false, "isID"=>false),
					"FieldName"=>array("regex"=>"/^[-_0-9a-zA-Z]{0,30}$/", "isText"=>true, "isID"=>false),
					"TargetField"=>array("regex"=>"/^[0345]{1,1}$/", "isText"=>false, "isID"=>false),
					"TargetValue"=>array("regex"=>"/^-?[0-9]{1,8}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"max_limit"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"min_limit"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"deduction"=>array("regex"=>"/^-?[0-9]{1,7}(\.[0-9]{1,5})?$/", "isText"=>false, "isID"=>false, "defaultValue"=>0),
					"OverwriteOnly"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"major_period"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"minor_period"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false),
					"major_period_bonus"=>array("regex"=>"/^[01]{1,1}$/", "isText"=>false, "isID"=>false)
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

		if(trim($param["FieldName"])!="") $param["TargetValue"]=0;
		if($param["ModifierType"]==2) {
			$param["TargetValue"]=0;
			$param["FieldName"]="";
		}else{
			$param["max_limit"]=0;
			$param["min_limit"]=0;
			$param["deduction"]=0;
		}

		$system_database_manager = system_database_manager::getInstance();
		if($param["id"]>0) {
			$result = $system_database_manager->executeUpdate("UPDATE `payroll_calculation_modifier` SET `payroll_account_ID`='".addslashes($param["payroll_account_ID"])."',`payroll_empl_filter_ID`=".$param["payroll_empl_filter_ID"].",`processing_order`=".$param["processing_order"].",`ModifierType`=".$param["ModifierType"].",`FieldName`='".addslashes($param["FieldName"])."',`TargetField`=".$param["TargetField"].",`TargetValue`=".$param["TargetValue"].",`max_limit`=".$param["max_limit"].",`min_limit`=".$param["min_limit"].",`deduction`=".$param["deduction"].",`major_period`=".$param["major_period"].",`minor_period`=".$param["minor_period"].",`major_period_bonus`=".$param["major_period_bonus"].",`OverwriteOnly`=".$param["OverwriteOnly"]." WHERE `id`=".$param["id"], "payroll_saveFieldModifierDetail");
		}else{
			$result = $system_database_manager->executeUpdate("INSERT INTO `payroll_calculation_modifier`(`payroll_account_ID`,`payroll_empl_filter_ID`,`processing_order`,`ModifierType`,`FieldName`,`TargetField`,`TargetValue`,`max_limit`,`min_limit`,`deduction`,`major_period`,`minor_period`,`major_period_bonus`,`OverwriteOnly`) VALUES('".addslashes($param["payroll_account_ID"])."',".$param["payroll_empl_filter_ID"].",".$param["processing_order"].",".$param["ModifierType"].",'".addslashes($param["FieldName"])."',".$param["TargetField"].",".$param["TargetValue"].",".$param["max_limit"].",".$param["min_limit"].",".$param["deduction"].",".$param["major_period"].",".$param["minor_period"].",".$param["major_period_bonus"].",".$param["OverwriteOnly"].")", "payroll_saveFieldModifierDetail");
		}
		
		require_once('changeManager.php');
		$changeManager = new changeManager();				
		$changeManager->changeManager("modifierChange", array("payroll_empl_filter_ID" => array(), "activeTransaction"=>false));

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	public function deleteFieldModifierDetail($param) {
		if(!preg_match( '/^[0-9]{1,9}$/', $param["id"])) {
			$response["success"] = false;
			$response["errCode"] = 10;
			$response["errText"] = "invalid id";
			return $response;
		}

		$system_database_manager = system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("DELETE FROM `payroll_calculation_modifier` WHERE `id`=".$param["id"], "payroll_deleteFieldModifierDetail");

		$response["success"] = true;
		$response["errCode"] = 0;
		return $response;
	}

	
}
?>
