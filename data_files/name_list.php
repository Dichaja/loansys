<?php

session_start();

require_once("../xsert/connect.php");
error_reporting(E_NOTICE ^ E_ALL);

header('Content-Type: application/json');

$search = $_POST['search'];
$table = $_POST['name_cat'];
$search_officer = $_POST['search_officer'];

$qry = "SELECT t.id, t.first_name, t.last_name FROM $table t, branches b ";
   if(!$_SESSION['general_user'])
          $qry .=", user_log u ";
            $qry .= " WHERE t.branch_id = b.id AND CONCAT(t.first_name,  ' ', t.last_name) LIKE  '%$search%' AND ";
          if(!$_SESSION['general_user'])
              $qry .= " t.branch_id = u.user_branch AND u.id='".$_SESSION['session_id']."' AND ";
            if($search_officer)
                $qry .= " t.status = '01' AND t.job='0001' AND ";
          $qry .= " 1 ORDER BY t.last_name, t.first_name ASC ";

$sql = mysqli_query($connect, $qry);
if(mysqli_num_rows($sql)){
    while($r=mysqli_fetch_array($sql)){
     $array_mem[]=array('first_name'=>$r[1],'last_name'=>$r[2],'id'=>$r[0]);
    }
}
print json_encode($array_mem);

?>