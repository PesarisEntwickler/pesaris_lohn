<?php

class baseLayout_UI {

	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;

//$fp = fopen('/usr/local/www/data/pesaris.com/p2/dbg.txt', 'a');
//fwrite($fp, date("Y-m-d H:i:s")." -> ".$functionName."\n");
//fclose($fp);
		switch($functionName) {
		case 'baseLayout.changePassword':
			if(sizeof($functionParameters) > 0) {
				//Mandatory and validity checks...
				$err = "";
				if(trim($functionParameters[0]["oldPwd"])=="" || trim($functionParameters[0]["newPwd1"])=="" || trim($functionParameters[0]["newPwd2"])=="") $err = "blChPwdErrEmpty";
				if($err == "" && $functionParameters[0]["newPwd1"] != $functionParameters[0]["newPwd2"]) $err = "blChPwdErrDifferent";
				if($err == "" && strlen($functionParameters[0]["newPwd1"]) < 6) $err = "blChPwdErrTooShort";

				if($err == "") {
//communication_interface::alert('UID:'.session_control::getSessionInfo("id"));
					$blResponse = blFunctionCall('coreFunctions.changePassword',session_control::getSessionInfo("id"),$functionParameters[0]["oldPwd"],$functionParameters[0]["newPwd1"]);
					if(!$blResponse["success"]) {
						switch($blResponse["errCode"]) {
						case 101:
							$err = "blChPwdErrOldPW";
							break;
						case 102:
							$err = "blChPwdErrInvalidCharacter";
							break;
						default:
							$err = "blChPwdErrUnknown";
							break;
						}
					}
				}

				if($err == "") {
					communication_interface::jsExecute("$('#modalContainer').mb_close();");
				}else{
					$objWGUI = new wgui("baseLayout", "main");
					communication_interface::assign('baseLayoutPwdErr', 'style.borderColor', '#F00');
					communication_interface::assign('baseLayoutPwdErr', 'style.backgroundColor', '#FCC');
					communication_interface::assign('baseLayoutPwdErr', 'innerHTML', $objWGUI->getText($err));
				}
			}else{
				$objWindow = new wgui_window("baseLayout", "modalWindow");
				$objWindow->windowTitle($objWindow->getText("blChPwdTitle"));
				$objWindow->windowIcon("key48x48.png");
				$objWindow->windowWidth(455);
				$objWindow->windowHeight(200);
				$objWindow->modal(true);
				$objWindow->loadContent("changePassword",$data,"main"); // <-- funktioniert auch einwandfrei!!
				$objWindow->showWindow();
				communication_interface::jsExecute("document.getElementById('oldPwd').focus();");
			}
			break;
		case 'baseLayout.userManager':
				$objWindow = new wgui_window("baseLayout", "userManager");
//				$objWindow->windowTitle("Benutzerverwaltung");
				$objWindow->windowTitle($objWindow->getText("blUserAdmTitle")); // <--- wenn Text dynamisch eingefügt wird, wird das Template nicht korrekt kompiliert... (ohne Labels...)
				$objWindow->windowIcon("users48x48.png");
				$objWindow->windowWidth(600);
				$objWindow->windowHeight(400);
				$objWindow->dockable(true);
				$objWindow->fullscreen(true);
				$objWindow->loadContent("userAdmin",$data,"main");
				$objWindow->addEventFunction_onResize("$('#useradmTableUsers_wrapper .dataTables_scrollBody').height( $('#useradmTabs').height() - 160 );");
				$objWindow->addEventFunction_onResize("$('#useradmTableUsers').dataTable().fnAdjustColumnSizing();");
				$objWindow->addEventFunction_onResize("$('#useradmTableGroups_wrapper .dataTables_scrollBody').height( $('#useradmTabs').height() - 160 );");
				$objWindow->addEventFunction_onResize("$('#useradmTableGroups').dataTable().fnAdjustColumnSizing();");
				$objWindow->showWindow();
				communication_interface::jsExecute("$('#useradmTabs').tabs();");
				communication_interface::jsExecute("$('#useradmTabs').css('height', '96%');");
				communication_interface::jsExecute("$('#useradmTableUsers').dataTable({\"bJQueryUI\": true, \"aoColumns\": [ { \"sWidth\": \"20px\", \"sClass\": \"center\", \"bSortable\": false }, { \"sWidth\": \"30%\" }, { \"sWidth\": \"30%\" }, { \"sWidth\": \"30%\" }, { \"sWidth\": \"10%\" }, { \"sWidth\": \"20px\", \"sClass\": \"center\", \"bSortable\": false }, { \"sWidth\": \"20px\", \"sClass\": \"center\", \"bSortable\": false } ], \"bAutoWidth\": false, \"bScrollInfinite\": false, \"bScrollCollapse\": false, \"sScrollY\": \"200px\", \"bLengthChange\": false, \"bPaginate\": false, \"oLanguage\": { \"sZeroRecords\": \"".$objWindow->getText("blUserAdmUsersZeroRecords")."\", \"sInfo\": \"".$objWindow->getText("blUserAdmUsersInfo")."\", \"sInfoEmpty\": \"".$objWindow->getText("blUserAdmUsersInfoEmpty")."\", \"sInfoFiltered\": \"".$objWindow->getText("blUserAdmUsersInfoFiltered")."\", \"sSearch\": \"".$objWindow->getText("blUserAdmUsersSearch")."\" }});");
				communication_interface::jsExecute("$('#useradmTableGroups').dataTable({\"bJQueryUI\": true, \"aoColumns\": [ { \"sWidth\": \"40%\" }, { \"sWidth\": \"40%\" }, { \"sWidth\": \"20%\" }, { \"sWidth\": \"20px\", \"sClass\": \"center\", \"bSortable\": false }, { \"sWidth\": \"20px\", \"sClass\": \"center\", \"bSortable\": false } ], \"bAutoWidth\": false, \"bScrollInfinite\": false, \"bScrollCollapse\": false, \"sScrollY\": \"200px\", \"bLengthChange\": false, \"bPaginate\": false, \"oLanguage\": { \"sZeroRecords\": \"".$objWindow->getText("blUserAdmGrpsZeroRecords")."\", \"sInfo\": \"".$objWindow->getText("blUserAdmGrpsInfo")."\", \"sInfoEmpty\": \"".$objWindow->getText("blUserAdmGrpsInfoEmpty")."\", \"sInfoFiltered\": \"".$objWindow->getText("blUserAdmGrpsInfoFiltered")."\", \"sSearch\": \"".$objWindow->getText("blUserAdmGrpsSearch")."\" }});");
//				communication_interface::jsExecute("$('#useradmTabs').bind('tabsselect', function(event, ui) { var curTable = $('#useradmTabs').tabs('option', 'selected')==0 ? '#useradmTableUsers' : '#useradmTableGroups'; $(curTable).dataTable().fnAdjustColumnSizing();"); //window.setTimeout('$(\\''+curTable+'\\').dataTable().fnAdjustColumnSizing();', 500) });
				communication_interface::jsExecute("$('#useradmTabs').bind('tabsselect', function(event, ui) { var curTable = $('#useradmTabs').tabs('option', 'selected')==1 ? '#useradmTableUsers' : '#useradmTableGroups'; window.setTimeout('$(\\''+curTable+'\\').dataTable().fnAdjustColumnSizing();', 500) });");

				$listData = "";
				$blUserList = blFunctionCall('coreFunctions.getUserList','uid');
				foreach($blUserList["data"] as $singleUser) {
					$statusImg = "bullet_grey16x16.png";
//					$statusImg = "bullet_green16x16.png";
					if($singleUser["active"] == 0) $statusImg = "bullet_red16x16.png";
					$listData .= ($listData == "" ? "" : ", ")."['<img src=\"web/img/$statusImg\"/>','".$singleUser["uid"]."','".$singleUser["full_name"]."','".$singleUser["email"]."','".$singleUser["language"]."','<img src=\"web/img/edit16x16.png\" onclick=\"cb(\\'baseLayout.userEditor\\',".$singleUser["id"].");\"/>','<img src=\"web/img/delete16x16.png\" onclick=\"cb(\\'baseLayout.deleteUser\\',".$singleUser["id"].");\"/>']";
//					$listData .= ($listData == "" ? "" : ", ")."['<img src=\"web/img/$statusImg\"/>','".$singleUser["uid"]."','".$singleUser["full_name"]."','".$singleUser["email"]."','".$singleUser["language"]."','<img src=\"web/img/edit16x16.png\"/>','<img src=\"web/img/delete16x16.png\"/>']";
				}
				if($listData != "") communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnAddData( [ $listData ] );");

				$listData = "";
				$blGroupList = blFunctionCall('coreFunctions.getGroupList','name');
				foreach($blGroupList["data"] as $singleGroup) {
					$listData .= ($listData == "" ? "" : ", ")."['".$singleGroup["name"]."','".$singleGroup["description"]."','".($singleGroup["active"] == 1 ? 'aktiv' : 'inaktiv')."','<img src=\"web/img/edit16x16.png\" onclick=\"cb(\\'baseLayout.groupEditor\\',".$singleGroup["id"].");\"/>','<img src=\"web/img/delete16x16.png\" onclick=\"cb(\\'baseLayout.deleteGroup\\',".$singleGroup["id"].");\"/>']";
				}
				if($listData != "") communication_interface::jsExecute("$('#useradmTableGroups').dataTable().fnAddData( [ $listData ] );");

				communication_interface::jsExecute("$('#useradmTableUsers_wrapper .ui-widget-header:first').prepend('<button onclick=\"cb(\\'baseLayout.userEditor\\');\">".$objWindow->getText("blUserAdmBtnNewUser")."</button>');");
				communication_interface::jsExecute("$('#useradmTableGroups_wrapper .ui-widget-header:first').prepend('<button onclick=\"cb(\\'baseLayout.groupEditor\\');\">".$objWindow->getText("blUserAdmBtnNewGroup")."</button>');");
			break;
		case 'baseLayout.userEditor':
			if(count($functionParameters) > 0) {
				$data = $this->prepareUserEditorData($functionParameters[0]);
				$windowTitle = "blUserEditorTitleEdit";
			}else{
				$data = $this->prepareUserEditorData();
				$windowTitle = "blUserEditorTitleNew";
			}

			$objWindow = new wgui_window("baseLayout", "modalWindow");
			$objWindow->windowTitle($objWindow->getText($windowTitle));
			$objWindow->windowIcon("users48x48.png");
			$objWindow->windowWidth(550);
			$objWindow->windowHeight(420);
			$objWindow->modal(true);
			$objWindow->loadContent("userAdmin",$data,"userEditor");
			$objWindow->showWindow();
			communication_interface::jsExecute("$('#userEditorTabs').tabs();");
			communication_interface::jsExecute("toggleTimeRestrictionFields();");
			communication_interface::jsExecute("toggleIpRestrictionFields();");
			communication_interface::jsExecute("document.getElementById('uid').focus();");
			communication_interface::jsExecute("$('.csUserEditorGroups').crossSelect({listWidth: 165,clicksAccumulate: true,select_txt: \">\",remove_txt: \"<\",selectAll_txt: \">>\",removeAll_txt: \"<<\"});");
			communication_interface::jsExecute("$('#userEditorTabs .jqxs_optionsList li').width(165);");
			communication_interface::jsExecute("$('#userEditorTabs .jqxs_chosenList li').width(165);");
			communication_interface::jsExecute("$('#userEditorTabs .jqxs ul').height(150);");
			communication_interface::jsExecute("$('#userEditorTabs').css('height', '310');");

			break;
		case 'baseLayout.groupEditorGetPermissions':
			$permissionContent = uiFunctionCall('coreFunctions.getPermissionContent', $functionParameters[0], $functionParameters[1]);
//			communication_interface::jsExecute("$('#gePermissionContainer').append('<div>".$functionParameters[0]."/".$functionParameters[1]."</div>');");
			communication_interface::jsExecute("$('#gePermissionContainer').append('".$permissionContent."');");
//			communication_interface::jsExecute("alert('".$functionParameters[0]."');");
			break;
		case 'baseLayout.groupEditor':
			$mapConfigMenu = uiFunctionCall('coreFunctions.getPermissionMenu');
			$modulePermissionMenu = '<option id="top">--Modul wählen--</option>';
			foreach($mapConfigMenu as $mainMenuItemName => $mainMenuItem) {
				if(count($mainMenuItem["subitems"])>0) {
					//there are submenu items
					$modulePermissionMenu .= '<optgroup id="'.$mainMenuItemName.'" label="'.$mainMenuItem["label"].'">';
					foreach($mainMenuItem["subitems"] as $subMenuItemName => $subMenuItem) {
						if($mainMenuItem["functionCall"] != "") {
							$curAction = $mainMenuItem["functionCall"];
						}else{
							$curAction = "if($('#prmsct_".$subMenuItemName."').length == 0) cb('baseLayout.groupEditorGetPermissions', '".$subMenuItem["pluginName"]."', '".$subMenuItemName."'); $('#gePermissionContainer div').hide(); $('#prmsct_".$subMenuItemName."').show();";
						}
						$modulePermissionMenu .= '<option id="'.$subMenuItemName.'" label="'.$subMenuItem["label"].'" onclick="'.$curAction.'">'.$subMenuItem["label"].'</option>';
					}
					$modulePermissionMenu .= '</optgroup>';
				}else{
					//there are NO submenu items
					$modulePermissionMenu .= '<option id="'.$mainMenuItemName.'" label="'.$mainMenuItem["label"].'">'.$mainMenuItem["label"].'</option>';
				}
			}
			$data["moduleList"] = $modulePermissionMenu;
//			$data["moduleList"] = '<option>'.serialize($mapConfigMenu).'</option>';

			if(count($functionParameters) > 0) {
//				$data = $this->prepareUserEditorData($functionParameters[0]);
//				$blUserSettingsFull = blFunctionCall('coreFunctions.getUserSettings',$core_user_ID);
				$blGroupSettings = blFunctionCall('coreFunctions.getGroupSettings',$functionParameters[0]);
				$data["id"] = $blGroupSettings["data"]["id"];
				$data["name"] = $blGroupSettings["data"]["name"];
				$data["description"] = $blGroupSettings["data"]["description"];
				$data["inactive"] = $blGroupSettings["data"]["active"]==1 ? '' : ' checked';
				$windowTitle = "blGroupEditorTitleEdit";
			}else{
//				$data = $this->prepareUserEditorData();
				$data["id"] = "0";
				$windowTitle = "blGroupEditorTitleNew";
			}
/*
<button id="btnGroupDataSubmit" onclick="cb('baseLayout.saveGroupEditorData',xajax.getFormValues('groupEditForm',1));">Speichern</button>
<wgui:var name="id"/>
<wgui:var name="name"/>
<wgui:var name="description"/>
<wgui:var name="inactive"/>
*/
			$objWindow = new wgui_window("baseLayout", "modalWindow");
			$objWindow->windowTitle($objWindow->getText($windowTitle));
			$objWindow->windowIcon("users48x48.png");
			$objWindow->windowWidth(550);
			$objWindow->windowHeight(420);
			$objWindow->modal(true);
			$objWindow->loadContent("userAdmin",$data,"groupEditor");
			$objWindow->showWindow();
			communication_interface::jsExecute("$('#groupEditorTabs').tabs();");
			communication_interface::jsExecute("document.getElementById('name').focus();");
			communication_interface::jsExecute("$('#groupEditorTabs').css('height', '310');");
			break;
		case 'baseLayout.saveGroupEditorData':
			$savePermissionReturn = blFunctionCall('coreFunctions.saveGroupPermission',$functionParameters[0]);

			$coreData = array();
			$fieldMapping = array(	"rec_id" => "id",
						"name" => "name",
						"description" => "description",
						"inactive" => "active"
			);
			foreach($fieldMapping as $in => $out) $forwardParam[$out] = $functionParameters[0][$in];
			$forwardParam["active"] = $forwardParam["active"]==1 ? 0 : 1;

//$dummy = "";
//foreach($functionParameters[0] as $key => $value) $dummy .= $key.", ";
//communication_interface::alert($dummy);

			$blSaveResult = blFunctionCall('coreFunctions.saveGroupSettings',$forwardParam);
			if(!$blSaveResult["success"]) {
				switch($blSaveResult["errCode"]) {
				case 105:
					$tabIndex = 0;
					$fieldName = "name";
					$errTxt = "Gruppenname darf nicht leer sein.";
					break;
				case 106:
					$tabIndex = 0;
					$fieldName = "name";
					$errTxt = "Ungültige Eingabe in Feld 'Gruppenname'.";
					break;
				case 111:
					$tabIndex = 0;
					$fieldName = "description";
					$errTxt = "Ungültige Eingabe in Feld 'Beschreibung'.";
					break;
				default:
					$fieldName = "";
					$errTxt = "Unbekannter Fehler (#".$blSaveResult["errCode"].")";
					break;
				}
				communication_interface::assign("baseLayoutUaErr","innerHTML",$errTxt);
				communication_interface::assign("baseLayoutUaErr","style.visibility","visible");
				if($fieldName != "") {
					communication_interface::jsExecute("$('#groupEditorTabs').tabs('select', $tabIndex);");
					communication_interface::jsExecute("for(var tmpBlink=0;tmpBlink<3;tmpBlink++) { $('#$fieldName').animate({ backgroundColor: '#F00' }, 500 ); $('#$fieldName').animate({ backgroundColor: '#FFF' }, 500 ); }");
					communication_interface::jsExecute("$('#$fieldName').focus();");
				}

			}else{
/*
				if($functionParameters[0]["ueListChange"] == "1") {
					communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnClearTable();");
					$listData = "";
					$blUserList = blFunctionCall('coreFunctions.getUserList','uid');
					foreach($blUserList["data"] as $singleUser) {
						$statusImg = "bullet_grey16x16.png";
						if($singleUser["active"] == 0) $statusImg = "bullet_red16x16.png";
						$listData .= ($listData == "" ? "" : ", ")."['<img src=\"web/img/$statusImg\"/>','".$singleUser["uid"]."','".$singleUser["full_name"]."','".$singleUser["email"]."','".$singleUser["language"]."','<img src=\"web/img/edit16x16.png\" onclick=\"cb(\\'baseLayout.userEditor\\',".$singleUser["id"].");\"/>','<img src=\"web/img/delete16x16.png\" onclick=\"cb(\\'baseLayout.deleteUser\\',".$singleUser["id"].");\"/>']";
					}
					if($listData != "") {
						communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnAddData( [ $listData ] );");
						communication_interface::jsExecute("window.setTimeout(\"$('#useradmTableUsers').dataTable().fnAdjustColumnSizing();\", 500);");
					}
				}
*/

				communication_interface::jsExecute("$('#modalContainer').mb_close();");
			}
			break;
		case 'baseLayout.saveUserEditorData':
			$fieldMapping = array(	"rec_id" => "core_user_ID",
						"uid" => "uid",
						"full_name" => "full_name",
						"email" => "email",
						"language" => "language",
						"inactive" => "active",
						"timeout_minutes" => "timeout_minutes",
						"force_pwd_change" => "force_pwd_change",
						"pwd_change_period" => "pwd_change_period",
						"disallow_pwd_change" => "allow_pwd_change"
			);
			$securityFields = array("timeRestriction", "bMon", "bTue", "bWed", "bThu", "bFri", "bSat", "bSun", "timeFrom", "timeUntil", "ipRestriction", "ipInclude1", "subnetInclude1", "ipInclude2", "subnetInclude2", "ipInclude3", "subnetInclude3", "ipInclude4", "subnetInclude4", "ipInclude5", "subnetInclude5");
			foreach($fieldMapping as $in => $out) $forwardParam[$out] = $functionParameters[0][$in];
			$forwardParam["groupAssignments"] = array();
			foreach($functionParameters[0]["ueGrpSelect"] as $key => $value) $forwardParam["groupAssignments"][] = $value;
			$forwardParam["securitySettings"] = array();
			foreach($securityFields as $securityItem) $forwardParam["securitySettings"][$securityItem] = $functionParameters[0][$securityItem];

			$forwardParam["active"] = $forwardParam["active"]==1 ? 0 : 1;
			$forwardParam["allow_pwd_change"] = $forwardParam["allow_pwd_change"]==1 ? 0 : 1;
			$forwardParam["force_pwd_change"] = $forwardParam["force_pwd_change"]==1 ? 1 : 0;
			$forwardParam["pwd_change_period"] = $forwardParam["pwd_change_period"]*7;

			$blSaveResult = blFunctionCall('coreFunctions.saveUserSettings',$forwardParam);
			if(!$blSaveResult["success"]) {
				switch($blSaveResult["errCode"]) {
				case 105:
					$tabIndex = 0;
					$fieldName = "uid";
					$errTxt = "Benutzername darf nicht leer sein.";
					break;
				case 106:
					$tabIndex = 0;
					$fieldName = "uid";
					$errTxt = "Ungültige Eingabe in Feld 'Benutzername'.";
					break;
				case 110:
					$tabIndex = 0;
					$fieldName = "full_name";
					$errTxt = "'Nachname, Vorname' darf nicht leer sein.";
					break;
				case 111:
					$tabIndex = 0;
					$fieldName = "full_name";
					$errTxt = "Ungültige Eingabe in Feld 'Nachname, Vorname'.";
					break;
				case 115:
					$tabIndex = 0;
					$fieldName = "email";
					$errTxt = "'E-Mailadresse' darf nicht leer sein.";
					break;
				case 116:
					$tabIndex = 0;
					$fieldName = "email";
					$errTxt = "Ungültige Eingabe in Feld 'E-Mailadresse'.";
					break;
				case 130:
					$tabIndex = 0;
					$fieldName = "timeout_minutes";
					$errTxt = "'Autom. abmelden nach' darf nicht leer sein.";
					break;
				case 131:
					$tabIndex = 0;
					$fieldName = "timeout_minutes";
					$errTxt = "Ungültige Eingabe in Feld 'Autom. abmelden nach'.";
					break;
				default:
					$fieldName = "";
					$errTxt = "Unbekannter Fehler";
					break;
				}
				communication_interface::assign("baseLayoutUaErr","innerHTML",$errTxt);
				communication_interface::assign("baseLayoutUaErr","style.visibility","visible");
				if($fieldName != "") {
					communication_interface::jsExecute("$('#userEditorTabs').tabs('select', $tabIndex);");
					communication_interface::jsExecute("for(var tmpBlink=0;tmpBlink<3;tmpBlink++) { $('#$fieldName').animate({ backgroundColor: '#F00' }, 500 ); $('#$fieldName').animate({ backgroundColor: '#FFF' }, 500 ); }");
					communication_interface::jsExecute("$('#$fieldName').focus();");
				}

			}else{
				if($functionParameters[0]["ueListChange"] == "1") {
					communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnClearTable();");
					$listData = "";
					$blUserList = blFunctionCall('coreFunctions.getUserList','uid');
					foreach($blUserList["data"] as $singleUser) {
						$statusImg = "bullet_grey16x16.png";
						if($singleUser["active"] == 0) $statusImg = "bullet_red16x16.png";
						$listData .= ($listData == "" ? "" : ", ")."['<img src=\"web/img/$statusImg\"/>','".$singleUser["uid"]."','".$singleUser["full_name"]."','".$singleUser["email"]."','".$singleUser["language"]."','<img src=\"web/img/edit16x16.png\" onclick=\"cb(\\'baseLayout.userEditor\\',".$singleUser["id"].");\"/>','<img src=\"web/img/delete16x16.png\" onclick=\"cb(\\'baseLayout.deleteUser\\',".$singleUser["id"].");\"/>']";
					}
					if($listData != "") {
						communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnAddData( [ $listData ] );");
						communication_interface::jsExecute("window.setTimeout(\"$('#useradmTableUsers').dataTable().fnAdjustColumnSizing();\", 500);");
					}
				}

				communication_interface::jsExecute("$('#modalContainer').mb_close();");
			}
			break;
		case 'baseLayout.deleteUser':
			switch(count($functionParameters)) {
			case 1:
				$objWindow = new wgui_window("baseLayout", "modalWindow");
				$objWindow->windowTitle("Benutzer löschen");
	//			$objWindow->windowHeight(300);
				$objWindow->setContent("<br/>Bitte bestätigen Sie die Löschung.<br/><br/><button onclick=\"cb('baseLayout.deleteUser',".$functionParameters[0].",1);\">Löschen</button><button onclick=\"$('#modalContainer').mb_close();\">Abbrechen</button>");
				$objWindow->showQuestion();
				break;
			case 2:
				$blSaveResult = blFunctionCall('coreFunctions.deleteUser',$functionParameters[0]);

				communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnClearTable();");
				$listData = "";
				$blUserList = blFunctionCall('coreFunctions.getUserList','uid');
				foreach($blUserList["data"] as $singleUser) {
					$statusImg = "bullet_grey16x16.png";
					if($singleUser["active"] == 0) $statusImg = "bullet_red16x16.png";
					$listData .= ($listData == "" ? "" : ", ")."['<img src=\"web/img/$statusImg\"/>','".$singleUser["uid"]."','".$singleUser["full_name"]."','".$singleUser["email"]."','".$singleUser["language"]."','<img src=\"web/img/edit16x16.png\" onclick=\"cb(\\'baseLayout.userEditor\\',".$singleUser["id"].");\"/>','<img src=\"web/img/delete16x16.png\" onclick=\"cb(\\'baseLayout.deleteUser\\',".$singleUser["id"].");\"/>']";
				}
				if($listData != "") communication_interface::jsExecute("$('#useradmTableUsers').dataTable().fnAddData( [ $listData ] );");

				communication_interface::jsExecute("$('#modalContainer').mb_close();");
				break;
			}
			break;
		case 'baseLayout.appMenuRefresh':
			communication_interface::jsExecute("$('#accordion1').accordion('destroy').accordion();");
			break;
		case 'baseLayout.appMenuAddSection':
			$newSectionID = $functionParameters[0];
			$newSectionLabel = $functionParameters[1];
			$newSectionInsertPosition = count($functionParameters)>2 ? $functionParameters[2] : "append"; //top, append, before, after, bottom
			$newSectionInsertReference = count($functionParameters)>3 ? $functionParameters[3] : "main";
//communication_interface::alert("appMenuAddSection: $newSectionLabel");
			switch($newSectionInsertPosition) {
			case 'before':
				communication_interface::jsExecute("$('h3[appmenu=\"h_".$newSectionInsertReference."\"]').before('<h3 appmenu=\"h_".$newSectionID."\"><a href=\"#\">".$newSectionLabel."</a></h3><div appmenu=\"c_".$newSectionID."\"><ul appmenu=\"u_".$newSectionID."\" class=\"appMenu\"></ul></div>');");
				break;
			case 'after':
				communication_interface::jsExecute("$('div[appmenu=\"c_".$newSectionInsertReference."\"]').after('<h3 appmenu=\"h_".$newSectionID."\"><a href=\"#\">".$newSectionLabel."</a></h3><div appmenu=\"c_".$newSectionID."\"><ul appmenu=\"u_".$newSectionID."\" class=\"appMenu\"></ul></div>');");
				break;
			case 'append':
				communication_interface::jsExecute("$('h3[appmenu=\"h_system\"]').before('<h3 appmenu=\"h_".$newSectionID."\"><a href=\"#\">".$newSectionLabel."</a></h3><div appmenu=\"c_".$newSectionID."\"><ul appmenu=\"u_".$newSectionID."\" class=\"appMenu\"></ul></div>');");
				break;
			case 'top':
				communication_interface::jsExecute("$('div[appmenu=\"main\"]').prepend('<h3 appmenu=\"h_".$newSectionID."\"><a href=\"#\">".$newSectionLabel."</a></h3><div appmenu=\"c_".$newSectionID."\"><ul appmenu=\"u_".$newSectionID."\" class=\"appMenu\"></ul></div>');");
				break;
			case 'bottom':
				communication_interface::jsExecute("$('div[appmenu=\"main\"]').append('<h3 appmenu=\"h_".$newSectionID."\"><a href=\"#\">".$newSectionLabel."</a></h3><div appmenu=\"c_".$newSectionID."\"><ul appmenu=\"u_".$newSectionID."\" class=\"appMenu\"></ul></div>');");
				break;
			}
			break;
		case 'baseLayout.appMenuAddItem':
			$sectionInsertReference = $functionParameters[0];
			$newItemID = $functionParameters[1];
			$newItemLabel = $functionParameters[2];
			$newItemIcon = $functionParameters[3];
			$newItemFunction = $functionParameters[4];
			$newItemFunction = str_replace("'", "\\'", $newItemFunction); //falls jemand ein Apostroph mitsendet, wird dieses Zeichen mit einem Escape-Zeichen versehen
			$newItemInsertPosition = count($functionParameters)>5 ? $functionParameters[5] : "append"; //top, append, before, after, bottom
//communication_interface::alert("appMenuAddSection: $newItemLabel");
			switch($newItemInsertPosition) {
			case 'before':
				communication_interface::jsExecute("$('#appMenu_".$sectionInsertReference."').before('<li id=\"appMenu_".$newItemID."\" style=\"background-image: url(".$newItemIcon.");\"><a href=\"#\" onclick=\"".$newItemFunction."\" onmouseup=\"this.blur();\">".$newItemLabel."</a></li>');");
				break;
			case 'after':
				communication_interface::jsExecute("$('#appMenu_".$sectionInsertReference."').after('<li id=\"appMenu_".$newItemID."\" style=\"background-image: url(".$newItemIcon.");\"><a href=\"#\" onclick=\"".$newItemFunction."\" onmouseup=\"this.blur();\">".$newItemLabel."</a></li>');");
				break;
			case 'append':
				communication_interface::jsExecute("$('ul[appmenu=\"u_".$sectionInsertReference."\"]').append('<li id=\"appMenu_".$newItemID."\" style=\"background-image: url(".$newItemIcon.");\"><a href=\"#\" onclick=\"".$newItemFunction."\" onmouseup=\"this.blur();\">".$newItemLabel."</a></li>');");
				break;
			case 'top':
				communication_interface::jsExecute("$('ul[appmenu=\"u_".$sectionInsertReference."\"]').prepend('<li id=\"appMenu_".$newItemID."\" style=\"background-image: url(".$newItemIcon.");\"><a href=\"#\" onclick=\"".$newItemFunction."\" onmouseup=\"this.blur();\">".$newItemLabel."</a></li>');");
				break;
			}
			break;
		case 'baseLayout.appMenuRemoveSection':
			break;
		case 'baseLayout.appMenuDisableSection':
			break;
		case 'baseLayout.appMenuEnableSection':
			break;
		case 'baseLayout.appMenuRemoveItem':
			break;
		case 'baseLayout.appMenuDisableItem':
			break;
		case 'baseLayout.appMenuEnableItem':
			break;
		case 'baseLayout.changeConfiguration':
			communication_interface::jsExecute("cb('baseLayout.appMenuAddSection','pim','PIM');");
			communication_interface::jsExecute("cb('baseLayout.appMenuAddItem','pim','contacts','Adressen','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\\\\\\'test\\\\\\');');");
			communication_interface::jsExecute("cb('baseLayout.appMenuAddItem','pim','schedule','Termine','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\\\\\\'test\\\\\\');');");
			communication_interface::jsExecute("cb('baseLayout.appMenuAddItem','pim','notes','Notizen','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\\\\\\'test\\\\\\');');");
			communication_interface::jsExecute("cb('baseLayout.appMenuRefresh');");
			break;
		case 'baseLayout.sysLoader':
			$objWGUI = new wgui("baseLayout", "main"); // <--- $cTemplate "testTemplate"
			$data["iconPath"] = "plugins/baseLayout_V00_01_00/code_ui/media/icons/";

			communication_interface::assign('htmlBody', 'innerHTML', $objWGUI->processTemplate($data,"main")); //code_ui/gui/webbrowser/main.html   session_control::pluginPath("baseLayout")."code_ui/templates/main.html"
//communication_interface::alert("test");
//return;
			communication_interface::assign('splashStatusTxt', 'innerHTML', 'Lade Module...');
			communication_interface::cssFileInclude('web/js/css/redmond/jquery-ui-1.8.7.custom.css','all');
			communication_interface::cssFileInclude('web/css/layout-default.css','all');
			communication_interface::cssFileInclude('web/css/mbContainer.css','all');
			communication_interface::cssFileInclude('web/css/jquery.crossSelect.css','all');
			communication_interface::cssFileInclude('web/css/superfish.css','all');
			communication_interface::cssFileInclude('web/css/slick.grid.css','all');

			$jqueryLoader = "function loadScript(url, callback) {\n";
			$jqueryLoader .= "var head = document.getElementsByTagName('head')[0];\n";
			$jqueryLoader .= "var script = document.createElement('script');\n";
			$jqueryLoader .= "script.type = 'text/javascript';\n";
			$jqueryLoader .= "script.src = url;\n";
//			$jqueryLoader .= "script.onreadystatechange = callback;\n";
			$jqueryLoader .= "script.onreadystatechange = function() { if(this.readyState == 'loaded' || this.readyState == 'complete') { this.onreadystatechange = null; callback(); } }\n";
// function() { if (this.readyState == 'complete') { callback(); }
			$jqueryLoader .= "script.onload = callback;\n";
			$jqueryLoader .= "head.appendChild(script);\n";
			$jqueryLoader .= "}\n";
			$jqueryLoader .= "var loadJQUERYUI = function() {\n";
//			$jqueryLoader .= "document.getElementById('splashStatusTxt').innerHTML = 'jquery-ui.js';\n";
			$jqueryLoader .= "document.getElementById('splashStatusTxt').innerHTML = 'jquery-ui';\n";
//			$jqueryLoader .= "loadScript('web/js/jquery-ui.js', loadJQUERYLAYOUT);\n";
			$jqueryLoader .= "loadScript('web/js/jquery-ui-1.10.0.custom.min.js', loadJQUERYLAYOUT);\n";
			$jqueryLoader .= "};\n";
			$jqueryLoader .= "var loadJQUERYLAYOUT = function() {\n";
			$jqueryLoader .= "document.getElementById('splashStatusTxt').innerHTML = 'jquery.layout.js';\n";
//			$jqueryLoader .= "loadScript('web/js/jquery.layout.js', loadMainScript);\n";
			$jqueryLoader .= "loadScript('web/js/jquery.layout.min-1.3.0.js', loadMainScript);\n";
			$jqueryLoader .= "};\n";
			$jqueryLoader .= "var loadMainScript = function() {\n";
			$jqueryLoader .= "document.getElementById('splashStatusTxt').innerHTML = 'Init Script';\n";
			$jqueryLoader .= "loadScript('plugins/baseLayout_V00_01_00/code_ui/js/main-init.js', loadJQUERYdone);\n";
			$jqueryLoader .= "};\n";
			$jqueryLoader .= "var loadJQUERYdone = function() {\n";
			$jqueryLoader .= "if(document.getElementById('splashStatusTxt').innerHTML!='restliche JS laden') { document.getElementById('splashStatusTxt').innerHTML = 'restliche JS laden';\n";
			$jqueryLoader .= "cb('baseLayout.fireLoadIncludes'); }\n";
			$jqueryLoader .= "};\n";
//			$jqueryLoader .= "document.getElementById('splashStatusTxt').innerHTML = 'jquery.js';\n";
			$jqueryLoader .= "document.getElementById('splashStatusTxt').innerHTML = 'jquery';\n";
//			$jqueryLoader .= "loadScript('web/js/jquery.js', loadJQUERYUI);\n";
			$jqueryLoader .= "loadScript('web/js/jquery-1.8.3.min.js', loadJQUERYUI);\n";
			communication_interface::jsExecute($jqueryLoader);

			communication_interface::cssFileInclude('plugins/baseLayout_V00_01_00/code_ui/css/baseLayout.css','all'); //gui/webbrowser/baseLayout.css

			$session_control = session_control::getInstance();
			communication_interface::assign('appCustomer', 'innerHTML', $session_control->getSessionInfo("customer_login_name"));
			communication_interface::assign('appUser', 'innerHTML', $session_control->getSessionInfo("uid"));
			break;
		case 'baseLayout.titleBar_addItem':
			//links oder rechts anhängen?
			break;
		case 'baseLayout.applicationMenu_addItem':
			//oben oder unten anhängen? Oberhalb oder unterhalb von welcher ID?
			break;
		case 'baseLayout.fireLoadIncludes':
			if(session_control::getSessionSettings("baseLayout", "bootStatus")=="fireLoadIncludes") break; //TODO: Testen, ob das den IE-Bug behebt...
/*
			communication_interface::jsFileInclude('web/js/jquery.metadata.js','text/javascript','jqueryMetadata');
			communication_interface::jsFileInclude('web/js/mbContainer.js','text/javascript','mbContainer');
			communication_interface::jsFileInclude('web/js/jquery.dataTables.min.js','text/javascript','dataTables');
			communication_interface::jsFileInclude('web/js/jQuery.crossSelect-0.5.js','text/javascript','crossSelect');
			communication_interface::jsFileInclude('web/js/hoverIntent.js','text/javascript','hoverIntent');
			communication_interface::jsFileInclude('web/js/superfish.js','text/javascript','superfish');
			communication_interface::jsFileInclude('web/js/autoNumeric-min.js','text/javascript','autoNumeric');
			communication_interface::jsFileInclude('web/js/jquery.event.drag-2.0.min.js','text/javascript','jqueryEventDrag');
			communication_interface::jsFileInclude('web/js/slick.core.js','text/javascript','slickCore');
			communication_interface::jsFileInclude('web/js/slick.dataview.js','text/javascript','slickDataview');
			communication_interface::jsFileInclude('web/js/slick.grid.js','text/javascript','slickGrid');
*/
			$jsLoader = "var mnucallpending=true;\nfunction loadScript(url, callback) {\n";
			$jsLoader .= "var head = document.getElementsByTagName('head')[0];\n";
			$jsLoader .= "var script = document.createElement('script');\n";
			$jsLoader .= "script.type = 'text/javascript';\n";
			$jsLoader .= "script.src = url;\n";
			$jsLoader .= "script.onreadystatechange = function() { if(this.readyState == 'loaded' || this.readyState == 'complete') { this.onreadystatechange = null; callback(); } }\n";
			$jsLoader .= "script.onload = callback;\n";
			$jsLoader .= "head.appendChild(script);\n";
			$jsLoader .= "}\n";

			$jsLoader .= "var scriptsToLoad = new Array('web/js/slick.grid.js','web/js/slick.dataview.js','web/js/slick.core.js','web/js/jquery.event.drag-2.0.min.js','web/js/autoNumeric-min.js','web/js/superfish.js','web/js/hoverIntent.js','web/js/jQuery.crossSelect-0.5.js','web/js/jquery.dataTables.min.js','web/js/mbContainer.js','web/js/jquery.metadata.js');\n";
			$jsLoader .= "var processNextScript = function() {\n";
			$jsLoader .= 	"scriptsToLoad.pop();\n";
			$jsLoader .= 	"if(scriptsToLoad.length<1) {if(mnucallpending) cb('baseLayout.fireLoadMenu');mnucallpending=false;return;}\n";
			$jsLoader .= 	"loadScript(scriptsToLoad[scriptsToLoad.length-1], processNextScript);\n";
			$jsLoader .= "}\n";
			$jsLoader .= "loadScript(scriptsToLoad[scriptsToLoad.length-1], processNextScript);\n";
			communication_interface::jsExecute($jsLoader);

			uiFireEvent("core.bootLoadIncludes");
//			communication_interface::jsExecute("cb('baseLayout.fireLoadMenu');");
			session_control::setSessionSettings("baseLayout", "bootStatus", "fireLoadIncludes", false); //true = save permanently	TODO: Testen, ob das den IE-Bug behebt...
			break;
		case 'baseLayout.fireLoadMenu':
			if(session_control::getSessionSettings("baseLayout", "bootStatus")=="fireLoadMenu") break; //TODO: Testen, ob das den IE-Bug behebt...
			uiFireEvent("core.bootLoadMenu");
			communication_interface::assign('splashStatusTxt', 'innerHTML', 'Ladevorgang wird abgeschlossen...');
			communication_interface::jsExecute("cb('baseLayout.fireBootComplete');");
			session_control::setSessionSettings("baseLayout", "bootStatus", "fireLoadMenu", false); //true = save permanently	TODO: Testen, ob das den IE-Bug behebt...
			break;
		case 'baseLayout.fireBootComplete':
			if(session_control::getSessionSettings("baseLayout", "bootStatus")=="fireBootComplete") break; //TODO: Testen, ob das den IE-Bug behebt...
			uiFireEvent("core.bootComplete");
			communication_interface::jsExecute("$('#accordion1').accordion('destroy').accordion();");
			communication_interface::jsExecute("window.setTimeout(\"$('#splashScreen').hide('slow'); $('#splashScreen').delay(3000).remove();\", 1000);");
			session_control::setSessionSettings("baseLayout", "bootStatus", "fireBootComplete", false); //true = save permanently	TODO: Testen, ob das den IE-Bug behebt...

			$jsDatepickerConfig = "$.datepicker.regional['de'] = {\n";
			$jsDatepickerConfig .= "closeText: 'schließen',\n";
			$jsDatepickerConfig .= "prevText: '&#x3c;zurück',\n";
			$jsDatepickerConfig .= "nextText: 'Vor&#x3e;',\n";
			$jsDatepickerConfig .= "currentText: 'heute',\n";
			$jsDatepickerConfig .= "monthNames: ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],\n";
			$jsDatepickerConfig .= "monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun','Jul','Aug','Sep','Okt','Nov','Dez'],\n";
			$jsDatepickerConfig .= "dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],\n";
			$jsDatepickerConfig .= "dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],\n";
			$jsDatepickerConfig .= "dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],\n";
			$jsDatepickerConfig .= "weekHeader: 'Wo',\n";
			$jsDatepickerConfig .= "dateFormat: 'dd.mm.yy',\n";
			$jsDatepickerConfig .= "firstDay: 1,\n";
			$jsDatepickerConfig .= "isRTL: false,\n";
			$jsDatepickerConfig .= "showMonthAfterYear: false,\n";
			$jsDatepickerConfig .= "yearSuffix: ''};\n";
			$jsDatepickerConfig .= "$.datepicker.setDefaults($.datepicker.regional['de']);\n";
			communication_interface::jsExecute($jsDatepickerConfig);
			break;
		default:
			return "Funktion unbekannt";
			break;
		}
	}

	public function eventListener($functionName, $functionParameters) {
		global $aafwConfig;
	}

	private function prepareUserEditorData($core_user_ID = 0) {
		$blGroupList = blFunctionCall('coreFunctions.getGroupList','name');

		if($core_user_ID == 0) {
			$data["id"] = 0;
			$data["uid"] = "";
			$data["full_name"] = "";
			$data["email"] = "";
			$data["language_de"] = "";
			$data["inactive"] = "";
			$data["timeout_minutes"] = "30";
			$data["force_pwd_change"] = "";
			$data["pwd_change_period_0"] = " selected";
			$data["pwd_change_period_1"] = "";
			$data["pwd_change_period_2"] = "";
			$data["pwd_change_period_4"] = "";
			$data["pwd_change_period_6"] = "";
			$data["pwd_change_period_8"] = "";
			$data["pwd_change_period_12"] = "";
			$data["disallow_pwd_change"] = "";
			$data["timeRestriction"] = "";
			$data["bMon"] = " checked";
			$data["bTue"] = " checked";
			$data["bWed"] = " checked";
			$data["bThu"] = " checked";
			$data["bFri"] = " checked";
			$data["bSat"] = "";
			$data["bSun"] = "";
			$data["timeFrom"] = "06:30";
			$data["timeUntil"] = "17:30";
			$data["ipRestriction"] = "";
			$data["ipInclude1"] = $_SERVER["REMOTE_ADDR"];
			$data["subnetInclude1"] = "255.255.255.0";
			$data["ipInclude2"] = "";
			$data["subnetInclude2"] = "";
			$data["ipInclude3"] = "";
			$data["subnetInclude3"] = "";
			$data["ipInclude4"] = "";
			$data["subnetInclude4"] = "";
			$data["ipInclude5"] = "";
			$data["subnetInclude5"] = "";
			$data["group_list"] = array();
			foreach($blGroupList["data"] as $singleGroup) $data["group_list"][] = array("core_group_ID" => $singleGroup["id"], "group_name" => $singleGroup["name"], "selected" => 0);

			return $data;
		}else{
			$blUserSettingsFull = blFunctionCall('coreFunctions.getUserSettings',$core_user_ID);
//communication_interface::alert("test");
//return;
			if(!$blUserSettingsFull["success"]) {
				switch($blUserSettingsFull["errCode"]) {
				case 101: //übermittelte ID nicht numerisch!
					$err = "blChPwdErrOldPW"; //k
					break;
				default:
					$err = "blChPwdErrUnknown";
					break;
				}
			}
			$blUserSettings = $blUserSettingsFull["data"];
//foreach($blUserSettings as $key => $val) $out .= "$key => $val / ";
//communication_interface::alert($out);

			$data["id"] = $blUserSettings["id"];
			$data["uid"] = $blUserSettings["uid"];
			$data["full_name"] = $blUserSettings["full_name"];
			$data["email"] = $blUserSettings["email"];
			$data["language_de"] = $blUserSettings["language"] == "de" ? " selected" : "";
			$data["inactive"] = $blUserSettings["active"] == 1 ? "" : " checked";
			$data["timeout_minutes"] = $blUserSettings["timeout_minutes"];
			$data["force_pwd_change"] = $blUserSettings["force_pwd_change"] == 1 ? " checked" : "";
			$data["pwd_change_period_0"] = "";
			$data["pwd_change_period_1"] = "";
			$data["pwd_change_period_2"] = "";
			$data["pwd_change_period_4"] = "";
			$data["pwd_change_period_6"] = "";
			$data["pwd_change_period_8"] = "";
			$data["pwd_change_period_12"] = "";
			$data["pwd_change_period_".round($blUserSettings["pwd_change_period_days"]/7,0)] = " selected";
			$data["disallow_pwd_change"] = $blUserSettings["allow_pwd_change"] == 1 ? "" : " checked";
			foreach($blUserSettings["securitySettings"] as $securityRow) {
				switch($securityRow["name"]) {
				case 'timeRestriction':
				case 'bMon':
				case 'bTue':
				case 'bWed':
				case 'bThu':
				case 'bFri':
				case 'bSat':
				case 'bSun':
				case 'ipRestriction':
					$data[$securityRow["name"]] = $securityRow["value"]==1 ? " checked" : "";
					break;
				default:
					$data[$securityRow["name"]] = $securityRow["value"];
					break;
				}
			}
			$data["group_list"] = array();
			foreach($blGroupList["data"] as $singleGroup) $data["group_list"][] = array("core_group_ID" => $singleGroup["id"], "group_name" => $singleGroup["name"], "selected" => ( in_array($singleGroup["id"], $blUserSettings["groupAssignments"]) ? 1 : 0 ));

//			$result = $system_database_manager->executeQuery("SELECT id, uid, full_name, email, language, active, timeout_minutes, allow_pwd_change, force_pwd_change, pwd_change_period_days, deleted, core_user_ID_pwd_change, datetime_pwd_change, core_user_ID_change, datetime_change, core_user_ID_create, datetime_create, core_user_ID_delete, datetime_delete FROM core_user WHERE id=".addslashes($core_user_ID), "getSecurityGroupList");
//			$resGroupAssignment = $system_database_manager->executeQuery("SELECT core_group_ID FROM core_user_group WHERE core_user_ID=".addslashes($core_user_ID), "getSecurityGroupList");
//			$response["data"]["groupAssignments"] = $resGroupAssignment;

			return $data;
		}
	}
}

$SYS_PLUGIN["ui"]["baseLayout"] = new baseLayout_UI();

?>
