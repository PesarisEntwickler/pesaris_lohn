<?php
/*
function mysql_safeinteger($hotParam)
{
	if(checkInteger($hotParam)) return $hotParam;
	else {
		echo "ERROR: Parameter not numeric (INT)!";
		exit();
	}
}

function mysql_safedecimal($hotParam)
{
	if(checkDecimal($hotParam)) return str_replace(",",".",$hotParam);
	else {
		echo "ERROR: Parameter not numeric (DECIMAL)!";
		exit();
	}
}
*/
require_once("configuration.php"); //actually not neccessary here, since already included in internationalization.php
require_once("internationalization.php");

class number_manager extends internationalization {

	public function __construct() {
		// call the parent constructor and set any defaults
		parent::__construct();
		$this->setDefaultSystemSettings(); //TEST_DATE <- eigentlich nicht nötig, da bereits im parent die Benutzer-Einstellungen geladen werden müssten!
	}

	public function isValidNumber($value, $checkFormat, $maxDecimals=0) {
		switch($checkFormat) {
		case NUMBER_FORMAT_INT:
			$rexp = "^([-])?[0-9]+$";
			break;
		case NUMBER_FORMAT_UINT:
			$rexp = "^[0-9]+$";
			break;
		case NUMBER_FORMAT_DECIMAL:
			$rexp = $maxDecimals>0 ? "^([-])?[0-9]+([.,][0-9]{1,$maxDecimals})?$" : "^([-])?[0-9]+([.,][0-9]+)?$";
			break;
		case NUMBER_FORMAT_UDECIMAL:
			$rexp = $maxDecimals>0 ? "^[0-9]+([.,][0-9]{1,$maxDecimals})?$" : "^[0-9]+([.,][0-9]+)?$";
			break;
		case NUMBER_FORMAT_DECIMAL_STRICT:
			$rexp = $maxDecimals>0 ? "^([-])?[0-9]+([.,][0-9]{1,$maxDecimals}){1,1}$" : "^([-])?[0-9]+([.,][0-9]+){1,1}$";
			break;
		case NUMBER_FORMAT_UDECIMAL_STRICT:
			$rexp = $maxDecimals>0 ? "^[0-9]+([.,][0-9]{1,$maxDecimals}){1,1}$" : "^[0-9]+([.,][0-9]+){1,1}$";
			break;
		default:
			return false;
		}
		if(!ereg($rexp, $value)) return false;
		else return true;
	}
}
/*
$nm = new number_manager();
echo $nm->isValidNumber(12456798, NUMBER_FORMAT_UINT) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(12456798.04, NUMBER_FORMAT_UINT) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(-12456798.04, NUMBER_FORMAT_UINT) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(-1235678, NUMBER_FORMAT_UINT) ? "TRUE<br>" : "FALSE<br>";
echo "<br>";
echo $nm->isValidNumber(12456798.214, NUMBER_FORMAT_UDECIMAL, 2) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(12456798.04, NUMBER_FORMAT_UDECIMAL_STRICT, 2) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(12456798, NUMBER_FORMAT_UDECIMAL_STRICT, 2) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(-12456798.04, NUMBER_FORMAT_UDECIMAL, 2) ? "TRUE<br>" : "FALSE<br>";
echo $nm->isValidNumber(-1235678.654, NUMBER_FORMAT_UDECIMAL, 2) ? "TRUE<br>" : "FALSE<br>";
*/
?>
