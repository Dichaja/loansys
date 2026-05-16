<?php
$host="localhost";
$user="shamwkqc_loanusr";
$pwd = "Sample@123?";
$db = "shamwkqc_loan";
$root = 3306;
error_reporting(0);
$connect = mysqli_connect($host,$user,$pwd,$db,$root);

if($connect){
  mysqli_select_db($connect,$db);
} else {
	die("Can not Connect to the Database: " . mysqli_connect_error());
}
?>