<?php

class coreFunctions_UI {

	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;
		global $SYS_PLUGIN;

		switch($functionName) {
		case 'coreFunctions.getConfigMenu':
			$blResponse = blFunctionCall('coreFunctions.getPluginList');

			foreach($blResponse["pluginList"] as $pluginName) {
				//Call a local function
				$pluginFile = 'plugins/'.$pf[0]."_".$aafwConfig["plugins"][$pluginName]["currentVersion"].'/code_ui/plugin.php';
				if(file_exists($pluginFile)) {
					require_once($pluginFile);
					$answer = $SYS_PLUGIN["ui"][$pluginName]->sysConfig();
					foreach($answer as $menuItem) {
						switch($menuItem[0]) {
						case 'configItemAdd':
							$menuMap[$menuItem[1]]["label"] = $menuItem[2];
							$menuMap[$menuItem[1]]["functionCall"] = $menuItem[3];
							$menuMap[$menuItem[1]]["subitems"] = array();
							break;
						case 'configSubItemAdd':
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["label"] = $menuItem[3];
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["functionCall"] = $menuItem[4];
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["subitems"] = array();
							break;
						}
					}
//			answer = array(array("configItemAdd","generalSettings","Allg. Einstellungen","JS:alert('test1');"), array("configSubItemAdd","generalSettings","generalSettingsFormats","Datums- und Zahlenformate","JS:alert('test2');"), );
				}

			}
			return $menuMap;
			break;
		case 'coreFunctions.getPermissionContent':
			$pluginName = $functionParameters[0];
			$permissionSection = $functionParameters[1];
			$core_group_ID = 2;

			$blResponse = blFunctionCall('coreFunctions.getGroupInformation', $core_group_ID, 'ModulePermission/'.$pluginName, '*');
//			return "<div>*".$pluginName."*".$permissionSection."*</div>";
/*
coreFunctions.getGroupInformation
$core_group_ID = $functionParameters[0];
$registryPath = $functionParameters[1];
$registryName = $functionParameters[2];

$blResponse = blFunctionCall('coreFunctions.getGroupInformation', $core_group_ID, 'ModulePermission/baseLayout', '*');
GROUPS/2/ModulePermission/baseLayout

communication_interface::alert(print_r($blResponse,true));
Array
(
    [success] => 1
    [errCode] => 0
    [errText] => 
    [data] => Array
	(
		[0] => Array
			(
				[name] => configEnabled
				[value] => 0
			)
		)
	)
*/
			if($blResponse["success"]) {
				foreach($blResponse["data"] as $data) {
					$modulePermissionData[$data["name"]] = $data["value"];
				}
			}

			$permissionContent = '<div id="prmsct_'.$permissionSection.'"><input type="hidden" name="marker_'.$pluginName.'_'.$permissionSection.'" value="1">';
			$permissionItems = "";

			//Call a local function
			$pluginFile = 'plugins/'.$pluginName."_".$aafwConfig["plugins"][$pluginName]["currentVersion"].'/code_logic/plugin.php';
			if(file_exists($pluginFile)) {
				require_once($pluginFile);
				$answer = $SYS_PLUGIN["bl"][$pluginName]->sysPermission($permissionSection);
				foreach($answer as $prmItemName => $prmItemValues) {
					switch($prmItemValues["type"]) {
					case 'bool':
						if(isset($modulePermissionData[$prmItemName])) $checked = $modulePermissionData[$prmItemName]==1 ? ' checked' : '';
						else $checked = $prmItemValues["default"] ? ' checked' : '';
						$permissionItems .= '<li><label for="'.$pluginName.'_'.$prmItemName.'">'.$prmItemValues["title"].'</label><input type="checkbox" name="'.$pluginName.'_'.$prmItemName.'" value="1"'.$checked.' /></li>';
						break;
					}
				}
			}
//			$permissions["systemUser"]   ["enabled"]["title"] = "Benutzerverwaltung freigeben";
//			$permissions["systemUser"]   ["enabled"]["type"] = "bool";
//			$permissions["systemUser"]   ["enabled"]["default"] = false;

			if($permissionItems != "") $permissionItems = '<ul>'.$permissionItems.'</ul>';

			$permissionContent .= $permissionItems.'</div>';

			return $permissionContent;
			break;
		case 'coreFunctions.getPermissionMenu':
			$blResponse = blFunctionCall('coreFunctions.getPluginList');

			foreach($blResponse as $data) {
				$pluginName = $data["name"];

				//Call a local function
				$pluginFile = 'plugins/'.$pluginName."_".$aafwConfig["plugins"][$pluginName]["currentVersion"].'/code_logic/plugin.php';
				if(file_exists($pluginFile)) {
					require_once($pluginFile);
					$answer = $SYS_PLUGIN["bl"][$pluginName]->sysPermission();
					foreach($answer as $menuItem) {
						switch($menuItem[0]) {
						case 'permissionItemAdd':
							$menuMap[$menuItem[1]]["label"] = $menuItem[2];
							$menuMap[$menuItem[1]]["functionCall"] = $menuItem[3];
							$menuMap[$menuItem[1]]["pluginName"] = $pluginName;
							$menuMap[$menuItem[1]]["subitems"] = array();
							break;
						case 'permissionSubItemAdd':
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["label"] = $menuItem[3];
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["functionCall"] = $menuItem[4];
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["pluginName"] = $pluginName;
							$menuMap[$menuItem[1]]["subitems"][$menuItem[2]]["subitems"] = array();
							break;
						}
					}
//			answer = array(array("configItemAdd","generalSettings","Allg. Einstellungen","JS:alert('test1');"), array("configSubItemAdd","generalSettings","generalSettingsFormats","Datums- und Zahlenformate","JS:alert('test2');"), );
				}
			}
			return $menuMap;
			break;
		default:
			return "Funktion unbekannt";
			break;
		}
	}
}

$SYS_PLUGIN["ui"]["coreFunctions"] = new coreFunctions_UI();

?>
