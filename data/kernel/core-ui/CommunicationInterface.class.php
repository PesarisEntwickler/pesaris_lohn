<?php

class communication_interface {
	private static $instance = 0;
	private static $isDebug;		//	true/false if true log messages will be written in log file otherwise not to write 
	private static $ajaxResponseHandle;

	private function __construct() {
//logOutput("construct\n");
		communication_interface::$isDebug = false;
	}

	private function logError($message) {
		if ($this->isDebug === true) {
			logError($message);
		}
	}

	public static function setResponseHandle(&$rh) {
		communication_interface::$ajaxResponseHandle = $rh;
	}

	public static function getResponseHandle() {
		return communication_interface::$ajaxResponseHandle;
	}

//	public function __destruct() {
//logOutput("destruct\n");
//		communication_interface::$ajaxResponseHandle->alert("hello from the constructor");
//		communication_interface::$ajaxObjectHandle->processRequest();
//	}


	/**
	 * Returns a reference to the session_manager object
	 **/

	public static function getInstance() {
		if (communication_interface::$instance === 0) {
			communication_interface::$instance = new communication_interface();
//			communication_interface::$instance->logError("internationalization Instance Created");
		}

//		communication_interface::$instance->logError("internationalization->getInstance()");
		return communication_interface::$instance;
	}


	/**
	 * 
	 * Create/Update a session variable
	 *
	 * @param $sessionName: Name of the session variable
	 * @param $sessionValue: value for the session variable
	 *
	 */
	public function alert($msg) {
//logOutput("alert\n");
		communication_interface::$ajaxResponseHandle->alert($msg);
	}

	public function jsExecute($js) {
		communication_interface::$ajaxResponseHandle->script($js);
	}

	public function jsSetFunction($sFunction, $sArgs, $sScript) {
		communication_interface::$ajaxResponseHandle->setFunction($sFunction, $sArgs, $sScript);
	}

	public function jsFileInclude($sFileName, $sType = null, $sId = null) {
		communication_interface::$ajaxResponseHandle->includeScript($sFileName, $sType, $sId);
	}

	public function jsFileIncludeOnce($sFileName, $sType = null, $sId = null) {
		communication_interface::$ajaxResponseHandle->includeScriptOnce($sFileName, $sType, $sId);
	}

	public function jsFileRemove($sFileName, $sUnload = '') {
		communication_interface::$ajaxResponseHandle->removeScript($sFileName, $sUnload);
	}

	public function cssFileInclude($sFileName, $sMedia = null) {
		communication_interface::$ajaxResponseHandle->includeCSS($sFileName, $sMedia);
	}

	public function cssFileRemove($sFileName, $sMedia = null) {
		communication_interface::$ajaxResponseHandle->removeCSS($sFileName, $sMedia);
	}

	public function assign($sTarget,$sAttribute,$sData) {
		communication_interface::$ajaxResponseHandle->assign($sTarget,$sAttribute,$sData);
	}

	public function elementRemove($sTarget) {
		communication_interface::$ajaxResponseHandle->remove($sTarget);
	}

	public function elementInsertBefore($referenceId, $newTag, $newID) {
		communication_interface::$ajaxResponseHandle->insert($referenceId, $newTag, $newID);
	}

	public function elementInsertAfter($referenceId, $newTag, $newID) {
		communication_interface::$ajaxResponseHandle->insertAfter($referenceId, $newTag, $newID);
	}

	public function elementInsertInto($referenceId, $newTag, $newID) {
		communication_interface::$ajaxResponseHandle->create($referenceId, $newTag, $newID);
	}

	public function elementAddEventHandler($sTarget,$sEvent,$sHandler) {
		communication_interface::$ajaxResponseHandle->addHandler($sTarget,$sEvent,$sHandler);
	}

	public function elementRemoveEventHandler($sTarget,$sEvent,$sHandler) {
		//Funktioniert nicht!!!
		communication_interface::$ajaxResponseHandle->removeHandler($sTarget,$sEvent,$sHandler);
	}
}
?>
