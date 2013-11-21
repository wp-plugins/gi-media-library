<?php

require_once('includes/admin-functions.php');

if(isset($_GET['fileid'])) {
	$file = base64_decode($_GET['fileid']);
	if(empty($file) || !$file)
		die ('Unknown file.');
}else
	die ();
	
$fileinfo = pathinfo($file);
if($fileinfo['dirname']==".") 
	die("Unknown file.");
header('Content-type: application/x-msdownload', true, 200);
header("Content-Disposition: attachment; filename=" . basename($file));
header("Pragma: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
@readfile($file);
?>