<?php
function wgui_auszahlen($var_auszahlen) {
$retval="";
$retval .= wgui_auszahlen_wguiBlockAuszahlenPeriodenwahl($var_auszahlen);
$retval .= wgui_auszahlen_TestWindowZwei($var_auszahlen);

return $retval;
}
function wgui_auszahlen_wguiBlockAuszahlenPeriodenwahl($var_auszahlen) {
if(is_array($var_auszahlen)) extract($var_auszahlen, EXTR_SKIP);
$retval="";
$retval .= "<form><br/><div class=\"ui-tabs ui-widget-content ui-corner-all\"><table width=\"100%\"><tr><td>Periodenwahl:\n</td><td></td><td>Dateien der Periode:\n</td></tr><tr><td><select id=\"ausz_PeriodenSelect\" name=\"ausz_PeriodenSelect\" size=\"5\" style=\"width: 140px;\">\n";
if(isset($DirectoriesLoop)) foreach($DirectoriesLoop as $selectLoop) {
$retval .= "<option value=\"".$selectLoop["id"]."\"\n";
if(isset($selectLoop["selected"])) switch($selectLoop["selected"]) {
case '1':
$retval .= "selected\n";
break;
}
$retval .= ">".$selectLoop["bezeichnung"]."</option>\n";
}
$retval .= "</select></td><td><button id=\"btnAuszahlenBerechnen\" name=\"btnAuszahlenBerechnen\" class=\"PesarisButton\">".$btnNeuNochmals."</button></td><td><div class=\"prlPeriodenFileWrapper\" id=\"prlPeriodenFileWrapper\">\n";
if(isset($PeriodenFiles)) foreach($PeriodenFiles as $fileLoop) {
$retval .= "<a href='".$fileLoop["technFilename"]."' target='_blank'>".$fileLoop["fileName"]."</a><br/>\n";
}
$retval .= "</div></td></tr></table></div></form><button id=\"btnAuszahlenClose\" name=\"btnAuszahlenClose\" class=\"PesarisButton\">Schliessen</button>\n";
return $retval;
}
function wgui_auszahlen_TestWindowZwei($var_auszahlen) {
$retval="";
$retval .= "zwei\n";
return $retval;
}

?>