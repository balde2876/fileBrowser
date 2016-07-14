<?php
session_start();

$fileloc = str_replace("/","\\",str_replace(array("///aphe///","///quot///","///equa///","///ques///","///amps///","///fstp///"),array("'",'"',"=","?","&","."),$_POST['path']));
$f1 = fopen("groups.json", "r") or die("No groups file");
$json = fread($f1,filesize("groups.json"));
fclose($f1);
$groupSettings = json_decode($json,true)[$_SESSION["group"]];
$allPermissions = $groupSettings['permissions'];
$permissions = [];

$breadcrumbs = explode("\\", $fileloc);
$i = 1;
$temp1 = "";
foreach ($breadcrumbs as &$value) {
	$temp1 = $temp1.$value."/";
	if (array_key_exists($temp1, $allPermissions)) {
		$permissions = $allPermissions[$temp1];
	}
	$i = $i + 1;
}

foreach ($_FILES as $curfile){
	$fileName = $curfile["name"]; // The file name
	$file = $fileloc."\\".$fileName;
	$fileTmpLoc = $curfile["tmp_name"]; // File in the PHP tmp folder
	$fileSize = $curfile["size"]; // File size in bytes
	$fileErrorMsg = $curfile["error"]; // 0 for false... and 1 for true
	if (!$fileTmpLoc) { // if file not chosen
	    echo "SCBF04";
	    exit();
	}
	if (in_array("WRITE",$permissions)){
	  $logfile = fopen($_SESSION["logfile"], "a");
	  fwrite($logfile, "   UPLOAD ( WRITE ) at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	  fwrite($logfile, "      File : ".$file."\r\n");
		fwrite($logfile, "      TempLocation : ".$fileTmpLoc."\r\n");
	  fclose($logfile);
	  rename($fileTmpLoc,$file);
	} else {
	  $logfile = fopen($_SESSION["logfile"], "a");
	  fwrite($logfile, "   DENIED UPLOAD ( WRITE ) at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	  fwrite($logfile, "      File : ".$file."\r\n");
	  fclose($logfile);
	  unlink($fileTmpLoc);
	}
}

?>
