<?php

$servername = "localhost";
$username = "aalyan_youtube";
$password = "aalyan_youtube";
$dbname = "aalyan_youtube";

$conn = mysqli_connect($servername,$username,$password,$dbname);

if(!$conn) {

die(" PROBLEM WITH CONNECTION : " . mysqli_connect_error());

}
  
?>