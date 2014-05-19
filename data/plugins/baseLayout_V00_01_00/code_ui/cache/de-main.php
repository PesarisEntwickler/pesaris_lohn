<?php
function wgui_main($var_main) {
$retval="";
$retval .= wgui_main_main($var_main);

return $retval;
}
function wgui_main_main($var_main) {
if(is_array($var_main)) extract($var_main, EXTR_SKIP);
$retval="";
$retval .= "<span id=\"modalDock\"></span><div id=\"splashScreen\" style=\"position:absolute; top:0; left:0; height:100%; width:100%; z-index: 1000; background-color:#252525; background-repeat:no-repeat; text-align:center;\"><span id=\"splashStatusTxt\" style=\"color:#ffffff;\"></span></div><div class=\"ui-layout-center\" id=\"ui-layout-center\"></div><div class=\"ui-layout-north\"><img id=\"appLogo\" src=\"web/img/blue/applogo.gif\" style=\"position:absolute;top:4px;left:18px;z-index:4;\" /><div id=\"loginStatus\"><table cellpadding=\"0\" cellspacing=\"0\"><tr><td>Mandant:</td><td id=\"appCustomer\">&#151;</td></tr><tr><td>Benutzer:</td><td id=\"appUser\">&#151;</td></tr></table></div><img id=\"btnLogout\" src=\"web/img/shutdown_box_red.png\" onclick=\"cb('core.logout');\" alt=\"Abmelden\" title=\"Abmelden\" /><div id=\"dock\"></div><form id=\"dlForm\" method=\"post\"><input type=\"hidden\" name=\"param\" value=\"\"/></form><iframe id=\"dlFrame\" name=\"dlFrame\"></iframe></div><div class=\"ui-layout-west\"><div id=\"accordion1\" class=\"basic\" appmenu=\"main\"><h3 appmenu=\"h_system\"><a href=\"#\">System</a></h3><div appmenu=\"c_system\"><ul class=\"appMenu\"><li id=\"appMenuSysConfig\" style=\"background-image: url(".$iconPath."config24x24.png);\"><a href=\"#\" onclick=\"cb('baseLayout.changeConfiguration');\">Konfiguration</a></li><li id=\"appMenuSysUsers\" style=\"background-image: url(".$iconPath."users24x24.png);\"><a href=\"#\" onclick=\"cb('baseLayout.userManager');\">Benutzerverwaltung</a></li><li id=\"appMenuSysPwd\" style=\"background-image: url(".$iconPath."key24x24.png);\"><a href=\"#\" onclick=\"cb('baseLayout.changePassword');\">Passwort aendern</a></li></ul></div></div></div><div id=\"mainContent\"></div>\n";
return $retval;
}

?>