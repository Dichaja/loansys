<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess(); //check user loggin

echo date_set_back('2024-07-28',37);
?>
<table>
<?php

$check_date = date('Y-m-d');

    if($_POST['post_search']){

     $search_date = $_POST['search_date'];
      if($search_date)
        $check_date = date('Y-m-d',strtotime($search_date));

        $branch = $_POST['branch_details'];
        $pay_status = $_POST['pay_status'];
        $search_names = $_POST['mem_names'];
    }  

    if($_GET['id']){

       $loan_pay_id = $_GET['id'];
       $loan = $_GET['loan'];

        $s = mysqli_query($connect,"SELECT c.id, CONCAT(c.first_name,' ', c.last_name) as 'client_name' FROM clients c, loan_entries l, loan_payments p WHERE c.id = l.client AND l.id = p.loan AND p.id='$loan_pay_id' ");
         $r = mysqli_fetch_array($s);
         $search_names = $r['client_name'];
    }   

    $search='';
     if($check_date)
       $search .= ', '.date('d/m/Y',strtotime($check_date));
      if($search_names)
         $search .= ', '.$search_names;
       if($branch){
              $q = mysqli_query($connect,"SELECT * FROM branches WHERE id='$branch' ");
                $rows = mysqli_fetch_array($q);
                 $search .= ', '.$rows[1];
           }
          if($pay_status){
              if($pay_status=='01')
                 $search .= ', Non Defaulters ';
                if($pay_status=='00')
                   $search .= ', Defaulters ';
          }
 ?>
 <tr>
   <td><span>Loan Payments Activity <?php echo $search ?></span></td>
 </tr>
<?php
$qry = "SELECT c.id, CONCAT(c.first_name,' ', c.last_name) as 'client_name', l.loan_amount, l.interest, l.period, l.date_entry, l.duration, p.amount_paid, p.pay_date, l.id as 'loan', p.id as 'payments', l.status, b.branch_name, c.data_id FROM clients c LEFT JOIN loan_entries l ON c.id = l.client LEFT JOIN loan_payments p ON p.loan = l.id LEFT JOIN branches b ON b.id = c.branch_id  ";

             if(!$_SESSION['general_user'])
                       $qry .=" LEFT JOIN user_log u ON c.branch_id = u.user_branch ";
                  $qry .= " WHERE l.id != 'NULL' AND c.id='22992767' AND ";
               if(!$_SESSION['general_user'])
                    $qry .= "u.id='".$_SESSION['session_id']."' AND ";
                   if($branch)
                       $qry .= " b.id = '$branch' AND ";
                     if($search_names)
                        $qry .= " CONCAT(c.first_name,' ', c.last_name) LIKE '%$search_names%' AND ";
                      if($loan)
                          $qry .= " l.id = '$loan' AND ";
                  $qry .= " 1  GROUP BY c.id, loan ORDER BY client_name, l.date_entry ASC ";

       $sql = mysqli_query($connect,$qry);
              
                  $count = 0;
                  $general_pay = 0;
                  $tot_loan = 0;
                  $display_paid = '1';
                  $display_due = '1';
                  $tot_due = 0;
                  $tot_paid = 0;

                  if($pay_status){
                     if($pay_status=='00')
                       $display_paid='';
                     if($pay_status=='01')
                       $display_due='';
                  }

              if(mysqli_num_rows($sql)){

                    while($r = mysqli_fetch_array($sql)){

                      $status = '';
                      $return_row = '01';
                      $tot_loan += $r['loan_amount'];    

                      //Return Branch Name Initials
                        $split = explode(' ',$r['branch_name']);
                        $branch_init = '';
                         foreach($split as $key){
                          $branch_init .= substr($key,0,1);
                         }                  
                      
                if($r['status']=='00'){  
                  $q_str = "SELECT p.pay_date FROM loan_entries l , loan_payments p WHERE l.id = p.loan AND l.id='".$r['loan']."' ORDER BY p.pay_date DESC LIMIT 0,1";
                   $q = mysqli_query($connect,$q_str);
                     $rw = mysqli_fetch_array($q);
                      if(date('Y-m-d',strtotime($check_date)) > $rw[0])
                        $return_row = '00';
                      $date_null_val = NULL;
                   }

        if($r['pay_date']!=NULL && $display_paid && strtotime($r['date_entry']) <= strtotime($check_date)){
              $status = 'Paid'; 
              $query = mysqli_query($connect,"SELECT * FROM loan_payments WHERE pay_date='$check_date' AND loan = '".$r['loan']."'");
                if(mysqli_num_rows($query) && $return_row=='01'){
                    $row = mysqli_fetch_array($query);
                    $count += 1;
                    $general_pay += $row['amount_paid'];
                    $tot_pay = 0;
                    $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }
                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;
                    if($display_paid){
                      $tot_paid+=1;
                         if($loan_bal<0)
                             $loan_bal=0;
                    ?>
                       <tr>
                        <td><?php echo $status ?></td>
                      </tr>
                    <?php
                    }
                  }else{
                    $status = 'Over-dues';
                    $tot_pay = 0;                    
                         $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                        }
                        $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                        $tot_bal += $loan_bal;                    
                    if($return_row=='01' && $display_due){
                         $count += 1;
                         $tot_due += 1;
                    ?>
                       <tr>
                        <td><?php echo $status ?></td>
                      </tr>
                    <?php
                      }
                    }
                  }else if($r['pay_date']==$date_null_val && $return_row=='01' && strtotime($r['date_entry']) <= strtotime($check_date)){
                    $status = 'Over-duez';
                    $count += 1;
                    $tot_pay = 0;
                    $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }
                      $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                      $tot_bal += $loan_bal;

                    if($display_due){
                      $tot_due += 1;
                    ?>
                       <tr>
                        <td><?php echo $status ?></td>
                      </tr>
                    <?php
                     }
                   }
              } 
          }else {
                  ?>
                <tr>
                   <td><div style="height:90px;width:100%">No Record(s) Found</div></td>
                </tr>
          <?php
          }
       ?>
    </table>
    SELECT c.first_name, c.last_name, l.id, l.date_entry, l.loan_amount, l.status, COUNT(*) as 'Freq' FROM loan_entries l, clients c 
WHERE l.date_entry BETWEEN '2024-06-27' AND '2024-07-05' AND c.id = l.client
GROUP BY l.client  
ORDER BY `l`.`date_entry` DESC

<?php
              
                  $count = 0;
                  $general_pay = 0;
                  $tot_loan = 0;
                  $display_paid = '1';
                  $display_due = '1';
                  $tot_due = 0;
                  $tot_paid = 0;

                  if($pay_status){
                     if($pay_status=='00')
                       $display_paid='';
                     if($pay_status=='01')
                       $display_due='';
                     
                  }

              if(mysqli_num_rows($sql)){

                    while($r = mysqli_fetch_array($sql)){

                      $status = '';
                      $return_row = '01';
                      $tot_loan += $r['loan_amount'];    

                      //Return Branch Name Initials
                        $split = explode(' ',$r['branch_name']);
                        $branch_init = '';
                         foreach($split as $key){
                          $branch_init .= substr($key,0,1);
                         }                  
                      
                if($r['status']=='00'){  
                  $q_str = "SELECT p.pay_date FROM loan_entries l , loan_payments p WHERE l.id = p.loan AND l.id='".$r['loan']."' ORDER BY p.pay_date DESC LIMIT 0,1";
                   $q = mysqli_query($connect,$q_str);
                     $rw = mysqli_fetch_array($q);
                      if(date('Y-m-d',strtotime($check_date)) > $rw[0])
                        $return_row = '00';
                      $date_null_val = NULL;
                   }

            if(!$search_date && $_POST['post_search'] OR $_GET['id']){

                    $status = 'Over-due';
                    $loan_bal = 0;
                    $acc_pmt = 0;

                    $period = status_period($r['date_entry'],$r['duration'],'');

                  for($x=1; $x<$period; $x++){
                     
                     $day_count = $x+1;     
                     $return_date = endCycle($r['date_entry'], $x, $r['duration']);

                      $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date='".date('Y-m-d',strtotime($return_date))."' ORDER BY pay_date ASC ");

                    $acc_pmt += loan_status($connect,$r['loan'],'pmt');

                    if(mysqli_num_rows($qry)){
                       while ($rs = mysqli_fetch_array($qry)) {
                    
                          $tot_pay += $rs['amount_paid'];
                          $loan_bal += loan_status($connect,$r['loan'],'pmt') - $rs['amount_paid'];
                          
                          $count += 1;
                          $status = 'Paid';

                          if($loan_bal<0){
                            $status = 'Advance Pay';
                            $loan_bal = 0;
                          }

                if($tot_pay == loan_status($connect,$r['loan'],'payable_loan'))
                     $x = $period;
                     $bal = $loan_bal;

                    ?>
                       <tr>
                        <td><?php echo $count ?></td>
                        <td><?php echo $r['data_id'] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $r['client_name'] ?></td>
                        <td><?php echo $branch_init ?></td>
                        <td><?php echo date('d/m/y',strtotime($r['date_entry']))?></td>
                        <td><?php echo number_format($r['loan_amount']) ?></td>
                        <td><?php echo $return_date ?></td>     
                        <td><?php echo number_format($rs['amount_paid']) ?></td>
                        <td><?php echo number_format($loan_bal); ?></td>
                        <td><?php echo $status ?></td>                         
                        <td>
                          <select name="select_action" id="<?php echo $rw[0] ?>" class="text-input" style="width:80px;">
                            <option value="">Action</option>
                            <option value="edit_<?php echo $rs[0] ?>">Edit</option>
                            <option value="receipt_<?php echo $rs[0].'_'.$r['loan'] ?>">View Receipt</option>
                            <option value="delete_<?php echo $rs[0] ?>">Delete</option>
                          </select>
                        </td>
                      </tr>
                    <?php
                    }
                  }else{

                     $count += 1;
                     $loan_bal =  ($acc_pmt - $tot_pay);
                     
                     $status = 'Over-due';

                          if($loan_bal<0){
                            $status = 'Advance Pay';
                            $loan_bal = 0;
                          }
                      $bal = $loan_bal;
                    ?>
                       <tr>
                        <td><?php echo $count ?></td>
                        <td><?php echo $r['data_id'] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $r['client_name'] ?></td>
                        <td><?php echo $branch_init ?></td>
                        <td><?php echo date('d/m/y',strtotime($r['date_entry']))?></td>
                        <td><?php echo number_format($r['loan_amount']) ?></td>
                        <td><?php echo $return_date ?></td>     
                        <td><?php echo 0 ?></td>
                        <td><?php echo number_format($loan_bal); ?></td>
                        <td><?php echo $status ?></td>                         
                        <td>
                          <select name="select_action" id="<?php echo $rw[0] ?>" class="text-input" style="width:80px;">
                            <option value="">Action</option>
                          </select>
                        </td>
                      </tr>
                    <?php
                  }
                 
                
              }
            $tot_bal += $bal;
            $general_pay = $tot_pay;

        } else if($r['pay_date']!=NULL && $display_paid && strtotime($r['date_entry']) <= strtotime($check_date)){

              $status = 'Paid'; 
              $query = mysqli_query($connect,"SELECT * FROM loan_payments WHERE pay_date='$check_date' AND loan = '".$r['loan']."'");

                if(mysqli_num_rows($query) && $return_row=='01'){

                    $row = mysqli_fetch_array($query);
                    $count += 1;
                    $general_pay += $row['amount_paid'];
                    $tot_pay = 0;

                    $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }

                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;

                    if($display_paid){
                      $tot_paid+=1;
                         if($loan_bal<0)
                             $loan_bal=0;
                    ?>
                       <tr>
                        <td><?php echo $count ?></td>
                        <td><?php echo $r['data_id'] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $r['client_name'] ?></td>
                        <td><?php echo $branch_init ?></td>
                        <td><?php echo date('d/m/y',strtotime($r['date_entry']))?></td>
                        <td><?php echo number_format($r['loan_amount']) ?></td>
                        <td><?php echo date('d/m/y',strtotime($row['pay_date'])); ?></td>
                        <td><?php echo number_format($row['amount_paid']) ?></td>
                        <td><?php echo number_format($loan_bal); ?></td>
                        <td><?php echo $status ?></td>                         
                        <td><select name="select_action" id="action_<?php echo $count ?>" class="text-input" style="width:80px;">
                            <option value="">Action</option>
                            <option value="edit_<?php echo $row[0] ?>">Edit</option>
                            <option value="receipt_<?php echo $row[0].'_'.$r['loan'] ?>">View Receipt</option>
                            <option value="delete_<?php echo $row[0] ?>">Delete</option>
                          </select></td>
                      </tr>
                    <?php
                    }
                  }else{

                    $status = 'Over-dues';
                    $tot_pay = 0;
                    
                         $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }

                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;
                    
                    if($return_row=='01' && $display_due){
                         $count += 1;
                         $tot_due += 1;
                    ?>
                       <tr>
                        <td><?php echo $count ?></td>
                        <td><?php echo $r['data_id'] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $r['client_name'] ?></td>
                        <td><?php echo $branch_init ?></td>
                        <td><?php echo date('d/m/y',strtotime($r['date_entry']))?></td>
                        <td><?php echo number_format($r['loan_amount']) ?></td>
                        <td><?php echo '-' ?></td>                           
                        <td><?php echo 0 ?></td>
                        <td><?php echo number_format($loan_bal); ?></td>
                        <td><?php echo $status ?></td>                         
                        <td><select name="select_action" id="<?php echo $rw[0] ?>" class="text-input" style="width:80px;">
                            <option value="">Action</option>
                          </select></td>
                      </tr>
                    <?php
                      }
                    }
                  }else if($r['pay_date']==$date_null_val && $return_row=='01' && strtotime($r['date_entry']) <= strtotime($check_date)){

                    $status = 'Over-due';
                    $count += 1;
                    $tot_pay = 0;
                    

                         $qry=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='".$r['loan']."' AND pay_date >= '".date('Y-m-d',strtotime($r['date_entry']))."' AND pay_date <= '".date('Y-m-d',strtotime($check_date))."'  ");
                          if(mysqli_num_rows($qry)){
                            while ($rs = mysqli_fetch_array($qry)) {
                              $tot_pay += $rs['amount_paid'];
                            }
                          }

                          $loan_bal =  loan_status($connect,$r['loan'],'payable_loan') - $tot_pay;
                          $tot_bal += $loan_bal;

                    if($display_due){
                      $tot_due += 1;
                    ?>
                       <tr>
                        <td><?php echo $count ?></td>
                        <td><?php echo $r['data_id'] ?></td>
                        <td style="text-transform: capitalize;"><?php echo $r['client_name'] ?></td>
                        <td><?php echo $branch_init ?></td>
                        <td><?php echo date('d/m/y',strtotime($r['date_entry']))?></td>
                        <td><?php echo number_format($r['loan_amount']) ?></td>
                        <td><?php echo '-' ?></td>                           
                        <td><?php echo 0 ?></td>
                        <td><?php echo number_format($loan_bal); ?></td>
                        <td><?php echo $status ?></td>                         
                        <td>
                          <select name="select_action" id="<?php echo $rw[0] ?>" class="text-input" style="width:80px;">
                            <option value="">Action</option>
                          </select>
                        </td>
                      </tr>
                    <?php
                     }
                   }
              }


<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML><HEAD>
<TITLE>509 Bandwidth Limit Exceeded</TITLE>
</HEAD><BODY>
<H1>Bandwidth Limit Exceeded</H1>
        
The server is temporarily unable to service your
request due to the site owner reaching his/her
bandwidth limit. Please try again later.
</BODY></HTML>
