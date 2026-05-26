<?php

$servername = "localhost";
$username = "androidengine_PHP";
$password = "androidengine_PHP";
$dbname = "androidengine_PHP";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn) {

die(" PROBLEM WITH CONNECTION : " . mysqli_connect_error());

}
  
?>