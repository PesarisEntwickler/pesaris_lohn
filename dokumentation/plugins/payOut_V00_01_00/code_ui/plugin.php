<?php
class payOut_UI {
	public function sysListener($functionName, $functionParameters) {
		global $aafwConfig;

		switch($functionName) {
		case 'payOut.sysLoader': // <-- FIX vorgegebener Name. Wird waehrend des Login-Prozesses automatisch aufgerufen. 
			//Damit lassen sich Plugin-spezifische CSS- und JS-Dateien laden
//			communication_interface::cssFileInclude('plugins/payOut_V00_00_01/code_ui/css/test.css','all');
			communication_interface::jsFileInclude('plugins/payOut_V00_00_01/code_ui/js/test.js','text/javascript','payOut');
			break;
		case 'payOut.helloWorld': // hier ein Beispiel fuer die Anzeige einer Message-Box
			$objWindow = new wgui_window("payroll", "infoBox");
			$objWindow->windowTitle("Mein Titel");
			$objWindow->windowWidth(450);
			$objWindow->windowHeight(180);
			$objWindow->setContent("<br/>DTA Verwaltung<br/><br/><button onclick='$(\"#modalContainer\").mb_close();'>OK</button>");
			$objWindow->showInfo(); //alternativ: showQuestion(), showAlert()
			break;
		case 'payOut.largerWindow':
			// Wenn keine Daten ans Template Uebergeben werden, sollte der Array einfach leer initialisiert werden: $data = array();
			// In diesem Beispiel werden die Formulardaten direkt ins Template "generiert"
			$data["meinName"] = "Mein Name ist Hase";
			$data["DatenFuerLoop"] = array();
			$data["DatenFuerLoop"][] = array("id"=>"1","bezeichnung"=>"Option 1","selected"=>"0");
			$data["DatenFuerLoop"][] = array("id"=>"2","bezeichnung"=>"Option 2","selected"=>"0");
			$data["DatenFuerLoop"][] = array("id"=>"3","bezeichnung"=>"Option 3","selected"=>"0");
			$data["DatenFuerLoop"][] = array("id"=>"4","bezeichnung"=>"Option 4","selected"=>"1"); //Option 4 soll ausgewaehlt werden
			$data["DatenFuerLoop"][] = array("id"=>"5","bezeichnung"=>"Option 5","selected"=>"0");
			$data["meineCheckbox"] = 1;

			$objWindow = new wgui_window("payOut", "TestWindow1"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
			$objWindow->windowTitle($objWindow->getText("MeinTitel1"));
			$objWindow->windowIcon("users24x24.png"); //anstatt der 24x24 Pixel sollte ein Icon mit 32x32 Pixeln verwendet werden
			$objWindow->windowWidth(550);
			$objWindow->windowHeight(225);
			$objWindow->loadContent("testdatei",$data,"TestWindowEins"); //1. Parameter: Name der Template-Datei / 2. Parameter: an die Datei zu uebergebende Daten / 3. Parameter: Name des Template-Blocks
			$objWindow->showWindow();
			break;
		case 'payOut.largerWindowBL':
			//In diesem Beispiel werden die Formulardaten mittels JavaScript eingefuegt. Hier mit der Funktion 'testPlgLoad' in der Datei 'test.js'.
			$fb = blFunctionCall('payOut.braucheDaten');
			if($fb["success"]) {
				$data["DatenFuerLoop"] = $fb["data"];

				$objWindow = new wgui_window("payOut", "TestWindow2"); //1. Parameter: immer Name des aufrufenden Plugins / 2. Parameter: wird im HTML als "id" gesetzt, damit ist das Fenster per JS, resp. jQuery ansprechbar
				$objWindow->windowTitle($objWindow->getText("MeinTitel2"));
				$objWindow->windowIcon("users24x24.png");
				$objWindow->windowWidth(550);
				$objWindow->windowHeight(225);
				$objWindow->dockable(false);
				$objWindow->buttonMaximize(false);
				$objWindow->resizable(false);
				$objWindow->fullscreen(false);
				$objWindow->modal(true);
										//1. Parameter: Name der Template-Datei 
													// 2. Parameter: an die Datei zu uebergebende Daten 
															// 3. Parameter: Name des Template-Blocks
				$objWindow->loadContent("testdatei",$data,"TestWindowZwei"); 
//				$objWindow->addEventFunction_onResize("");
				$objWindow->showWindow();

				communication_interface::jsExecute("testPlgLoad('prfx2_',{'meinText':'das ist mein Text','meinSelect':'CH','meineCheckbox':1});");
			} //else{ z.B. eine Fehlermeldung... }
			break;
		case 'payOut.largerWindowSave':
			communication_interface::alert("Server hat empfangen: ".$functionParameters[0]["meinText"].", ".$functionParameters[0]["meinSelect"].", ".$functionParameters[0]["meineCheckbox"]);
			break;
		case 'payOut.alert':
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
			uiFunctionCall('baseLayout.appMenuAddSection','payOut','Auszahlen');
			uiFunctionCall('baseLayout.appMenuAddItem','payOut','menupayOuthello','DTA ausloesen...','plugins/payOut_V00_01_00/code_ui/media/icons/config24x24.png','cb(\'payOut.helloWorld\');return false;');
			uiFunctionCall('baseLayout.appMenuAddItem','payOut','menupayOutalert','JS ALERT','plugins/baseLayout_V00_01_00/code_ui/media/icons/config24x24.png','cb(\'payOut.alert\');return false;');
			break;
		case 'core.bootComplete':
//			blFunctionCall('payOut.onBootComplete');
			break;
		}
	}
}

$SYS_PLUGIN["ui"]["payOut"] = new payOut_UI();

?>
