<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require_once('../xsert/connect.php');
//custom function to filter

function filterData(&$str){
	 $str = preg_replace("/\t/","\\t", $str);
	 $str = preg_replace("/\r?\n/", "\\n", $str);
	 if(strstr($str,'"')) 
	 	$str = '"'.str_replace('"', '""',$str).'"';
}

$fileName = "members-data_".date('Y-m-d').".xls";

$fields = array('ID','MEMBER NAMES','GENDER','CONTACTS');
$excelData = implode("\t", array_values($fields)). "\n";

$start=0;
$limit=40;

$qry = "SELECT * FROM clients c, branches b ";
       if(!$_SESSION['general_user'])
          $qry .=", user_log u ";
            $qry .= " WHERE c.branch_id = b.id AND c.status='01' AND ";
          if(!$_SESSION['general_user'])
              $qry .= " c.branch_id = u.user_branch AND ";
        if($client !=''){
            if($client_id)
              $qry .= " c.id = '$client_id' AND ";
              else
                 $qry .= " CONCAT(c.first_name,' ',c.last_name) LIKE  '%$client%' AND ";
             }
         if($address!='')
                $qry .= " c.residence = '".$address."' AND ";
            if($month!='')
                $qry .= " c.monthname(date_created) = '".$month."' AND ";
             if($gender!='')
                  $qry .= " c.gender = '".ucfirst($gender)."' AND ";
              if(!$_SESSION['general_user'])
                    $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                 if($branch)
                     $qry .= " b.id = '$branch' AND ";
               $qry2 .= $qry.' 1 ';
             $qry .= " 1 ORDER BY c.first_name, c.last_name ASC LIMIT $start, $limit ";

    $result = mysqli_query($connect,$qry);

   if(mysqli_num_rows($result)){
        $count=0;
        while($rw=mysqli_fetch_array($result)){
       $lineData = array($rw['id'],$rw['first_name'].' '.$rw['last_name'],$rw['gender'],$rw['contacts']); 
       array_walk($lineData,'filterData');
       $excelData .= implode("\t",array_values($lineData)) . "\n"; 
  }
}

//headers for download
header('Content-Type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=\"$fileName\"");

/* Set the Content-Type for XLSX format
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$fileName\"");*/

echo $excelData;

?>