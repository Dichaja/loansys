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
      ?>
         <!-- Main Content Side-Right -->
           <div class="main-sidebar col-lg-9">
            <div style="width:80%;margin:10px auto">
              <div class="report_header" style="align-items: center;">
                <span></span>
                <span style="display:inline-block;text-align: right;font-size: 12px;font-weight: normal;">
                  <span id="get_report" style="display: inline-block;margin-right: 10px;border-radius: 5px;background-color: #ccc;padding: 5px;cursor: pointer;">Generate PDF</span>
                  <span id="print_rpt">
                    <span>Print</span>
                    <span><img src="../img_file/print-icon.svg" width="20" height="20"></span>
                  </span>
                </span>
       </div>
       <?php
        $date = date('Y-m-d');
          if(isset($_POST['search_date'])){
            $date = $_POST['search_date'];
          }
           $backDate = date_set_back($date,1);
       ?>
               <div class="grid-2" style="display: grid;">
               <div>CASH BOOK SUMMARY AS AT <?php echo date('d/m/Y',strtotime($date)); ?></div>
        <div style="text-align: right;">
          <!--Search Wrapper -->
           <form name="form1" method="post" action="" id="form1">
            <input type="hidden" name="post_search" value="1" />
             <div style="width:100%;height:40px;display: grid;grid-template-columns: 2fr 1fr;">
               <div style="height:40px;border:solid 1px #CCC;background-color:#fff">
                  <input type="text" class="search_text" name="search_date" placeholder="Search Date" id="picker" autocomplete="off" />
                      <img src="../img_file/search.png" id="open_search" data-usr="" width="18px" height="18px" />
                      <div id="drop-box" class="drop_down drop_large_size" style="width:280px;"></div>
                   </div> 
                 <input type="submit" name="search" value="Search" class="button_search">
              </div>                      
           </form>
       </div>
    </div>
     
    <div style="margin:15px auto;">
              <table width="100%" cellspacing="0" cellpadding="5px" class="report_display">
                <tr>
                  <td>
                    <div class="grid-3">
                      <div>Date</div>
                      <div>Account</div>
                      <div>Amount</div>
                    </div>
                  </td>
                  <td>
                    <div class="grid-3">
                      <div>Date</div>
                      <div>Account</div>
                      <div>Amount</div>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="grid-3">
                      <div></div>
                      <div></div>
                      <div>Cash In</div>
                    </div>
                  </td>
                  <td>
                    <div class="grid-3">
                      <div></div>
                      <div></div>
                      <div>Cash Out</div>
                    </div>
                  </td>
                </tr>
                <tr>
                   <td>
                   <div class="grid-3">
                      <div></div>
                      <div>Opening Balance</div>
                      <div>
                  <?php
                $qry = mysqli_query($connect,"SELECT * FROM loan_payments WHERE date_entry <= '$backDate'");
                    $totPay=0;
                    if(mysqli_num_rows($qry)){
                       while($r = mysqli_fetch_array($qry)){
                           $totPay += $r['amount_paid'];
                         } 
                      }

                    $qry = mysqli_query($connect,"SELECT * FROM expense WHERE entry_date <= '$backDate'  ");
                     if(mysqli_num_rows($qry)){
                       while($r = mysqli_fetch_array($qry)){
                           $totPay -= ($r['amount'] * $r['qty']);
                         } 
                      }
                        echo number_format($totPay);
                      ?></div>
                    </div></td>
                   <td>
                     <div class="grid-3">
                      <div></div>
                      <div></div>
                      <div></div>
                    </div>
                   </td>
                </tr>
                <tr>
                   <td>
                    <div class="grid-3">
                      <div></div>
                      <div>Processing Fees</div>
                      <div><?php echo number_format(summaryTot($connect,$date,'loan_fees','loan_entries','date_entry')) ?></div>
                    </div>
                    <div class="grid-3">
                      <div></div>
                      <div>Loan Payments</div>
                      <div><?php echo number_format(summaryTot($connect,$date,'loan','loan_payments','pay_date')) ?></div>
                    </div>
                     </td>
                     <td>
                      <?php
                       $q = "SELECT i.item, SUM(e.amount * e.qty) as 'costs'  FROM expense e, expense_items i WHERE e.expense = i.id AND ";
                         if($date)
                           $q .= " entry_date = '".date('Y-m-d',strtotime($date))."' AND ";
                         $q .= " 1 GROUP BY e.expense";
                       $sql = mysqli_query($connect,$q);
                       $totExp=0;
                       if(mysqli_num_rows($sql)){
                        while($r = mysqli_fetch_array($sql)){
                          $totExp += $r['costs'];
                      ?>
                     <div class="grid-3">
                      <div></div>
                      <div style="text-transform:capitalize;"><?php echo strtolower($r['item']) ?></div>
                      <div><?php echo number_format($r['costs']) ?></div>
                    </div>
                     <?php
                          }
                        }
                      ?>
                   </td>
                </tr>
                
                <tr style="font-weight:bold;">
                   <td>
                     <div class="grid-3">
                      <div>Total</div>
                      <div></div>
                      <div>
                        <?php
                          echo number_format((summaryTot($connect,$date,'loan_fees','loan_entries','date_entry')+summaryTot($connect,$date,'loan','loan_payments','pay_date')));
                        ?>
                      </div>
                    </div>
                   </td>
                   <td>
                     <div class="grid-3">
                      <div></div>
                      <div></div>
                      <div><?php echo number_format($totExp) ?></div>
                    </div>
                   </td>
                </tr>
                 <tr style="font-weight:bold;">
                   <td>
                     <div class="grid-3">
                      <div></div>
                      <div></div>
                      <div></div>
                    </div>
                   </td>
                   <td>
                     <div class="grid-3">
                      <div></div>
                      <div>Closing Balance</div>
                      <div>
                        <?php
                          $cb = (summaryTot($connect,$date,'loan_fees','loan_entries','date_entry')+summaryTot($connect,$date,'loan','loan_payments','pay_date'));
                          echo number_format(($cb + $totPay) - $totExp);
                        ?>
                      </div>
                    </div>
                   </td>
                </tr>
              </table>
            </div>
        </div>
      </div>


<!-- Search Form -->
<div id="search-form">
    <div class="form_element">
  <input type="text" name="date" placeholder="Date From" class="text-input" id="datetimepicker" autocomplete="off" />
    </div>
    <div class="form_element">
     <input type="text" name="date2" placeholder="Date To" class="text-input" id="picker" autocomplete="off" />
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
     <input type="text" name="year" placeholder="Year" class="text-input" id="year" autocomplete="off" />
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
</div>
<?php } ?>
</div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>