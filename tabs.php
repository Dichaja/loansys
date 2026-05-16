<?php
require("xsert/connect.php");
require("data_files/sys_function.php");
?>
<html>
  <head><title>Scipt Testing</title>
    <script type="text/javascript" src="data_scripts/jquery-2.1.1.min.js"></script>

    <script type="text/javascript">
      //to check all checkboxes
$(document).on('click','#all',function(){
	$('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
});

$(function(){
 for(var x=1; x<=$('.bal').length; x++){
   var next_cell = x+1;
   var count = $('.bal').length;
   var val = $("#bal_"+x).val();
   $("#cell_"+next_cell).html(val);
 }
})
    </script>
  </head>
  <body>
  <?php
  	if(isset($_GET['get_loan'])){
 $loan_id = $_GET['get_loan'];
 $period_status = $_POST['period'];
 $sql = mysqli_query($connect,"SELECT c.client_names, l.loan_amount, l.date_entry, l.duration, l.period, c.id, l.interest, l.status FROM clients c, loan_entries l WHERE c.id=l.client AND l.id='$loan_id'");
 $rw = mysqli_fetch_array($sql);

 //return loan$loan=0;
 $loan= $rw[1];
 $int = $rw[6];
 if($rw[7]=='00'){
            $period_status=$rw[7];
          }else{
          $period_status = ($rw[4]-return_period($rw[2],$rw[3]));//returns period remain for loan
         }
          
?>
<span class="header">Loan Payment Form</span>
  <form method="post" name="form" id="loans" action="../data_rp/loan_activity.php">
    <input type="hidden" name="loan_client" value="<?php echo $rw[5] ?>" />
    <input type="hidden" name="loan" id="loan_id" value="<?php echo $loan_id ?>" />
      <div style="display:block;font-size:14px;">
        <div style="width:200px;float:left;">Client</div> <b><?php echo $rw[0] ?></b>
      </div>
      <div style="display:block;font-size:14px;">
        <div style="width:200px;float:left;">Loan</div><b><?php echo number_format($loan) ?></b>
      </div>
      <div style="display:block;font-size:14px;">
        <div style="width:200px;float:left;">Issue Date</div> <b><?php echo date("d-m-Y",strtotime($rw[2])) ?></b>
      </div>
      <div style="display:block;font-size:14px;">
        <div style="width:200px;float:left;">Loan Period</div> <b><?php echo $rw[4]." ".$rw[3]." (s)" ?></b>
      </div>
    <div style="display:block;" id="details">
        <?php
           $client=0;           
        ?>
        <table width="90%" align="center" cellpadding="5" cellspacing="0" style="border-collapse:collapse;">
          <tr>
            <td colspan="4"><b>Payment Details...</b></td>
          </tr>
          <tr style="color: #145FA7 !important;font-weight:bold;">
             <td class="line" style="color: #145FA7;">No</td>
             <td class="line" style="color: #145FA7;">Period</td>
             <td class="line" style="color: #145FA7;">Staff In-charge</td>
             <td class="line" style="color: #145FA7;">Pay Date</td>
             <td class="line" style="color: #145FA7;" align="right">Amount Paid</td>
          </tr>
          <?php
            $total=0;
           for($i=0; $i<($rw[4]-$period_status); $i++){
            
            if($i==0){
              $start_date=$rw[2];
            }
            $final = endCycle($start_date, '1', $rw[3]);
            $count+=1;
            if($rw[3]=='month'){
            $sql=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='$loan_id' AND monthname(pay_date)='".date("F",strtotime($final))."'");
            if(mysqli_num_rows($sql)){
              while($rws = mysqli_fetch_array($sql)){
                if($total==0){
                   $interest2 = $loan*($int/100);
                   $bal =  $loan-($rws[1]-$interest2-$acc_int);
                                     
                }else{                  
                  $interest = $bal*($int/100);
                  $int_bal = $rws[1]-($acc_int+$interest);  
                  if($int_bal<0){
                    $bal = (-$int_bal+$bal);
                  }else if($int_bal>0){
                    $bal = $bal-$int_bal;
                  }             
                  
                }
                
                $total += $rws[1];
                $acc_int += $interest;
              ?>
             <tr>
                <td class="line" style="color: #145FA7;"><?php echo $count ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $final ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $interest." ".$acc_int." ".$bal ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $total; //date("d-m-Y",strtotime($rws[2])) ?></td>
                <td class="line" style="color: #145FA7;" align="right"><?php echo number_format($rws[1]) ?></td>
              </tr>
              <?php
                $total += $rws[1];
                $pay_int=$int;
              }
            }else{
              if($bal>0){
               $interest = $bal*($int/100);
             }else{
               $interest = $loan*($int/100);
             }
               $acc_int += $interest;
              ?>
              <tr>
                <td class="line" style="color: #145FA7;"><?php echo $count ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $final ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $interest." ".$acc_int." ".$bal ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $total; //date("d-m-Y",strtotime($rws[2])) ?></td>
                <td class="line" style="color: #145FA7;" align="right"><?php echo number_format($rws[1]) ?></td>
              </tr>
            <?php
            }
          }else if($rw[3]=='day'){
      $sql=mysqli_query($connect,"SELECT * FROM loan_payments WHERE loan='$loan_id' AND pay_date='".date("Y-m-d",strtotime($final))."'");
            if(mysqli_num_rows($sql)){
              while($rws = mysqli_fetch_array($sql)){
                if($total==0){
                   $interest2 = $loan*($int/100);
                   $bal =  $loan-($rws[1]-$interest2-$acc_int);
                                     
                }else{                  
                  $interest = $bal*($int/100);
                  $int_bal = $rws[1]-($acc_int+$interest);  
                  if($int_bal<0){
                    $bal = (-$int_bal+$bal);
                  }else if($int_bal>0){
                    $bal = $bal-$int_bal;
                  }             
                  
                }
                
                $total += $rws[1];
                $acc_int += $interest;
              ?>
             <tr>
                <td class="line" style="color: #145FA7;"><?php echo $count ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $final ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $interest." ".$acc_int." ".$bal ?></td>
                <td class="line" style="color: #145FA7;"><?php echo $total; //date("d-m-Y",strtotime($rws[2])) ?></td>
                <td class="line" style="color: #145FA7;" align="right"><?php echo number_format($rws[1]) ?></td>
              </tr>
              <?php               
              }
            }else{
               if($bal>0){
               $interest = $bal*($int/100);
             }else{
               $interest = $loan*($int/100);
             }
               $acc_int += $interest;
              ?>
              <tr>
                 <td class="line" style="color: #145FA7;"><?php echo $count ?></td>
                 <td class="line" style="color: #145FA7;"><?php echo $final ?></td>
                 <td class="line" style="color: #145FA7;"><?php echo $interest." ".$acc_int." ".$bal ?></td>
                 <td class="line" style="color: #145FA7;"><?php echo $total ?></td>
                 <td class="line" style="color: #145FA7;" align="right">-</td>
               </tr>
            <?php
              
            }
          }
             $start_date=$final;
          }

      if($period_status<0){
            ?>
              <tr>
               <td colspan="5"><b>Loan Overdue</b></td>
             </tr>
             <tr>
               <td colspan="2">Action Taken</td>
               <td colspan="3" align="right"><input type="text" name="action" value="" class="text-input"/></td>
             </tr>
             <tr id="row">
               <td colspan="4" class="line"></td>
               <td class="line" align="right"><input type="radio" name="action" value="apprehend" id="apprehend" />&nbsp;Apprehend Security<br>
                   <input type="radio" name="action" value="extend" id="extend" />&nbsp;Extend Loan Period</td>
             </tr>
            <?php
          }
          ?>
          <tr>
            <td colspan="2">Amount Covered</td>
            <td colspan="3" align="right"><?php echo number_format($total); ?></td>
          </tr>
           <tr>
            <td colspan="2" >Current Balance</td>
            <td colspan="3" align="right"><?php
                 $remaining_bal = ($rw[1]+$bal_int)-$total;
                // echo number_format($remaining_bal);//number_format($loan-$total);
                 echo $acc_int+$bal;
  
            ?><input type="hidden" value="<?php echo $remaining_bal ?>" name="balance" /></td>
          </tr>
          <tr id="amount_entry">
            <td colspan="2">Pay Amount</td>
            <td colspan="3" align="right"><input type="text" name="pay_loan" class="text-input" id="pay" /></td>
          </tr>
          <tr>
            <td colspan="2">Pay Date</td>
            <td colspan="3" align="right"><input type="text" name="date" id="datetimepicker" class="text-input" autocomplete="off" /></td>
          </tr>
          <tr>
            <td colspan="5" align="right"><button type="submit" name="btnSubmit" class="button-input" id="submit_loan">Submit</button></td>
          </tr>
        </table>
      </div>      
   </div>
   <input type="hidden" name="interest"  value="<?php echo $rw[6] ?>" />
   <input type="hidden" name="duration"  value="<?php echo $rw[3] ?>" />
 </form>
<?php
}
?>
  </body>
</html>