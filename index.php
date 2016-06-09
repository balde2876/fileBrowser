<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>File Browser</title>
<link rel="icon" type="image/png" href="icon.png">
<script src="//code.jquery.com/jquery-1.12.0.js"></script>
<meta id="viewport" name="viewport" content="width=500">
<style>
@import url(styleSheet.css);
#uploadButton {
  -webkit-border-radius: 64;
  -moz-border-radius: 64;
  display:inline-block;
  border-radius: 64px;
  box-shadow: 0px 2px 12px -3px #444444;
  -webkit-transition-duration: 0.2s;
  transition-duration: 0.2s;
  -webkit-transition-property: box-shadow, transform;
  transition-property: box-shadow, transform;
}
#uploadButton:hover {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
  box-shadow: 0px 5px 25px -5px #444444;
}
</style>

</head>
<body>

<div class="material" id="jss001" style="width:calc(100% - 68px);background-color: #ffffff; position:absolute; top: 10px; left:10px; overflow: hidden; z-index:1;
padding-left:24px;padding-top:122px;padding-bottom:20px;padding-right:24px;
margin-bottom:10px;
">
<div style="width:100%;background-color: #444444; position:absolute; 
top: 0px; z-index:0; height:67px; left: 0px;
">
<div style="height:36px;position:absolute; 
top: 0px; z-index:0; left: 0px;
padding-left:24px;padding-top:12px;padding-bottom:19px;padding-right:24px;
margin-bottom:0px;
">
<?php
//This function was taken from https://sourceforge.net/projects/soloadmin/
//User:                        https://sourceforge.net/u/ucl/profile/
function filesize64($file)
{
    static $iswin;
    if (!isset($iswin)) {
        $iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
    }

    static $exec_works;
    if (!isset($exec_works)) {
        $exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');
    }

    // try a shell command
    if ($exec_works) {
        $cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : "stat -c%s \"$file\"";
        @exec($cmd, $output);
        if (is_array($output) && ctype_digit($size = trim(implode("\n", $output)))) {
            return $size;
        }
    }

    // try the Windows COM interface
    if ($iswin && class_exists("COM")) {
        try {
            $fsobj = new COM('Scripting.FileSystemObject');
            $f = $fsobj->GetFile( realpath($file) );
            $size = $f->Size;
        } catch (Exception $e) {
            $size = null;
        }
        if (ctype_digit($size)) {
            return $size;
        }
    }

    // if all else fails
    return filesize($file);
}
//This function was taken from http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
//Author:                      Jeffrey Sambells
function human_filesize($bytes, $dec = 2) 
{
    $size   = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $factor = floor((strlen($bytes) - 1) / 3);

    return sprintf("%.{$dec}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

$f1 = fopen("config.json", "r") or die("No config file");
$json = fread($f1,filesize("config.json"));
fclose($f1);
$settings = json_decode($json,true);
$icons = $settings['icons'];
$fileExtensions = $settings['fileExtensions'];
$driveRoot = $settings['driveRoot'];
$driveAccessRoot = $settings['driveAccessRoot'];
$iconImageRoot = $settings['iconImageRoot'];
$defaultIcon = $settings['defaultIcon'];
$folderIcon = $settings['folderIcon'];
$dir = $driveRoot;
if (isset($_GET['filepos'])){
$dir = $driveRoot.$_GET['filepos'];
} else {
$_GET['filepos'] = "";
}

$breadcrumbs = explode("\\", $_GET['filepos']);
$i = 1;
foreach ($breadcrumbs as &$value) {
	$value1 = $value;
	if ($i == 1) {
		$value1 = "Root";
	}
	if ($i < count($breadcrumbs)){
		echo "<a style='position:relative;top:-10px;height:36px;display:inline;padding-right:5px;' href='index.php?filepos=".implode("\\", array_slice($breadcrumbs,0,$i))."'><h3 style='color:#ffffff;position:relative;top:2px;height:36px;display:inline;'>".$value1."<img src='img/breadcrumb.png' style='position:relative;top:11px;left:5px;height:36px;display:inline;'></img>";
	} else {
		echo "<a style='position:relative;top:5px;height:36px;display:inline;padding-right:5px;' href='index.php?filepos=".implode("\\", array_slice($breadcrumbs,0,$i))."'><h3 style='color:#ffffff;position:relative;top:2px;height:36px;display:inline;'>".$value1;
	}
	echo "</h3></a>";
	$i = $i + 1;
}
echo '</div></div><div style="width:100%;background-color: #eeeeee; position:absolute; 
top: 67px; z-index:0; left: 0px; height: 32px;">
<p style="color:#444444;position:absolute;top:4px;left:61px;">Name</p><p style="color:#444444;position:absolute;right:50px;width:50px;top:4px;">Size</p></div>';
$files1 = scandir($dir);
foreach ($files1 as &$value) {
	$filepathpieces = explode("\\", $value);
    $filepieces = explode(".", end($filepathpieces));
	$desthref = "index.php?filepos=".$_GET['filepos']."\\".$value;
	$icon = $defaultIcon;
	$fileSize = "";
	if (array_key_exists(end($filepieces), $fileExtensions)) {
		$icon = $icons[$fileExtensions[end($filepieces)]];
	}
	if ($value == ".."){
		$temp1 = explode("\\",$_GET['filepos']);
		array_pop($temp1);
		$desthref = "index.php?filepos=".implode("\\", $temp1);
	}
	if (is_dir($driveRoot.$_GET['filepos']."\\".$value)){
		$icon = $folderIcon;
	} else {
		$desthref = "getFile.php?path=".$driveAccessRoot.$_GET['filepos']."\\".$value;
		//$fileSizeRaw = filesize($driveAccessRoot.$_GET['filepos']."\\".$value);
		$fileSize = human_filesize(filesize64($driveAccessRoot.$_GET['filepos']."\\".$value));
	}
	if ($value == "."){
		
	} else {
		echo "<div style='position:relative;top:0px;left:0px;width:100%;height:32px;'>";
		echo "<a href='".$desthref."' style='position:absolute;top:5px;left:40px;'>".$value."</a>";
		echo "<img style='position:absolute;top:0px;left:0px;width:32px;height:32px;' src='".$iconImageRoot.$icon."'></img>";
		echo "<p style='color:#444444;position:absolute;right:26px;width:50px;top:4px;'>".$fileSize."</p>";
		echo "</div>";
	}
}
?>

</div>
</body>
</html>
