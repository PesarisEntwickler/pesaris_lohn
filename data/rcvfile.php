<?php
	ini_set('max_execution_time', 300); //5 minutes maximum execution time

	if(!isset($_POST["param"])) exit();

	if(!isset($_COOKIE["aafw"])) exit();

	require_once("kernel/common-functions/configuration.php");
	require_once("kernel/core-logic/system_database_manager.php");
	require_once("kernel/core-logic/session_control.php");
	require_once("kernel/common-functions/file_manager.php");
	$session_control = session_control::getInstance();
	if(!$session_control->setSessionToken($_COOKIE["aafw"])) exit();



$fm = new file_manager();
$token = $fm->createTmpDir();

$uploaddir = $fm->getFullPath();
$uploadfile = $uploaddir.basename($_FILES['rcvfile']['name']);

$decrypted = unserialize(openssl_decrypt($_POST['param'], "aes128", "pw_rcvfile_pw"));

$decrypted["data"]["tmpDirToken"] = $token;
$decrypted["data"]["fileName"] = basename($_FILES['rcvfile']['name']);

if (move_uploaded_file($_FILES['rcvfile']['tmp_name'], $uploadfile)) {
	$decrypted["data"]["success"] = true;
	echo "<script>parent.cb('".$decrypted["cb_function"]."',".json_encode($decrypted["data"]).");</script>\n";
} else {
	$fm->deleteDir();
	$decrypted["data"]["success"] = false;
	if(isset($decrypted["cb_function"])) {
		echo "<script>parent.cb('".$decrypted["cb_function"]."',".json_encode($decrypted["data"]).");</script>\n";
	}else{
		echo "<script>alert('fehler'); parent.$('#modalContainer').mb_close();</script>\n";
	}
}
?>
