<?php
	if(!isset($_POST["param"])) exit();

	if(!isset($_COOKIE["aafw"])) exit();

	require_once("kernel/common-functions/configuration.php");
	require_once("kernel/core-logic/system_database_manager.php");
	require_once("kernel/core-logic/session_control.php");
	require_once("kernel/common-functions/file_manager.php");
	$session_control = session_control::getInstance();
	if(!$session_control->setSessionToken($_COOKIE["aafw"])) exit();


function downloadFile( $fullPath, $transmissionFileName ) {
	// Must be fresh start
	if( headers_sent() ) die('Headers Sent');

	// Required for some browsers
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off');

	// File Exists?
	if( file_exists($fullPath) ){
		// Parse Info / Get Extension
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts["extension"]);

		// Determine Content Type
		switch ($ext) {
			case "pdf": $ctype="application/pdf"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "doc": $ctype="application/msword"; break;
			case "zip": $ctype="application/zip"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			default: $ctype="application/force-download";
		}

		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=\"".$transmissionFileName."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$fsize);
		ob_clean();
		flush();
		readfile( $fullPath );
	}else die('File Not Found');
} 


	$param = unserialize($_POST["param"]);

	if(isset($param["tmpPathID"])) {
		$fm = new file_manager();
		if( $fm->setTmpDir($param["tmpPathID"]) ) {
			$metadata = unserialize( $fm->setFile("metadata.dat")->getContents() ); //serialize(array("fileFormat"=>"pdf","realFileName"=>"compileme.pdf","transmissionFileName"=>"CalculationJournal.pdf")
			downloadFile($fm->setFile($metadata["realFileName"])->getFullPath().$fm->getFile(), $metadata["transmissionFileName"]);
			$fm->deleteDir();
		}else {echo "file not found"; exit();}

	}
?>
