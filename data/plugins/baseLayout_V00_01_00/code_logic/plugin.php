<?php
class baseLayout_BL {

	public function sysListener($functionName, $functionParameters) {
/*
		switch($functionName) {
		case 'baseLayout.changePassword':
			return $response;
			break;
		default:
			return "Funktion unbekannt";
			break;
		}
*/
	}

	public function sysPermission($getContent="") {
		if($getContent!="") {
			//systemUser
			$permissions["systemUser"]["userAdminEnabled"]["title"] = "Benutzerverwaltung freigeben";
			$permissions["systemUser"]["userAdminEnabled"]["type"] = "bool";
			$permissions["systemUser"]["userAdminEnabled"]["default"] = false;

			$permissions["systemUser"]["userListDisplay"]["title"] = "Benutzerliste anzeigen";
			$permissions["systemUser"]["userListDisplay"]["type"] = "bool";
			$permissions["systemUser"]["userListDisplay"]["default"] = true;

			$permissions["systemUser"]["userDetailDisplay"]["title"] = "Benutzerdetails anzeigen";
			$permissions["systemUser"]["userDetailDisplay"]["type"] = "bool";
			$permissions["systemUser"]["userDetailDisplay"]["default"] = true;

			$permissions["systemUser"]["userAdd"]["title"] = "Benutzer hinzufügen";
			$permissions["systemUser"]["userAdd"]["type"] = "bool";
			$permissions["systemUser"]["userAdd"]["default"] = true;

			$permissions["systemUser"]["userEdit"]["title"] = "Benutzer ändern";
			$permissions["systemUser"]["userEdit"]["type"] = "bool";
			$permissions["systemUser"]["userEdit"]["default"] = true;

			$permissions["systemUser"]["userDelete"]["title"] = "Benutzer löschen";
			$permissions["systemUser"]["userDelete"]["type"] = "bool";
			$permissions["systemUser"]["userDelete"]["default"] = true;

			$permissions["systemUser"]["groupListDisplay"]["title"] = "Gruppenliste anzeigen";
			$permissions["systemUser"]["groupListDisplay"]["type"] = "bool";
			$permissions["systemUser"]["groupListDisplay"]["default"] = true;

			$permissions["systemUser"]["groupDetailDisplay"]["title"] = "Gruppendetails anzeigen";
			$permissions["systemUser"]["groupDetailDisplay"]["type"] = "bool";
			$permissions["systemUser"]["groupDetailDisplay"]["default"] = true;

			$permissions["systemUser"]["groupAdd"]["title"] = "Gruppe hinzufügen";
			$permissions["systemUser"]["groupAdd"]["type"] = "bool";
			$permissions["systemUser"]["groupAdd"]["default"] = true;

			$permissions["systemUser"]["groupEdit"]["title"] = "Gruppe ändern";
			$permissions["systemUser"]["groupEdit"]["type"] = "bool";
			$permissions["systemUser"]["groupEdit"]["default"] = true;

			$permissions["systemUser"]["groupDelete"]["title"] = "Gruppe löschen";
			$permissions["systemUser"]["groupDelete"]["type"] = "bool";
			$permissions["systemUser"]["groupDelete"]["default"] = true;

			//systemConfig
			$permissions["systemConfig"]["configEnabled"]["title"] = "Konfiguration freigeben";
			$permissions["systemConfig"]["configEnabled"]["type"] = "bool";
			$permissions["systemConfig"]["configEnabled"]["default"] = false;

			return $permissions[$getContent];
		}else{
//			return array(array("permissionItemAdd","permissionSystem","System","JS:alert('test1');"), array("permissionSubItemAdd","permissionSystem","systemUser","Benutzerverwaltung","JS:alert('test2');"), array("permissionSubItemAdd","permissionSystem","systemConfig","Konfiguration","JS:alert('test3');") );
			return array(array("permissionItemAdd","permissionSystem","System",""), array("permissionSubItemAdd","permissionSystem","systemUser","Benutzerverwaltung",""), array("permissionSubItemAdd","permissionSystem","systemConfig","Konfiguration","") );
		}
	}

	public function sysConfig($getContent=false) {
		if($getContent) {
		}else{
			return array(array("configItemAdd","generalSettings","Allg. Einstellungen","JS:alert('test1');"), array("configSubItemAdd","generalSettings","generalSettingsFormats","Datums- und Zahlenformate","JS:alert('test2');") );
		}
	}
}

$SYS_PLUGIN["bl"]["baseLayout"] = new baseLayout_BL();
?>
