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
    
   <title><?php echo sys_tab_hdr() ?></title>
   <?php include('../data_files/link_docs.php') ?>
<script type="text/javascript">

</script>
</head>

<body>
   
   <!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  
  <?php        
    tp_hdr(); //Page Header, Menu
       side_menu_content(); // Side Menu
      
//search posts
 if(isset($_POST['search'])){
   $year = $_POST['search_year'];
 }else{
   $year = date('Y');
 }
?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <div style="width:80%;margin:10px auto">
             <div class="grid-2" style="display: grid;width: 70%;margin: 0 auto 15px;">
                <span></span>
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" style="display: inline-block;margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;">Generate PDF</span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
              </div>
              <div class="grid-2" style="display: grid;width: 70%;margin: 0 auto 15px;">
               <div>INCOME STATEMENT AS AT <?php echo $year;?></div>
               <div style="text-align: right;">
                 <!--Search Wrapper -->
           <form name="form1" method="post" action="" id="form1">
            <input type="hidden" name="post_search" value="1" />
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff;width:200px;" id="drop_wrapper">
                  <input type="text" class="search_text" name="search_date" placeholder="Search Date" id="name_search" autocomplete="off" />
                      <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px" />
                      <div id="drop-box" class="drop_down drop_large_size" style="width:336.7px;"></div>
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
    </div>
<?php
// accounts value
$borrowers = loan_performance($connect,'borrowers',$month,$year);
$disbured = loan_performance($connect,'total_loans',$month,$year);
$totInterest = loan_performance($connect,'interest',$month,$year);
$loan_fees = loan_performance($connect,'loan_fees',$month,$year);
?>
  <div style="width: 70%;margin: auto;">
        <table width="100%" class="report_display">
          <tr>
           <td></td>
           <td colspan="2">Particuler(s)</td>
           <td>Amount</td>
          </tr>
           <tr style="font-weight:bold;">
              <td colspan="4">REVENUES</td>
           </tr>
           <tr>
            <td></td>
            <td>Number of Borrowers</td>
            <td></td>
            <td><?php echo number_format($borrowers) ?></td>
          </tr>
          <tr>
           <td></td>
           <td>Loans Disbursed</td>
           <td></td>
           <td><?php echo number_format($disbured) ?></td>
         </tr>
         <tr style="font-style:italic;font-weight: 600;">
           <td>Income</td>
           <td></td>
           <td></td>
           <td></td>
         </tr>
         <tr>
           <td></td>
           <td>Interest Income</td>
           <td></td>
           <td><?php echo number_format($totInterest) ?></td>
         </tr>
         <tr>
           <td></td>
           <td>Additional Income(Loan Fees)</td>
           <td></td>
           <td><?php echo number_format($loan_fees) ?></td>
         </tr>
         <tr>
           <td></td>
           <td>Interest Expense</td>
           <td></td>
           <td>0</td>
         </tr>
         <tr>
           <td></td>
           <td>Loss on Loans</td>
           <td></td>
           <td>0</td>
         </tr>
         <tr style="font-style:italic;font-weight: 600;">
           <td>Gross Profit</td>
           <td></td>
           <td></td>
           <td><?php echo number_format($loan_fees+$disbured+$totInterest)?></td>
         </tr>
         <tr style="font-weight:bold;">
           <td colspan="5">EXPENSES</td>
         </tr>
              <?php
                $totExp=0;
                $expenseTag='';
                $q = "SELECT c.category, i.item, SUM(e.amount * e.qty) as 'costs'  FROM expense e, expense_items i, expense_cat c WHERE c.id = i.category AND e.expense = i.id AND date_format(e.entry_date,'%Y') = '$year' AND ";
                    $q .= " 1 GROUP BY i.category";
                $sql = mysqli_query($connect,$q);
                 if(mysqli_num_rows($sql)){
                    while ($r = mysqli_fetch_array($sql)) {
                        $totExp += $r['costs'];
                        $expenseTag = $r['category']=='' ? 'General Expense' : $r['category'];
                      ?>
                      <tr>
                        <td></td>
                        <td><?php echo $expenseTag; ?></td>
                        <td></td>
                        <td><?php echo number_format($r['costs']) ?></td>
                      </tr>
                     <?php
                    }
                 }else{
                   ?>
                   <tr>
                      <td colspan="4"></td>
                   </tr>
                  <?php
                 }
              ?>
              <tr style="font-style:italic;font-weight: 600;">
                  <td>Total Expense</td>
                  <td></td>
                  <td></td>
                  <td><?php echo number_format($totExp) ?></td>
              </tr>
              <tr style="font-weight:bold;">
                 <td>NET INCOME</td>
                 <td></td>
                 <td></td>
                 <td><?php echo number_format(($loan_fees+$disbured+$totInterest) - $totExp)?></td>
              </tr>
          </table>
      </div>
        </div>
      </div>
<!-- returns seach form elements -->
<div id="search-form">
    <div class="form_element">
     <input type="text" id="picker" name="search_date" value="" class="text-input" placeholder="Date To" />
    </div>
    <div class="form_element">
      <select name="month" id="month" class="text-input">
        <option selected="selected" value="">Search Month</option>
         <?php 
          $array_month = array('January','February','March','April','May','June','July','August','September','October','November','December');
          foreach($array_month as $val){
            echo '<option value="'.$val.'">'.$val.'</option>';
          }
        ?>
      </select>
    </div>
    <div class="form_element">
     <input type="text" id="year" name="search_year" value="" class="text-input" placeholder="Year" />
    </div>
    <?php
      if($_SESSION['general_user']) { ?>
<div class="form_element">
  <select name="branch_details" class="text-input">
    <option value="" selected="selected">Search Branch</option>
     <?php
       $sql = mysqli_query($connect,"SELECT * FROM branches");
         if(mysqli_num_rows($sql)){
            while($r = mysqli_fetch_array($sql)){
              if($r[1])
               echo '<option value="'.$r['id'].'">'.$r['branch_name'].'</option>';
           }
        }
      ?>
   </select>
</div><?php } ?>
</div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>