<?php
$host="localhost";
$user="root";
$pwd = "";
$db = "db_loans";

$connect = mysqli_connect($host,$user,$pwd);

if($connect){
  mysqli_select_db($connect,$db);
} else {
	mysqli_error("Can not Connect to the Database");
}
?>