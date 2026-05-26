<?php 

$folder_path1 = "writable/logs/"; 
$files1 = glob($folder_path1.'/*');  
foreach($files1 as $file) { if(is_file($file)) unlink($file); } 

$folder_path2 = "writable/session/"; 
$files2 = glob($folder_path2.'/*');  
foreach($files2 as $file) { if(is_file($file)) unlink($file); } 

$folder_path3 = "writable/debugbar/"; 
$files3 = glob($folder_path3.'/*');  
foreach($files3 as $file) { if(is_file($file)) unlink($file); } 

header('Location: public');
     exit();
?>