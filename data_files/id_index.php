<?php

session_start();

require_once("../xsert/connect.php");
error_reporting(E_NOTICE ^ E_ALL);

header('Content-Type: application/json');

$tab = $_GET['tab']; //gets table 
$tab_index = $_GET['tab_index']; //return selected table column

if($tab=='requisition'){

$sql = mysqli_query($connect,"SELECT * FROM requisition GROUP BY requisition_no ORDER BY requisition_no ");
if(mysqli_num_rows($sql)){
while($r=mysqli_fetch_array($sql)){
    $index[] = $r[1];
  }
 } else {
 	$index[] = '';
 } 
}

if($tab=='asset'){

$sql = mysqli_query($connect,"SELECT * FROM office_asset_purchase GROUP BY voucher_no ORDER BY voucher_no ");
if(mysqli_num_rows($sql)){
while($r=mysqli_fetch_array($sql)){
    $index[] = $r[1];
  }
 } else {
  $index[] = '';
 } 
}

if($tab){

$sql = mysqli_query($connect,"SELECT * FROM $tab GROUP BY $tab_index ORDER BY $tab_index ");
   if(mysqli_num_rows($sql)){
        while($r=mysqli_fetch_array($sql)){
          $index[] = $r[$tab_index];
      }
    } else {
      $index[] = '';
   } 
}

$unique = array_unique($index);
print json_encode($index);

?>