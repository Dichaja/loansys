<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');  
?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" />    
   <title><?php echo sys_tab_hdr() ?></title>

<?php include('../data_files/link_docs.php') ?>

</head>
<body>

<?php

$limit = 40;
$year = date('Y');            
//how many items to show per page

   
$qry = "SELECT c.id, CONCAT(c.first_name,' ',c.last_name) as 'client_names', c.contacts, c.email, l.id as 'loan_id', l.loan_amount, l.interest, l.duration, l.period, l.date_entry, l.status, b.branch_name, c.data_id, l.loan_fees FROM clients c, loan_entries l, branches b WHERE c.id = l.client AND c.branch_id = b.id AND date_format(l.date_entry,'%Y') = '".$year."' AND  l.status!='03' ORDER BY l.date_entry DESC ";
              
   $result = mysqli_query($connect,$qry);
     $total_pages = mysqli_num_rows($result);
       

    $last_page = ceil($total_pages / $limit); //divide total row count by limit size
      if($page==0)
        $page=1;
      //previous page is page - 1 
       if($page==$lastpage2){
           $next=$lastpage2;
         } else {
           $next = $page + 1; //next page is page + 1 
       }

       ?>
       <input type="hidden" id="srch_val" value="<?php echo $client.','.$client_id.','.$date.','.$date2.','.$month.','.$year.','.$pay_status ?>" />

   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php  
     echo $qry2;     
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <?php if($_GET){ include('../data_files/action_msg.php'); } ?>
            
   <div class="grid-2" style="display: grid;">
        <div></div>
        
    </div>
  
   <div class="report_wrap">
   <div id="header_wrap">
          <div id="header_tpl"><?php echo po_address($connect) ?></div>
        </div>               
    <div class="report_header" style="align-items: center;">
                <span>Loans Performance Overview <?php echo $search ?></span>
                <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="loan_activity.php" id="form1">
            <input type="hidden" name="post_search" value="1" />
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff">
                  <input type="text" class="search_text" name="name_search" placeholder="Search Client" id="name_search" autocomplete="off" data-src="clients" />
                      <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px" />
                       <input type="hidden" name="client_id" value="" id="client_id" />
                      <div id="drop-box" class="drop_down drop_large_size" style="width:336.7px;"></div>
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
               </div>
          </div>
<div class="report_wrap">
  <div style="font-size:12px;font-weight: normal;display: grid; grid-template-columns: 1fr 1fr;">
    <span>Loans Disbursed : <?php echo $total_pages ?></span>
    <span style="display: grid; grid-template-columns: 1fr 1fr;">
              <div style="width:100%;text-align:right;font-size:14px;">
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" style="display: inline-block;margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;">Generate PDF</span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
              </div>
              <div style="width:100%;text-align:right;font-size:14px;">
                     <span style="padding:5px;">Page <?php echo $page ?> <b>of</b> <?php echo $last_page ?></span>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$next&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$prev&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=1&limit=$total_pages&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">View All</a>"; ?></span>
                  </div>
        </span>
    </div>
</div>
 
  <table align="center" cellpadding="5" cellspacing="0" width="100%" class="report_display">                
     <tr>
      <td>No</td>
      <td>Issue Date</td>
      <td>Id</td>
      <td>Client</td>
      <td>Branch</td> 
      <td>Loan Officer</td>     
      <td>Status (Days)</td>
      <td align="center">Loan</td>
      <td align="">Loan Fees</td>
      <td align="center">Payments</td>
      <td align="right">Balance</td>
      <td></td>
     </tr>
     <?php

       if(mysqli_num_rows($result)){

        $count=0;
        $total_loan=0;
        $total_pay=0;
        $total_bal=0;

        while($rw=mysqli_fetch_array($result)){

          $loan=(round($rw[6]/100,2)*$rw[5])+$rw[5];
          $acc_int=0;
          $bal=0;
          $overdue = "";
          $count += 1;
          $total_pay += $rw['amount_paid'];
          $total_fees += $rw['loan_fees'];

          //return church name initials
           $split = explode(' ',$rw['branch_name']);
           $branch_init = '';
            foreach($split as $key){
              $branch_init .= substr($key,0,1);
            } 
         
         
          if($rw['status']=='00' OR $rw['status']=='03'){
            $status_period=0;
          }else if(loan_status($connect,$rw['loan_id'],$status)<=0){
            mysqli_query($connect,"UPDATE loan_entries SET status='00' WHERE id = '".$rw['loan_id']."' ");
            $status_period=0;
          }else{
            $status_period = ($rw[8]-return_period($rw[9],$rw[7]));//returns period remain for loan
           }
          
        ?>
        <tr <?php if($rw['status']=='03'){ echo 'style="color:#F00;" '; } ?>>
         <td><?php echo $start+=1 ?></td>
         <td><?php echo date("d-m-Y",strtotime($rw[9])) ?></td>
         <td><?php echo $rw['data_id'] ?></td>
         <td style="text-transform:capitalize;"><?php echo strtolower($rw[1]) ?></td>  
         <td><?php echo $branch_init ?></td>
         <td><?php echo $rw['loan_officer'] ?></td>       
         <td><?php 
              if($status_period < 0 ){ 
                echo '<span style="color:#F00">'.$status_period.'</span>';
                  if($status_period >= -7)
                    mysqli_query($connect,"UPDATE loan_entries SET status='02' WHERE id = '".$rw['loan_id']."' ");
                  else
                    extend_loan($connect,$rw['loan_id']);
                    //mysqli_query($connect,"UPDATE loan_entries SET status='02' WHERE id = '".$rw['loan_id']."' ");
               }else{
                  echo '<span>'.$status_period.'</span>';
            } ?></td>
         <td align="right"><?php echo number_format($rw[5]) ?></td>
         <td align="right"><?php echo number_format($rw['loan_fees']) ?></td>
         <td align="right"><?php echo number_format(loan_status($connect,$rw['loan_id'],'payments')); $total_pay += loan_status($connect,$rw['loan_id'],'payments')  ?></td>
         <td align="right">
             <?php
                                  $total_loan_balance += loan_status($connect,$rw['loan_id'],$status);
                                  echo number_format(loan_status($connect,$rw['loan_id'],$status));
            ?></td>
      <td>
        <input type="hidden" name="clientId" id="postClient_<?php echo $count ?>" value="<?php echo $rw[0] ?>" />
        <select name="select_action" class="text-input" id="action_<?php echo $count ?>" style="width:80px;">
             <option value="" selected="selected">Action</option>
             <option value="<?php echo $rw[4] ?>_loan" >Pay Loan</option>
             <option value="<?php echo $rw[4] ?>_amortize">Preview Amortization</option>
             <option value="<?php echo $rw[4] ?>_statement">View Statement</option>
             <?php if(!$rw['amount_paid']){
               //edit only loan has no payment
              ?>
               <option value="<?php echo $rw[4] ?>_edit" id="edit2_<?php echo $count?>">Edit Loan</option>
               <option value="<?php echo $rw[4] ?>_delete" id="delete_<?php echo $count?>">Delete</option>
              <?php } ?>
          </select>
         </td>
       </tr>
        <?php
        if($rw['status']!='03')
            $total_loan += $rw[5];          
          $total_bal += $bal; 
        }
        ?>
         <tr style="font-weight:bold;">
           <td colspan="7">Total</td>
           <td align="right"><?php echo number_format($total_loan) ?></td>
           <td align="right"><?php echo number_format($total_fees) ?></td>
           <td align="right"><?php echo number_format($total_pay) ?></td>
           <td align="right"><?php echo number_format($total_loan_balance) ?></td>
           <td></td>
         </tr>
        <?php
       }else{
          ?>
          <tr>
            <td style="height:100px;width:70%" colspan="12">
             No Result(s) Found.
            </td>
          </tr>
          <?php
        }
      ?>
       <tr>
          <td colspan="12">
           <div style="width:100%;text-align:right;font-size:14px;">
                     <span style="padding:5px;">Page <?php echo $page ?> <b>of</b> <?php echo $last_page ?></span>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$next&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Next</a>"; ?></span>
                     <?php 
                        if($page!=1) {
                            $prev = $page - 1; ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=$prev&limit=$limit&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">Back</a>"; ?></span>
                     <?php } ?>
                     <span style="background:#ddd;padding:5px;border-radius:5px;margin-left:10px;font-size:12px;"><?php echo "<a href=\"$target_page?page=1&limit=$total_pages&month=$month&year=$year&pay_status=$pay_status&date=$date&date2=$date2&branch=$branch\">View All</a>"; ?></span>
                  </div>
           </td>
       </tr>
    </table>
  </div>
</div>
    
  </div>
  </body>
</html>