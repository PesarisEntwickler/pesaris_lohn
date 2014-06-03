<?php
class testPlugin_UI {
	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;

		switch($functionName) {
		case 'testPlugin.sysLoader': // <-- FIX vorgegebener Name. Wird während des Login-Prozesses automatisch aufgerufen. Damit lassen sich Plugin-spezifische CSS- und JS-Dateien laden
//			communication_interface::cssFileInclude('plugins/testPlugin_V00_00_01/code_ui/css/test.css','all');
			communication_interface::jsFileInclude('plugins/testPlugin_V00_00_01/code_ui/js/test.js','text/javascript','testPlugin');
			break;
		case 'testPlugin.helloWorld': // hier ein Beispiel für die Anzeige einer Message-Box
			$objWindow = new wgui_window("payroll", "infoBox");
			$objWindow->windowTitle("Mein Titel");
			$objWindow->windowWidth(450);
			$objWindow->windowHeight(180);
			$objWindow->setContent("<br/>Hello World.<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
			$objWindow->showInfo(); //alternativ: showQuestion(), showAlert()
			break;
		case 'testPlugin.largerWindow':
			// Wenn keine Daten ans Template übergeben werden, sollte der Array einfach leer initialisiert werden: $data = array();
			// In diesem Beispiel werden die Formulardaten direkt ins Template "generiert"
			$data["meinName"] = "Mein Name ist Hase";
			$data["DatenFuerLoop"] = array();
			$data["DatenFuerLoop"][] = array("id"=>"1","bezeichnung"=>"Option 1","selected"=>"0");
			$data["DatenFuerLoop"][] = array("id"=>"2","bezeichnung"=>"Option 2","selected"=>"0");
			$data["DatenFuerLoop"][] = array("id"=>"3","bezeichnung"=>"Option 3","selected"=>"0");
			$data["DatenFuerLoop"][] = array("id"=>"4","bezeichnung"=>"Option 4","selected"=>"1"); //Option 4 soll ausgewählt werden
			$data["DatenFuerLoop"][] = array("id"=>"5","bezeichnung"=>"Option 5","selected"=>"0");
			$data["meineCheckbox"] = 1;

			$objWindow = new wgui_window("testPlugin", "TestWindow1"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
			$objWindow->windowTitle($objWindow->getText("MeinTitel1"));
			$objWindow->windowIcon("users24x24.png"); //anstatt der 24x24 Pixel sollte ein Icon mit 32x32 Pixeln verwendet werden
			$objWindow->windowWidth(550);
			$objWindow->windowHeight(225);
			$objWindow->loadContent("testdatei",$data,"TestWindowEins"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
			$objWindow->showWindow();
			break;
		case 'testPlugin.largerWindowBL':
			//In diesem Beispiel werden die Formulardaten mittels JavaScript eingefügt. Hier mit der Funktion 'testPlgLoad' in der Datei 'test.js'.
			$fb = blFunctionCall('testPlugin.braucheDaten');
			if($fb["success"]) {
				$data["DatenFuerLoop"] = $fb["data"];

				$objWindow = new wgui_window("testPlugin", "TestWindow2"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("MeinTitel2"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(225);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
				$objWindow->loadContent("testdatei",$data,"TestWindowZwei"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu übergebende Daten / 3. Parameter: Name des Template-Blocks
//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();

				communication_interface::jsExecute("testPlgLoad('prfx2_',{'meinText':'das ist mein Text','meinSelect':'CH','meineCheckbox':1});");
			} //else{ z.B. eine Fehlermeldung... }
			break;
		case 'testPlugin.largerWindowSave':
			communication_interface::alert("Server hat empfangen: ".$functionParameters[0]["meinText"].", ".$functionParameters[0]["meinSelect"].", ".$functionParameters[0]["meineCheckbox"]);
			break;
		case 'testPlugin.alert':
			communication_interface::alert("ein einfacher ALERT"); //alert sollte nur zu Debug-Zwecken eingesetzt werden
			break;
		default:
			return "Funktion unbekannt";
			break;
		}

	}

	public function eventListener($eventName, $eventParameters) {
		global $aafwConfig;

		switch($eventName) {
		case 'core.bootLoadMenu':
			uiFunctionCall('baseLayout.appMenuAddSection','testPlugin','Test Plugin');
			uiFunctionCall('baseLayout.appMenuAddItem','testPlugin','menutestpluginhello','Hello world','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\'testPlugin.helloWorld\');return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','testPlugin','menutestpluginwl','Ein grösseres Fenster','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\'testPlugin.largerWindow\');return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','testPlugin','menutestpluginwl2','Funktionsaufruf BL','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\'testPlugin.largerWindowBL\');return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','testPlugin','menutestpluginalert','JS ALERT','plugins/baseLayout_V00_01_00/code_ui/media/icons/key24x24.png','cb(\'testPlugin.alert\');return false;');
			break;
		case 'core.bootComplete':
//			blFunctionCall('testPlugin.onBootComplete');
			break;
		}
	}
}

$SYS_PLUGIN["ui"]["testPlugin"] = new testPlugin_UI();

?>
