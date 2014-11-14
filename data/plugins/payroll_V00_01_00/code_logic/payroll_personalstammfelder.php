<?php
class personalstammfelder {

	public function getListenWerte($personalstammListenwert) {
		$sql  = "SELECT * FROM payroll_empl_list AS L, payroll_empl_list_label AS LL ";
		$sql .= " WHERE LL.payroll_empl_list_ID = L.id AND L.id = ".$personalstammListenwert;
		$sql .= " ORDER BY id, language";
		//communication_interface::alert("getEmplListWerte($personalstammListenwert): ".$sql);
		
 		$system_database_manager = system_database_manager::getInstance();
 		$result = $system_database_manager->executeQuery($sql, "getListWerte");
 		$response["id"] = $personalstammListenwert;
 		if(count($result) > 0) {
 		 	$response["ListType"] = $result[0]["ListType"];
 		 	$response["Code"] = $result[0]["ListItemToken"];
 		 	$response["Sortierzahl"] = $result[0]["ListItemOrder"];
 		 	$response["Sprachen"] = $result[0]["language"];
 		 	$response["language_".$result[0]["language"]] = $result[0]["label"];
 		}
 		if(count($result) > 1) {
 			for ($i = 1; $i < count($result); $i++) {
 				$response["Sprachen"] = $response["Sprachen"].",".$result[$i]["language"];
 				$response["language_".$result[$i]["language"]] = $result[$i]["label"];
 			}
 		}
 		//communication_interface::alert("getEmplListWerte($personalstammListenwert): ".print_r($response, true));
 		return $response;
	}
	
	public function saveListenWerte($personalstammListenwerte, $data, $fieldName) {
		//communication_interface::alert("saveListenWerte: \npLw:".$personalstammListenwerte."\nfieldName:".print_r($fieldName,true)."\n".print_r($data,true));	
		$system_database_manager = system_database_manager::getInstance();
		$ret = 0;
		$sqlL  = "";
		$id = $data["id"];
		if ($id == "NEW") {
			//evaluiere eine neue id$
			$sqlL  = "SELECT MAX(id)+1 AS newId FROM payroll_empl_list";
			$result = $system_database_manager->executeQuery($sqlL, "getNewIdfromListenWerte");
			$id = $result[0]["newId"];
			
			//evaluiere die ListGroup zur Einordnung für das Eingabe-field
			$sql = "SELECT dataSourceGroup FROM payroll_employee_field_def WHERE fieldName = '".print_r($fieldName,true)."';";
			$result = $system_database_manager->executeQuery($sql, "get dataSourceGroup");
			$dataSourceGroup = $result[0]["dataSourceGroup"];
			
			//inserte mit neuer id und übernehme sie für die Inserts der Label			
			$sqlL  = "INSERT INTO `payroll_empl_list` (`id`,`ListGroup`,`ListType`,`ListItemOrder`,`ListItemToken`) ";
			$sqlL .= "VALUES ('".$id."', '".$dataSourceGroup."', '1', '".strip_tags($data["sort"])."', '".strip_tags($data["code"])."') ";			
		} else {
			$sqlL  = "UPDATE `payroll_empl_list` SET";
			$sqlL .= "  `ListItemOrder`='".strip_tags($data["sort"])."'";
			$sqlL .= ", `ListItemToken`='".strip_tags($data["code"])."'";
			$sqlL .= " WHERE `id`='".$id."'";
		}
		$result = $system_database_manager->executeUpdate($sqlL, "saveListenWerte");
		if ($result == true) {$ret += 1;} else {$ret -= 1; }
		//communication_interface::alert("saveListenWerte result :".print_r($result, true)."\n".$sqlL);
		
		if (strlen(trim($data["txt_de"])) > 1) {
			$z=4;
			$x = $this->saveListenWerte_Labels($z , $id, "de", $data["txt_de"], "");
			$ret += $x;
		}
				
		if (strlen(trim($data["txt_fr"])) > 1) {
			$z=8;
			$y = $this->saveListenWerte_Labels($z , $id, "fr", $data["txt_fr"], "");
			$ret += $z;
		}
		
		if ($ret > 2) {
			return true;
		} else {
			return false;
		}
		
	}
	
	private function saveListenWerte_Labels($retInkrement, $id, $sprache, $label, $tokenLabel) {
		$ret = 0;		
		$system_database_manager = system_database_manager::getInstance();
		$sqlLLWhere = " WHERE `payroll_empl_list_ID`='".$id."' and `language`='".$sprache."'";		
		$sqlLL  = "SELECT * FROM `payroll_empl_list_label` ";
		$sqlLL .= $sqlLLWhere;
		$result = $system_database_manager->executeQuery($sqlLL, "getListWerte");
		if (count($result) < 1) {
			$sqlLL  = "INSERT INTO `payroll_empl_list_label` ";
			$sqlLL .= "(`payroll_empl_list_ID`, `language`, `label`, `tokenLabel`) VALUES ";
			$sqlLL .= "('".$id."', '".$sprache."', '".strip_tags($label)."', '');";
		} else {
			$sqlLL  = "UPDATE `payroll_empl_list_label` SET";
			$sqlLL .= " `label`='".strip_tags($label)."'";
			$sqlLL .= $sqlLLWhere;
		}
		$result = $system_database_manager->executeUpdate($sqlLL, "saveListenWerte");
		if ($result == true) {$ret += $retInkrement;} 
		//communication_interface::alert("saveListenWerte result=$ret\n".print_r($result, true)."\n".$sqlLL);
		return $ret;					
	}

   
}

?>
