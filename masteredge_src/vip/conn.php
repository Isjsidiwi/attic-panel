<?php

$servername = "localhost";
$username = "princeaalyan_paidfile";
$password = "princeaalyan_paidfile";
$dbname = "princeaalyan_paidfile";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn) {

die(" PROBLEM WITH CONNECTION : " . mysqli_connect_error());

}
  
?>