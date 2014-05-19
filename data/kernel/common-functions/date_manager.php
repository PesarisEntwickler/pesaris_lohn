<?php
require_once("configuration.php"); //actually not neccessary here, since already included in internationalization.php
require_once("internationalization.php");
require_once("number_manager.php");

class date_manager extends internationalization {

	public function __construct() {
		// call the parent constructor and set any defaults
		parent::__construct();
		$this->setDefaultSystemSettings(); //TEST_DATE <- eigentlich nicht noetig, da bereits im parent die Benutzer-Einstellungen geladen werden muessten!
	}

	public function convertDate($date, $fromFormat, $toFormat)
	{
		switch($fromFormat) {
		case DATE_FORMAT_UNIX:
			if(number_manager::isValidNumber($date, NUMBER_FORMAT_UINT)) {
				$fDay = date("d", $date);
				$fMonth = date("m", $date);
				$fYear = date("Y", $date);
			}else{
				return false;
			}
			break;
		case DATE_FORMAT_MYSQL:
		case DATE_FORMAT_USER:
			$arrDate = preg_split("/\.|-|\//", $date);
			if(sizeof($arrDate)==3) {
				$systemYear = date("Y");
				switch($fromFormat==DATE_FORMAT_MYSQL ? 3 : $GLOBALS["aafwConfig"]["international"][$this->getLocale()]["dateOrder"]) {
				case 1: //dmy
					$fDay = $arrDate[0];
					$fMonth = $arrDate[1];
					$fYear = substr($systemYear,0,strlen($systemYear)-strlen($arrDate[2])).$arrDate[2];
					break;
				case 2: //mdy
					$fDay = $arrDate[1];
					$fMonth = $arrDate[0];
					$fYear = substr($systemYear,0,strlen($systemYear)-strlen($arrDate[2])).$arrDate[2];
					break;
				case 3: //ymd
					$fDay = $arrDate[2];
					$fMonth = $arrDate[1];
					$fYear = substr($systemYear,0,strlen($systemYear)-strlen($arrDate[0])).$arrDate[0];
					break;
				default:
					//Datumformat nicht konfiguriert!
					return false;
				}
				if(!checkdate($fMonth, $fDay, $fYear)) return false;
			}else return false;
			break;
		default:
			//DATE_FORMAT_USER_SHORT || DATE_FORMAT_USER_LONG || DATE_FORMAT_USER_MONTHNAME are not allowed in $fromFormat
			return false;
		}

		switch($toFormat) {
		case DATE_FORMAT_UNIX:
			$ret = mktime(0, 0, 0, $fMonth, $fDay, $fYear);
			break;
		case DATE_FORMAT_MYSQL:
			$ret = "$fYear-$fMonth-$fDay";
			break;
		case DATE_FORMAT_USER_SHORT:
			$ret = date($GLOBALS["aafwConfig"]["international"][$this->getLocale()]["dateShort"], mktime(0, 0, 0, $fMonth, $fDay, $fYear));
			break;
		case DATE_FORMAT_USER_LONG:
			$ret = date($GLOBALS["aafwConfig"]["international"][$this->getLocale()]["dateLong"], mktime(0, 0, 0, $fMonth, $fDay, $fYear));
			break;
		case DATE_FORMAT_USER_MONTHNAME:
			$ret = str_replace("X", $GLOBALS["aafwConfig"]["international"][$this->getLocale()]["monthNames"][$fMonth-1], date($GLOBALS["aafwConfig"]["international"][$this->getLocale()]["dateWithMonthName"], mktime(0, 0, 0, $fMonth, $fDay, $fYear)));
			break;
		default:
			//DATE_FORMAT_USER is not allowed in $toFormat
			return false;
		}

		return $ret;
	}
}
/*
$time_start = microtime(true);

$dm = new date_manager();

echo $dm->convertDate("2010-12-31", DATE_FORMAT_MYSQL, DATE_FORMAT_USER_MONTHNAME)."<br><br>";

//echo $dm->convertDate("31.12.2010", DATE_FORMAT_USER, DATE_FORMAT_MYSQL)."<br><br>";

//$test = $dm->convertDate("31/12/2010", DATE_FORMAT_USER, DATE_FORMAT_UNIX);
//echo $test."<br><br>";

//echo $dm->convertDate($test, DATE_FORMAT_UNIX, DATE_FORMAT_USER_LONG)."<br><br>";

echo "number_manager = ".number_manager::isValidNumber(-12456798, NUMBER_FORMAT_UINT);

$time_end = microtime(true);
$time = $time_end - $time_start;
echo "<br><br>$time<br><br>";
*/
?>
