<?php
putenv("PATH=/sbin:/bin:/usr/sbin:/usr/bin:/usr/games:/usr/local/sbin:/usr/local/bin:/root/bin");

define("CORE_DB_HOST", "localhost");
define("CORE_DB_DBNAME", "development");
define("CORE_DB_USERNAME", "webuser");
define("CORE_DB_PASSWORD", "w4tZuP3h");
define("CORE_WGUI_CACHE", "");

define("DATE_FORMAT_USER", 1);
define("DATE_FORMAT_USER_SHORT", 2);
define("DATE_FORMAT_USER_LONG", 3);
define("DATE_FORMAT_USER_MONTHNAME", 4);
define("DATE_FORMAT_MYSQL",10);
define("DATE_FORMAT_UNIX", 20);

define("NUMBER_FORMAT_INT", 1);
define("NUMBER_FORMAT_UINT", 2);
define("NUMBER_FORMAT_DECIMAL", 11);
define("NUMBER_FORMAT_UDECIMAL", 12);
define("NUMBER_FORMAT_DECIMAL_STRICT", 13);
define("NUMBER_FORMAT_UDECIMAL_STRICT", 14);

define("AUSZAHLDIR", "payments");
define("PERIODENPREFIX", "periode-");
define("PAYMENTVIEWDIR", "web/transfer/");
define("LF", "\n");
define("CR", "\r");
define("CRLF", "\r\n");
define("TAB", "\t");

//Communication in general
$aafwConfig["communication"]["functionCalls"]["remoteMode"] = false;
$aafwConfig["communication"]["functionCalls"]["remoteServices"] = array("nuncius.php");

//Plugin configuration
$aafwConfig["plugins"]["coreFunctions"]["currentVersion"] = "V00_01_00";
$aafwConfig["plugins"]["baseLayout"]["currentVersion"] = "V00_01_00";
$aafwConfig["plugins"]["pim"]["currentVersion"] = "V00_01_00"; //pim = Personal information manager (contacts, schedule, notes)
$aafwConfig["plugins"]["acc"]["currentVersion"] = "V00_01_00"; //acc = Accounting
$aafwConfig["plugins"]["payroll"]["currentVersion"] = "V00_01_00";
//$aafwConfig["plugins"]["testPlugin"]["currentVersion"] = "V00_00_01";

//Core functions
$aafwConfig["international"]["default"]["language"] = "de";
$aafwConfig["international"]["default"]["locale"] = "de-ch";
$aafwConfig["international"]["default"]["country"] = "ch";

$aafwConfig["international"]["de-ch"]["dateShort"] = "d.m.y";
$aafwConfig["international"]["de-ch"]["dateLong"] = "d.m.Y";
$aafwConfig["international"]["de-ch"]["dateWithMonthName"] = "j. X Y";
$aafwConfig["international"]["de-ch"]["dateOrder"] = 1; //dmy
$aafwConfig["international"]["de-ch"]["monthNames"] = array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
$aafwConfig["international"]["de-ch"]["currency"]["currencyShort"] = "CHF";
$aafwConfig["international"]["de-ch"]["currency"]["currencyLong"] = "Schweizerfranken";
$aafwConfig["international"]["de-ch"]["currency"]["decimals"] = 2;
$aafwConfig["international"]["de-ch"]["numbers"]["dec_point"] = ".";
$aafwConfig["international"]["de-ch"]["numbers"]["thousands_sep"] = "'";

$aafwConfig["international"]["de-de"]["dateShort"] = "d.m.y";
$aafwConfig["international"]["de-de"]["dateLong"] = "d.m.Y";
$aafwConfig["international"]["de-de"]["dateWithMonthName"] = "j. X Y";
$aafwConfig["international"]["de-de"]["dateOrder"] = 1; //dmy
$aafwConfig["international"]["de-de"]["monthNames"] = array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
$aafwConfig["international"]["de-de"]["currency"]["currencyShort"] = "EUR";
$aafwConfig["international"]["de-de"]["currency"]["currencyLong"] = "Euro";
$aafwConfig["international"]["de-de"]["currency"]["decimals"] = 2;
$aafwConfig["international"]["de-de"]["numbers"]["dec_point"] = ",";
$aafwConfig["international"]["de-de"]["numbers"]["thousands_sep"] = ".";

// Enable corresponding Environment

//Core Paths SRV1

//$aafwConfig["paths"]["file_manager"]["rootPath"] = "/usr/local/www/apache22/data-hidden/";
//$aafwConfig["paths"]["session_control"]["rootPathData"] = "/usr/local/www/apache22/data/";
//$aafwConfig["paths"]["session_control"]["sessionCachePath"] = "/usr/local/www/apache22/data/kernel/cache/sessions/";
//$aafwConfig["paths"]["plugin"]["customerDir"] = "/usr/local/www/apache22/data-hidden/CUSTOMER/";
//$aafwConfig["paths"]["plugin"]["mysqldump"] = "/usr/local/bin/mysqldump";
//$aafwConfig["paths"]["reports"]["templates"] = "/usr/local/www/apache22/data-hidden/GLOBAL/templates/";

//$aafwConfig["paths"]["utilities"]["xsltproc"] = "xsltproc";
//$aafwConfig["paths"]["utilities"]["pdflatex"] = "pdflatex";
//$aafwConfig["paths"]["utilities"]["stdout"] = "/dev/null";
//$aafwConfig["paths"]["utilities"]["mysql"] = "mysql";

//Core Paths SRV2

$aafwConfig["paths"]["file_manager"]["rootPath"] = "/usr/local/www/apache22/data-hidden/";
$aafwConfig["paths"]["session_control"]["rootPathData"] = "/usr/local/www/apache22/data/";
$aafwConfig["paths"]["session_control"]["sessionCachePath"] = "/usr/local/www/apache22/data/kernel/cache/sessions/";
$aafwConfig["paths"]["plugin"]["customerDir"] = "/usr/local/www/apache22/data-hidden/CUSTOMER/";
$aafwConfig["paths"]["plugin"]["mysqldump"] = "/usr/local/bin/mysqldump";
$aafwConfig["paths"]["reports"]["templates"] = "/usr/local/www/apache22/data-hidden/GLOBAL/templates/";

$aafwConfig["paths"]["utilities"]["xsltproc"] = "xsltproc";
$aafwConfig["paths"]["utilities"]["pdflatex"] = "pdflatex";
$aafwConfig["paths"]["utilities"]["stdout"] = "/dev/null";
$aafwConfig["paths"]["utilities"]["mysql"] = "/usr/local/bin/mysql";

//Core Paths Windows Ascami

//$aafwConfig["paths"]["file_manager"]["rootPath"] = "C:/VS/mahrens/projects/Presida/PesarisLohn/data-hidden/";
//$aafwConfig["paths"]["session_control"]["rootPathData"] = "C:/VS/mahrens/projects/Presida/PesarisLohn/data/";
//$aafwConfig["paths"]["session_control"]["sessionCachePath"] = "C:/VS/mahrens/projects/Presida/PesarisLohn/data/kernel/cache/sessions/";
//$aafwConfig["paths"]["plugin"]["customerDir"] = "C:/VS/mahrens/projects/Presida/PesarisLohn/data-hidden/CUSTOMER/";
//$aafwConfig["paths"]["plugin"]["mysqldump"] = "C:/VS/mahrens/projects/Presida/PesarisLohn/mysqldump";
//$aafwConfig["paths"]["reports"]["templates"] = "C:/VS/mahrens/projects/Presida/PesarisLohn/data-hidden/GLOBAL/templates/";

//$aafwConfig["paths"]["utilities"]["xsltproc"] = "C:/xsltproc/xsltproc.exe";
//$aafwConfig["paths"]["utilities"]["pdflatex"] = "C:/MiKTeX/miktex/bin/pdflatex.exe";
//$aafwConfig["paths"]["utilities"]["stdout"] = "NUL";
//$aafwConfig["paths"]["utilities"]["mysql"] = "/usr/local/bin/mysql";

//Core Paths Windows Harald

//$aafwConfig["paths"]["file_manager"]["rootPath"] = "C:/git/pesaris_lohn/payroll/data-hidden/";
//$aafwConfig["paths"]["session_control"]["rootPathData"] = "C:/git/pesaris_lohn/payroll/data/";
//$aafwConfig["paths"]["session_control"]["sessionCachePath"] = "C:/git/pesaris_lohn/payroll/data/kernel/cache/sessions/";
//$aafwConfig["paths"]["plugin"]["customerDir"] = "C:/git/pesaris_lohn/payroll/data-hidden/CUSTOMER/";
//$aafwConfig["paths"]["plugin"]["mysqldump"] = "C:/git/pesaris_lohn/payroll/mysqldump";
//$aafwConfig["paths"]["reports"]["templates"] = "C:/git/pesaris_lohn/payroll/data-hidden/GLOBAL/templates/";
//
//$aafwConfig["paths"]["utilities"]["xsltproc"] = "C:/Users/Harald G. Mueller/HM/XSLT/bin/xsltproc.exe";
//$aafwConfig["paths"]["utilities"]["pdflatex"] = "C:/Program Files (x86)/MiKTeX 2.9/miktex/bin/pdflatex.exe";
//$aafwConfig["paths"]["utilities"]["stdout"] = "NUL";
////$aafwConfig["paths"]["utilities"]["mysql"] = "/usr/local/bin/mysql";
//$aafwConfig["paths"]["utilities"]["mysql"] = "C:/Users/Harald G. Mueller/HM/XAMPP/mysql/data";

?>
