<?php
require_once("configuration.php");

/**
 * Defines the interface for the session_manager class
 *
Im Prinzip erweitern diese Internationalisierungsfunktionen die Conversions-Funktionen

internationalization
	getLocale
	getLang
	getCountry
	setLang
	setCountry
	setLocale
	getDefaultLocale()
	getDefaultLanguage()
	getDefaultCountry()
->falls ein "maschineller User" (z.B. eine Kasse) mit dem System kommuniziert, muss der Text zwar in Deutsch sein,
aber die Nummern u.U. ohne Tausenderseparator und Dezimalstellen mit Punkt anstatt Komma getrennt.
Das könnte man z.B. mit einem Pseudo-Locale 'de-xx' lösen, oder?

Date	-> erweitert 'Internatonal'
	mysqlQueryDateFormat	IN:FieldName	[UNIX, USER, MYSQL]
	convertDate		(UNIX<->USER), (UNIX<->MYSQL), (USER<->MYSQL)
	isValidDate		UNIX, USER, MYSQL

Number	-> erweitert 'Internatonal'
	mysqlQueryNumberFormat	IN:FieldName	[SYS, USER, MYSQL]
	convertNumber		(SYS<->USER)
	formatNumber		
	isValidNumber		INT, UINT, FLOAT, UFLOAT
->könnte man nicht beispielsweise mit ARRAY_WALK eine Massenformatierung in einem Array laufen lassen?

Translation('de-de', 'testPlugin')	-> erweitert 'Internatonal' [beide Parameter optional -> wenn nicht angegeben, wird angenommen USER-LOCALES und CORE]
	addSource
	getSources
	clearSources
	getText
	addBatchItem
	runBatch
 */
class internationalization {
//	private static $instance = 0;
	private $isDebug;		//	true/false if true log messages will be written in log file otherwise not to write 
	private $locale;
	private $language;
	private $country;
	private $countryCodes;
	private $languageCodes;

	private function __construct() {
		$this->isDebug = false;
		$this->countryCodes = array('de', 'ch', 'li', 'at', 'fr', 'gb');
		$this->languageCodes = array('de', 'fr', 'en');
	}

	private function logError($message) {
		if ($this->isDebug === true) {
			logError($message);
		}
	}


	/**
	 * Returns a reference to the session_manager object
	 *

	public static function getInstance() {
		if (internationalization::$instance === 0) {
			internationalization::$instance = new session_manager();
			internationalization::$instance->logError("internationalization Instance Created");
		}

		internationalization::$instance->logError("internationalization->getInstance()");
		return internationalization::$instance;
	}
	 */


	/**
	 * 
	 * Create/Update a session variable
	 *
	 * @param $sessionName: Name of the session variable
	 * @param $sessionValue: value for the session variable
	 *
	 */
	public function getLocale() {
		return $this->locale;
	}

	/**
	 * 
	 * Returns the value of session variable given in $sessionName
	 *
	 * @param $sessionName: Name of the session variable
	 *
	 */
	public function getLang() {
		return $this->language;
	}
	/**
	 * 
	 * Returns the value of session variable given in $sessionName
	 *
	 * @param $sessionName: Name of the session variable
	 *
	 */
	public function getCountry() {
		return $this->country;
	}

	public function setCountry($countryCode) {
		$countryCode = strtolower($countryCode);
		if(in_array($countryCode, $this->countryCodes)) {
			$this->country = $countryCode;
			return true;
		}else{
			return false;
		}
	}

	public function setLanguage($languageCode) {
		$languageCode = strtolower($languageCode);
		if(in_array($languageCode, $this->languageCodes)) {
			$this->language = $languageCode;
			return true;
		}else{
			return false;
		}
	}

	public function setLocale($localeCode) {
		$localeCode = strtolower($localeCode);
		$arr = explode("-", $localeCode);
		if(sizeof($arr)!=2) {
			return false;
		}
		if(in_array($languageCode, $this->languageCodes) && in_array($countryCode, $this->countryCodes)) {
			$this->country = $countryCode;
			$this->language = $languageCode;
			$this->locale = $localeCode;
			return true;
		}else{
			return false;
		}
	}

	public function setDefaultUserSettings() {
	}

	public function setDefaultSystemSettings() {
		$this->country = $GLOBALS["aafwConfig"]["international"]["default"]["country"];
		$this->language = $GLOBALS["aafwConfig"]["international"]["default"]["language"];
		$this->locale = $GLOBALS["aafwConfig"]["international"]["default"]["locale"];
	}	

	public function getSettingsByLocaleCode($locale) {
		return $GLOBALS["aafwConfig"]["international"][$locale];
	}

}
?>
