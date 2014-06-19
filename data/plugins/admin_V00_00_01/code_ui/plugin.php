<?php
class admin_UI {
	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;
		

		switch($functionName) :
		
			case 'admin.sysLoader': // <-- FIX vorgegebener Name. Wird während des Login-Prozesses automatisch aufgerufen. Damit lassen sich Plugin-spezifische CSS- und JS-Dateien laden
				communication_interface::cssFileInclude('plugins/admin_V00_00_01/code_ui/css/admin.css','all');
				communication_interface::jsFileInclude('plugins/admin_V00_00_01/code_ui/js/slickgrid.js','text/javascript','admin');
	//			communication_interface::cssFileInclude('plugins/pim_V00_01_00/code_ui/css/pim.css','all');
				
				break;
		
			case 'admin.showCustomers':
				$this->showCustomers( $functionParameters[0]['wndStatus'] );
				//else{ z.B. eine Fehlermeldung... }
				break;
		
			case 'admin.showCustomer':
				$this->showCustomer( $functionParameters[0]['wndStatus'] , $functionParameters[0]['customerID'] , $functionParameters[0]['customerDB'] );
				break;
		
			case 'admin.showNewCustomer':
				//session_control::setSessionSettings("admin", "newCustomer", time(), false);
				$this->showNewCustomer( $functionParameters[0]['wndStatus'] );
				break;
		
			case 'admin.showPluginsForNewCustomer':
				$this->showPluginsForNewCustomer();
				break;
		
			case 'admin.cancelNewCustomer':
				
				$this->cancelNewCustomer();
				break;
		
			case 'admin.loadNewCustomerFirstStep':
				
				$this->loadNewCustomerFirstStep();
				break;
		
			case 'admin.newCustomerFirstStep':
				
				$token 	= session_control::getSessionSettings("admin", "token" );
				$fm 	= new file_manager();
				
				if( $fm->setTmpDir( $token ) ) :
				
					$wizard = unserialize($fm->setFile("newCustomerValues.txt")->getContents());
					
				else :
				
					$token 	= $fm->createTmpDir();
					session_control::setSessionSettings("admin" , "token" , $token , false);
					
				endif;
				
				// datenbank eintrag
				$wizard["admin"]["customer"] 	= $functionParameters[0]['customer'];
				if ( isset( $functionParameters[0]['customer'] ) ) :
					$find 		= array("/ä/","/ö/","/ü/","/Ä/","/Ö/","/Ü/","/ß/","/ /"); 
					$replace 	= array("ae","oe","ue","Ae","Oe","Ue","ss","/_/");
					$customerDB = preg_replace( $find , $replace , $functionParameters[0]['customer'] );
					$wizard['admin']['customerDB'] = $this->createCustomerDB( $customerDB );
				endif;
				
				$wizard["admin"]["userID"] 				= $functionParameters[0]['userID'];
				$wizard["admin"]["userPassword"] 		= $functionParameters[0]['userPassword'];
				$wizard["admin"]["userName"] 			= $functionParameters[0]['userName'];
				$wizard["admin"]["userMail"] 			= $functionParameters[0]['userMail'];
				$wizard["admin"]["existingCustomerSel"]	= $functionParameters[0]['existingCustomerSel'];
				
				$fm->setFile("newCustomerValues.txt")->putContents( serialize( $wizard ) );
				$fb = blFunctionCall('admin.getCustomer',$functionParameters[0]['customer']);
				
				if ( $fb['success'] ) :
				
					$this->loadNewCustomerFirstStep( 1 );
				
				else :
				
					$this->loadNewCustomerSecondStep();
				
				endif;
				
				break;
		
			case 'admin.loadNewCustomerSecondStep':
				
				$this->loadNewCustomerSecondStep();
				break;
		
			case 'admin.newCustomerSecondStep':
				
				$fm 	= new file_manager();
				$token 	= session_control::getSessionSettings("admin", "token" );
				
				if( $fm->setTmpDir($token) ) :
				
					$wizard = unserialize($fm->setFile("newCustomerValues.txt")->getContents());
					
					$wizard["admin"]["customerName"] 		= $functionParameters[0]['customerName'];
					$wizard["admin"]["customerAddress1"] 	= $functionParameters[0]['customerAddress1'];
					$wizard["admin"]["customerAddress2"] 	= $functionParameters[0]['customerAddress2'];
					$wizard["admin"]["customerAddress3"] 	= $functionParameters[0]['customerAddress3'];
					$wizard["admin"]["customerStreet"]	 	= $functionParameters[0]['customerStreet'];
					$wizard["admin"]["customerPLZ"] 		= $functionParameters[0]['customerPLZ'];
					$wizard["admin"]["customerPlace"] 		= $functionParameters[0]['customerPlace'];
					$wizard["admin"]["customerCountry"] 	= $functionParameters[0]['customerCountry'];
					$wizard["admin"]["customerPhone"] 		= $functionParameters[0]['customerPhone'];
					$wizard["admin"]["customerEMail"] 		= $functionParameters[0]['customerEMail'];
					
					$fm->setFile("newCustomerValues.txt")->putContents( serialize( $wizard ) );
					$this->loadNewCustomerThirdStep();
					
				else :
				
					communication_interface::alert( 'no file found' );
					
				endif;
				
				break;
		
			case 'admin.loadNewCustomerThirdStep':
				
				$this->loadNewCustomerThirdStep();
				break;
		
			case 'admin.newCustomerThirdStep':
				
				$fm 	= new file_manager();
				$token 	= session_control::getSessionSettings("admin", "token" );
				
				if( $fm->setTmpDir($token) ) :
				//communication_interface::alert( $functionParameters[0]['customerPlugins'] );
					$wizard 							= unserialize($fm->setFile("newCustomerValues.txt")->getContents());
					$customerPlugins 					= explode( '@@' , $functionParameters[0]['customerPlugins'] );
					$wizard["admin"]['customerPlugins'] = $functionParameters[0]['customerPlugins'];
					//communication_interface::alert( 'customerPlugin: '.$functionParameters[0]['customerPlugins'].' '.count($customerPlugins) );
					foreach( $customerPlugins as $rowCustomerPlugins ) :
					//communication_interface::alert( 'rowCustomerPlugin: '.$rowCustomerPlugins );
						$customerPlugin = explode( '##' , $rowCustomerPlugins );
					//communication_interface::alert( 'customerPlugin: '.$customerPlugin[0].' '.$customerPlugin[1] );	
						$wizard['admin']['pluginID_'.$customerPlugin[1]] = $customerPlugin[0];
						
					endforeach;
					
					$fm->setFile("newCustomerValues.txt")->putContents( serialize( $wizard ) );
					$this->loadNewCustomerFourthStep( 0 );
					
				else :
				
					communication_interface::alert( 'no file found' );
					
				endif;
				
				break;
		
			case 'admin.newCustomerFourthStep':
				
				$number 		= $functionParameters[0]['number'];
				$pluginID 		= $functionParameters[0]['pluginID'];
				$pluginName 	= $functionParameters[0]['pluginName'];
				$pluginData 	= $functionParameters[0]['pluginData'];
				$pluginGroups 	= $functionParameters[0]['pluginGroups'];
				$values 		= $functionParameters[0]['values'];
				//communication_interface::alert( $pluginGroups );
				$fm 	= new file_manager();
				$token 	= session_control::getSessionSettings("admin", "token" );
				
				if( $fm->setTmpDir($token) ) :
				
					$wizard = unserialize($fm->setFile("newCustomerValues.txt")->getContents());
					
					if ( !empty( $pluginGroups ) ) :
					
						$arrPluginGroup = explode( '@@' , $pluginGroups );
						//communication_interface::alert( $pluginGroups );
					//communication_interface::alert( count($arrPluginGroup) );
						for( $g=0; $g<count($arrPluginGroup); $g++ ) :
						
							/*
							 * $arrPluginGroup[$g] = groupID.'##'.customerDB.'##'.customerVersion */
							$pluginGroup = explode( '##' , $arrPluginGroup[$g] );
							
							if ( $pluginGroup[1] == 0 ) :
								$db = explode( '##' , $wizard['admin']['pluginID_'.$pluginID] );
								//communication_interface::alert( 'pluginID: '.$pluginID.' dbStandard: '.$db[0] );
								$wizard['admin']['groupID_'.$pluginGroup[0]] = $pluginGroup[1].'##'.$db[0];
							else :
								$wizard['admin']['groupID_'.$pluginGroup[0]] = $pluginGroup[1].'##'.$pluginGroup[2];
							endif;
							
						
						endfor;
					
					endif;
					
					switch( $pluginName ) :
					
						case 'payroll':
							//communication_interface::alert( 'payroll setzen: '.$values );
							$value = explode( '##' , $values );
							
							$wizard["admin"]['payrollDate'] = $value[0];
							
							$languages = explode( '@@' , $value[1] );
							foreach( $languages as $rowLanguages ) :
								$wizard["admin"][$rowLanguages] = 1;
							endforeach;
							//communication_interface::alert( $value[2] );
							$wizard["admin"]['standardLanguage'] = $value[2];
							break;
					
					endswitch;
					//communication_interface::alert( $wizard["admin"][$pluginName].' = '.$pluginData.' '.$pluginName );
					$wizard['admin'][$pluginName] = $pluginData;
					$fm->setFile("newCustomerValues.txt")->putContents( serialize( $wizard ) );
					
					$this->loadNewCustomerFourthStep( $number );
					
				else :
				
					communication_interface::alert( 'no file found' );
					
				endif;
				
				break;
		
			case 'admin.loadNewCustomerFourthStep':
				
				$this->loadNewCustomerFourthStep(0);
				
				break;
		
			case 'admin.newCustomerCreate':
				
				$this->newCustomerCreate();
				
				break;
		
		case 'admin.showPluginDataByCustomers':
			
			$this->showPluginDataByCustomers( $functionParameters[0]['wndStatus'] , $functionParameters[0]['tmpCustomer'] , $functionParameters[0]['pluginID'] , $functionParameters[0]['pluginName'] , $functionParameters[0]['versionID'] );
			break;
		
		case 'admin.showPlugins':
			//communication_interface::alert($functionParameters[0]['plugin'].', '.$functionParameters[0]['version']);
			$this->showPlugins( $functionParameters[0]['wndStatus'] );
			break;
		
		case 'admin.showPlugin':
			//communication_interface::alert($functionParameters[0]['plugin'].', '.$functionParameters[0]['version']);
			$this->showPlugin( $functionParameters[0]['wndStatus'] , $functionParameters[0]['pluginID'] );
			break;
		
		case 'admin.showNewPlugin':

				$this->showNewPlugin( $functionParameters[0]['wndStatus'] );
				
			break;
		
		case 'admin.savePlugin':
			
				$queryOptions['plugin'] 		= $functionParameters[0]['plugin'];
				$queryOptions['version'] 		= $functionParameters[0]['version'];
				$queryOptions['tablePreface'] 	= $functionParameters[0]['tablePreface'];
				
				$fb = blFunctionCall('admin.saveNewPlugin',$queryOptions);
				if ( !$fb ) :
					communication_interface::alert( $fb );
				else :
					communication_interface::jsExecute("$('#showNewPluginWindow').mb_close();");
					$this->showPlugins( 1 );
				endif;
				
			break;
		
		case 'admin.savePluginVersion':
			
				$queryOptions['pluginID'] 			= $functionParameters[0]['pluginID'];
				$queryOptions['version'] 			= $functionParameters[0]['version'];
				$queryOptions['newVersionStatus'] 	= $functionParameters[0]['newVersionStatus'];
				$queryOptions['isCurrent'] 			= $functionParameters[0]['isCurrent'];
				
				$fb = blFunctionCall('admin.savePluginVersion',$queryOptions);
				if ( !$fb ) :
					communication_interface::alert( $fb );
				else :
					$this->showPlugin( 1 , $functionParameters[0]['pluginID'] );
				endif;
			
			break;
		
		case 'admin.showPluginVersion':

				$this->showPluginVersion( $functionParameters[0]['wndStatus'] , $functionParameters[0]['pluginID'] , $functionParameters[0]['versionID'] );
				
			break;
		
		case 'admin.showTablesByGroup':
		
				$this->showTablesByGroup( $functionParameters[0]['wndStatus'] , $functionParameters[0]['pluginID'] , $functionParameters[0]['versionID'] , $functionParameters[0]['groupID'] );
			break;
		
		case 'admin.savePluginTable':
			
				$queryOptions['pluginID'] 	= $functionParameters[0]['pluginID'];
				$queryOptions['tableName'] 	= $functionParameters[0]['tablePreface'].$functionParameters[0]['tableName'];
				
				$fb = blFunctionCall('admin.savePluginTable',$queryOptions);
				if ( !$fb ) :
					communication_interface::alert( $fb );
				else :
					$this->showPlugin( 0 , $functionParameters[0]['pluginID'] );
				endif;
			
			break;
		
		case 'admin.saveTableByGroup':
			
				$queryOptions['groupID'] 	= $functionParameters[0]['groupID'];
				$queryOptions['tableID']	= $functionParameters[0]['tableID'];
				
				$fb = blFunctionCall('admin.saveTableByGroup',$queryOptions);
			
			break;
		
		case 'admin.deleteTableByGroup':
			
				$queryOptions['groupID'] 	= $functionParameters[0]['groupID'];
				$queryOptions['tableID']	= $functionParameters[0]['tableID'];
				
				$fb = blFunctionCall('admin.deleteTableByGroup',$queryOptions);
			
			break;
		
		case 'admin.getVersionTablesFromDB':
			
				//communication_interface::alert( $fb['data'] );
				$this->showVersionTablesFromDB( 0 , $functionParameters[0]['pluginID'] , $functionParameters[0]['versionID'] , $functionParameters[0]['tablePreface'] , $functionParameters[0]['customerDB'] );
			
			break;
		
		case 'admin.saveVersionTable':
			
				$queryOptions['versionID'] 	= $functionParameters[0]['versionID'];
				$queryOptions['table'] 		= $functionParameters[0]['table'];
				$queryOptions['withData']	= $functionParameters[0]['withData'];
				$queryOptions['groupID']	= $functionParameters[0]['groupID'];
				
				$fb = blFunctionCall('admin.saveVersionTable',$queryOptions);
			
			break;
		
		case 'admin.savePluginGroup':
			
				$queryOptions['pluginID'] 	= $functionParameters[0]['pluginID'];
				$queryOptions['groupName'] 	= $functionParameters[0]['groupName'];
				$fb = blFunctionCall('admin.savePluginGroup',$queryOptions);
				
				if ( !$fb ) :
					communication_interface::alert( $fb );
				else :
					//communication_interface::alert(  );
					$this->showPluginGroups( $functionParameters[0]['pluginID'] );
				endif;
			
			break;
		
		case 'admin.showPluginGroup':

				$this->showPluginGroup( $functionParameters[0]['wndStatus'] , $functionParameters[0]['pluginID'] , $functionParameters[0]['groupID'] );
				
			break;
		
		case 'admin.editPluginGroup':
			
				$queryOptions['groupID'] 	= $functionParameters[0]['groupID'];
				$queryOptions['groupName'] 	= $functionParameters[0]['groupName'];
				
				$fb = blFunctionCall('admin.editPluginGroup',$queryOptions);
				if ( !$fb ) :
					communication_interface::alert( $fb );
				else :
					//communication_interface::alert( $fb );
					$this->showPlugin( 0 , $functionParameters[0]['pluginID'] );
				endif;
			
			break;
		
		case 'admin.deletePluginGroup':

				$fb = blFunctionCall('admin.deletePluginGroup',$functionParameters[0]['groupID']);
				$this->showPlugin( 0 , $functionParameters[0]['pluginID'] );
				
			break;
		
		case 'admin.showAssignPluginTablesToGroup':
		
			$this->showAssignPluginTablesToGroup( $functionParameters[0]['wndStatus'] , $functionParameters[0]['pluginID'] , $functionParameters[0]['versionID'] , $functionParameters[0]['groupID'] );
	
			break;
		
		case 'admin.saveTableForGroup':
		
			$queryOptions['groupID'] = $functionParameters[0]['groupID'];
			$queryOptions['tableID'] = $functionParameters[0]['tableID'];
			$fb = blFunctionCall('admin.saveTableForGroup', $queryOptions);
	
			break;
		
		case 'admin.deleteTableForGroup':
		
			$queryOptions['groupID'] = $functionParameters[0]['groupID'];
			$queryOptions['tableID'] = $functionParameters[0]['tableID'];
			$fb = blFunctionCall('admin.deleteTableForGroup', $queryOptions);
	
			break;
		
		case 'admin.showUserGroup':
			$fb = blFunctionCall('admin.getUserGroup');
			if($fb["success"]) {
				$data["userGroup"] = $fb["data"];

				$objWindow = new wgui_window("admin", "windowUserGroup"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("userByGroup"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(225);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("testdatei",$data,"windowUserGroup"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();

//				communication_interface::jsExecute("");
			} //else{ z.B. eine Fehlermeldung... }
			
			break;
		default:
			return "Funktion unbekannt";
			break;
		
		endswitch;
		

	}
	
	public function showCustomers( $wndStatus=0 ) {
		
		$fb = blFunctionCall('admin.getCustomers');
			
		if($fb["success"]) :
			
			if ( $wndStatus == 0 ) :
			
				$objWindow = new wgui_window("admin", "showCustomersWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showCustomers"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(350);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(true);
				$objWindow->fullscreen(true);
				$objWindow->modal(false);
				$objWindow->loadContent("customer",'',"showCustomersWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
	//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();

			endif;
			//communication_interface::alert( ('2013-'.substr('012', -2).'-01') );
			$arrCustomers = array();
			$i = 1;
			
			if( !empty( $fb["data"] ) ) :
			
				foreach( $fb["data"] as $row ) :
				
					$arrCustomers[] = 	"{'id':".$i.", 'customerID':'".$row["customerID"]."' , 'customerDB':'".str_replace("'","\\'",$row["customerDB"])."' , 'customerName':'".str_replace("'","\\'",$row['name'])."' , 'customerStreet':'".str_replace("'","\\'",$row['street'])."' , 'customerPlace':'".$row['postalcode']." ".str_replace("'","\\'",$row['place'])."' }";
					$i++;
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute("data = [".implode(",",$arrCustomers)."];");
			communication_interface::jsExecute("admGritInit( 'customers' )");
			
			
		endif;
		
	}
	
	public function showCustomer( $wndStatus , $customerID , $customerDB ) {
		//communication_interface::alert($customerID.' '.$customerDB);
		$customerDetail = blFunctionCall( 'admin.getCustomer' , $customerID );
		//communication_interface::alert( $customerDetail['data'][0]['name'] );
		if ( $customerDetail["success"] ) :
		
			$data["customer"] 	= $customerID;
			$data['customerDB']	= $customerDB;
			//communication_interface::alert( $customerDetail['data'][0]['name'] );
			//$data['plugins']	= $fb['plugins'];
			$customerPlugins = blFunctionCall( 'admin.getPluginsByCustomer' , $customerDB );
			
			if ( $wndStatus == 0 ) :
			
				$objWindow = new wgui_window("admin", "showCustomerWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showCustomer"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(400);
				$objWindow->dockable(true);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(true);
				//$objWindow->disable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->addEventFunction_onResize("");
				$objWindow->loadContent("customer",'',"showCustomerWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
				$objWindow->bringToFront();
				$objWindow->showWindow();
				
			endif;
			
			$arrPlugins = array();
			
			if( !empty( $customerPlugins['plugins'] ) ) :
			
				foreach( $customerPlugins['plugins'] as $rowCustomerPlugins ) :
				//communication_interface::alert( $rowCustomerPlugins['pluginID'] );
					$arrPlugins[] = '{ "pluginID":"'.$rowCustomerPlugins["pluginID"].'" , "pluginName":"'.str_replace("'","\\'",$rowCustomerPlugins["pluginName"]).'" , "version":"'.$rowCustomerPlugins["version"].'"}';
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute( 'admPluginsByCustomer = ['.implode(",",$arrPlugins).'];');
			communication_interface::jsExecute( 'customerDetail = ["'.$customerID.'","'.$customerDB.'","'.$customerDetail['data'][0]['name'].'","'.$customerDetail['data'][0]['address1'].'","'.$customerDetail['data'][0]['address2'].'","'.$customerDetail['data'][0]['address3'].'","'.$customerDetail['data'][0]['street'].'","'.$customerDetail['data'][0]['postalcode'].'","'.$customerDetail['data'][0]['place'].'","'.$customerDetail['data'][0]['country'].'","'.$customerDetail['data'][0]['phone'].'","'.$customerDetail['data'][0]['email'].'"]' );
			communication_interface::jsExecute( 'loadCustomer( "'.$customerID.'" , "'.$customerDB.'" )' );
				
		
	
		endif; //else{ z.B. eine Fehlermeldung... }
		
	}
	
	public function cancelNewCustomer() {
		
		communication_interface::jsExecute("$('#modalContainer').mb_close();");
		$token 	= session_control::getSessionSettings("admin", "token" );
		$fm 	= new file_manager();
		
		if( $fm->setTmpDir( $token ) ) :
			$fm->deleteDir();
		endif;
		
	}
	public function showNewCustomer( $wndStatus ) {
		
		if ( $wndStatus == 0 ) :
		/*	$userID 		= session_control::getSessionSettings( 'admin' , 'core_user_ID' );
			$userCustomerDB = session_control::getSessionSettings( 'admin' , 'db_name' );
		communication_interface::alert( $userID.' '.$userCustomerDB  );*/
				$objWindow = new wgui_window("admin", "showNewCustomerWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showNewCustomer"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(600);
				$objWindow->windowHeight('auto');
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("customer",'',"showNewCustomerWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
		//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
				$objWindow->bringToFront();
			
		endif;
		
		$this->loadNewCustomerFirstStep();
		
	}
	
	public function loadNewCustomerFirstStep( $customerExists=0 ) {
		
		$token 					= session_control::getSessionSettings("admin", "token" );
		$customer 				= '';
		$customerDB				= '';
		$userID 				= '';
		$userPassword			= '';
		$userName 				= '';
		$userMail 				= '';
		$existingCustomerSel	= '';
		$existingCustomers 		= blFunctionCall('admin.getCustomers');
		
		// setzen der temporaeren datei fuer die session-werte
		$fm 	= new file_manager();
		if( $fm->setTmpDir($token) ) :
		
			$wizard = unserialize($fm->setFile("newCustomerValues.txt")->getContents());
			
			if ( isset( $wizard['admin']['customer'] ) ) 			: 	$customer 				= $wizard['admin']['customer']; endif;
			if ( isset( $wizard['admin']['customerDB'] ) ) 			: 	$customerDB 			= $wizard['admin']['customerDB']; endif;
			if ( isset( $wizard['admin']['userID'] ) ) 				:	$userID 				= $wizard['admin']['userID']; endif;
			if ( isset( $wizard['admin']['userPassword'] ) )		: 	$userPassword 			= $wizard['admin']['userPassword']; endif;
			if ( isset( $wizard['admin']['userName'] ) ) 			:	$userName 				= $wizard['admin']['userName']; endif;
			if ( isset( $wizard['admin']['userMail'] ) ) 			:	$userMail 				= $wizard['admin']['userMail']; endif;
			if ( isset( $wizard['admin']['existingCustomerSel'] ) )	:	$existingCustomerSel	= $wizard['admin']['existingCustomerSel']; endif;
			
		endif;
		
		// mandanten anzeigen zur uebernahme von weiteren benutzern
		if ( $existingCustomers ) :
			
			$arrExistingCustomers = array();
			$arrExistingCustomers[] = "{'customerDB':'none' , 'customerName':'keine' , 'selected':0}";
			
			foreach( $existingCustomers['data'] as $rowExistingCustomers ) :
			
				if ( $existingCustomerSel == $rowExistingCustomers['customerDB'] ) :
					$selected = 1;
				else :
					$selected = 0;
				endif;
				
				if ( $rowExistingCustomers['customerName'] == '' ) :
					$rowExistingCustomers['customerName'] = $rowExistingCustomers['customerID'];
				endif;
				
				$arrExistingCustomers[] = "{'customerDB':'".$rowExistingCustomers['customerDB']."' , 'customerName':'".str_replace("'","\\'",$rowExistingCustomers['customerName'])."' , 'selected':".$selected."}";
			
			endforeach;
			
			communication_interface::jsExecute("admExistingCustomers = [".implode(",",$arrExistingCustomers)."];");
			
		endif;
	
		communication_interface::jsExecute("adminShowNewCustomerFirstStep( '".$customer."' , '".$customerDB."' , '".$userID."' , '".$userPassword."' , '".$userName."' , '".$userMail."' )");
		
		if ( $customerExists == 1 ) :
		
			communication_interface::jsExecute("adminShowErrorMessage( 'customerExists' ) ");
			
		endif;
	}
	
	public function createCustomerDB( $customerDB ) {
			
		$fb = blFunctionCall('admin.checkForCustomerDB',$customerDB);
			
		if ( !$fb ) :
		
			return $customerDB;
				
		else :

			$customerDB .= '_01';
			return $this->createCustomerDB( $customerDB );
		
		endif;
				
	}

	public function loadNewCustomerSecondStep() {
		
		$lang 				= session_control::getSessionInfo("language");
		$countries 			= blFunctionCall('admin.getCountries',strtolower($lang));
		//communication_interface::alert( $lang );
		$token 				= session_control::getSessionSettings("admin", "token" );
		$customerDB			= '';
		$customerName 		= '';
		$customerName 		= '';
		$customerAddress1 	= '';
		$customerAddress2 	= '';
		$customerAddress3 	= '';
		$customerStreet 	= '';
		$customerPLZ 		= '';
		$customerPlace 		= '';
		$customerCountry 	= 'CH';
		$customerPhone 		= '';
		$customerEMail 		= '';
		
		$fm 	= new file_manager();
		
		if( $fm->setTmpDir($token) ) :
			
			$wizard = unserialize($fm->setFile("newCustomerValues.txt")->getContents());
			
			if ( isset( $wizard['admin']['customerDB'] ) ) 		: $customerDB 		= $wizard['admin']['customerDB'];endif;
			if ( isset( $wizard['admin']['customerName'] ) ) 	: $customerName 	= $wizard['admin']['customerName'];endif;
			if ( isset( $wizard['admin']['customerAddress1'] ) ): $customerAddress1 = $wizard['admin']['customerAddress1']; endif;
			if ( isset( $wizard['admin']['customerAddress2'] ) ): $customerAddress2 = $wizard['admin']['customerAddress2']; endif;
			if ( isset( $wizard['admin']['customerAddress3'] ) ): $customerAddress3	= $wizard['admin']['customerAddress3']; endif;
			if ( isset( $wizard['admin']['customerStreet'] ) )	: $customerStreet	= $wizard['admin']['customerStreet']; endif;
			if ( isset( $wizard['admin']['customerPLZ'] ) ) 	: $customerPLZ 		= $wizard['admin']['customerPLZ']; endif;
			if ( isset( $wizard['admin']['customerPlace'] ) ) 	: $customerPlace 	= $wizard['admin']['customerPlace']; endif;
			if ( isset( $wizard['admin']['customerCountry'] ) ) : $customerCountry 	= $wizard['admin']['customerCountry']; endif;
			if ( isset( $wizard['admin']['customerPhone'] ) ) 	: $customerPhone 	= $wizard['admin']['customerPhone']; endif;
			if ( isset( $wizard['admin']['customerEMail'] ) ) 	: $customerEMail 	= $wizard['admin']['customerEMail']; endif;
		
			// select-werte fuer die auswahl des landes
			if ( $countries ) :
			
				$arrCountries = array();
				
				foreach( $countries['data'] as $rowCountries ) :
				
					if ( $customerCountry == $rowCountries['countryID'] ) :
						$selected = 1;
					else :
						$selected = 0;
					endif;
					//communication_interface::alert( $rowCountries['countryName'].' '.$selected );
					$arrCountries[] = "{'countryID':'".$rowCountries['countryID']."' , 'countryName':'".str_replace("'","\\'",$rowCountries['countryName'])."' , 'selected':".$selected."}";
				
				endforeach;
				
				communication_interface::jsExecute("admCountries = [".implode(",",$arrCountries)."];");
				
			endif;
			
			communication_interface::jsExecute("adminShowNewCustomerSecondStep( '".$customerDB."' , '".$customerName."' , '".$customerAddress1."' , '".$customerAddress2."' , '".$customerAddress3."' , '".$customerStreet."' , '".$customerPLZ."' , '".$customerPlace."' , '".$customerCountry."' , '".$customerPhone."' , '".$customerEMail."' )");
			
		else :
		
			communication_interface::alert( 'no file found' );
			
		endif;
		
	}
	
	public function loadNewCustomerThirdStep() {
		
		$customerPlugins 	= blFunctionCall('admin.getPlugins',1);
		$token 				= session_control::getSessionSettings("admin", "token" );
		$fm 				= new file_manager();
		
		if( $fm->setTmpDir($token) ) :
		
			$wizard = unserialize($fm->setFile("newCustomerValues.txt")->getContents());
			$wizardCustomerPlugins = array();
			
			if( !empty( $customerPlugins['data'] ) ) :
			
				$arrPlugins				= array();
				
				foreach( $customerPlugins['data'] as $row ) :
				
					if ( isset( $wizard['admin'][$row['pluginName']] ) ) :
						//communication_interface::alert( 'pluginName: '.$wizard['admin'][$row['pluginName']] );
						$checked = 1;
						
					else :
					
						$checked = 0;
						
					endif;
					
					$arrPlugins[] 	= "{ 'pluginID':'".$row["pluginID"]."' , 'pluginName':'".$row["pluginName"]."' , 'mandatory':'".$row['mandatory']."' , 'versionID':'".$row['versionID']."' , 'checked':".$checked." }";
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute("admPluginsForNewCustomer = [".implode(",",$arrPlugins)."];");
			communication_interface::jsExecute("adminShowNewCustomerThirdStep()");
			
		else :
		
			communication_interface::alert( 'no file found' );
		
		endif;
		
		
	}
	
	public function loadNewCustomerFourthStep( $number ) {
		
		$token 	= session_control::getSessionSettings("admin", "token" );
		$fm 	= new file_manager();
		
		if( $fm->setTmpDir($token) ) :
		
			$wizard 					= unserialize($fm->setFile("newCustomerValues.txt")->getContents());
			$customerPlugins 			= explode( '@@' , $wizard["admin"]['customerPlugins'] );
			//
			if ( !empty( $customerPlugins[$number] ) ) :
		//communication_interface::alert( $customerPlugins[$number] );
				/* $customerPlugin[0]	= pluginName
				 * $customerPlugin[1]	= pluginID
				 */
				$customerPlugin				= explode( '##' , $customerPlugins[$number] );
				$pluginName					= blFunctionCall('admin.getPluginDetail',$customerPlugin[1]);
				$customerPerPlugin			= blFunctionCall('admin.getCustomersByPlugin',$customerPlugin[1] );
				$pluginGroups 				= blFunctionCall('admin.getPluginGroups',$customerPlugin[1]);
				$arrCustomersPerPlugin[] 	= "{ 'customerName':'Quellmandant' , 'customerDB':'standard' , 'version':'".$pluginName['version'][0]['version']."' , 'checked':0 }";
				
				if( $customerPerPlugin ) :
		//communication_interface::alert( $customerPlugin[1] );
					foreach( $customerPerPlugin as $row ) :
					
						if ( isset( $wizard['admin'][$customerPlugin[0]] ) ) :
						
							/* $customerPluginData[0]	= customerID
							* $customerPluginData[1]	= version
							*/
							$customerPluginData = explode( '##' , $wizard["admin"][$customerPlugin[0]] );
							//communication_interface::alert( 'einzelne plugin werte: '.$customerPluginData[0].' == '.$row['customerID'] );
							if ( $customerPluginData[0] == $row['customerDB'] ) :
							
								$checked = 1;
								
							else :
							
								$checked = 0;
								
							endif;
						
						else :
						
							$checked = 0;
							
						endif;
						
						if ( empty( $row["customerName"] ) ) : $row["customerName"] = $row["customerID"]; endif;
						//communication_interface::alert( $row["customerName"].' '.$row["customerID"].' '.$row["version"] );
						$arrCustomersPerPlugin[] = "{ 'customerDB':'".$row['customerDB']."' , 'customerName':'".$row["customerName"]."' , 'customerID':'".$row["customerID"]."' , 'version':'".$row["version"]."' , 'checked':".$checked."}";
						
					endforeach;
					
				endif;
				
				communication_interface::jsExecute("admCustomersPerPlugin = [".implode(",",$arrCustomersPerPlugin)."];");
					
				if( !empty( $pluginGroups ) ) :
					
					foreach( $pluginGroups as $row ) :
						
						if ( isset( $wizard['admin']['groupID_'.$row['groupID']] ) ) :
						//communication_interface::alert( $wizard['admin']['groupID_'.$row['groupID']] );
							$groupWerte = explode( '##' , $wizard['admin']['groupID_'.$row['groupID']] );
							$selectedDB = $groupWerte[0];
							
						else :
						
							$selectedDB = '';
						
						endif;
						
						$arrPluginGroups[] 	= "{ 'groupID':".$row["groupID"]." , 'groupName':'".$row["groupName"]."' , 'selectedDB':'".$selectedDB."'}";
						
					endforeach;
				
					communication_interface::jsExecute("admPluginGroups = [".implode(",",$arrPluginGroups)."];");
					
				endif;
				
				switch( $customerPlugin[0] ) :
					
					case 'payroll':
						
						if ( isset( $wizard["admin"]['payrollDate'] ) ) :
						
							$dateValues 	= explode( '@@' , $wizard["admin"]['payrollDate'] );
							//communication_interface::alert($wizard["admin"]['payrollDate']);
							$monthSelected 	= $dateValues[0];
							$yearSelected	= $dateValues[1];
							
						else :
						
							$monthSelected 	= 1;
							$yearSelected	= date( 'Y' );
							
						endif;
						
						$year 		= date( 'Y' );
						$startYear 	= ($year-5);
						$endYear 	= ($year+5);
						
						for( $y=$startYear; $y<=$endYear; $y++ ) :
						
							if ( $y == $yearSelected ) :
								$admPayrollYear[] = "{ 'year':".$y." , 'checked':1 }";
							else :
								$admPayrollYear[] = "{ 'year':".$y." , 'checked':0 }";
							endif;
							
						
						endfor;
						
						$admLanguages = array( 'de' , 'fr' , 'it' , 'en' );
						
						foreach( $admLanguages as $rowAdmLanguages ) :
						//communication_interface::alert( $rowAdmLanguages.' == '.$wizard["admin"][$rowAdmLanguages] );
							if ( isset( $wizard["admin"][$rowAdmLanguages] ) && $wizard["admin"][$rowAdmLanguages] == 1 ) :
							
								if ( isset( $wizard["admin"]['standardLanguage'] ) && $wizard["admin"]['standardLanguage'] == $rowAdmLanguages ) :
									$admPayrollLanguages[] = "{ 'wert':'".$rowAdmLanguages."' , 'text':'".$rowAdmLanguages."' , 'checked':1 , 'standard':1  }";
								else :
									$admPayrollLanguages[] = "{ 'wert':'".$rowAdmLanguages."' , 'text':'".$rowAdmLanguages."' , 'checked':1 , 'standard':0  }";
								endif;
								
							else :
							
								if ( $rowAdmLanguages == 'de' ) :
									$admPayrollLanguages[] = "{ 'wert':'".$rowAdmLanguages."' , 'text':'".$rowAdmLanguages."' , 'checked':1 , 'standard':1  }";
								else :
									$admPayrollLanguages[] = "{ 'wert':'".$rowAdmLanguages."' , 'text':'".$rowAdmLanguages."' , 'checked':0 , 'standard':0  }";
								endif;
								
							endif;
						
						endforeach;
						
						communication_interface::jsExecute("admPayrollYear = [".implode(",",$admPayrollYear)."];");
						communication_interface::jsExecute("admPayrollLanguages = [".implode(",",$admPayrollLanguages)."];");
						communication_interface::jsExecute("admPayrollMonth = ".$monthSelected);
						
						break;
				
				endswitch;
				//communication_interface::alert( 'number: '.$number.' Plugin Name: '.$customerPlugin[0].' Plugin ID: '.$customerPlugin[1] );
				communication_interface::jsExecute("adminShowNewCustomerFourthStep(".($number+1)." , ".$customerPlugin[1]." , '".$customerPlugin[0]."' , '".$pluginName['version'][0]['version']."')");
							
			else :
			
				$this->loadNewCustomerFifthStep();
				
			endif;
			
		else :
		
			communication_interface::alert( 'no file found' );
		
		endif;
		
	}
	
	// zusammenfassung
	public function loadNewCustomerFifthStep() {
		
		$token 	= session_control::getSessionSettings("admin", "token" );
		$fm 	= new file_manager();
		if( $fm->setTmpDir($token) ) :
			
			$wizard 			= unserialize($fm->setFile("newCustomerValues.txt")->getContents());
			$customerPlugins 	= explode( '@@' , $wizard['admin']['customerPlugins'] );
				
			for( $i=0; $i<count($customerPlugins); $i++ ) :

				$customerPlugin	= explode( '##' , $customerPlugins[$i] );
				$pluginDetail	= blFunctionCall('admin.getPluginDetail',$customerPlugin[1]);
				
				if ( isset( $wizard['admin'][$customerPlugin[0]] ) ) :
				
					$pluginData = $wizard['admin'][$customerPlugin[0]];
					
				else :
				
					$pluginData = '';
					
				endif;
				
				/* $customerPlugin[0]	= pluginID
				 * $customerPlugin[1]	= version
				 */
				$customerPluginData	= explode( '##' , $pluginData );
				$pluginImg 			= 'green';
				$pluginHref			= '';
				
				if ( $customerPluginData[1] != $pluginDetail['version'][0]['version'] ) :
					
					$pluginImg 	= 'yellow';
					
				endif;
				//communication_interface::alert( $customerPlugin[0]." ".$pluginDetail['data'][0]['pluginName']." ".$pluginImg );
				$arrSelectedPlugins[] 	= "{ 'pluginID':'".$customerPlugin[0]."' , 'pluginName':'".$pluginDetail['data'][0]['pluginName']."' , 'pluginImg':'".$pluginImg."' }";
		
			endfor;
			
			communication_interface::jsExecute("admSelectedPlugins = [".implode(",",$arrSelectedPlugins)."];");
			communication_interface::jsExecute("adminShowNewCustomerFifthStep()");
			
		else :
		
			error_log( "keine datei gefunden\n" , 3 , '/var/log/debug.log' );
			
		endif;
		
	}
	
	// mandant anlegen
	public function newCustomerCreate() {
		
		$token 	= session_control::getSessionSettings("admin", "token" );
		$fm 	= new file_manager();
		
		if( $fm->setTmpDir($token) ) :
		
			$wizard 	= unserialize($fm->setFile("newCustomerValues.txt")->getContents());
			$sortOrder 	= 1;
			$fm = new file_manager();
			$fm->customerSpace('admindev')->setPath('TMP/')->makeDir(); //Verzeichnis erstellen
			$fm->setFile("createNewDB.sql");
			$sql 		= "CREATE DATABASE ".$wizard['admin']['customerDB'].";\n"; //neue Datenbank anlegen
			$fm->putContents( $sql."\n" );
			exec("mysql -u backup -p63i7E24ce < ".$fm->getFullPath()."createNewDB.sql");
			$sql 		= "USE ".$wizard['admin']['customerDB'].";\n";
			
			$mandatoryPlugins = blFunctionCall('admin.getMandatoryPlugins');
			
			if ( $mandatoryPlugins['success'] ) :
			
				$pluginTables = '';
				
				foreach( $mandatoryPlugins['data'] as $rowMandatoryPlugins ) :
			
					//$tableArrayForDump 	= '';
					$standardCustomerDB	= $rowMandatoryPlugins['standardCustomerDB'];
					$pluginTables 		= blFunctionCall('admin.getPluginTables',$rowMandatoryPlugins['versionID']);
					$tableDumpNoData	= array();
					$tableDumpWithData	= array();
					$insertStatement	= '';
					
					if ( $pluginTables ) :
					
						foreach( $pluginTables as $rowPluginTables ) :
						
							$queryColumnsOptions['dbName']		= $standardCustomerDB;
							$queryColumnsOptions['tableName']	= $rowPluginTables['tableName'];
							//$tableArrayForDump[] 				= $rowPluginTables['tableName'];
							$columnsFromTable 					= blFunctionCall('admin.getColumnsFromTable',$queryColumnsOptions);
							//$sql 				.= 'CREATE TABLE `'.$rowPluginTables['tableName'].'` ( ';
							$i 					= 1;
							$insertStatement	= 'INSERT INTO '.$rowPluginTables['tableName'].' (';
							$selectStatement	= 'SELECT ';
							$autoIncrementField = '';
							$dump 				= 0;
							$s = 1;
							
							if ( $rowPluginTables['tableName'] == 'core_group' ) :
								
								$standardCoreGroupDB = $standardCustomerDB;
								
							endif;
							
							if ( $rowPluginTables['withData'] == 1 ) :
								
								$dump = 1;
								$tableDumpWithData[]	= $rowPluginTables['tableName'];
								
							else :
							//communication_interface::alert( $rowPluginTables['tableName'] );
								$tableDumpNoData[] = $rowPluginTables['tableName'];
								
							endif;
								
						//error_log( date('H:i:s').' '.$insertStatement."\n" , 3 , '/var/log/debug.log' );
						endforeach;
						
					else :
					
						communication_interface::alert( 'no tables found for plugin' );
						continue;
					
					endif;
					//error_log( date('H:i:s').' '.$sql."\n" , 3 , '/var/log/debug.log' );
					$pluginDBAndVersion[] = array( 'db' => $rowMandatoryPlugins['pluginName'] , 'pluginID' => $rowMandatoryPlugins['pluginID'] , 'version' => $rowMandatoryPlugins['version'] , 'sortOrder' => $sortOrder );
					$sortOrder++;
					
					if ( !empty( $tableDumpNoData ) ) :
					
						$dumpNoData = '--no-data '.$standardCustomerDB;
						foreach( $tableDumpNoData as $rowTableDumpNoData ) :
					
							$dumpNoData .= ' '.$rowTableDumpNoData;
						
						endforeach;
						//communication_interface::alert( $dumpNoData );
						exec("mysqldump -u backup -p63i7E24ce ".$dumpNoData." > ".$fm->getFullPath().$rowMandatoryPlugins['pluginName']."_noData.sql");
						exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath().$rowMandatoryPlugins['pluginName']."_noData.sql");
						
					endif;
					
					if ( !empty( $tableDumpWithData ) ) :
						$dumpWithData = $standardCustomerDB;
						foreach( $tableDumpWithData as $rowTableDumpWithData ) :
					
							$dumpWithData .= ' '.$rowTableDumpWithData;
						
						endforeach;
						//communication_interface::alert( $dumpWithData );
						exec("mysqldump -u backup -p63i7E24ce ".$dumpWithData." > ".$fm->getFullPath().$rowMandatoryPlugins['pluginName'].".sql");
						exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath().$rowMandatoryPlugins['pluginName'].".sql");
						
					endif;
					
				endforeach;
				
				
				if ( isset( $wizard['admin']['existingCustomerSel'] ) && $wizard['admin']['existingCustomerSel'] != 'none' ) :
				
					// ueberpruefen ob neu angelegter benutzer in der fremd db besteht
					$queryOptionsExistingUser['userID'] = $wizard['admin']['userID'];
					$queryOptionsExistingUser['dbName'] = $wizard['admin']['existingCustomerSel'];
					$existingUser = blFunctionCall('admin.getGroupByUser',$queryOptionsExistingUser);
					
					$sqlUsers = 'SELECT * FROM core_user WHERE uid != "'.$wizard['admin']['userID'].'"';
					$tablesForUsers = 'core_group core_user core_user_group';
					
					communication_interface::alert( $wizard['admin']['existingCustomerSel'].' , '.$sqlUsers );
					
					/*$dumpUsers 		= $wizard['admin']['existingCustomerSel'].' core_user --where="id!='.$wizard['admin']['userID'].'"';
					exec("mysqldump -u backup -p63i7E24ce ".$dumpUsersGroup." > ".$fm->getFullPath()."users.sql");*/
					
					$dumpUsers 		= $wizard['admin']['existingCustomerSel'].' core_user --where="uid != "'.$wizard['admin']['userID'].'"';
					exec("mysqldump -u backup -p63i7E24ce ".$dumpUsers." > ".$fm->getFullPath()."users.sql");
					exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."users.sql");
					
					/*$dumpGroup 		= $wizard['admin']['existingCustomerSel'].' core_group';
					exec("mysqldump -u backup -p63i7E24ce ".$dumpGroup." > ".$fm->getFullPath()."groups.sql");
					
					
					
					$dumpUsersGroup = $wizard['admin']['existingCustomerSel'].' core_user_group --where="core_user_ID NOT IN '.implode(',',$existingUser[0]).'"';
					exec("mysqldump -u backup -p63i7E24ce ".$dumpUsersGroup." > ".$fm->getFullPath()."groupusers.sql");
						
					communication_interface::alert( $dumpWithData );
					exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."groups.sql");	
					
					exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."groupusers.sql");*/
					
					// auto increment der tabelle core_user erhoehen
					$queryOptionsExistingCustomer['sql'] = 'SELECT MAX(id) FROM core_user';
					$queryOptionsExistingCustomer['dbName'] = $wizard['admin']['existingCustomerSel'];
					$numberOfExistingCustomer = blFunctionCall('admin.getSQLResult',$queryOptionsExistingCustomer);
					$autoIncrementUpdate 	  = "ALTER TABLE core_user AUTO_INCREMENT = ".($numberOfExistingCustomer['data'][0][0]+1);
					
				else :
				
					$dumpGroup 		= '--no-create-db --no-create-info '.$standardCoreGroupDB.' core_group';
					exec("mysqldump -u backup -p63i7E24ce ".$dumpGroup." > ".$fm->getFullPath()."groups.sql");
				
				endif;
			
			endif;
			
			// gewaehlte plugins
			$customerPlugins = explode( '@@' , $wizard['admin']['customerPlugins'] );
			//communication_interface::alert( count($customerPlugins) );
			
			for( $cp=0; $cp<count($customerPlugins); $cp++ ) :
		
				$payrollCustomerYearUpdate 		= 0;
				$setDateValue		= 0;
				$languageData 		= 0;
				$selectionedDB		= '';
				$tableDumpNoData	= '';
				$tableDumpWithData	= '';
				$customerPlugin		= explode( '##' , $customerPlugins[$cp] );
				$pluginDetail 		= blFunctionCall('admin.getPluginDetail',$customerPlugin[1]);
				$pluginTables		= blFunctionCall('admin.getPluginTables',$pluginDetail['version'][0]['versionID']);
				
				if ( !empty( $pluginDetail['version'][0]['versionID'] ) ) :
				
					// standardmaessig zu verfuegende datenbank
					$standardCustomerDB = $pluginDetail['version'][0]['standardCustomerDB'];
					// falls fuer das ganze plugin eine andere db gewaehlt wird , wird die standard db geaendert
					if ( isset( $wizard['admin'][$customerPlugin[0]] ) ) :
					
						$customerDB			= explode( '##' , $wizard['admin'][$customerPlugin[0]] );
						if ( $customerDB[0] != 'standard' ) :
							$selectionedDB	= $customerDB[0];
						endif;
						
					endif;
					
					if ( !empty( $selectionedDB ) ) :
						
						$dumpNoData 	= '--no-data '.$standardCustomerDB;
						$dumpWithData 	= '--no-create-db --no-create-info '.$selectionedDB;
						
						foreach( $pluginTables as $rowPluginTables ) :
							
							$dumpNoData 	.= ' '.$rowPluginTables['tableName'];
							
							if ( !empty( $wizard['admin']['groupID_'.$rowPluginTables['groupID']] ) ) :
	
								$groupData 		= explode( '##' , $wizard['admin']['groupID_'.$rowPluginTables['groupID']] );
								$groupDataDB 	= $groupData[0];
								$tableDumpWithDataForeignDB[$groupDataDB][] = $rowPluginTables['tableName'];
								
							else :
							
								$dumpWithData 	.= ' '.$rowPluginTables['tableName'];
							
							endif;
							
						endforeach;
					
						exec("mysqldump -u backup -p63i7E24ce ".$dumpNoData." > ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName']."_noData.sql");
						exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName']."_noData.sql");
						
						exec("mysqldump -u backup -p63i7E24ce ".$dumpWithData." > ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName'].".sql");
						exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName'].".sql");
						
						if( isset( $tableDumpWithDataForeignDB ) ) :
							
							foreach ( $tableDumpWithDataForeignDB as $rowTableDumpWithDataForeignDB => $valueTableDumpWithDataForeignDB ) :
								
								$dump = '--no-create-db --no-create-info '.$rowTableDumpWithDataForeignDB;
								
								foreach( $valueTableDumpWithDataForeignDB as $row ) :
								
									$dump .= ' '.$row;
								
								endforeach;
								
								exec("mysqldump -u backup -p63i7E24ce ".$dump." > ".$fm->getFullPath()."foreignData.sql");
								exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."foreignData.sql");
								
							endforeach;
							
						endif;
						
						// kein update der tabelle payroll_languages
						$languageData = 1;
						
						exec("mysqldump -u backup -p63i7E24ce --opt --skip-extended-insert --no-create-db --no-create-info --no-data --routines ".$selectionedDB." > ".$fm->getFullPath()."storedProcedures.sql");
						exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."storedProcedures.sql");

					else :
					
						foreach( $pluginTables as $rowPluginTables ) :
						
							$groupDataDB = '';
							// checkt ob gruppe einen mandanten gesetzt hat
							if ( !empty( $wizard['admin']['groupID_'.$rowPluginTables['groupID']] ) ) :
	
								$groupData 		= explode( '##' , $wizard['admin']['groupID_'.$rowPluginTables['groupID']] );
								$groupDataDB 	= $groupData[0];
								
							endif;
							
							if ( empty ( $groupDataDB ) ) :
								
								if ( $rowPluginTables['withData'] == 1 ) :
									
									switch( $rowPluginTables['tableName'] ) :
										
										case 'payroll_account': case 'payroll_account_label': case 'payroll_account_linker': case 'payroll_period':
									
											if ( $payrollCustomerYearUpdate == 0 ) :
											
												$customerYearIDOptions['sql'] 		= 'SELECT MAX(payroll_year_ID) as payrollYearID FROM '.$rowPluginTables['tableName'];
												$customerYearIDOptions['dbName'] 	= $standardCustomerDB;
												$customerYearIDSQL					= blFunctionCall('admin.getSQLResult1',$customerYearIDOptions);
			
												if ( $customerYearIDSQL['totalNumber'] > 0 ) :
													
													$payrollCustomerYearID 		= $customerYearIDSQL['data'][0]['payrollYearID'];
													//communication_interface::alert( $payrollCustomerYearID );
												endif;
												
												$payrollCustomerYearUpdate 		= 1;
												$payrollCustomerYearUpdateDB 	= $standardCustomerDB;
											
											endif;
											
											$tableDumpNoData[] = $rowPluginTables['tableName'];
											
											break;
										
										default :
											
											$tableDumpWithData[] = $rowPluginTables['tableName'];
											
										//communication_interface::alert( $selectStatement );
										
									endswitch;
									
								else :
								
									if ( $rowPluginTables['tableName'] == 'payroll_year' ) :
									
										$dateValues 	= explode( '@@' , $wizard["admin"]['payrollDate'] );
										//communication_interface::alert( 'payroll mandant: '.$customerPlugin[0] );
										$customerMonth 				= $dateValues[0];
										$customerYear				= $dateValues[1];
										$startDate					= $customerYear.'-'.str_pad( $customerMonth , 2 , '0' , STR_PAD_LEFT )."-01";
										$endDate					= $customerYear."-12-01";
												
										$sql .= "INSERT INTO payroll_year (`id`,`date_start`,`date_end`) VALUES ( ".$customerYear.",'".$startDate."','".$endDate."' );\n";
										$sql .= "INSERT INTO `payroll_period`(`payroll_year_ID`, `major_period`, `minor_period`, `major_period_associated`, `StatementDate`, `Wage_DateFrom`, `Wage_DateTo`, `Salary_DateFrom`, `Salary_DateTo`, `HourlyWage_DateFrom`, `HourlyWage_DateTo`, `datetime_created`, `core_user_ID_created`, `locked`, `datetime_locked`, `core_user_ID_locked`, `finalized`, `datetime_finalized`, `core_user_ID_finalized`) VALUES (".$customerYear.", ".$customerMonth.", 0, ".$customerMonth.", '0000-00-00', '".$startDate."', '".$endDate."' , '".$startDate."', '".$endDate."', '".$startDate."', '".$endDate."' , '".$startDate."', 0, 0, '0000-00-00', 0, 0, '0000-00-00', 0);\n";
									
									endif;
									
									$tableDumpNoData[] = $rowPluginTables['tableName'];
									
								endif;
							
							else :
							
								$tableDumpNoData[] = $rowPluginTables['tableName'];
								
								if ( $rowPluginTables['tableName'] == 'payroll_languages' ) :
											
									$languageData = 1;
							
								endif;
								
								$tableDumpWithDataForeignDB[$groupDataDB][] = $rowPluginTables['tableName'];
								
							endif;
					
						endforeach;
					
						if ( !empty( $tableDumpNoData ) ) :
							$dumpNoData = '--no-data '.$standardCustomerDB;
							foreach( $tableDumpNoData as $rowTableDumpNoData ) :
								$dumpNoData .= ' '.$rowTableDumpNoData;
							endforeach;
							//communication_interface::alert( $dumpNoData );
							exec("mysqldump -u backup -p63i7E24ce ".$dumpNoData." > ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName']."_noData.sql");
							exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName']."_noData.sql");
						endif;
						
						if ( !empty( $tableDumpWithData ) ) :
							$dumpWithData 	= $standardCustomerDB;
							foreach( $tableDumpWithData as $rowTableDumpWithData ) :
								$dumpWithData .= ' '.$rowTableDumpWithData;
							endforeach;
							//communication_interface::alert( $dumpWithData );
							exec("mysqldump -u backup -p63i7E24ce ".$dumpWithData." > ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName'].".sql");
							exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath().$pluginDetail['data'][0]['pluginName'].".sql");
						endif;
						
						if( isset( $tableDumpWithDataForeignDB ) ) :
							
							foreach ( $tableDumpWithDataForeignDB as $rowTableDumpWithDataForeignDB => $valueTableDumpWithDataForeignDB ) :
								
								$dump = '--no-create-db --no-create-info '.$rowTableDumpWithDataForeignDB;
								
								foreach( $valueTableDumpWithDataForeignDB as $row ) :
								
									$dump .= ' '.$row;
								
								endforeach;
								
								exec("mysqldump -u backup -p63i7E24ce ".$dump." > ".$fm->getFullPath()."foreignData.sql");
								exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."foreignData.sql");
								
							endforeach;
							
						endif;
						
						if ( $payrollCustomerYearUpdate == 1 ) :
						
							$dump = $payrollCustomerYearUpdateDB.' payroll_account payroll_account_label payroll_account_linker payroll_period --where="payroll_year_ID='.$payrollCustomerYearID.'"';
							exec("mysqldump -u backup -p63i7E24ce ".$dump." > ".$fm->getFullPath()."payrollSpecial.sql");
							exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."payrollSpecial.sql");
							/*$sql .= 'UPDATE payroll_account SET payroll_year_ID = '.$customerYear.";\n";
							$sql .= 'UPDATE payroll_account_label SET payroll_year_ID = '.$customerYear.";\n";
							$sql .= 'UPDATE payroll_account_linker SET payroll_year_ID = '.$customerYear.";\n";*/
							
						endif;
						
						exec("mysqldump -u backup -p63i7E24ce --opt --skip-extended-insert --no-create-db --no-create-info --no-data --routines ".$standardCustomerDB." > ".$fm->getFullPath()."storedProcedures.sql");
						exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."storedProcedures.sql");
						
					endif;
					
					$pluginDBAndVersion[] = array( 'db' => $pluginDetail['data'][0]['pluginName'] , 'pluginID' => $customerPlugin[1] , 'version' => $pluginDetail['version'][0]['version'] , 'sortOrder' => $sortOrder );
					$sortOrder++;
					
				else :
				
					error_log( 'no plugin-version found for '.$pluginDetail['data'][0]['pluginID']."\n" , 3 , '/var/log/debug.log' );
				
				endif;
		
			endfor;
			
			$n = 1;
			$insertStatementCustomerPlugin = 'INSERT INTO customer_plugin (`customer_ID`,`plugins_ID`,`version`) VALUES ';
			//$sql .= "TRUNCATE TABLE core_plugins;\n";
			$insertStatementCorePlugins = 'INSERT INTO core_plugins (`name`,`version`,`sort_order`) VALUES ';
			
			foreach( $pluginDBAndVersion as $rowPluginDBAndVersion ) :
				//communication_interface::alert( $rowPluginDBAndVersion['db'] );
				if ( $rowPluginDBAndVersion['db'] == 'payroll' ) :
					
					if ( $languageData == 0 ) :
					
						$sql .= 'INSERT INTO payroll_languages (`core_intl_language_ID`,`DefaultLanguage`,`UseForAccounts`,`UseForEmployees`) VALUES ';
						$admLanguages = array( 'de' , 'fr' , 'it' , 'en' );
						$l = 0;
					
						foreach( $admLanguages as $rowAdmLanguages ) :
							//communication_interface::alert( $rowAdmLanguages.' == '.$wizard["admin"][$rowAdmLanguages] );
							if ( isset( $wizard["admin"][$rowAdmLanguages] ) && $wizard["admin"][$rowAdmLanguages] == 1 ) :
							
								if ( $l > 0  ) :
							
									$sql .= ',';
							
								endif;
							
								$sql .= "('".$rowAdmLanguages."',";
							
								if ( isset( $wizard["admin"]['standardLanguage'] ) && $wizard["admin"]['standardLanguage'] == $rowAdmLanguages ) :
										
									$sql .= '1,';
								
								else :
							
									$sql .= '0,';
								
								endif;
							
								$sql .= '1,1 )';
							
								$l++;
							
							endif;
							
						endforeach;
						
						$sql .= ";\n";
					
					endif;
					
				endif;
				
				$insertStatementCustomerPlugin .= "( '".$wizard['admin']['customer']."' , '".$rowPluginDBAndVersion['pluginID']."' , '".$rowPluginDBAndVersion['version']."' )";
				$insertStatementCorePlugins .= "( '".$rowPluginDBAndVersion['db']."' , '".$rowPluginDBAndVersion['version']."' , ".$rowPluginDBAndVersion['sortOrder']." )";
				
				if ( $n < count( $pluginDBAndVersion ) ) :

					$insertStatementCustomerPlugin 	.= ',';
					$insertStatementCorePlugins 	.= ',';
						
				endif;
				
				$n++;
				
			endforeach;
			
//			$sql .= 'TRUNCATE '
			$sql .= $insertStatementCorePlugins.";\n";
			
			//$fm->putContents( $sql."\n".$insertStatement."\n" ); //tabelle anlegen
			$sql .= "GRANT SELECT,INSERT,UPDATE,DELETE,EXECUTE,DROP,CREATE ON ".$wizard['admin']['customerDB'].".* TO 'debug'@'localhost';\n";
			$sql .= "GRANT SELECT,INSERT,UPDATE,DELETE,EXECUTE,DROP,CREATE ON ".$wizard['admin']['customerDB'].".* TO 'webuser'@'localhost';\n";
			
			$sql .= "USE appcustomers;\n".$insertStatementCustomerPlugin.";\n";
			//communication_interface::alert( session_control::getSessionInfo('db_name').' '.session_control::getSessionInfo('id') );
			$fm->putContents( $sql."\n" ); //tabelle anlegen
			exec("mysql -u backup -p63i7E24ce ".$wizard['admin']['customerDB']." < ".$fm->getFullPath()."createNewDB.sql"); //TODO: ACHTUNG nur für SRV2 gültig!!!
			//sleep( 5 );
			
			$queryOptionCustomer['customerAddress1'] 	= '';
			$queryOptionCustomer['customerAddress2'] 	= '';
			$queryOptionCustomer['customerAddress3'] 	= '';
			$queryOptionCustomer['customer'] 			= $wizard['admin']['customer'];
			$queryOptionCustomer['customerDB'] 			= $wizard['admin']['customerDB'];
			$queryOptionCustomer['customerCreatedDB'] 	= session_control::getSessionInfo('db_name');
			$queryOptionCustomer['customerCreatedID'] 	= session_control::getSessionInfo('id');
			$queryOptionCustomer['customerName']		= $wizard['admin']['customerName'];
			if ( isset( $wizard['admin']['customerAddress1'] ) ): $queryOptionCustomer['customerAddress1']	= $wizard['admin']['customerAddress1']; endif;
			if ( isset( $wizard['admin']['customerAddress2'] ) ): $queryOptionCustomer['customerAddress2']	= $wizard['admin']['customerAddress2']; endif;
			if ( isset( $wizard['admin']['customerAddress3'] ) ): $queryOptionCustomer['customerAddress3']	= $wizard['admin']['customerAddress3']; endif;
			$queryOptionCustomer['customerStreet'] 		= $wizard['admin']['customerStreet'];
			$queryOptionCustomer['customerPostalCode'] 	= $wizard['admin']['customerPLZ'];
			$queryOptionCustomer['customerPlace'] 		= $wizard['admin']['customerPlace'];
			$queryOptionCustomer['customerCountry'] 	= $wizard['admin']['customerCountry'];
			$queryOptionCustomer['customerPhone'] 		= $wizard['admin']['customerPhone'];
			$queryOptionCustomer['customerEMail'] 		= $wizard['admin']['customerEMail'];
			$customerID 								= blFunctionCall('admin.saveNewCustomer',$queryOptionCustomer);
			
			$queryOptionUser['dbName'] 			= $wizard['admin']['customerDB'];
			//communication_interface::alert($wizard['admin']['customerDB']);
			$queryOptionUser['idCoreGroup']		= 1;
			$queryOptionUser['userID']			= $wizard['admin']['userID'];
			$queryOptionUser['userPassword'] 	= $wizard['admin']['userPassword'];
			$queryOptionUser['userName'] 		= $wizard['admin']['userName'];
			$queryOptionUser['userMail'] 		= $wizard['admin']['userMail'];
			$queryOptionUser['language'] 		= session_control::getSessionInfo("language");
			$userID 							= blFunctionCall('admin.saveNewUser',$queryOptionUser);
			
			//sleep( 5 );
			$this->cancelNewCustomer();
			communication_interface::alert( 'mandant angelegt' );
			//communication_interface::jsExecute("$('#modalContainer').mb_close();");
			$this->showCustomers( 1 );
			
		
		else :
		
			error_log( "keine datei gefunden\n" , 3 , '/var/log/debug.log' );
			
		endif;
		
	}
	
	public function createPlugin( $dbName , $pluginID ) {
		
		$pluginGroups 				= blFunctionCall('admin.getPluginGroups',$pluginID);
					
		if ( $pluginGroups ) :
	
			foreach( $pluginGroups as $rowPluginGroups ) :
			
				$queryOptions['versionID'] 	= $rowMandatoryPlugins['versionID'];
				$queryOptions['groupID'] 	= $rowPluginGroups['groupID'];
				//communication_interface::alert( $rowPluginGroups['groupID'] );
				$pluginTables = blFunctionCall('admin.getTablesForVersionGroup',$queryOptions);
			
			endforeach;
	
		endif;
		
	}
	
	public function showPluginDataByCustomers( $wndStatus , $tmpCustomer , $pluginID , $pluginName , $versionID ) {
		
		$queryOptions['tmpCustomer'] 	= $tmpCustomer;
		$queryOptions['pluginID'] 		= $pluginID;
		$queryOptions['pluginName'] 	= $pluginName;
		$fb = blFunctionCall('admin.getPluginDataByCustomers',$queryOptions);
		
		if ( !$fb ) :
		
			communication_interface::alert( $fb );
			
		else :
		
			if ( $wndStatus == 0 ) :
			
				/*$data['pluginGroups']	= $fb['pluginGroups'];
				$data['pluginsData']	= $fb['pluginsData'];*/
				
				$objWindow = new wgui_window("admin", "showPluginDataByCustomersWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showPluginDataByCustomer"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(400);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(true);
				$objWindow->fullscreen(false);
				$objWindow->modal(false);
				$objWindow->loadContent("customer",'',"showPluginDataByCustomersWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
	//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();

			endif;
			
			$arrGroups 		= array();
			
			if( !empty( $fb["pluginGroups"] ) ) :
			
				foreach( $fb["pluginGroups"] as $row ) :
				
					$arrGroups[] 	= "{ 'groupID':".$row["groupID"]." , 'groupName':'".$row["groupName"]."' }";
					
				endforeach;
				
			endif;
			
			$admPluginsCustomer	= array();
			
			if( !empty( $fb["customers"] ) ) :
			
				$admPluginsCustomer[] 	= "{ 'customerID':0 , 'customerDB':'standard' , 'pluginVersion':0 }";
				
				foreach( $fb["customers"] as $row ) :
//communication_interface::alert($row['customerID']);
					$admPluginsCustomer[] 	= "{ 'customerID':".$row["customerID"]." , 'customerDB':'".$row["customerDB"]."' , 'pluginVersion':'".$row['pluginVersion']."' }";
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute("admPluginGroupsCustomer = [".implode(",",$admPluginsCustomer)."]" );
			communication_interface::jsExecute("admPluginGroups = [".implode(",",$arrGroups)."]" );
			communication_interface::jsExecute( "loadPluginData( '".$pluginName."' )" );
			
		endif;
			
	}
	
	// alle plugins anzeigen
	public function showPlugins( $wndStatus ) {
		
		$fb = blFunctionCall('admin.getPlugins',0);
				
		if($fb["success"]) :
		
			if ( $wndStatus == 0 ) :
			
				$data["plugins"] = $fb["data"];
				$objWindow = new wgui_window("admin", "showPluginsWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("allPlugins"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(350);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(true);
				$objWindow->fullscreen(true);
				$objWindow->modal(false);
				$objWindow->loadContent("plugins",'',"showPluginsWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
	//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
			
			endif;
			
			$arrPlugins = array();
			$i = 1;
			if( !empty( $fb["data"] ) ) :
				foreach( $fb["data"] as $row ) :
					$arrPlugins[] = 	"{'id':".$i.", 'pluginID':'".$row["pluginID"]."' , 'pluginName':'".str_replace("'","\\'",$row["pluginName"])."' , 'tablePreface':'".str_replace("'","\\'",$row["tablePreface"])."' , 'mandatory':'".$row["mandatory"]."'}";
					$i++;
				endforeach;
			endif;
			
			communication_interface::jsExecute("data = [".implode(",",$arrPlugins)."];");
			communication_interface::jsExecute("admGritInit( 'plugins' )");
			communication_interface::jsExecute("admGritData( 'plugins' )");
			
		else :
		
			communication_interface::alert( 'no plugins found' );
			$this->showNewPlugin();
			
		endif;
		
	}
	
	public function showPlugin( $wndStatus , $pluginID ) {
		//communication_interface::alert($pluginID);
		$fb = blFunctionCall('admin.getPlugin',$pluginID);
		
		if($fb["success"]) :
		
			$data['pluginID']		= $pluginID;
			$data['pluginName']		= $fb['data'][0]['pluginName'];
			$data['tablePreface']	= $fb['data'][0]['tablePreface'];
			$data['customers']		= $fb['customers'];
			$data['versions'] 		= $fb['versions'];
			
			if ( $wndStatus == 0 ) :
			
				$objWindow = new wgui_window("admin", "showPluginWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showPlugin"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(600);
				$objWindow->windowHeight(520);
				$objWindow->dockable(true);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(false);
				$objWindow->loadContent("plugins",$data,"showPluginWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
	//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
				$objWindow->bringToFront();
				
			endif;
			
			$arrVersions = array();

			if( !empty( $fb["versions"] ) ) :
			
				foreach( $fb["versions"] as $row ) :
				//communication_interface::alert($row['version']);
					$arrVersions[] 	= "{'id':".$row['versionID'].", 'version':'".$row["version"]."' , 'status':".$row["status"]." , 'current':".$row['current']."}";
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute("pluginVersions = [".implode(",",$arrVersions)."];");
			communication_interface::jsExecute("loadPlugin( ".$pluginID." , '".$data['pluginName']."' , '".$data['tablePreface']."' )");
			
			$this->showPluginGroups( $pluginID );
			
		endif; //else{ z.B. eine Fehlermeldung... }
		
	}
	
	public function showNewPlugin( $wndStatus ) {
		//communication_interface::alert($pluginID);
		if ( $wndStatus == 0 ) :
			
			$objWindow = new wgui_window("admin", "showNewPluginWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
			$objWindow->windowTitle($objWindow->getText("showNewPlugin"));
			$objWindow->windowIcon("users24x24.png");
			$objWindow->windowWidth(600);
			$objWindow->windowHeight(520);
			$objWindow->dockable(true);
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->modal(true);
			$objWindow->loadContent("plugins",'',"showNewPluginWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
//				$objWindow->addEventFunction_onResize("");
			$objWindow->showWindow();
			$objWindow->bringToFront();
				
		endif;
		
	}
	
	public function showPluginGroups( $pluginID ) {
		
		$pluginGroups = blFunctionCall('admin.getPluginGroups',$pluginID);
		$arrGroups = array();

		if( !empty( $pluginGroups ) ) :
			
			foreach( $pluginGroups as $row ) :
			//communication_interface::alert($row['groupID']);
				$arrGroups[] 	= "{'id':".$row['groupID'].", 'groupName':'".$row["groupName"]."' }";
					
			endforeach;
				
		endif;
		
		communication_interface::jsExecute("admPluginGroups = [".implode(",",$arrGroups)."];");
		communication_interface::jsExecute("loadPluginGroups( ".$pluginID." )");
		
	}
	
	public function showPluginVersion( $wndStatus , $pluginID , $versionID ) {
		
		$queryOptions['pluginID'] 	= $pluginID;
		$queryOptions['versionID'] 	= $versionID;
		$fb 						= blFunctionCall('admin.getPluginVersion',$queryOptions);
		
		if($fb["success"]) :
		
			$data['pluginID']		= $pluginID;
			$data['versionID']		= $versionID;
			$data['tablePreface']	= $fb['data'][0]['tablePreface'];

			if ( $fb['data'][0]['current'] == 0 ) :
				$data['versionCurrent']	= 'nein';
			else :
				$data['versionCurrent']	= 'ja';
			endif;
			
			//$data['versionTables'] 	= $fb['versionTables'];
			$data['customers'] 		= $fb['customers'];
				
			if ( $wndStatus == 0 ) :
			
				$objWindow = new wgui_window("admin", "showPluginVersionWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showPluginVersion"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(470);
				$objWindow->dockable(true);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(false);
				$objWindow->loadContent("plugins",$data,"showPluginVersionWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
	//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
				$objWindow->bringToFront();
				
			endif;
			
			$arrGroups 		= array();
			$i = 1;
			
			/*foreach( $fb['groupTables'] as $rowGroupTables ) :
				
				communication_interface::alert( $rowGroupTables[0]['groupID'].' , '.$rowGroupTables[0]['tableName'] );
								
							
			endforeach;*/
			
			//$this->showTablesByPluginGroups();
			if( !empty( $fb["pluginGroups"] ) ) :
			
				foreach( $fb["pluginGroups"] as $row ) :
					
					$arrGroups[] = "{ 'groupID':".$row["groupID"]." , 'groupName':'".$row["groupName"]."' }";
					
					if( !empty( $fb["versionTables"] ) ) :
			
						$queryOptions['groupID'] = $row['groupID'];
						
						foreach( $fb["versionTables"] as $rowTables ) :
						
							$queryOptions['tableID'] = $rowTables['tableID'];
							$tableExist = blFunctionCall('admin.getTableForGroup',$queryOptions);
							
							if ( $tableExist ) :
							
								$arrVersionTablesByGroup[$row['groupID']] = "{ 'tableID':".$rowTables["tableID"]." , 'tableName':'".$rowTables["tableName"]."' }";
								
							endif;
			
						endforeach;
							
					endif;
					
				endforeach;
				
			endif;
			
			$arrVersionTables = array();
			if( !empty( $fb["versionTables"] ) ) :
			
				foreach( $fb["versionTables"] as $row ) :
				
					$arrVersionTables[] 	= "{ 'tableID':".$row["tableID"]." , 'tableName':'".$row["tableName"]."' }";
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute("admPluginGroups = [".implode(",",$arrGroups)."];");
			communication_interface::jsExecute("admVersionTables = [".implode(",",$arrVersionTables)."];");
			communication_interface::jsExecute('loadPluginVersion( '.$pluginID.' , '.$versionID.' , "'.$fb['data'][0]['version'].'" , "'.$fb['data'][0]['status'].'" , "'.$data['versionCurrent'].'" )');
			
			/*foreach( $arrVersionTablesByGroup as $rowVersionTablesByGroup => $valueVersionTablesByGroup ) :
			communication_interface::alert($rowVersionTablesByGroup.' '.$valueVersionTablesByGroup);
				communication_interface::jsExecute("admVersionTablesByGroup = [".implode(",",$valueVersionTablesByGroup)."];");
				communication_interface::jsExecute('loadPluginVersionTableByGroups( '.$rowVersionTablesByGroup.' )');
			endforeach;*/
			
		endif; //else{ z.B. eine Fehlermeldung... }
		
		
	}
	
	public function showTablesByGroup( $wndStatus , $pluginID , $versionID , $groupID ) {
		
		$group 					= blFunctionCall('admin.getPluginGroup',$groupID);
		$versionTables 			= blFunctionCall('admin.getVersionTables',$versionID);
		
		if ( $group ) :
		
			$data['pluginID'] 		= $pluginID;
			$data['versionID'] 		= $versionID;
			$data['groupID'] 		= $groupID;
			$data['groupName'] 		= $group['groupName'];
				
			if ( $wndStatus == 0 ) :
				
				//$data['tableNames']	= $group['tableNames'];
				$objWindow = new wgui_window("admin", "showTablesByGroupWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("pluginGroup"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(350);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(false);
				$objWindow->loadContent("plugins",$data,"showTablesByGroupWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
		//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
				
			endif;
			
			$arrVersionTables = array();
			$i = 1;
			if( !empty( $versionTables ) ) :
				foreach( $versionTables as $row ) :
				
					$queryOptions['groupID'] = $groupID;
					$queryOptions['tableID'] = $row['tableID'];
					$tableExist = blFunctionCall('admin.getTablesByGroup',$queryOptions);
					if ( $tableExist ) :
						$checked = 1;
					else :
						$checked = 0;
					endif;
					
					$arrVersionTables[] 	= "{ 'tableID':".$row["tableID"]." , 'tableName':'".$row["tableName"]."' , 'checked':".$checked." }";
					$i++;
				endforeach;
			endif;
			
			communication_interface::jsExecute("admVersionTables = [".implode(",",$arrVersionTables)."];");
			communication_interface::jsExecute("loadTablesByGroup( ".$pluginID." , ".$versionID." , ".$groupID." , '".$group['groupName']."' )");
		
		endif;
		
	}
	
	public function showPluginGroup( $wndStatus , $pluginID , $groupID ) {
		
		$group = blFunctionCall('admin.getPluginGroup',$groupID);
		
		if ( $group ) :
		
			if ( $wndStatus == 0 ) :
			
				$data['pluginID'] 		= $pluginID;
				$data['groupID'] 		= $groupID;
				$data['groupName'] 		= $group['groupName'];
				//$data['tableNames']	= $group['tableNames'];
				$objWindow = new wgui_window("admin", "showPluginGroupWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("pluginGroup"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(140);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(false);
				$objWindow->loadContent("plugins",$data,"showPluginGroupWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
		//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
				
			endif;
		
		endif;
		
	}
	
	public function showAssignPluginTablesToGroup( $wndStatus , $pluginID , $versionID , $groupID ) {
		
		$group 	= blFunctionCall('admin.getPluginGroup',$groupID);
		$tables = blFunctionCall('admin.getVersionTables',$versionID);
		
		if ( $group ) :
		
			if ( $wndStatus == 0 ) :
			
				$data['pluginID'] = $pluginID;
				$data['versionID'] = $versionID;
				$data['groupID'] = $groupID;
				$objWindow = new wgui_window("admin", "showAssignPluginTablesToGroupWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("showAssignPluginTablesToGroup"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight('auto');
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(false);
				$objWindow->loadContent("plugins",$data,"showAssignPluginTablesToGroupWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
		//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();
				
			endif;
			
			$arrVersionTables = array();
			if( !empty( $tables ) ) :
			
				$queryOptions['groupID'] 	= $groupID;
				
				foreach( $tables as $row ) :
				
					$queryOptions['tableID'] = $row['tableID'];	
					$tableExist = blFunctionCall('admin.getTableForGroup',$queryOptions);
					//communication_interface::alert($checked);
					if ( $tableExist ) :
						$checked = 1;
					else :
						$checked = 0;
					endif;
					
					$arrVersionTables[] 	= "{ 'tableID':".$row["tableID"]." , 'tableName':'".$row["tableName"]."' , 'checked':".$checked." }";
					
				endforeach;
				
			endif;
			
			communication_interface::jsExecute("admVersionTables = [".implode(",",$arrVersionTables)."];");
			communication_interface::jsExecute("loadTablesForGroup( ".$pluginID." , ".$versionID." , ".$groupID." , '".$group['groupName']."' )");
			
		
		endif;
		
	}
	
	public function showVersionTablesFromDB( $wndStatus , $pluginID , $versionID , $tablePreface , $customerDB ) {
		
		$queryOptions['versionID'] 		= $versionID;
		$queryOptions['tablePreface'] 	= $tablePreface;
		$queryOptions['customerDB']		= $customerDB;
				
		$tables = blFunctionCall('admin.getVersionTablesFromDB',$queryOptions);
		$data['pluginID'] 		= $pluginID;
		$data['versionID'] 		= $versionID;
		$data['tables'] 		= $tables;
		
		if ( $wndStatus == 0 ) :
			
			//$data['tableNames']	= $group['tableNames'];
			$objWindow = new wgui_window("admin", "showVersionTablesWindow"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
			$objWindow->windowTitle($objWindow->getText("versionTables"));
			$objWindow->windowIcon("users24x24.png");
			$objWindow->windowWidth(650);
			$objWindow->windowHeight(400);
			$objWindow->dockable(false);
			$objWindow->buttonMaximize(false);
			$objWindow->resizable(false);
			$objWindow->fullscreen(false);
			$objWindow->modal(false);
			$objWindow->loadContent("plugins",$data,"showVersionTablesWindow"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
	//				$objWindow->addEventFunction_onResize("");
			$objWindow->showWindow();
			
		endif;
		
		if ( $tables ) :
		
			foreach( $tables['data'] as $rowTables ) :
				
				$queryOptionsTable['versionID'] = $versionID;
				$queryOptionsTable['table'] 	= $rowTables[0];
				$isInTable 						= blFunctionCall('admin.getTableDetail',$queryOptionsTable);
				
				if ( $isInTable ) :
					$checked 	= $isInTable[0]['withData'];;
					$tableID	= $isInTable[0]['tableID'];
					$groupID	= $isInTable[0]['groupID'];
					//communication_interface::alert( $isInTable[0]['groupID'] );
				else :
					$checked 	= 0;
					$tableID	= 0;
					$groupID	= 0;
				endif;
				
				$arrTables[] 	= "{ 'tableName':'".$rowTables[0]."' , 'checked':".$checked." , 'tableID':".$tableID." , 'groupID':".$groupID." }";
				
			endforeach;
			
			communication_interface::jsExecute( "admVersionTables = [".implode(",",$arrTables)."];" );
			
			$pluginGroups = blFunctionCall('admin.getPluginGroups',$pluginID);
			$arrGroups = array();
	
			if( !empty( $pluginGroups ) ) :
				
				foreach( $pluginGroups as $row ) :
				//communication_interface::alert($row['groupID']);
					$arrGroups[] 	= "{'groupID':".$row['groupID'].", 'groupName':'".$row["groupName"]."' }";
						
				endforeach;
					
			endif;
			
			communication_interface::jsExecute( 'admPluginGroups = ['.implode(',',$arrGroups).'];' );
			communication_interface::jsExecute( 'admInitVersionTables('.$tables['totalNumber'].')' );
			
		else :
		
			communication_interface::alert( 'no tables found' );
		
		endif;
		
	}
	
	public function eventListener($eventName, $eventParameters) {
		global $aafwConfig;

		switch($eventName) {
		case 'core.bootLoadMenu':
			uiFunctionCall('baseLayout.appMenuAddSection','admin','Applikationsverwaltung');
			uiFunctionCall('baseLayout.appMenuAddItem','admin','menuadminCustomers','Mandanten','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','showCustomers();return false;');
			//uiFunctionCall('baseLayout.appMenuAddItem','admin','menuadminStandardCustomers','StandardMandanten','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','showStandardCustomers();return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','admin','menuadminPlugins','Plugins','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','showPlugins();return false;');
			//uiFunctionCall('baseLayout.appMenuAddItem','admin','menuadminbeGr','Benutzer/Gruppen','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\'admin.showUserGroup\');return false;');
			break;
		case 'core.bootComplete':
//			blFunctionCall('admin.onBootComplete');
			break;
		}
	}
}

$SYS_PLUGIN["ui"]["admin"] = new admin_UI();

?>
