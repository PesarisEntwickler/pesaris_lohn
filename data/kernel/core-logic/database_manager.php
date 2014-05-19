<?php
/**
 * The database_manager is an abstract class that knows how to interact with a database
 *
 * There is only one system database in the application, but there are numerous site databases.
 * To query a database, you must have one of the concrete classes that subclass the database_manager class:
 * Systemdatabase_manager.
 * <code>
 * // here is an example that shows how to use the Database API to query the system database
 * $systemdatabase_manager = Systemdatabase_manager::getInstance();
 * $query = "SELECT * FROM account_profile;";
 * $results = $systemdatabase_manager->executeQuery($query);
 * </code>
 *
 * @package Database
 * @see Systemdatabase_manager
 */
abstract class database_manager {

	protected $connection;
	protected $databaseName;
	
	public static $lastDatabaseSelected = 0;
	public static $SYSTEM_NOT_INITIALIZED = 0;
	public static $SYSTEM_INITIALING = 1;
	public static $SYSTEM_INITIALIZED = 2;
	
	
	protected function __construct($host, $username, $password, $databaseName) {
		$this->databaseName = $databaseName;
		$this->connection = database_manager::connectToServer($host, $username, $password);
	}
	

	private static function connectToServer($DBHost, $DBUsername, $DBPassword) {
			
		$connection = mysql_connect($DBHost, $DBUsername, $DBPassword);
		
		if ($connection === false) {
			error_log("Unable to connect to the database on ".$DBHost."\nError MESSAGE: ".mysql_error()."\nERRNO: ".mysql_errno()."\n");
			exit;
		}

		if($connection) {
			mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
		}

		return $connection;
	}

	
	/**
	 * creates a new database in the server.
	 */
	public function createDatabase($DBName) {
		$update = "CREATE DATABASE IF NOT EXISTS $DBName;";
		database_manager::executeUpdateOnConnection($this->connection, $update, $DBName);
	}
	

	/**
	 * selects a specific database in the application
	 *
	 * @access public
	 * @static
	 * @param $database The name of the database
	 * @param $connection The connection to use
	 *
	 * @return true if database selected successfully, false otherwise
	 */
	public static function selectDatabase($database, $connection) {
		$success = mysql_select_db($database, $connection);
		if ($success === false) {			
			//funktion gibt es noch nicht...		logError("Failed to select database '$database'", ERROR_SEVERITY);
			return false;
		}
		
		return true;
	}


	public function selectDB($database) {
		$this->databaseName = $database;
		$success = mysql_select_db($database, $this->connection);
		if ($success === false) {			
			return false;
		}
		
		return true;
	}


	/**
	 * returns true if the SYSTEM database has been defined, false otherwise
	 */
	private static function doesSystemDBexist($tempConnection) {
		$result = database_manager::executeQueryOnConnection($tempConnection, "show databases like '" . SYSTEM_DATABASE_PREFIX . "';");
		return count($result) == 1;
	}


	/**
	 * returns true if a table has been defined
	 */
	public static function doesTableExist($tempConnection, $tableName) {
		if (!database_manager::selectDatabase(SYSTEM_DATABASE_PREFIX, $tempConnection)) {
			exit;
		}
		$result = database_manager::executeQueryOnConnection($tempConnection, "show tables like '$tableName';");
		return count($result) == 1;
	}
	
	
	/**
	 * makes sure that a certain column exists in a table in the system database.
	 */
	private static function doesColumnExist($tempConnection, $tableName, $column) {
		$result = database_manager::executeQueryOnConnection($tempConnection, "SHOW COLUMNS FROM $tableName FROM system;");
		if (count($result) == 0) {
			return false;	
		}
		
		for ($i=0; $i<count($result); $i++) {
			if (strcasecmp($result[$i]['Field'], $column) == 0) {
				return true;
			}			
		}
		
		return false;
	}


	/**
	 * returns the status of the sytem (as defined in this class)
	 */
	public static function getSystemStatus() {

		global $FP; require_once($FP . '/Library/Database/Systemdatabase_manager.inc.php');
		$tempConnection = database_manager::connectToServer(Systemdatabase_manager::$HOST, Systemdatabase_manager::$USERNAME, Systemdatabase_manager::getPassword());

		if (!database_manager::doesSystemDBexist($tempConnection) || !database_manager::doesTableExist($tempConnection, 'population') || !database_manager::doesColumnExist($tempConnection, 'population', 'Population_Status')) {
			return database_manager::$SYSTEM_NOT_INITIALIZED;
		}

		$result = database_manager::executeQueryOnConnection($tempConnection, "select Population_Status from population;");
		
		if (count($result) == 0) {
			return database_manager::$SYSTEM_NOT_INITIALIZED;
		}
		
		return $result[0]['Population_Status'];
	}


	/**
	 * closes the connection to the MySQL server on a specific connection.
	 */
	private static function closeConnection($connection='') {
		$result = mysql_close($connection);
		if ($result === false) {
			exit;
		}
	}



	/**
	 * performs a set of DDL queries.
	 */
	public static  function runBatch($batchContent) {
		database_manager::runBatchOnConnection($batchContent);
	}


	/**
	 * closes the connection to the MySQL server for this object.
	 */
	public function close() {
		database_manager::closeConnection($this->connection);
	}



	/**
	 * performs a set of DDL queries.
	 */
	public static function runBatchOnConnection($batchContent) {		
		$commands = explode(';', $batchContent);
		foreach ($commands as $command) {
			$command = trim($command);
			if ($command == '') {
				continue;
			}
			$command = stripslashes($command);
			$result = mysql_query($command);
			if ($result === false) {
				error_log("Error in QUERY: ".$command."\nError MESSAGE: ".mysql_error()."\nERRNO: ".mysql_errno()."\n");
				database_manager::closeConnection($connection);
				die("mysql query error");
				exit;
			}
		}		
	}


	/**
	 * performs a DDL query.
	 */
	public function executeUpdate($update, $comment = '') {
		return database_manager::executeUpdateOnConnection($this->connection, $update, $this->databaseName);
	}



	/**
	 * performs a DDL query, for a specific connection.
	 */
	private static function executeUpdateOnConnection($connection, $update, $databaseName) {
		
		//TODO: remove these
		//quickLog($update);
		
		$result = mysql_query($update, $connection);
		if($result === false) {						
			error_log("Error in QUERY: ".$update."\nError MESSAGE: ".mysql_error()."\nERRNO: ".mysql_errno()."\n");
			database_manager::closeConnection($connection);
			die("mysql query error");
			return false;
		} 
		return true;
	}


	/**
	 * performs a delete query.
	 */
	public function executeDelete($delete, $comment = '') {
		return database_manager::executeDeleteOnConnection($this->connection, $delete, $this->databaseName);
	}



	/**
	 * performs a delete query, for a specific connection.
	 */
	private static function executeDeleteOnConnection($connection, $delete, $databaseName) {
		
		//TODO: remove these
		//quickLog($update);
		
		$result = mysql_query($delete, $connection);
		if ($result === false) {						
			error_log("Error in QUERY: ".$delete."\nError MESSAGE: ".mysql_error()."\nERRNO: ".mysql_errno()."\n");
			database_manager::closeConnection($connection);
			die("mysql query error");
			exit;
		} else {
			return true;
		}
	}


	/**
	 * get last insert id.
	 */
	public static function getLastInsertId() {
		return mysql_insert_id();
	}


	/**
	 * performs a DML query returning an array holding the query results.
	 *
	 * The column names are used as keys for the secondary array:
	 * i.e. $result[0][<columnName>]
	 * i.e. $result[1][<columnName>]
	 * ...
	 */
	public function executeQuery($query, $comment = '') {	
		return database_manager::executeQueryOnConnection($this->connection, $query);
	}
	

	/**
	 * performs a DML query returning an array holding the query results, for a specific connection.
	 */
	private static function executeQueryOnConnection($connection, $query) {
		
		//TODO: remove these
		//quickLog($query);

		$result = mysql_query($query, $connection);
		if ($result === false) {
			error_log("Error in QUERY: ".$query."\nError MESSAGE: ".mysql_error()."\nERRNO: ".mysql_errno()."\n");
			database_manager::closeConnection($connection);
			die("mysql query error");
			exit;
		}
		
		// extract data from results, returning an associative array
		$rows = Array();
		while ($row = mysql_fetch_assoc($result)) {			
			$rows[] = $row;
		}

		return $rows;
	}
}

?>
