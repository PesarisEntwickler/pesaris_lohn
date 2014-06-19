<?php
// WICHTIG: Datenbank-Zugriffe nur hier im Business-Logic-Plugin!

class admin_BL {

	public function sysListener($functionName, $functionParameters) {
		
		switch($functionName) :
		
			case 'admin.getCustomers':
				return $this->getCustomers();
				break;
			
			case 'admin.getCustomersByPlugin':
				return $this->getCustomersByPlugin( $functionParameters[0] );
				break;
			
			case 'admin.getCustomer':
				return $this->getCustomer( $functionParameters[0] );
				break;
			
			case 'admin.checkForCustomerDB':
				return $this->checkForCustomerDB( $functionParameters[0] );
				break;
			
			case 'admin.saveNewCustomer':
				return $this->saveNewCustomer( $functionParameters[0]['customer'] , $functionParameters[0]['customerDB'] , $functionParameters[0]['customerCreatedDB'] , $functionParameters[0]['customerCreatedID'] , $functionParameters[0]['customerName'] , $functionParameters[0]['customerAddress1'] , $functionParameters[0]['customerAddress2'] , $functionParameters[0]['customerAddress3'] , $functionParameters[0]['customerStreet'] , $functionParameters[0]['customerPostalCode'] , $functionParameters[0]['customerPlace'] , $functionParameters[0]['customerCountry'] , $functionParameters[0]['customerPhone'] , $functionParameters[0]['customerEMail'] );
				break;
			
			case 'admin.saveNewUser':
				return $this->saveNewUser( $functionParameters[0]['dbName'] , $functionParameters[0]['idCoreGroup'] , $functionParameters[0]['userID'] , $functionParameters[0]['userPassword'] , $functionParameters[0]['userName'] , $functionParameters[0]['userMail'] , $functionParameters[0]['language'] );
				break;
			
			case 'admin.getCurrentYear':
				return $this->getCurrentYear( $functionParameters[0]['dbName'] );
				break;
			
			case 'admin.getPluginDataByCustomers':
				return $this->getPluginDataByCustomers( $functionParameters[0]['tmpCustomer'] , $functionParameters[0]['pluginID'] , $functionParameters[0]['pluginName'] );
				break;
			
			case 'admin.getPluginsByCustomer':
				return $this->getPluginsByCustomer( $functionParameters[0] );
				break;
			
			case 'admin.getPluginByCustomers':
				return $this->getPluginByCustomers( $functionParameters[0] );
				break;
			
			case 'admin.getPlugins':
				return $this->getPlugins( $functionParameters[0] );
				break;
			
			case 'admin.getMandatoryPlugins':
				return $this->getMandatoryPlugins();
				break;
			
			case 'admin.getPluginDetail':
				return $this->getPluginDetail( $functionParameters[0] );
				break;
			
			case 'admin.getPlugin':
				return $this->getPlugin( $functionParameters[0] );
				break;
			
			case 'admin.saveNewPlugin':
				return $this->saveNewPlugin( $functionParameters[0]['plugin'] , $functionParameters[0]['version'] , $functionParameters[0]['tablePreface'] );
				break;
			
			case 'admin.savePluginVersion':
				return $this->savePluginVersion( $functionParameters[0]['pluginID'] , $functionParameters[0]['version'] , $functionParameters[0]['newVersionStatus'] , $functionParameters[0]['isCurrent'] );
				break;
			
			case 'admin.getPluginVersion':
				return $this->getPluginVersion( $functionParameters[0]['pluginID'] , $functionParameters[0]['versionID'] );
				break;
			
			case 'admin.saveTableByGroup':
				return $this->saveTableByGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['tableID'] );
				break;
			
			case 'admin.deleteTableByGroup':
				return $this->deleteTableByGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['tableID'] );
				break;
			
			case 'admin.getVersionDetail':
				return $this->getVersionDetail( $functionParameters[0]['versionID'] );
				break;
			
			case 'admin.getTablesForVersion':
				return $this->getTablesForVersion( $functionParameters[0] );
				break;
			
			case 'admin.getPluginGroup':
				return $this->getPluginGroup( $functionParameters[0] );
				break;
			
			case 'admin.getPluginGroups':
				return $this->getPluginGroups( $functionParameters[0] );
				break;
			
			case 'admin.getPluginTables':
				return $this->getPluginTables( $functionParameters[0] );
				break;
			case 'admin.getTablesForVersionGroup':
				return $this->getTablesForVersionGroup( $functionParameters[0]['versionID'] , $functionParameters[0]['groupID'] );
				break;
			
			case 'admin.savePluginGroup':
				return $this->savePluginGroup( $functionParameters[0]['pluginID'] , $functionParameters[0]['groupName'] );
				break;
			
			case 'admin.editPluginGroup':
				return $this->editPluginGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['groupName'] );
				break;
			
			case 'admin.deletePluginGroup':
				return $this->deletePluginGroup( $functionParameters[0] );
				break;
			
			case 'admin.getVersionTables':
				return $this->getVersionTables( $functionParameters[0] );
				break;
			
			case 'admin.getTablesByGroup':
				return $this->getTablesByGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['tableID'] );
				break;
			
			case 'admin.savePluginTable':
				return $this->savePluginTable( $functionParameters[0]['pluginID'] , $functionParameters[0]['tableName'] );
				break;
			
			case 'admin.getTableDetail':
				return $this->getTableDetail( $functionParameters[0]['versionID'] , $functionParameters[0]['table'] );
				break;
			
			case 'admin.getVersionTablesFromDB':
				return $this->getVersionTablesFromDB( $functionParameters[0]['versionID'] , $functionParameters[0]['tablePreface'] , $functionParameters[0]['customerDB'] );
				break;
			
			case 'admin.saveVersionTable':
				return $this->saveVersionTable( $functionParameters[0]['versionID'] , $functionParameters[0]['table'] , $functionParameters[0]['withData'] , $functionParameters[0]['groupID'] );
				break;
			
			case 'admin.saveTableForGroup':
				return $this->saveTableForGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['tableID'] );
				break;
			
			case 'admin.deleteTableForGroup':
				return $this->deleteTableForGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['tableID'] );
				break;
			
			case 'admin.getTableForGroup':
				return $this->getTableForGroup( $functionParameters[0]['groupID'] , $functionParameters[0]['tableID'] );
				break;
			
			case 'admin.getSQLResult':
				return $this->getSQLResult( $functionParameters[0]['sql'] , $functionParameters[0]['dbName'] );
				break;
			
			case 'admin.getSQLResult1':
				return $this->getSQLResult1( $functionParameters[0]['sql'] , $functionParameters[0]['dbName'] );
				break;
			
			case 'admin.executeSQLResult':
				return $this->executeSQLResult( $functionParameters[0]['sql'] , $functionParameters[0]['dbName'] );
				break;
			
			case 'admin.getColumnsFromTable':
				return $this->getColumnsFromTable( $functionParameters[0]['dbName'] , $functionParameters[0]['tableName'] );
				break;
			
			
			case 'admin.getCountries':
				return $this->getCountries( $functionParameters[0] );
				break;
			
			case 'admin.getGroupByUser':
				return $this->getGroupByUser( $functionParameters[0]['dbName'] , $functionParameters[0]['userID'] );
				break;
			
			case 'admin.getUserGroup':
				return $this->getUserGroup();
				break;
			
			default:
				return "Funktion unbekannt";
				break;
			
		endswitch;
	}
	
	
	public function getCustomers() {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$result						= $system_database_manager->executeQuery("SELECT customer.id as customerID, customer.name as customerName , customer.databaseName as customerDB, customer.active , customer.name , customer.street , customer.postalcode , customer.place FROM customer ORDER BY customer.id", "admin_getCustomers" );
		$response					= array();
		
		if(count($result) < 1) :
		
			$response["success"] 	= false;
			$response["data"] 		= "no data found";
			error_log("No customers found\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["data"] 		= $result;
			
		endif;
		
		return $response;
		
	}
	
	public function getCustomersByPlugin( $pluginID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$result						= $system_database_manager->executeQuery('SELECT customer.name as customerName , customer.databaseName as customerDB , customer_plugin.customer_ID as customerID , customer_plugin.version FROM customer , customer_plugin WHERE customer.id = customer_plugin.customer_ID AND customer_plugin.plugins_ID = '.$pluginID.' AND version IS NOT NULL ORDER BY customer_plugin.customer_ID', 'admin_getCustomersByPlugin' );
		error_log( date('H:i:s')." SELECT customer.name as customerName , customer.databaseName as customerDB , customer_plugin.customer_ID as customerID , customer_plugin.version FROM customer , customer_plugin WHERE customer.id = customer_plugin.customer_ID AND customer_plugin.plugins_ID = ".$pluginID." AND version IS NOT NULL ORDER BY customer_plugin.customer_ID\n" , 3 , '/var/log/debug.log' );
		if( count( $result ) > 0 ) :
		
			return $result;
			
		else :
		
			return false;
			error_log("No Customers for Plugin".$pluginID."\n", 3, "/var/log/debug.log");
			
		endif;
		
	}
	
	public function getCustomer( $customerID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$result						= $system_database_manager->executeQuery("SELECT customer.id as customerID, customer.databaseName as databaseName, customer.active , customer.datetime_created as datetimeCreated , customer.datetime_deleted as datetimeDeleted , customer.datetime_changed as datetimeChanged , customer.name , customer.address1 , customer.address2 , customer.address3 , customer.street , customer.postalcode , customer.place , customer.country , customer.phone , customer.email FROM customer WHERE id = '".$customerID."'" , "admin_getCustomer" );
		$response					= array();
error_log( "SELECT customer.id as customerID, customer.databaseName as databaseName, customer.active , customer.datetime_created as datetimeCreated , customer.datetime_deleted as datetimeDeleted , customer.datetime_changed as datetimeChanged , customer.name , customer.address1 , customer.address2 , customer.address3 , customer.street , customer.postalcode , customer.place , customer.country , customer.phone , customer.email FROM customer WHERE id = '".$customerID."'\n" , 3 , '/var/log/debug.log' );		
		if( count($result) == 0 ) :
		
			$response["success"] 	= false;
			$response["data"] 		= "no data found";
			error_log(count($result)."\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["data"] 		= $result;
			
		endif;
		
		return $response;
		
	}
	
	
	public function checkForCustomerDB( $customerDB ) {
		
		$system_database_manager 	= system_database_manager::getInstance();

		if ( $system_database_manager->selectDB( $customerDB ) ) :
		
			return true;
		
		else :
		//error_log( $customerDB."\n" , 3 , '/var/log/debug.log' );
			return false;
		
		endif;
		
	}
	
	
	public function saveNewCustomer( $customer , $customerDB , $customerCreatedDB , $customerCreatedID , $customerName , $customerAddress1 , $customerAddress2 , $customerAddress3 , $customerStreet , $customerPostalcode , $customerPlace , $customerCountry , $customerPhone , $customerEMail ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		
		$system_database_manager->executeUpdate("INSERT INTO `customer` (`id`,`databaseName`,`active`,`datetime_created`,`customer_created_customer_DB`,`customer_created_core_user_ID`,`name`,`address1`,`address2`,`address3`,`street`,`postalcode`,`place`,`country`,`phone`,`email`) VALUES ('".$customer."','".$customerDB."',1,'".date( 'Y-m-d H:i:s' )."','".$customerCreatedDB."',".$customerCreatedID.",'".$customerName."','".$customerAddress1."','".$customerAddress2."','".$customerAddress3."','".$customerStreet."','".$customerPostalcode."','".$customerPlace."','".$customerCountry."','".$customerPhone."','".$customerEMail."')" , "saveNewCustomer" );
		$idCustomer = $system_database_manager->getLastInsertId();
		
		if( $idCustomer ) :
		
			return $idCustomer;
			
		else :
		
			return false;
			
		endif;
		
	}
	
	
	public function saveNewUser( $dbName , $idCoreGroup , $userID , $userPassword , $userName , $userEMail , $language ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( $dbName );
		$system_database_manager->executeUpdate("INSERT INTO `core_user` (`uid`,`pwd`,`full_name`,`email`,`language`,`active`,`datetime_create`) VALUES ( '".$userID."' , PASSWORD('".$userPassword."') , '".$userName."' , '".$userEMail."' , '".$language."' , 1 ,'".date( 'Y-m-d H:i:s' )."')" , "admin.saveUser" );
		error_log( date( 'H:i:s' ).' '.$dbName." INSERT INTO `core_user` (`uid`,`pwd`,`full_name`,`email`,`language`,`active`,`datetime_create`) VALUES ( '".$userID."' , PASSWORD('".$userPassword."') , '".$userName."' , '".$userEMail."' , '".$language."' , 1 ,'".date( 'Y-m-d H:i:s' )."')\n" , 3 , '/var/log/debug.log' );
		$idUser 					= $system_database_manager->getLastInsertId();
		
		if ( $idUser ) :
		
			$system_database_manager->executeUpdate("INSERT INTO `core_user_group` (`core_user_ID`,`core_group_ID`) VALUES (".$idUser.",".$idCoreGroup.")" , "admin.saveUserGroup" );
			return $idUser;
		
		else :
		
			return false;
		
		endif;
	
	}
	
	public function getCurrentYear( $dbName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( $dbName );
		$result = $system_database_manager->executeQuery("SELECT MAX(payroll_year_ID) as currentYear FROM payroll_account" , "admin.saveUser" );
		error_log( "SELECT MAX(payroll_year_ID) as currentYear FROM payroll_account\n" , 3 , '/var/log/debug.log' );
		if ( count($result) > 0 ) :
		
			return $result;
		
		else :
		
			return false;
		
		endif;
	
	}
	
	public function getPluginByCustomers( $pluginName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		
		$customers 			= $this->getCustomers();
		$customerPlugins 	= '';
			
		foreach( $customers['data'] as $rowCustomers ) :
		
			if ( $rowCustomers['customerDB'] != 'appcustomers' ) :
			
				$system_database_manager->selectDB( $rowCustomers['customerDB'] );
				$pluginByCustomer = $system_database_manager->executeQuery("SELECT core_plugins.version FROM core_plugins WHERE core_plugins.name = '".$pluginName."'", "admin_getPluginByCustomers" );
				
				if ( count( $pluginByCustomer > 1 ) ) :
					$customerPlugins[] = array( 'customerDB' => $rowCustomers['customerDB'] , 'pluginVersion' => $pluginByCustomer );
				endif;
				
			endif;
			
		endforeach;
			
			
		if ( !empty( $customerPlugins ) ) :
			$response["success"] 	= true;
			$response['plugins']	= $customerPlugins;
		else :
			$response["success"] 	= false;
			$response["plugins"] 	= "no data found";
			error_log("No Customer Plugins found\n", 3, "/var/log/debug.log");
		endif;
		
		return $response;
		
	}
	
	public function getPluginsByCustomer( $customerDB ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( $customerDB );
		$pluginsByCustomer 			= $system_database_manager->executeQuery("SELECT core_plugins.id as pluginID, core_plugins.name as pluginName, core_plugins.version FROM core_plugins", "admin_getPluginsByCustomer" );
		$response					= array();
		
		if ( count( $pluginsByCustomer ) < 1 ) :
		
			$response["success"] 	= false;
			$response["plugins"] 	= "no data found";
			error_log("No Plugins by customer found".$customerDB."\n", 3, "/var/log/debug.log");
			
		else :

			$response["success"] 		= true;
			$response['plugins']		= $pluginsByCustomer;
			
		endif;
		
		return $response;
		
	}
	
	public function getPluginDataByCustomers( $tmpCustomer_ID , $pluginID , $pluginName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$customers					= $this->getCustomersByPlugin( $pluginName );
		$pluginGroups				= $this->getPluginGroups( $pluginID );
		$response					= array();
		
		if( count($customers) == 0 ) :
		
			$response["success"] 		= false;
			$response["pluginData"] 	= false;
			error_log( date('d.m.Y H:i:s')." no plugin data\n", 3, "/var/log/debug.log");
			
		else :	
			
			$response['success'] 		= true;
			$response['customers']		= $customers;
			$response['pluginGroups']	= $pluginGroups;
			
		endif;
		
		return $response;
		
	}
	
	public function getPlugins( $currentVersion ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		if ( $currentVersion == 1 ) :
			$sql 					= "SELECT plugins.id as pluginID, plugins.name as pluginName, plugins.table_preface as tablePreface , plugins.mandatory , plugin_version.id as versionID , plugin_version.version FROM plugins, plugin_version WHERE plugins.id = plugin_version.plugins_ID AND plugin_version.current = 1 ORDER BY plugins.mandatory DESC,plugin_version.current,plugins.id";
			$result					= $system_database_manager->executeQuery( $sql, "admin.getPlugins" );
			error_log( $sql."\n" , 3 , '/var/log/debug.log');
		else :
			$result					= $system_database_manager->executeQuery("SELECT plugins.id as pluginID, plugins.name as pluginName, plugins.table_preface as tablePreface , plugins.mandatory FROM plugins ORDER BY plugins.mandatory DESC ,plugins.id", "admin_getPlugins" );
		endif;
		$response					= array();
		
		if( count($result) < 1 ) :
		
			$response["success"] 	= false;
			$response["data"] 		= "no data found";
			error_log("No Plugins found\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["data"] 		= $result;
			
		endif;
		
		return $response;
		
	}
	
	public function getMandatoryPlugins() {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$result						= $system_database_manager->executeQuery("SELECT plugins.id as pluginID, plugins.name as pluginName, plugins.table_preface as tablePreface , plugin_version.id as versionID , plugin_version.version , plugin_version.standard_customer_DB as standardCustomerDB FROM plugins, plugin_version WHERE plugins.mandatory= 1 AND plugins.id = plugin_version.plugins_ID AND plugin_version.current = 1" , 'admin.getMandatoryPlugins' );
		$response					= array();
		//error_log( "SELECT plugins.id as pluginID, plugins.name as pluginName, plugins.table_preface as tablePreface , plugin_version.id as versionID , plugin_version.version FROM plugins, plugin_version WHERE plugins.mandatory= 1 AND plugins.id = plugin_version.plugins_ID AND plugin_version.current = 1\n" , 3 , '/var/log/debug.log');
		if( count($result) < 1 ) :
		
			$response["success"] 	= false;
			$response["data"] 		= "no data found";
			error_log("No mandatory plugins found\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["data"] 		= $result;
			
		endif;
		
		return $response;
		
	}
	
	public function getPluginDetail( $pluginID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$pluginName					= $system_database_manager->executeQuery("SELECT plugins.name as pluginName, plugins.table_preface as tablePreface FROM plugins WHERE id = ".$pluginID , "admin_getPlugin" );
		$pluginVersions				= $system_database_manager->executeQuery("SELECT plugin_version.id as versionID, plugin_version.version, plugin_version.status , plugin_version.standard_customer_DB as standardCustomerDB FROM plugin_version WHERE plugins_ID = ".$pluginID." AND plugin_version.current = 1 ORDER BY current DESC, version DESC" , "admin_getPluginVersion" );
		//error_log( "SELECT plugin_version.id as versionID, plugin_version.version, plugin_version.status , plugin_version.standard_customer_DB as standardCustomerDB FROM plugin_version WHERE plugins_ID = ".$pluginID." AND plugin_version.current = 1 ORDER BY current DESC, version DESC\n" , 3 , '/var/log/debug.log' );
		if(count($pluginName) < 1) :
		
			$response["success"] 	= false;
			error_log("Keine Plugin-Detail gefunden".$pluginID."\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["errCode"] 	= 0;
			$response["data"] 		= $pluginName;
			
			if ( count( $pluginVersions ) > 0 ) :
			
				$response["version"]	= $pluginVersions;
				
			else :
			
				$response['version'] 	= false;
				
			endif;
			
		endif;
		
		return $response;
		
	}
	
	public function getPlugin( $pluginID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		$pluginName					= $system_database_manager->executeQuery("SELECT plugins.name as pluginName, plugins.table_preface as tablePreface FROM plugins WHERE id = ".$pluginID , "admin_getPlugin" );
		$pluginVersions				= $system_database_manager->executeQuery("SELECT plugin_version.id as versionID, plugin_version.version, plugin_version.status, plugin_version.current FROM plugin_version WHERE plugins_ID = ".$pluginID." ORDER BY current DESC, version DESC" , "admin_getPluginVersion" );
		
		$customers					= $this->getCustomers();
		$response					= array();
		
		if(count($pluginName) < 1) :
		
			$response["success"] 	= false;
			error_log("Keine Plugin-Detail gefunden\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["errCode"] 	= 0;
			$response["data"] 		= $pluginName;
			
			if ( count( $customers ) > 0 ) :
				$response["customers"]	= $customers['data'];
			else :
				$response['customers'] = false;
			endif;
			
			if ( count( $pluginVersions ) > 0 ) :
				$response["versions"]	= $pluginVersions;
			else :
				$response['versions'] 	= false;
			endif;
			
		endif;
		
		return $response;
		
	}
	
	public function saveNewPlugin( $plugin , $version , $tablePreface ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		if ( !$system_database_manager ) :
			return 'no db connection';
		elseif ( !$system_database_manager->selectDB( 'appcustomers' ) ) :
			return 'no db found';
		else :
			if( !$system_database_manager->executeUpdate("INSERT INTO `plugins` (`name`,`table_preface`) VALUES ('".$plugin."','".$tablePreface."')" , "savePlugins" ) ):
				return 'no db insert';
			else :
				$idPlugin = $system_database_manager->getLastInsertId();
				if ( !$system_database_manager->executeUpdate("INSERT INTO `plugin_version` (`plugins_ID`,`version`,`status`,`current`) VALUES (".$idPlugin.",'".$version."','active',1)" , "savePluginVersions" ) ) :
					return 'no db insert';
				else :
					return 'db insert successfully '.$idPlugin;
				endif;
			endif;
		endif;
		
	}
	
	
	public function getPluginVersion( $pluginID , $versionID ) {
		
		$versionData	= $this->getVersionDetail( $versionID );
		$response		= array();
		
		if(count($versionData) < 1) :
		
			$response["success"] 	= false;
			error_log('No plugin version'.$pluginID.' '.$versionID."\n", 3, "/var/log/debug.log");
			
		else :
		
			$response["success"] 	= true;
			$response["errCode"] 	= 0;
			$response["data"] 		= $versionData;
			
			$customers				= $this->getCustomers();
			$pluginGroups			= $this->getPluginGroups( $pluginID );
			$versionTables			= $this->getVersionTables( $versionID );
			
			if ( count( $customers ) > 0 ) :
				$response["customers"]	= $customers['data'];
			else :
				$response['customers'] 	= false;
			endif;
			
			if ( count( $versionTables ) > 0 ) :
				$response["versionTables"]	= $versionTables;
			else :
				$response['versionTables'] 	= false;
			endif;
			
			if ( count( $pluginGroups ) > 0 ) :
				
				foreach( $pluginGroups as $rowPluginGroups ) :
				
					$tablesByGroup = $this->getVersionTablesByGroup( $versionID , $rowPluginGroups['groupID'] );
					
					if ( $tablesByGroup ) :
					
						$versionTablesByGroup[$rowPluginGroups['groupID']]	= $tablesByGroup;
						
					else :
					
						$versionTablesByGroup[$rowPluginGroups['groupID']]	= 0;
						
					endif;
					
				endforeach;
				
				$response["pluginGroups"]	= $pluginGroups;
				$response['groupTables'] 	= $versionTablesByGroup;
				
			else :
			
				$response['pluginGroups'] 	= false;
				
			endif;
			
		endif;
		
		return $response;
		
	}
	
	public function saveTableByGroup( $groupID , $tableID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$result 					= $system_database_manager->executeQuery( "SELECT adm_group_tables.id FROM adm_group_tables WHERE adm_plugin_groups_ID = ".$groupID." AND adm_version_tables_ID = ".$tableID  , "admin.saveTableByGroup");
		
		if( !count( $result ) ) :
		
			$system_database_manager->executeUpdate("INSERT INTO `adm_group_tables` (`adm_plugin_groups_ID`,`adm_version_tables_ID`) VALUES (".$groupID.",".$tableID.")" , "insertTableByGroup" );
		
		endif;
		
	}
	
	public function deleteTableByGroup( $groupID , $tableID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$result 					= $system_database_manager->executeQuery( "SELECT adm_group_tables.id FROM adm_group_tables WHERE adm_plugin_groups_ID = ".$groupID." AND adm_version_tables_ID = ".$tableID  , "admin.saveTableByGroup");
		
		if( count( $result ) > 0 ) :
		
			$system_database_manager->executeUpdate("DELETE FROM `adm_group_tables` WHERE adm_plugin_groups_ID = ".$groupID." AND adm_version_tables_ID = ".$tableID , "deleteTableByGroup" );
		
		endif;
		
	}
	
	public function getVersionDetail( $versionID ) {
		
		$system_database_manager	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		//error_log( $versionID.' '.date('H:i:s')." SELECT plugins.name as pluginName , plugins.table_preface as tablePreface , plugin_version.version, plugin_version.plugins_ID as pluginID , plugin_version.status , plugin_version.current FROM plugins , plugin_version WHERE plugins.id = plugin_version.plugins_ID AND plugin_version.id = ".$versionID."\n" , 3 , '/var/log/debug.log' );
		$version					= $system_database_manager->executeQuery("SELECT plugins.name as pluginName , plugins.table_preface as tablePreface , plugin_version.version, plugin_version.plugins_ID as pluginID , plugin_version.status , plugin_version.current FROM plugins , plugin_version WHERE plugins.id = plugin_version.plugins_ID AND plugin_version.id = ".$versionID , "admin_getVersionDetail" );
		
		if ( count( $version ) > 0 ) :
		
			return  $version;
		
		else :
		
			return false;
		
		endif;
		
	}
	
	public function savePluginVersion( $pluginID , $version , $newVersionStatus , $isCurrent ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'appcustomers' );
		
		if ( $isCurrent == 1 ) :
		
			$resultIdIsCurrent = $system_database_manager->executeQuery("SELECT plugin_version.id FROM plugin_version WHERE plugin_version.plugins_ID = ".$pluginID." AND current = 1", "admin_getUserGroup" );
			
			if ( count($resultIdIsCurrent) > 0 ) :
				$system_database_manager->executeUpdate( "UPDATE `plugin_version` set `current` = 0 WHERE plugin_version.id = ".$resultIdIsCurrent[0]['id'] );
			endif;
			
		endif;
			
		if ( !$system_database_manager->executeUpdate("INSERT INTO `plugin_version` (`version`,`plugins_ID`,`status`,`current`) VALUES ('".$version."',".$pluginID.",'".$newVersionStatus."',".$isCurrent.")" , "savePluginVersion" ) ) :
			return false;
		else :
			return true;
		endif;
		
	}
	
	public function getVersionTables( $versionID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$versionTables				= $system_database_manager->executeQuery("SELECT adm_version_tables.id as tableID, adm_version_tables.table_name as tableName FROM adm_version_tables WHERE versions_ID = ".$versionID." ORDER BY adm_version_tables.table_name" , "admin_getVersionTables" );

		if ( count( $versionTables ) > 1 ) :
			
			return $versionTables;
			
		else :
		
			return false;
		
		endif;
	}
	
	public function getVersionTablesByGroup( $versionID , $groupID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		
		$versionTablesByGroup 		= $system_database_manager->executeQuery("SELECT adm_version_tables.id as tableID, adm_version_tables.table_name as tableName FROM adm_version_tables , adm_group_tables WHERE versions_ID = ".$versionID." AND adm_group_tables.adm_plugin_groups_ID = ".$groupID." AND adm_group_tables.adm_version_tables_ID = adm_version_tables.id ORDER BY adm_version_tables.table_name" , "admin_getVersionTablesByGroup" );
		//error_log( "SELECT adm_version_tables.id as tableID, adm_version_tables.table_name as tableName FROM adm_version_tables , adm_group_tables WHERE versions_ID = ".$versionID." AND adm_group_tables.adm_plugin_groups_ID = ".$groupID." AND adm_group_tables.adm_version_tables_ID = adm_version_tables.id ORDER BY adm_version_tables.table_name\n" , 3 , '/var/log/debug.log' );
		if ( count( $versionTablesByGroup ) > 0 ) :
			
			return $versionTablesByGroup;
			
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getTablesByGroup( $groupID , $tableID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		
		$versionTablesByGroup 		= $system_database_manager->executeQuery("SELECT adm_group_tables.id as groupTableID, adm_version_tables.table_name as tableName FROM adm_group_tables , adm_version_tables WHERE adm_group_tables.adm_plugin_groups_ID = ".$groupID." AND adm_group_tables.adm_version_tables_ID = ".$tableID." AND adm_group_tables.adm_version_tables_ID = adm_version_tables.id" , "admin_getTablesByGroup" );
		//error_log( "SELECT adm_group_tables.id as groupTableID, adm_version_tables.table_name as tableName FROM adm_group_tables , adm_version_tables WHERE adm_group_tables.adm_plugin_groups_ID = ".$groupID." AND adm_group_tables.adm_version_tables_ID = ".$tableID." AND adm_group_tables.adm_version_tables_ID = adm_version_tables.id\n" , 3 , '/var/log/debug.log' );
		if ( count( $versionTablesByGroup ) > 0 ) :
			
			return $versionTablesByGroup;
			
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getPluginGroups( $pluginID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$pluginGroups				= $system_database_manager->executeQuery("SELECT adm_plugin_groups.id as groupID, adm_plugin_groups.group_name as groupName FROM adm_plugin_groups WHERE plugins_ID = ".$pluginID." ORDER BY adm_plugin_groups.group_name" , "admin_getPluginGroups" );
		//error_log( "SELECT adm_plugin_groups.id as groupID, adm_plugin_groups.group_name as groupName FROM adm_plugin_groups WHERE plugins_ID = ".$pluginID." ORDER BY adm_plugin_groups.group_name\n" , 3 , '/var/log/debug.log' );
		if ( count( $pluginGroups ) > 0 ) :
		
			return $pluginGroups;
			
		else :
		
			return false;
		
		endif;
	}
	
	public function getPluginTables( $versionID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$pluginTables				= $system_database_manager->executeQuery("select adm_version_tables.group_ID as groupID , adm_version_tables.table_name as tableName , adm_version_tables.with_data as withData FROM adm_version_tables WHERE versions_ID = ".$versionID." ORDER BY adm_version_tables.id" , "admin_getPluginTables" );
		//error_log( "select adm_version_tables.group_ID as groupID , adm_version_tables.table_name as tableName , adm_version_tables.with_data as withData FROM adm_version_tables WHERE versions_ID = ".$versionID." ORDER BY adm_version_tables.id\n" , 3 , '/var/log/debug.log' );
		if ( count( $pluginTables ) > 0 ) :
		
			return $pluginTables;
			
		else :
		
			return false;
		
		endif;
	}
	
	public function getPluginGroup( $groupID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$group 						= $system_database_manager->executeQuery("SELECT adm_plugin_groups.group_name as groupName FROM adm_plugin_groups WHERE id = ".$groupID , "getPluginGroup" );
		//error_log( "SELECT adm_plugin_groups.group_name as groupName FROM adm_plugin_groups WHERE id = ".$groupID."\n" , 3 , '/var/log/debug.log' );
		if( !$group ) :
			$response['success'] = false;
		else :
			$response['success'] 	= true;
			$response['groupName'] 	= $group[0]['groupName'];
			
			/*$groupTables = $system_database_manager->executeUpdate("SELECT adm_group_tables.table_name as tableName FROM adm_group_tables WHERE groups_ID = ".$groupID , "getPluginGroupTables" );
			
			if ( !$groupTables ) :
			
				$response['groupTables'] = false;
			
			else :
			
				$response['groupTables'] = $groupTables;
			
			endif;*/
			
			return $response;
			
		endif;
		
	}
	
	public function savePluginGroup( $pluginID , $groupName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$idGroup = $system_database_manager->executeUpdate("INSERT INTO `adm_plugin_groups` (`plugins_ID`,`group_name`) VALUES (".$pluginID.",'".$groupName."')" , "savePluginGroup" );
		
		if( !$idGroup ) :
			return false;
		else :
			return $idGroup;
		endif;
		
	}
	
	public function editPluginGroup( $groupID , $groupName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("UPDATE `adm_plugin_groups` set `group_name` = '".$groupName."' WHERE id = ".$groupID , "editPluginGroup" );
		
		if( !$result ) :
			return false;
		else :
			return true;
		endif;
		
	}
	
	public function deletePluginGroup( $groupID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$result = $system_database_manager->executeUpdate("DELETE FROM `adm_plugin_groups` WHERE id = ".$groupID , "deletePluginGroup" );
		error_log( "DELETE FROM `adm_plugin_groups` WHERE id = ".$groupID , 3 , '/var/log/debug.log' );
		if( !$result ) :
			return false;
		else :
			return true;
		endif;
		
	}
	
	public function savePluginTable( $pluginID , $tableName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		
		$idTable = $system_database_manager->executeUpdate("INSERT INTO `adm_plugin_tables` (`plugins_ID`,`table_name`) VALUES (".$pluginID.",'".$tableName."')" , "savePluginTable" );
		
		if( !$idTable ) :
			return false;
		else :
			return true;
		endif;
		
	}
	
	public function getTableDetail( $versionID , $tableName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$table 						= $system_database_manager->executeQuery("SELECT adm_version_tables.id as tableID , adm_version_tables.with_data as withData , adm_version_tables.group_ID as groupID FROM adm_version_tables WHERE table_name = '".$tableName."' AND versions_ID = ".$versionID , "getTableDetail" );
		//error_log( "SELECT adm_version_tables.with_data as withData , adm_version_tables.group_ID as groupID FROM adm_version_tables WHERE table_name = '".$tableName."' AND versions_ID = ".$versionID."\n" , 3 , '/var/log/debug.log' );
		if( count( $table ) > 0 ) :
			
			return $table;
			
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getSQLResult( $sql , $dbName ) {
		
		$result 	= mysql_query( $sql );
//		$result = mysql_list_tables( $dbName );
		error_log( $dbName.' '.$sql."\n" , 3 , '/var/log/debug.log' );
		if ( $result ) :
		
			$numRows 	= mysql_num_rows( $result );
			$data 		= array();
			
			while( $row = mysql_fetch_row( $result ) ) :

				$data[] = $row;
			
			endwhile;
		
			return array( 'totalNumber' => $numRows , 'data' => $data );
		
		else :
		
			return false;
		
		endif;
		
	}
	
	
	public function getSQLResult1( $sql , $dbName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( $dbName );
		$result						= $system_database_manager->executeQuery( $sql , "admin_getSQLResult" );
		//$result 	= mysql_query( $sql );
//		$result = mysql_list_tables( $dbName );
		error_log( $dbName.' '.$sql."\n" , 3 , '/var/log/debug.log' );
		if ( $result ) :
		
			return array( 'totalNumber' => count( $result ) , 'data' => $result );
		
		else :
		
			return false;
		
		endif;
		
	}
	
	public function executeSQLResult( $sql , $dbName ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( $dbName );
		$result						= $system_database_manager->executeUpdate( $sql , "admin_executeSQLResult" );
		//$result 	= mysql_query( $sql );
//		$result = mysql_list_tables( $dbName );
		error_log( $dbName.' '.$sql."\n" , 3 , '/var/log/debug.log' );
		if ( $result ) :
		
			return true;
		
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getVersionTablesFromDB( $versionID , $tablePreface , $customerDB ) {
		
		$sql 	= "SHOW TABLES FROM ".$customerDB." LIKE '".$tablePreface."%'";
		$result = $this->getSQLResult( $sql , $customerDB );
		//error_log( $sql."\n" , 3 , '/var/log/debug.log' );
		if( !$result ) : return false;
		
		else : return $result;
		
		endif;
		
	}
	
	public function saveVersionTable( $versionID , $table , $withData , $groupID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery( 'SELECT adm_version_tables.id FROM adm_version_tables WHERE versions_ID = '.$versionID.' AND table_name = "'.$table.'"'  , 'admin.getVersionTable');
		//error_log( "SELECT adm_version_tables.id FROM adm_version_tables WHERE versions_ID = ".$versionID." AND table_name = '".$table."'\n" , 3 , '/var/log/debug.log' );
		if( count( $result ) > 0 ) :
		
			$system_database_manager->executeUpdate("UPDATE `adm_version_tables` SET with_data = ".$withData." , group_ID = ".$groupID." WHERE adm_version_tables.id = ".$result[0]['id'] , "updatePluginTable" );
		
		else :
		
			$system_database_manager->executeUpdate("INSERT INTO `adm_version_tables` (`versions_ID`,`table_name`,`with_data`,`group_ID`) VALUES (".$versionID.",'".$table."',".$withData.",".$groupID.")" , "savePluginTable" );
		
		endif;
		
	}
	
	public function saveTableForGroup( $groupID , $tableID ) {
		
		if( !$this->getTableForGroup( $groupID , $tableID ) ) :
		
			$system_database_manager 	= system_database_manager::getInstance();
			$system_database_manager->executeUpdate("INSERT INTO `adm_group_tables` (`adm_plugin_groups_ID`,`adm_version_tables_ID`) VALUES (".$groupID.",".$tableID.")" , "admin.saveTableForGroup" );
		
		endif;
		
	}
	
	public function deleteTableForGroup( $groupID , $tableID ) {
		
		$idTableExists = $this->getTableForGroup( $groupID , $tableID );
		
		if( $idTableExists ) :
		
			$system_database_manager 	= system_database_manager::getInstance();
			$system_database_manager->executeUpdate('DELETE FROM `adm_group_tables` WHERE id = '.$idTableExists , 'admin.deleTableForGroup' );
		
		endif;
		
	}
	
	public function getTableForGroup( $groupID , $tableID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$result = $system_database_manager->executeQuery( 'SELECT adm_group_tables.id FROM adm_group_tables WHERE adm_plugin_groups_ID = '.$groupID.' AND adm_version_tables_ID = '.$tableID  , 'admin.getTableForGroup');

		if( count( $result ) > 0 ) :
//error_log( $result[0]['id'].' '.date('H:i:s').' SELECT adm_group_tables.id FROM adm_group_tables WHERE adm_plugin_groups_ID = '.$groupID.' AND adm_version_tables_ID = '.$tableID."\n" , 3 , '/var/log/debug.log' );		
			return $result[0]['id'];
		
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getTablesForVersion( $versionID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$result = $system_database_manager->executeQuery( 'SELECT adm_version_tables.id as tableID , adm_version_tables.table_name as tableName , adm_version_tables.with_data as withData FROM adm_version_tables WHERE adm_version_tables.versions_ID = '.$versionID.' ORDER BY id'  , 'admin.getTablesForVersion');

		if( count( $result ) > 0 ) :
//error_log( date('H:i:s').' '.count($result)."\n" , 3 , '/var/log/debug.log' );		
			return $result;
		
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getColumnsFromTable( $dbName , $table ) {
		
		$sql 	= "SHOW COLUMNS FROM ".$table;
		$result = $this->getAssocResult( $dbName , $sql );
		//error_log( $sql."\n" , 3 , '/var/log/debug.log' );
		
		if( !$result ) :
		
			return false;
		
		else :
		
			return $result;
		
		endif;
		
	}
	
	public function getAssocResult( $dbName , $sql ) {
		
		//$conn = mysql_connect( CORE_DB_HOST , 'backup' , '63i7E24ce' );
		mysql_select_db( $dbName );
		//error_log( $dbName.' : '.$sql."\n" , 3 , '/var/log/debug.log' );
		$result = mysql_query( $sql );
		$data 	= array();
		$i		= 0;
		
		while( $row = mysql_fetch_assoc( $result ) ) :

			$data[] = $row;
			$i++;
			
		endwhile;
		
		return array( 'totalNumber' => $i , 'data' => $data );
		
	}
	
	public function getTablesForVersionGroup( $versionID , $groupID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$result = $system_database_manager->executeQuery( 'SELECT adm_version_tables.id as tableID , adm_version_tables.table_name as tableName , adm_version_tables.with_data as withData FROM adm_version_tables WHERE adm_version_tables.versions_ID = '.$versionID.' AND adm_version_tables.group_ID = '.$groupID  , 'admin.getTablesForVersionGroup');
//error_log( date('H:i:s').' SELECT adm_version_tables.id as tableID , adm_version_tables.table_name as tableName  , adm_version_tables.with_data as withData FROM adm_version_tables WHERE adm_version_tables.versions_ID = '.$versionID.' AND adm_version_tables.group_ID = '.$groupID."\n" , 3 , '/var/log/debug.log' );
		if( count( $result ) > 0 ) :

			return $result;
		
		else :
		
			return false;
		
		endif;
		
	}
	
	public function getCountries( $lang ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( 'admindev' );
		$result 					= $system_database_manager->executeQuery( 'SELECT core_intl_country_names.core_intl_country_ID as countryID , core_intl_country_names.country_name as countryName FROM core_intl_country_names WHERE core_intl_country_names.country_name_language = "'.$lang.'" ORDER BY core_intl_country_names.country_name' , "admin.getCountries");
		//error_log( 'SELECT core_intl_country_names.core_intl_country_ID as countryID , core_intl_country_names.country_name as countryName FROM core_intl_country_names WHERE core_intl_country_names.country_name_language = "'.$lang.'"'."\n" , 3 , '/var/log/debug.log' );

		if( count($result) < 1 ) :
			$response["success"] 	= false;
			$response["data"] 	= "no data found";
		else :
			$response["success"] 	= true;
			$response["errCode"] 	= 0;
			$response["data"] = $result;
		endif;
		
		return $response;
		
	}
	
	public function getGroupByUser( $dbName , $userID ) {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$system_database_manager->selectDB( $dbName );
		$result						= $system_database_manager->executeQuery( 'SELECT DISTINCT core_user_group.core_group_ID as coreGroupID FROM core_user , core_user_group WHERE core_user.uid = "'.$userID.'" AND core_user.id = core_user_group.core_user_ID', 'admin_getUserGroup' );
		error_log( "SELECT DISTINCT core_user.full_name as userName , core_user_group.core_group_ID FROM core_user , core_user_group WHERE core_user.uid = '".$userID."' AND core_user.id = core_user_group.core_user_ID\n" , 3 , '/var/log/debug.log' );
		
		if( count($result) < 1 ) :
			return false;
		else :
			return $result;
		endif;
		
	}
	
	public function getUserGroup() {
		
		$system_database_manager 	= system_database_manager::getInstance();
		$result						= $system_database_manager->executeQuery("SELECT core_user.id as idUser, core_user.full_name as userName, core_group.name as groupName FROM core_user,core_group,core_user_group WHERE core_user.id=core_user_group.core_user_ID AND core_group.id=core_user_group.core_group_ID ORDER BY core_user.full_name", "admin_getUserGroup" );
		$response					= array();
		
		if( count($result) < 1 ) :
			$response["success"] 	= false;
			$response["data"] 	= "no data found";
		else :
			$response["success"] 	= true;
			$response["errCode"] 	= 0;
			$response["data"] = $result;
		endif;
		
		return $response;
		
	}
	
}

$SYS_PLUGIN["bl"]["admin"] = new admin_BL();
?>
