<?php
//require_once('../common-functions/configuration.php');
//require_once('kernel/common-functions/configuration.php');
/*******************************************************************************
* Module:   Axerios wgui                                                       *
* Version:  0.01                                                               *
* Date:     2011-05-19                                                         *
* Author:   Daniel MUELLER                                                     *
*******************************************************************************/

if(!class_exists('wgui'))
{
define('wgui_VERSION','0.01');

class wgui
{
//Private properties
var $cPluginName;
var $templateName;
var $guiLanguage;
var $cachePath;
var $templatePath;
var $mediaPath;
var $translations;

/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/
function wgui($cPluginName="",$cTemplate="",$cLanguage="de")
{
	global $aafwConfig;
	$this->cPluginName = $cPluginName;
	$this->templateName = $cTemplate;
	$this->guiLanguage = strtolower($cLanguage);
//	$this->cachePath = CORE_WGUI_CACHE;
	$currentPluginVersion = session_control::getPluginVersion($cPluginName);
	$this->cachePath = 'plugins/'.$cPluginName."_".$currentPluginVersion.'/code_ui/cache/';
// die Sprache müssen wir nicht übergeben. Stattdessen müssen wir die Templateart ('webbrowser' oder 'mobile') und den Namen des Plugins übergeben. Sub-Plugins müssen ebenfalls berücksichtigt werden (wie? ev. mit Pfadangabe 'plugin1.subPlugin')
// /home/dwm/server-web/axerios/aafw/kernel/cache/templates-webbrowser
// /home/dwm/server-web/axerios/aafw/kernel/cache/templates-mobile
	$this->templatePath = 'plugins/'.$cPluginName."_".$currentPluginVersion.'/code_ui/templates/';
	$this->mediaPath = 'plugins/'.$cPluginName."_".$currentPluginVersion.'/code_ui/media/';
	$this->translations = null;
}

function getText($textID)
{
	if($this->translations == null) {
		require_once($this->templatePath.$this->guiLanguage."-text-resources.php");
		$this->translations = $textResourceMap;
	}
	return $this->translations[$textID];
}

function setTemplate($cTemplate="")
{
	$this->templateName = $cTemplate;
}

function processTemplate($data,$subTemplate="")
{
	$subTemplate = $subTemplate=="" ? "" : "_$subTemplate";
	$templateName = $this->templateName;
	$language = $this->guiLanguage;
	$templateFilename = $this->cachePath."$language-$templateName.php";
//communication_interface::alert($templateFilename);
//return $templateFilename;
	if(file_exists($templateFilename)) {
//	if(false) {
		require_once($templateFilename);
	}else{
//return "lade jetzt ".$this->templatePath."$language-text-resources.php";
//		require_once($this->templatePath."$language-text-resources.php");
		if($this->translations == null) {
			require_once($this->templatePath.$this->guiLanguage."-text-resources.php");
			$this->translations = $textResourceMap;
		}

		$sourceFilename = $this->templatePath."$templateName.html";
		$source = file_get_contents($sourceFilename);

		if(substr($source,0,13) == '<wgui:noop />') return substr($source,13);

		//Includes laden
		preg_match_all('/<wgui:include file="(?P<file>[\._a-zA-Z0-9]*)" block="(?P<block>[_a-zA-Z0-9]*)"\/>/s', $source, $matches);
		if(isset($matches[0]) && isset($matches[1])) {
			$numOfElements = sizeof($matches[1]);
			for($i=0;$i<$numOfElements;$i++) {
				preg_match('/<wgui:block name="'.$matches["block"][$i].'">(.*?)<\/wgui:block name="'.$matches["block"][$i].'">/s', file_get_contents($this->templatePath.$matches["file"][$i]), $incMatch);
				$source = str_replace($matches[0][$i],$incMatch[1],$source);
			}
		}


		$source = str_replace("\t","",$source);
		$source = preg_replace("/>([\\t\\s]*([\\n\\r])+[\\t\\s]*)*</", "><", $source);

		$source = preg_replace("/[\\n\\r]/", "\\n", $source);

		$source = "\n--sourcecode--<?php\n--sourcecode--function wgui_$templateName(\$var_$templateName) {\n--sourcecode--if(is_array(\$var_$templateName)) extract(\$var_$templateName, EXTR_SKIP);\n--sourcecode--\$retval=\"\";\n".$source;
		$source .= "\n--sourcecode--return \$retval;\n--sourcecode--}\n";

		//Text
		preg_match_all('/<wgui:text id="(?P<name>[_a-zA-Z0-9|]*)"[^>]*/', $source, $matches);
		if(isset($matches[0]) && isset($matches["name"])) {
			$numOfElements = sizeof($matches[0]);
			for($i=0;$i<$numOfElements;$i++) {
				$textID = $matches["name"][$i];
				$source = str_replace($matches[0][$i].">",$this->translations[$textID],$source);
			}
		}

		//Variabeln
		preg_match_all('/<wgui:var name="(?P<name>[_a-zA-Z0-9|]*)"[^>]*/', $source, $matches);
		if(isset($matches[0]) && isset($matches["name"])) {
			$numOfElements = sizeof($matches[0]);
			for($i=0;$i<$numOfElements;$i++) {
				$arrV = explode("|",$matches["name"][$i]);
				$varName = "";
				foreach($arrV as $varNamePart) $varName .= $varName == "" ? "\$$varNamePart" : '["'.$varNamePart.'"]';
				$source = str_replace($matches[0][$i].">",'".'.$varName.'."',$source);
			}
		}

		//Loop-Variabeln
		preg_match_all('/<wgui:loopvar loopname="(?P<loopname>[_a-zA-Z0-9]*)" varname="(?P<varname>[_a-zA-Z0-9]*)"[^>]*/', $source, $matches);
		if(isset($matches[0]) && isset($matches["loopname"]) && isset($matches["varname"])) {
			$numOfElements = sizeof($matches[0]);
			for($i=0;$i<$numOfElements;$i++) {
				$source = str_replace($matches[0][$i].">",'".$'.$matches["loopname"][$i].'["'.$matches["varname"][$i].'"]."',$source);
			}
		}

		//Sprachen/Labels
		$tag = '<wgui:language="'.$language.'">';
		preg_match_all('/(<wgui:language="[-a-zA-Z]{0,5}">){1,1}([^<]*)<\/wgui>/', $source, $matches);
		if(isset($matches[0]) && isset($matches[1]) && isset($matches[2])) {
			$numOfElements = sizeof($matches[1]);
			for($i=0;$i<$numOfElements;$i++) {
				if($matches[1][$i]==$tag) $source = str_replace($matches[0][$i],$matches[2][$i],$source);
				else $source = str_replace($matches[0][$i],"",$source);
			}
		}

		//Kommentare aus dem HTML entfernen
		$source = preg_replace("/<!--(.*?)-->/s", "", $source);

		//CASEs erzeugen
		while(preg_match('/<wgui:case switchname="(?P<attr>[_a-zA-Z0-9]*)" condition="(?P<condition>[_a-zA-Z0-9|]*)">(.*?)<\/wgui:case switchname="(?P=attr)">/s', $source, $matches)>0) {
			$source = str_replace($matches[0],"\n--sourcecode--case '".$matches["condition"]."':\n".$matches[3]."\n--sourcecode--break;\n",$source);
		}

		//SWITCHes erzeugen
		while(preg_match('/<wgui:switch name="(?P<attr>[_a-zA-Z0-9]*)" varname="(?P<varname>[_a-zA-Z0-9|]*)">(.*?)<\/wgui:switch name="(?P=attr)">/s', $source, $matches)>0) {
			$arrV = explode("|",$matches["varname"]);
			$varName = "";
			foreach($arrV as $varNamePart) $varName .= $varName == "" ? "\$$varNamePart" : '["'.$varNamePart.'"]';
			$source = str_replace($matches[0],"\n--sourcecode--if(isset(".$varName.")) switch(".$varName.") {\n".$matches[3]."\n--sourcecode--}\n",$source);
		}

		//LOOPs erzeugen
		while(preg_match('/<wgui:loop name="(?P<attr>[_a-zA-Z0-9]*)" varname="(?P<varname>[_a-zA-Z0-9|]*)">(.*?)<\/wgui:loop name="(?P=attr)">/s', $source, $matches)>0) {
			$arrV = explode("|",$matches["varname"]);
			$varName = "";
			foreach($arrV as $varNamePart) $varName .= $varName == "" ? "\$$varNamePart" : '["'.$varNamePart.'"]';
			$source = str_replace($matches[0],"\n--sourcecode--if(isset(".$varName.")) foreach(".$varName." as \$".$matches["attr"].") {\n".$matches[3]."\n--sourcecode--}\n",$source);
		}

		//BLOCK in neuer "function" erzeugen   <wgui:block name="table">
		while(preg_match('/<wgui:block name="(?P<attr>[_a-zA-Z0-9]*)">(.*?)<\/wgui:block name="(?P=attr)">/s', $source, $matches)>0) { // '/<wgui:block name="([_a-zA-Z0-9]*)">(.*?)<\/wgui:block>/s'
			$source = str_replace($matches[0],"\n--sourcecode--\$retval .= wgui_".$templateName."_".$matches[1]."(\$var_$templateName);\n",$source);
			$source .= "\n--sourcecode--function wgui_".$templateName."_".$matches[1]."(\$var_$templateName) {\n";
			$source .= "\n--sourcecode--if(is_array(\$var_$templateName)) extract(\$var_$templateName, EXTR_SKIP);\n";
			$source .= "\n--sourcecode--\$retval=\"\";\n";
			$source .= $matches[2];
			$source .= "\n--sourcecode--return \$retval;\n--sourcecode--}\n";
		}

		//unnötige 'extract' entfernen
		preg_match_all('/function [_a-zA-Z0-9]+\(\$[_a-zA-Z0-9]+\) \{(.*?)return \$retval;/s', $source, $matches);
		foreach($matches[0] as $hit) {
			$referenceString = str_replace("\$var_$templateName","",$hit);
			$referenceString = str_replace("\$retval","",$referenceString);
			if(preg_match("/\\$([_a-zA-Z0-9])+/s",$referenceString)<1) {
				$source = str_replace($hit,str_replace("--sourcecode--if(is_array(\$var_$templateName)) extract(\$var_$templateName, EXTR_SKIP);","",$hit),$source);
			}
		}

		$lines = preg_split("/[\\n\\r]/", $source, -1, PREG_SPLIT_NO_EMPTY);

		$out = "";
		foreach($lines as $line) {
			$dummy = trim($line);
			if(substr($dummy,0,14) == "--sourcecode--") {
				$dummy = substr($dummy,14);
				$out .= "$dummy\n";
			}else{
				$dummy = str_replace("\"","\\\"",$dummy);
				$out .= "\$retval .= \"$dummy\\n\";\n";
			}
		}
		$out .= "\n?>";

		$out = str_replace(".\"\".",".",$out);
		$out = str_replace("\"\".","",$out);
		$out = preg_replace("/(\\\\n){2,}/", "\\n", $out); // sucht nach '\n' das mehr als 2x hintereinander vorkommt und ersetzt es durch ein einziges '\n'

		// Anfürungs- und Schlusszeichen bei Variabeln wurden auch escaped. Diese müssen wir wieder unescapen!
		preg_match_all('/(\\\"\.)?\$([_a-zA-Z0-9])+(\[\\\"[_a-zA-Z0-9]+\\\"\])*(\.\\\")?/', $out, $matches);
		foreach($matches[0] as $hit) $out = str_replace($hit,str_replace("\\\"","\"",$hit),$out);

		$out = str_replace("\$retval .= \"\".","\$retval .= ",$out); // Zeile soll nicht mit einem leeren String beginnen
		$out = str_replace("\$retval .= \"\\n\".","\$retval .= ",$out); // Zeile soll nicht mit '\n' beginnen
		$out = str_replace("\$retval .= \"\\n\";","",$out); // Zeile soll nicht mit '\n' beginnen
		$out = str_replace("\$retval .= \"\\n","\$retval .= \"",$out); // Zeile soll nicht mit '\n' beginnen


		$fp = fopen($templateFilename, 'w');
		fwrite($fp, $out);
		fclose($fp);
		chmod($templateFilename,0666);
	}

	require_once($templateFilename);
	return call_user_func("wgui_$templateName$subTemplate", $data);
}

/*
DIE NACHFOLGENDEN BEIDEN FUNKTIONEN WERDEN IN EINE EIGENE KLASSE AUSGELAGERT!
function dialog($width,$title,$content,$buttons)
{
	$data["width"] = $width;
	$data["title"] = $title;
	$data["content"] = $content;
	foreach($buttons as $button) $data["button"][] = array("label" => $button[0],"action" => $button[1]);

	$htmlSrcDlgBox = str_replace("'","\\'",$this->processTemplate($data));
	$htmlSrcDlgBox = str_replace("\n","",$htmlSrcDlgBox);
	$htmlSrcDlgBox = str_replace("\r","",$htmlSrcDlgBox);

	return $htmlSrcDlgBox;
}

function msgBox($width,$title,$message,$buttons,$msgType)
{
	$data["width"] = $width;
	$data["type"] = $msgType;
	$data["title"] = $title;
	$data["message"] = $message;
	foreach($buttons as $button) $data["button"][] = array("label" => $button[0],"action" => $button[1]);

	$htmlSrcMsgBox = str_replace("'","\\'",$this->processTemplate($data));
	$htmlSrcMsgBox = str_replace("\n","",$htmlSrcMsgBox);
	$htmlSrcMsgBox = str_replace("\r","",$htmlSrcMsgBox);

	return $htmlSrcMsgBox;
}
*/

/*******************************************************************************
*                                                                              *
*                              Protected methods                               *
*                                                                              *
*******************************************************************************/

/*
function _loadLayout($prdoduceCompressedContent = true)
{
}
*/

//End of class
}

}
?>
