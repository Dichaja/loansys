<?php
$host="localhost";
$user="root";
$pwd = "";
$db = "db_loans";
$root = 3306;
error_reporting(0);
$connect = mysqli_connect($host,$user,$pwd,$db,$root);

if($connect){
  mysqli_select_db($connect,$db);
} else {
	die("Can not Connect to the Database: " . mysqli_connect_error());
}
?>