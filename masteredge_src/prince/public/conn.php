<?php

$servername = "localhost";
$username = "kuropan2_tigergamer";
$password = "kuropan2_tigergamer";
$dbname = "kuropan2_tigergamer";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn) {

die(" PROBLEM WITH CONNECTION : " . mysqli_connect_error());

}
  
?>