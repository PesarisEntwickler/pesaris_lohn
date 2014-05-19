<?php
function wgui_changePassword($var_changePassword) {
$retval="";
$retval .= wgui_changePassword_main($var_changePassword);

return $retval;
}
function wgui_changePassword_main($var_changePassword) {
$retval="";
$retval .= "<br/><form id=\"baseLayoutPwdForm\" action=\"javascript:void(0);\" class=\"baseLayoutForm\"><label for=\"oldPwd\">Altes Passwort:</label><input type=\"password\" id=\"oldPwd\" name=\"oldPwd\" value=\"\"><br/><label for=\"newPwd1\">Neues Passwort:</label><input type=\"password\" id=\"newPwd1\" name=\"newPwd1\" value=\"\"><br/><label for=\"newPwd2\">Neues Passwort (Kontrolleingabe):</label><input type=\"password\" id=\"newPwd2\" name=\"newPwd2\" value=\"\"><br/><div id=\"baseLayoutPwdErr\"></div><button id=\"btnPwdSubmit\" onclick=\"cb('baseLayout.changePassword',xajax.getFormValues('baseLayoutPwdForm'));\">OK</button><button id=\"btnPwdCancel\" onclick=\"$('#modalContainer').mb_close();\">Abbrechen</button></form>\n";
return $retval;
}

?>