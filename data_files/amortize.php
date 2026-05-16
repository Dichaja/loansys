<?php
require_once("../xsert/connect.php");
require_once("sys_function.php");

error_reporting(E_ALL ^ E_NOTICE);
if(isset($_POST['loan2'])){

 $id = $_POST['loan2'];

 $sql = mysqli_query($connect,"SELECT l.id, l.loan_amount, l.interest, l.period, l.date_entry, s.security, s.value, l.duration FROM  loan_security s, loan_entries l WHERE s.loan=l.id AND l.id='$id' ");

 if(mysqli_num_rows($sql)){
 $rw=mysqli_fetch_array($sql);
 $loan = $rw[1];
 $int_val = $rw[2];
 $period = $rw[3];
 $start_date = $rw[4];
 $duration = $rw[7];

  ?>
<div style="width:95%;margin: auto;">
 <div class="form_header">Armortization Details</div>
 <div class="grid-2">
  <span></span>
  <span id="print_rpt" style="display:inline-block;width:100%;text-align: right;"><img src="../img_file/print-icon.svg" width="20" height="20"></span>
 </div>
 <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr;">
     <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr 2fr 1fr;">
       <span><b>Interest Rate</b></span>
       <span><?php echo $int_val."%" ?></span>
       <span><b>Loan Period</b></span>
       <span><?php echo $period." ".$rw[7]."&nbsp;(s)" ?></span>
     </div>
     <div></div>
 </div>
 <table cellpadding="5" cellspacing="0" width="100%" align="center" class="report_display">
       <tr>
         <td>No</td>
         <td>Period</td>
         <td>Principal</td>
         <td>Acc Principal</td>
         <td>Interest</td>
         <td>Acc Interest</td>         
         <td>PMT</td>
         <td style="text-align: right;">Amount</td>
       </tr>
       <?php  
        $acc_principal=0;
        $acc_interest=0;
        $count = 0;
        $payment = 0;
        $index = 0;
        $acc_pmt=0;
      ?>
          
          <tr>
            <td></td>
            <td colspan="6"><em><?php echo date("d-m-Y",strtotime($rw[4])) ?></em></td>
            <td align="right"><em><?php echo number_format($loan) ?></em></td>
          </tr>
          <?php
          for($i=0; $i < $period; $i++){
            //pinicpal = loan / period
            $principal = $loan/$period;
            $int_rate=($int_val/100);
            $monthly_rate = round($int_rate/$period,2);
            $interest = $monthly_rate*$principal;
            ?>
            <tr>
              <td><?php echo $count += 1 ?>)</td>
              <td><?php
            if($count==1){
              $period_date = $start_date;
            }
            echo $final = endCycle($start_date, '1',$duration);
          ?></td>
              <td><?php echo number_format($principal) ?></td>
              <td><?php 
                     $acc_principal += $principal;
                     echo number_format($acc_principal);
                  ?></td>
              <td><?php echo number_format(return_int($int_val,$duration,$principal,$loan)) ?></td>
              <td><?php 
                   $acc_interest += return_int($int_val,$duration,$principal,$loan); 
                   echo number_format($acc_interest)
                   ?></td>
              <td ><?php echo number_format($principal+return_int($int_val,$duration,$principal,$loan)) ?></td>
              <td align="right"><?php echo number_format($loan - $acc_principal) ?></td>              
             </tr>
            <?php
            $start_date=$final;
            $acc_pmt += ($principal+return_int($int_val,$duration,$principal,$loan));
          }
       ?>
             <tr style="font-weight:bold;">
              <td colspan="3">Total</td>
              <td><?php echo number_format($acc_principal) ?></td>
              <td></td>
              <td><?php echo number_format($acc_interest) ?></td>
              <td ><?php echo number_format($acc_pmt) ?></td>
              <td align="right"></td>              
             </tr>
      </table>
    <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr;border-top: 1px solid rgba(0,0,0,.1);margin:10px 0;">
     <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr 2fr 1fr;">
       <span><b>Loan Security</b></span>
       <span><?php echo $rw[5] ?></span>
       <span><b>Value</b></span>
       <span><?php echo number_format($rw[6]) ?></span>
     </div>
     <div></div>
 </div>
  </div> 
  <?php
  }else{
  $sql = mysqli_query($connect,"SELECT l.id, l.loan_amount, l.interest, l.period, l.date_entry, l.duration FROM  loan_entries l WHERE l.id='$id' ");
 $rw=mysqli_fetch_array($sql);
 $loan = $rw[1];
 $int_val = $rw[2];
 $period = $rw[3];
 $start_date = $rw[4];
 $duration = $rw[5];
  ?>
 <div style="width:95%;margin: auto;">
 <div class="form_header">Armortization Details</div>
 <div class="grid-2">
  <span></span>
  <span id="print_rpt" style="display:inline-block;width:100%;text-align: right;"><img src="../img_file/print-icon.svg" width="20" height="20"></span>
 </div>
 <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr;">
     <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr 2fr 1fr;">
       <span><b>Interest Rate</b></span>
       <span><?php echo $int_val."%" ?></span>
       <span><b>Loan Period</b></span>
       <span><?php echo $period." ".$rw[7]."&nbsp;(s)" ?></span>
     </div>
     <div></div>
 </div>
 <table cellpadding="5" cellspacing="0" width="100%" align="center" class="report_display">
       <tr>
         <td>No</td>
         <td>Period</td>
         <td>Principal</td>
         <td>Acc Principal</td>
         <td>Interest</td>
         <td>Acc Interest</td>         
         <td>PMT</td>
         <td style="text-align:right;">Amount</td>
       </tr>
       <?php  
        $acc_principal=0;
        $acc_interest=0;
        $count = 0;
        $payment = 0;
        $index = 0;
        $acc_pmt=0;
      ?>
          
          <tr>
            <td></td>
            <td colspan="6"><em><?php echo date("d-m-Y",strtotime($rw[4])) ?></em></td>
            <td align="right"><em><?php echo number_format($loan) ?></em></td>
          </tr>
          <?php
          for($i=0; $i < $period; $i++){
            //pinicpal = loan / period
            $principal = $loan/$period;
            $int_rate=($int_val/100);
            $monthly_rate = round($int_rate/$period,2);
            $interest = $monthly_rate*$principal;
            ?>
            <tr>
              <td><?php echo $count += 1 ?>)</td>
              <td><?php
            if($count==1){
              $period_date = $start_date;
            }
            echo $final = endCycle($start_date, '1',$duration);
          ?></td>
              <td><?php echo number_format($principal) ?></td>
              <td><?php 
                     $acc_principal += $principal;
                     echo number_format($acc_principal);
                  ?></td>
              <td><?php echo number_format(return_int($int_val,$duration,$principal,$loan)) ?></td>
              <td><?php 
                   $acc_interest += return_int($int_val,$duration,$principal,$loan); 
                   echo number_format($acc_interest)
                   ?></td>
              <td ><?php echo number_format($principal+return_int($int_val,$duration,$principal,$loan)) ?></td>
              <td align="right"><?php echo number_format($loan - $acc_principal) ?></td>              
             </tr>
            <?php
            $start_date=$final;
            $acc_pmt += ($principal+return_int($int_val,$duration,$principal,$loan));
          }
       ?>
             <tr style="font-weight:bold;">
              <td colspan="3">Total</td>
              <td><?php echo number_format($acc_principal) ?></td>
              <td></td>
              <td><?php echo number_format($acc_interest) ?></td>
              <td ><?php echo number_format($acc_pmt) ?></td>
              <td align="right"></td>              
             </tr>
    </table>
    <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr;border-top: 1px solid rgba(0,0,0,.1);margin:10px 0;">
     <div style="width:100%;display: grid;grid-template-columns: 2fr 1fr 2fr 1fr;">
       <span><b>Loan Security</b></span>
       <span><?php echo $rw[5] ?></span>
       <span><b>Value</b></span>
       <span><?php echo number_format($rw[6]) ?></span>
     </div>
     <div></div>
 </div> 
  </div>
  <?php 
  }
}

if(isset($_POST['delete_loan'])){
	$id = $_POST['delete_loan'];
	$del = mysqli_query($connect,"DELETE l, s FROM loan_entries l, loan_security s WHERE l.id=s.loan AND l.id='$id' ");
	if(mysqli_affected_rows($connect)){
		echo 1;
	}else{
		echo mysqli_error($connect);
	}
}
?>