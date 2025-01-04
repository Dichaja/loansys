<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess(); //check user loggin

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    <?php include('../data_files/link_docs.php') ?>
   <title><?php echo sys_tab_hdr() ?></title>

<script type="text/javascript">

</script>
</head>

<body>
<?php

$year = date('Y');
$limit=40;
$target_page = 'expense_payments.php';

if($_POST['search']){
   $expense = $_POST['expense'];
   $id = $_POST['expense_id'];
   $date = $_POST['date'];
   $date2 = $_POST['date2'];
   $month1 = $_POST['month1'];
   $month2 = $_POST['month2'];
   $year = $_POST['year'];
}

if($_GET['page']){
  $page = $_GET['page'];
  $limit = $_GET['limit'];
  $month1 =  $_GET['month1'];
  $month2 = $_GET['month2'];
  $expense= $_GET['expense'];
  $date=$_GET['date'];
  $date2 = $_GET['date2'];
  $year=$_GET['year'];
}

 
//reset page counter
if($limit=='')
  $page=1;

if($page){
    $start=($page-1)*$limit;
 }else{
    $start=0;
  }  

      $q='';
        $q = " SELECT e.exp_ac, e.entry_date, m.name, a.acc_no, e.paid_to FROM expense_items i, expense e, mop m, mop_accounts a WHERE m.id = a.mop AND m.id = e.mop AND i.id=e.expense AND ";
         if($expense){
              if($id!='')
                 $q .= " e.expense = '$id' AND ";
               else 
                  $q .= " i.item LIKE '%$expense%' AND ";
           }
          if($year != ''){
                $q .= " DATE_FORMAT(e.entry_date, '%Y') = '$year' AND ";
                  }
           if($date !='' && $date2 !=''){
             $q .= " e.entry_date BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date2))."' AND ";
               }
              if($date != '' && $date2==''){
                     $q .= " e.entry_date = '".date('Y-m-d',strtotime($date))."' AND ";
                     }
                if($month1 != ''){
                        $q .= " monthname(e.entry_date) = '$month1' AND ";
                    } 
              $qry .= $q.' 1 GROUP BY e.exp_ac ';
            if($limit)      
               $q .= " 1 GROUP BY e.exp_ac ORDER BY e.entry_date DESC LIMIT $start,$limit ";
            else
               $q .= " 1 GROUP BY e.exp_ac ORDER BY e.entry_date DESC ";
            
            //return all rows from search
            $sql_tot = mysqli_query($connect,$qry);
              while($rw_count = mysqli_fetch_array($sql_tot)){
              $total_pages += 1;
            }
      //returns rows search per row limit
      $result = mysqli_query($connect,$q);
    
     /* Setup page vars for display. */
    if ($page == 0) $page = 1;  //if no page var is given, default to 1.
    $first=1;
    $prev = $page - 1;
    
    if($limit)
    $lastpage = ceil($total_pages/$limit);      //lastpage is = total pages / items per page, rounded up. 

    //previous page is page - 1 
    if($page==$lastpage)
    $next=$lastpage;
    else
    $next = $page + 1; //next page is page + 1 

    $lpm1 = $lastpage - 1; //last page minus 1

   $search_text = "Expense Report ";
              if($_POST or $_GET){
                $search_text .= ' - ';
                if($expense){
                  $search_text .= ' '.$expense;
                }
                  if($category){
                     $search_text .= ' '.$category;
                  }
                    if($date && $date2){
                      $search_text .= ' - '.date("d/m/Y",strtotime($date)).' To '.date("d/m/Y",strtotime($date2));
                    }else if($date!='' && $date2==''){
                      $search_text .= ' '.date("d/m/Y",strtotime($date));
                    }
                      if($month1){
                        $search_text .= ' '.$month1;
                      }
                       if($year){
                        $search_text .= ' '.$year;
                       }
             }else{
               $search_text .= ' As At '.date('d/m/Y');
             }

  if($_GET['action_msg']){
                if($_GET['action_msg']=="success"){
                  ?>
                <div style="width:90%;padding:5px;border-left:solid 5px green;background-color:#CCC;margin:10px;" class="action_msg">
                  <span style="color:#000">Action Complete...!!!</span>
                  <span style="float:right;margin-right:5px;cursor:pointer" class="times">&times</span>
                </div>
                <?php } if($_GET['action_msg']=="err"){ ?>
                <div style="width:90%;padding:5px;border-left:solid 5px #F00;background-color:#CCC;margin:10px;" class="action_msg">
                  <span style="color:#F00;">Something Went Wrong...!!!</span>
                  <span style="float:right;margin-right:5px;cursor:pointer" class="times">&times</span>
                </div><?php }
              }
           ?>
   
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">

            <!-- Search Wrapper -->

<div class="grid-2 search" style="display: grid;">
        <div></span>
      <span id="print_rpt"><img src="../img_file/print-icon.svg" width="20" height="20"></span></div>
        <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="expense_report.php" id="form1">
            <input type="hidden" name="post_search" value="1">
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff">
                   <input type="text" class="search_text" name="expense" placeholder="Expense" id="expense" autocomplete="off" />
                     <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px">
                     <input type="hidden" name="expense_id" id="expense_id" value="" /> 
                    <div id="suggesstion-box" class="suggest"></div> </div>
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
    </div>

<div style="width:100%;display:block;float:left;">
  <div style="width:98%;margin:15px auto 20px auto;padding: 5px 0px 5px 0px;margin-bottom: 25px;" id="page_id">
          <div style="width:55%; float:left;">
              <div class="headerList"><?php  echo $search_text;  ?></div>          
          </div>
          <div style="width:43%;float:right;display:flex;text-align: right;">
            <div style="flex:1" id="page"><b>Total: <?php 
               echo $total_pages.'&nbsp;&nbsp;</b>'; 
                 
                 if($limit)
                 echo "<a href=\"$targetpage?page=$first&limit=$limit&month1=$month1&date=$date&year=$year&expense=$expense&date2=$date2\">Prev</a>&nbsp;&nbsp<span style=\"font-weight:bold;text-transform: lowercase !important;\">".$page.' of '.$lastpage."</span>&nbsp;&nbsp;<a href=\"$targetpage?page=$next&limit=$limit&month1=$month1&date=$date&year=$year&expense=$expense&date2=$date2\">Next</a>&nbsp&nbsp";
               else
                 echo "<a href=\"$targetpage?page=1&limit=40&month1=$month1&date=$date&year=$year&expense=$expense&date2=$date2\">Back</a>&nbsp;&nbsp;";
                 echo "|&nbsp;&nbsp;<a href=\"$targetpage?page=1&limit=&month1=$month1&date=$date&year=$year&expense=$expense&date2=$date2\">View All</a>" ?>
            </div>
            <div style="width:20%;float: left;">
                <span id="print_rpt" style="border:solid 1px #000;padding:3px;border-radius:4px;">Print</span>
            </div>
        </div>
    </div>  
    <div style="float:right;width:18%;margin-top:10px;background-color:#CCC;padding:5px;" id="statistic_wrap">
      <div style="display:block;width:100%;margin-bottom:20px;float:left;font-weight:bold;font-size:13px;">
          <div style="width:100%;float:left;text-align:center">Expense - Summary</div>
      </div>
      <div style="display:block;width:100%;margin-bottom:10px;float:left;border-bottom:solid 1px #B2B555;font-weight:bold;font-size: 12px;">
               <div style="width:50%;float:left;">Expense</div>
               <div style="width:44%;float:right;text-align:right;">Total</div>
          </div>
    <?php
     $sql = mysqli_query($connect,"SELECT * FROM expense_items ORDER BY item ASC");
      while($r=mysqli_fetch_array($sql)){
      $search = "SELECT e.amount, e.qty FROM expense_items i, expense e, mop m, mop_accounts a WHERE e.acc_from = a.id AND m.id = e.mop AND i.id=e.expense AND i.id = '$r[0]' AND ";
        
          if($expense){
            if($id!='')
               $search .= " i.id = '$id' AND ";
            else
              $search .= " i.item LIKE '%$expense%' AND ";
          }  
         if($date !='' && $date2 !=''){
             $search .= " e.entry_date BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date2))."' AND ";
               }
              if($date != '' && $date2==''){
                     $search .= " e.entry_date = '".date('Y-m-d',strtotime($date))."' AND ";
                     }
                    if($month1 != ''){
                           $search .= " monthname(e.entry_date) = '$month1' AND ";
                          }
              if($year != ''){
                $search .= " DATE_FORMAT(e.entry_date, '%Y') = '$year' AND ";
              }
         $search .= " 1 ";
        $q = mysqli_query($connect, $search);
        $expense_amt=0;
         if(mysqli_num_rows($q)){
           while($rw=mysqli_fetch_array($q)){
            $qty = $rw[1];
            if($rw[1]==0)
               $qty=1;
            $expense_amt += $rw[0]*$qty;
           }
           $i+=1;
           
           echo '<div style="display:flex;width:100%;margin-bottom:10px;font-size:12px;">
               <div style="flex:1">'.$i.')</div><div style="flex:4;">'.strtolower($r[1]).'</div>
               <div style="flex:1">'.number_format($expense_amt).'</div>
              </div>';
         }

        
        $tot_exp+=$expense_amt;
      }
      
      echo '<div style="display:flex;width:100%;margin-bottom:10px;font-size:11px; font-weight: bold;">
               <div style="flex:1">&nbsp;</div><div style="flex:4;">Total</div>
               <div style="flex:1">'.number_format($tot_exp).'</div>
              </div>';
    ?>
   </div>
    <div style="margin-bottom:5px;float:left;width:75%; ">      
       <table cellspacing="0" cellpadding="5" width="100%" align="center" class="report_display">
        <tr>
          <td>No</td>
          <td>Voucher No</td>
          <td>Date</td>
          <td>
            <div class="grid-4">
              <div>Expense(s)</div>
              <div>Unit(s)</div>
              <div>Cost</div>
              <div>Amount Paid</div>
            </div></td>          
          <td>Mode of Pay</td>
          <td></td>
        </tr>
        <?php
           if(mysqli_num_rows($result)){

               $ct=0;
              while($rw=mysqli_fetch_array($result)){
                
                $start+=1;
                $ct+=1;
                  
                  ?><tr>
                           <td><?php echo $ct ?></td>
                           <td><?php echo $rw[0] ?></td>
                           <td><?php echo date('M-d-y',strtotime($rw[1])) ?></td>
                           <td>
                            <?php
                             $query = mysqli_query($connect,"SELECT i.item, e.amount, e.qty FROM expense e, expense_items i WHERE e.expense=i.id AND e.exp_ac='$rw[0]' ");
                             while($rws = mysqli_fetch_array($query)){
                              $exp_qty = $rws[2];
                                   if($rws[2]==0)
                                     $exp_qty = 1;

                                   $tot += $rws[1]*$exp_qty; //total expense cost
                                 ?>
                                   <div class="grid-4">
                                      <div><?php echo $rws[0] ?></div>
                                      <div><?php echo number_format($exp_qty) ?></div>
                                      <div><?php echo number_format($rws[1]) ?></div>
                                      <div><?php echo number_format($rws[1]*$exp_qty) ?></div>
                                   </div>
                            <?php } ?></td>
                           <td><?php echo $rw[2] ?></td>
                           <td>
                           <select name="option" style="height:30px; width:70px;" id="action_<?php echo $ct ?>" class="action">
                               <?php echo '<option value="" selected="selected">Action</option>
                               <option value="'.$rw[0].'_Edit">Edit</option>
                               <option value="'.$rw[0].'_voucher">View Voucher</option>
                               <option value="'.$rw[0].'_Delete">Delete</option>'; ?>
                            </select></td>
                        </tr>
                    <?php
             }

           }else{
            ?>
             <tr>
                <td colspan="7"><div style="height:60px;">No Result(s) Found...!!!</div></td>
             </tr>
            <?php
           }
      //----returns total expense */
        $q='';
          $q = "SELECT e.exp_ac FROM expense e WHERE "; 
           if($year != ''){
                $q .= " DATE_FORMAT(e.entry_date, '%Y') = '$year' AND ";
                  }
            if($date !='' && $date2 !=''){
             $q .= " e.entry_date BETWEEN '".date('Y-m-d',strtotime($date))."' AND '".date('Y-m-d',strtotime($date2))."' AND ";
               }
              if($date != '' && $date2==''){
                     $q .= " e.entry_date = '".date('Y-m-d',strtotime($date))."' AND ";
                   }
                if($month1 != ''){
                       $q .= " monthname(e.entry_date) = '$month1' AND ";
                      }          
            $q .= " 1 GROUP BY e.exp_ac";

              $sqls = mysqli_query($connect,$q);
              while($r=mysqli_fetch_array($sqls)){
                $y = "SELECT e.amount, e.qty FROM expense_items i, expense e WHERE i.id=e.expense AND e.exp_ac='$r[0]' AND ";
                    if($id!=''){
                      $y .= " i.id = '$id' AND "; 
                     }else{
                       if($expense!=''){
                         $y .= " i.item LIKE '%$expense%' AND ";
                        }
                       }   
                    $y .= " 1 ";
                    $qry = mysqli_query($connect,$y);
                    while($x=mysqli_fetch_array($qry)){
                      //$tot+=($x[0]*$x[1]);
                    }
              }

       /****** End of total ****/
        ?>
        <tr style="font-weight:bold">
          <td>Total</td>
          <td colspan="2"></td>
          <td>
              <div class="grid-4">
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div>&nbsp;</div>
                <div><?php echo number_format($tot) ?></div>
             </div></td>
          <td colspan="2"></td>
        </tr>
        <tr>
          <td colspan="7"><div id="bottom" style="font-size:12px;">&nbsp;</div></td>
        </tr>
        <tr>
          <td colspan="7"><div style="height:90px;">&nbsp;</div></td>
        </tr>
      </table>
</div>
        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>