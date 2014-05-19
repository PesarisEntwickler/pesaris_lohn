<?php
require_once("database_manager.php");

/**
 * The system_database_manager class provides the mechanism for interacting with the system database
 *
 * The class is written using the Singleton pattern.
 * Here is how a reference to an instance of the class is obtained:
 * <code>
 * $system_database_manager = system_database_manager::getInstance();
 * </code>
 *
 * @package Database
 * @author Kanav Kohli
 * made $userName, $host and $password as private members
 * replaced constant for database with a private member
 */

class system_database_manager extends database_manager {

	private static $host = CORE_DB_HOST;
//	private static $dbName = CORE_DB_DBNAME;
	private static $dbName = "appcustomers";
	private static $userName = CORE_DB_USERNAME;
	private static $password = CORE_DB_PASSWORD;
	
	private static $instance = null;
	private static $readQueries = Array();
	private static $writeQueries = Array();
	private static $readQueryComments = Array();
	private static $writeQueryComments = Array();
	
	
	protected function __construct() {
		parent::__construct(system_database_manager::$host, system_database_manager::$userName, system_database_manager::$password, system_database_manager::$dbName);
	}
	
	/**
	 * gets an instance of the system database.
	 *
	 * @return a reference to the system_database_manager upon success, false if no such database exists
	 */
	public static function getInstance() {
		if (system_database_manager::$instance === null) {
			
			system_database_manager::$instance = new system_database_manager();
		}
				
		if (!system_database_manager::selectDatabaseIfNecessary()) {
		
			// no such database
			return false;
		}
		
		return system_database_manager::$instance;
	}


	
	
	/**
	 * return true if the database was selected successully, false otherwise
	 *
	 * @access private
	 * @static
	 *
	 * @return true if the database was selected successully, false otherwise
	 */
	private static function selectDatabaseIfNecessary() {
		
		if (strcasecmp(database_manager::$lastDatabaseSelected, system_database_manager::$dbName) != 0) {
//			logError("connection established.");
			if (database_manager::selectDatabase(system_database_manager::$dbName, system_database_manager::$instance->connection)) {
				database_manager::$lastDatabaseSelected = system_database_manager::$dbName;
			}
			else {
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * executes a SELECT query in the database
	 *
	 * @access public
	 * @param $query The SQL SELECT query to execute
	 * @param $comment An optional comment explaining the query
	 *
	 * @return the result set on success, or false on error
	 */
	public function executeQuery($query, $comment = '') {
		
		if (!system_database_manager::selectDatabaseIfNecessary()) {
			logError("Failed to select system database while executing $query", ERROR_SEVERITY);
			return false;
		}		

		system_database_manager::$readQueries[] = $query;
		system_database_manager::$readQueryComments[] = $comment;
		return parent::executeQuery($query);
	}
	
	
	/**
	 * executes an INSERT/UPDATE query in the database
	 *
	 * @access public
	 * @param $query The SQL INSERT/UPDATE query to execute
	 * @param $comment An optional comment explaining the query
	 *
	 * @return true on success, or false on error
	 */
	public function executeUpdate($update, $comment = '') {
		if (!system_database_manager::selectDatabaseIfNecessary()) {
			logError("Failed to select system database while executing $update", ERROR_SEVERITY);
			return false;
		}

		system_database_manager::$writeQueries[] = $update;
		system_database_manager::$writeQueryComments[] = $comment;
		return parent::executeUpdate($update);		
//		 true;
	}	
	
	
	/**
	 * create and executes an UPDATE query in the database 
	 *
	 * @access public
	 * @param $table Table name
	 * @param $fields Table fields to be updated
	 * @param $values Coressponding values
	 * @param $where Where statement
	 * @param $comment An optional comment explaining the query
	 *
	 * @return true on success, or false on error
	 */
	public function runAutoUpdate($table,$fields, $values, $where,  $comment = '') {


		if ($where!="") {
			$update="UPDATE `$table` SET ";

			for ($i=0;$i<count($fields);$i++){
				$update.="`".$fields[$i]."`='".$values[$i]."'".(($i==count($fields)-1)?"":" , ");
			}
			
			$update.=" WHERE ($where)";
		}else{
			return false;
		}


		if (!system_database_manager::selectDatabaseIfNecessary()) {
			logError("Failed to select system database while executing $update", ERROR_SEVERITY);
			return false;
		}

		system_database_manager::$writeQueries[] = $update;
		system_database_manager::$writeQueryComments[] = $comment;
		parent::executeUpdate($update);		
		return true;
	}	
	
	
	/**
	 * create and executes an INSERT query in the database 
	 *
	 * @access public
	 * @param $table Table name
	 * @param $fields Table fields to be updated
	 * @param $values Coressponding values
	 * @param $comment An optional comment explaining the query
	 *
	 * @return true on success, or false on error
	 */
	public function runAutoInsert($table,$fields, $values, $comment = '') {

			$update="INSERT INTO `$table` SET ";

			for ($i=0;$i<count($fields);$i++){
				$update.="`".$fields[$i]."`='".$values[$i]."'".(($i==count($fields)-1)?"":" , ");
			}
			if (!system_database_manager::selectDatabaseIfNecessary()) {
			logError("Failed to select system database while executing $update", ERROR_SEVERITY);
			return false;
			
		}
		
		system_database_manager::$writeQueries[] = $update;
		system_database_manager::$writeQueryComments[] = $comment;
		parent::executeUpdate($update);
		return true;
	}	
	
	
	/**
	 * executes an DELETE query in the database
	 * @access public
	 * @param $query The SQL DELETE query to execute
	 * @param $comment An optional comment explaining the query
	 *
	 * @return true on success, or false on error
	 */
	public function runSingleDelete($delete, $comment = '') {
		if (!system_database_manager::selectDatabaseIfNecessary()) {
			logError("Failed to select system database while executing $update", ERROR_SEVERITY);
			return false;
		}

		system_database_manager::$writeQueries[] = $update;
		system_database_manager::$writeQueryComments[] = $comment;
		return parent::executeDelete($delete);		
	}	
	
	
	/**
	 * executes a query in the system database
	 *
	 * @param $query The SQL query to executes	 
	 * @param $comment An optional comment explaining the query
	 *
	 * @return The result set on success, or false on error
	 */
	public static function runSingleQuery($query, $comment = '') {
		$systemDBManager = system_database_manager::getInstance();
		system_database_manager::selectDatabaseIfNecessary();		
		return $systemDBManager->executeQuery($query, $comment);
	}	
	
	
	/**
	 * executes a update in the system database
	 *
	 * @param $update The SQL update to executes
	 * @param $comment An optional comment explaining the query	 
	 *
 	 * @return true on success, or false on error
	 */
	public static function runSingleUpdate($update, $comment = '') {
		$systemDBManager = system_database_manager::getInstance();
		system_database_manager::selectDatabaseIfNecessary();		
		return $systemDBManager->executeUpdate($update, $comment);
	}

	
		/**
	 * executes multiple inserts  in the system database
	 *
	 * @param $update The SQL update to executes
	 * @return true on success, or false on error
	 */
	public static function runBatchQueries($batchQueries) {
		$systemDBManager = system_database_manager::getInstance();
		system_database_manager::selectDatabaseIfNecessary();		
		return database_manager::runBatch($batchQueries);
	}

	
	/**
	 * returns last inserted id
	 *
	 * @param NA
	 * @param NA
	 *
 	 * @return last insert id on success, or false on error
	 */
	public function lastInsertId(){
		return parent::getLastInsertId();		
	}

	/**
	 * initializes the system database
	 */
	public static function init() {
		if (system_database_manager::$instance == 0) {
			system_database_manager::$instance = new system_database_manager();
		}
		
		system_database_manager::$instance->createDatabase(system_database_manager::$dbName);
	}


	public function doesSystemTableExist($tableName) {
		if (!system_database_manager::selectDatabaseIfNecessary()) {
			exit;
		}
		return database_manager::doesTableExist($this->connection, $tableName);
	}
	
	
	/**
	 * returns an array of all the read queries (SELECT QUERIES) performed in the system database
	 *
	 * @access public
	 *
	 * @return an array of all the read queries (SELECT QUERIES) performed in the system database
	 */
	public static function getReadQueries() {
		return system_database_manager::$readQueries;
	}
	
	
	/**
	 * returns the total number of read queries (SELECT QUERIES) performed in the system database
	 *
	 * @access public
	 *
	 * @return the total number of read queries (SELECT QUERIES) performed in the system database
 	 */
	public static function getCountReadQueries() {
		return count(system_database_manager::$readQueries);
	}
	
	
	/**
	 * returns an array of all the comments on read queries (SELECT QUERIES) performed in the system database
	 *
	 * @access public
	 *
	 * @return an array of all the comments on read queries (SELECT QUERIES) performed in the system database
	 */
	public static function getReadQueryComments() {
		return system_database_manager::$readQueryComments;
	}
	
	
	/**
	 * returns an array of all the write queries (INSERT/UPDATE QUERIES) performed in the system databas
	 *
	 * @access public
	 *
	 * @return an array of all the write queries (INSERT/UPDATE QUERIES) performed in the system databas
	 */
	public static function getWriteQueries() {
		return system_database_manager::$writeQueries;
	}
	
	
	/**
	 * returns the total number of write queries (INSERT/UPDATE QUERIES) performed in the system database
	 *
	 * @access public
	 *
	 * @return the total number of read queries (INSERT/UPDATE QUERIES) performed in the system database
 	 */	
	public static function getCountWriteQueries() {
		return count(system_database_manager::$writeQueries);
	}
	
	
	/**
	 * returns an array of all the comments on write queries (INSERT/UPDATE QUERIES) performed in the system database
	 *
	 * @access public
	 *
	 * @return an array of all the comments on write queries (INSERT/UPDATE QUERIES) performed in the system database
	 */
	public static function getWriteQueryComments() {
		return system_database_manager::$writeQueryComments;
	}

	public static function defaultDatabaseName($newDbName) {
		system_database_manager::$dbName = $newDbName;
	}
}

?>
