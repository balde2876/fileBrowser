<?php
session_start();
$file = str_replace("/","\\",str_replace(array("///aphe///","///quot///","///equa///","///ques///","///amps///","///fstp///"),array("'",'"',"=","?","&","."),$_GET['path']));
$f1 = fopen("groups.json", "r") or die("No groups file");
$json = fread($f1,filesize("groups.json"));
fclose($f1);
$groupSettings = json_decode($json,true)[$_SESSION["group"]];
$allPermissions = $groupSettings['permissions'];
$permissions = [];

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

$breadcrumbs = explode("\\", $file);
$i = 1;
$temp1 = "";
foreach ($breadcrumbs as &$value) {
	$temp1 = $temp1.$value."/";
	if (array_key_exists($temp1, $allPermissions)) {
		$permissions = $allPermissions[$temp1];
	}
	$i = $i + 1;
}
//echo json_encode($permissions);
if (in_array("DELETEFOLDER",$permissions)){
	//echo $file;
  $logfile = fopen($_SESSION["logfile"], "a");
  fwrite($logfile, "   DELETE FOLDER at ".date('H:i:s')." on ".date('d F Y')."\r\n");
  fwrite($logfile, "      Folder : ".$file."\r\n");
  fclose($logfile);

	if (file_exists($file)) {
		deleteDir($file);
		if (isset($_SERVER["HTTP_REFERER"])) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }
		exit;
	}
} else {
  $logfile = fopen($_SESSION["logfile"], "a");
	fwrite($logfile, "   DENIED DELETE FOLDER at ".date('H:i:s')." on ".date('d F Y')."\r\n");
	fwrite($logfile, "      Folder : ".$file."\r\n");
	fclose($logfile);

	header('Location: insufficientPermissions.php');
	exit;
}
?>
