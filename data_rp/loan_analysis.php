<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess(); //check user loggin

function return_monthly_loans($c,$month,$year){
      $qry = "SELECT * FROM loan_entries WHERE ";
        if($month!=''){
          $qry.= " monthname(date_entry) = '$month' AND ";
         }
          if($year!=''){
            $qry.= " date_format(date_entry,'%Y') = '$year' AND ";
             }
           $qry.=" 1 ";
      $sql = mysqli_query($c,$qry);
      while($rw=mysqli_fetch_array($sql)){
         $loan .= $rw[0].",";
      }
  return $loan;
}

if($_POST['search']){
  $year = $_POST['year'];
  $month = $_POST['month'];
  $date = $_POST['date'];
  $date2 = $_POST['date2'];
}else{
  $year = date("Y");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    <?php include('../data_files/link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">
function seperator(index){
   var myVal="";
   var myDec="";
   var index_val="";
   var amtVal = parseFloat(index).toFixed(2);
   var amt_split = amtVal.toString().split('.');

      // Filtering out the trash!
        amt_split[0] = amt_split[0].replace(/[^0-9]/g,""); 

      // Setting up the decimal part
        if ( ! amt_split[1] && amtVal.indexOf(".") > 1 ) {myDec = "."}
        if ( amt_split[1] ) { myDec = "."+ parseFloat(amt_split[1]) }

  // Adding the thousand separator
        while(amt_split[0].length > 3 ) {
            myVal = ","+amt_split[0].substr(amt_split[0].length-3, amt_split[0].length )+ myVal;
            amt_split[0]= amt_split[0].substr(0, amt_split[0].length-3);           
          }
        index_val = (amt_split[0]+myVal+myDec); 
    return index_val;
}
</script>
</head>

<body>
   
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
              
  <div class="grid-2" style="display: grid;">
        <div><span class="header"><?php
                         $search_text='';
                       if($date && $date2){
                             $search_text .= ', '.date('d/m/Y',strtotime($date)).' To '.date('d/m/Y',strtotime($date2));
                            }else if($date!='' && $date2==''){
                               $search_text .= ', '.date('d/m/Y',strtotime($date));
                           }
                       if($month){
                          $search_text .= ' '.$month;
                        }
                        if($year){
                            $search_text .= ' '.$year;
                         }
                           
                         ?></span>
      <span id="print_rpt"><img src="../img_file/print-icon.svg" width="20" height="20"></span></div>
        <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="" id="form1">
            <input type="hidden" name="post_search" value="1">
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff">
                  <input type="text" class="search_text" name="year" placeholder="Year" id="year" />
                      <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px">
                       <input type="hidden" name="client_id" value="" id="client_id">
                       <input type="hidden" name="year_search" id="year_search" value="2023">
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
    </div>
  <div class="report_header"><span>General Loan Performance <?php echo $search_text; ?> </span></div>
    <table align="center" cellpadding="4" cellspacing="0" width="100%" class="report_display">
     <?php
     $month_array = array('January','February','March','April','May','June','July','August','September','October','November','December');
     ?>
      <tr style="font-weight:bold;">
        <td>&nbsp;</td>
        <td>Period</td>
        <td>Disburments</td>
        <td>Amount Disbursed</td>
        <td>Expected Interest</td>
        <td>Loan Fees</td>
        <td colspan="2" >Actual Collection</td>
      <tr>
      <tr>
        <td colspan="6"></td>
        <td>Interest + Principal</td>
        <td></td>
      <tr>
      <tr>
        <td></td>
        <td>
          <?php

          $y = '';
          //return balance carried 
          $sql = mysqli_query($connect,"SELECT date_format(pay_date,'%Y') as 'Year' FROM loan_payments GROUP BY Year ASC ");
                  if(mysqli_num_rows($sql)){
                      while($r = mysqli_fetch_array($sql)){
                             $array[]=$r[0];
                          }
                    }
              $array = array_unique($array);
        
        //$arrayYears = ('2023','2024');

         for($x=0; $x<count($array);$x++){
            if($year==$array[$x]){
              $y = $x-1;
              if($y<0)
                 $y='01';
            }
         }
       
        if($y){
          $bf = 0;
        }else{
           $sql = mysqli_query($connect,"SELECT SUM(amount_paid) as 'total' FROM loan_payments WHERE date_format(pay_date,'%Y') BETWEEN '".$array[0]."' AND '".$array[$y]."'");
           $r = mysqli_fetch_array($sql);
           $bf = $r['total'];
        } ?>
         Annual Collection B/F</td>
        <td><span id="bal_frwd"><?php echo number_format($bf) ?></span></td>
        <td></td>
        <td></td>
        <td colspan="2"></td>
      <tr>
      <?php
        $count=0;

        foreach($month_array as $month_index){
          $count += 1;
          $month_tot = 0;
          $loan_fees=0;
          $sql = "SELECT loan_amount, id, interest, period, date_entry, loan_fees, duration  FROM loan_entries WHERE ";            
                if($month != ''){
                  $sql .= " monthname(date_entry)='$month' AND ";
                 }else{
                  $sql .= " monthname(date_entry)='$month_index' AND ";
                 }
                  if($date !='' && $date2 !=''){
                      $sql .= " date_entry BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date2))."' AND ";
                      }
                   if($date != '' && $date2==''){
                     $sql .= " date_entry = '".date('Y-m-d',strtotime($date))."' AND ";
                     }
                  if($year != ''){
                    $sql .= " date_format(date_entry,'%Y') = '$year' AND ";
                   }
                    $sql .= " 1 ";
              $qry = mysqli_query($connect,$sql); 
              $qry2 = mysqli_query($connect,$sql); 
          while($rw = mysqli_fetch_array($qry)){
            $month_tot += $rw[0];
            $loan_fees += $rw['loan_fees'];
          }
         $total_disbursed += $month_tot;
         $total_fees += $loan_fees;
         ?>
          <tr>
            <td style="font-weight:bold"><?php echo $count ?></td>
            <td style="font-weight:bold"><?php echo $month_index ?></td>
            <td><?php echo mysqli_num_rows($qry); ?></td>
            <td><?php  echo number_format($month_tot); ?></td>
            <td>
            <?php
                $total_expected_int=0;///returns total expect_interest
                while($rw=mysqli_fetch_array($qry2)){
                  //$acc_interest='';
                  $x=0;
                  $acc_principal=0;
                 for($i=0; $i<$rw[3]; $i++){
                    //pinicpal = loan / period
                        $int_val = $rw[2];
                        $loan = $rw[0];
                        $principal = $rw[0]/$rw[3];
                        $int_rate=($rw[2]/100);
                        $monthly_rate = round($int_rate/$rw[3],2);
                        $interest = $monthly_rate*$principal;
                        $acc_principal += $principal; 
                        $x +=  return_int($rw[2],$rw['duration'],$principal,$loan);               
                  }
                  $total_expected_int += $x;
                }
                           

              echo number_format($total_expected_int);
              $total_int += $total_expected_int;
            ?>
            </td>
            <td><?php echo number_format($loan_fees) ?></td>            
            <td colspan="2">
             <?php
                 if($_POST){
                 if($month==$month_index){
                   $month_qry = $month;                
                }
                if($month==''){
                  $month_qry=$month_index;
                }
               }else{
                  $month_qry=$month_index;
               }
                 $select = "SELECT amount_paid, loan FROM loan_payments WHERE ";
                    if($month_qry != ''){
                      $select .= " monthname(pay_date) = '$month_qry' AND ";
                    }
                    if($date !='' && $date2 !=''){
                      $select .= " pay_date BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date2))."' AND ";
                      }
                   if($date != '' && $date2==''){
                     $select .= " pay_date = '".date('Y-m-d',strtotime($date))."' AND ";
                     }
                    if($year != ''){
                      $select .= " date_format(pay_date,'%Y') = '$year' AND ";
                    }
                     $select .= " 1 ";
                     $sqls = mysqli_query($connect,$select);

                     $pay=0;
                     $amt=0;
                       while($rw=mysqli_fetch_array($sqls)){
                          $pay += $rw[0];
                        }
                   
                  echo number_format($pay);
                  $total_pay += $pay;              
            ?>
            </td>
          </tr>
         <?php
        }
      ?>
      <tr style="font-weight:bold;">
        <td colspan="3">Total</td>
        <td><?php echo number_format($total_disbursed)?></td>
        <td><?php echo number_format($total_int)?></td>
        <td><?php echo number_format($total_fees)?></td>
        <td id="total"><?php echo number_format($total_pay+$bf+$total_fees) ?></td>
      <tr>
    </table>
    <div style="float:left;height:250px;width:100%">&nbsp;</div>
  </div> 
</div>

<!-- returns seach form elements -->
<div id="search-form">    
    <div class="form_element">
      <input type="text" name="date" id="datetimepicker" class="text-input" placeholder="Date 1" autocomplete="off" />
    </div>
    <div class="form_element">
      <input type="text" name="date2" id="picker" class="text-input" placeholder="Date 2" autocomplete="off" />
    </div>
    <div class="form_element">
      <select name="month" id="month" class="select-input">
        <option selected="selected" value="">--Month--</option>
         <?php 
          $array_month = array('January','February','March','April','June','July','August','September','October','November','December');
          foreach($array_month as $val){
            echo '<option value="'.$val.'">'.$val.'</option>';
          }
        ?>
      </select>
    </div>
    <div class="form_element">
      <input type="submit" name="search" value="Search" class="button-input-small" />
    </div>
<script type="text/javascript">
$(function(){

 var year = $('#year_search').val();
 var total = $("#total").html();
 var total_split = total.split('.');
     total_split[0] = total_split[0].replace(/[^0-9]/g,"");
    $.ajax({
      type:'POST',
      url:'json_file.php',
      data:{
        'year':year
      },success:function(d){
        $("#bal_frwd").html(seperator(d));
        $("#grand").html(seperator(d+parseFloat(total_split[0])));

    }
  });
  console.log(year)
})
</script>
</div>

    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>