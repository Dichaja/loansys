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
                <td><div class="grid-3"><div></div><div></div><div>Cash In</div></div></td>
                <td><div class="grid-3"><div></div><div></div><div>Cash Out</div></div></td>
            </tr>
            <tr>
                <td>
                    <div class="grid-3">
                        <div></div>
                        <div>Opening Balance</div>
                        <div>
                            <?php
                            // Opening balance from account_transactions prior to reporting date
                            $open_bal = 0;
                            $open_sql = mysqli_query($connect, "SELECT SUM(CASE WHEN trans_type IN ('deposit','loan_payment') THEN amount WHEN trans_type IN ('withdrawal','expense') THEN -amount ELSE 0 END) as bal FROM account_transactions WHERE date < '".mysqli_real_escape_string($connect,$date)."'");
                            $open_row = mysqli_fetch_assoc($open_sql);
                            if ($open_row && isset($open_row['bal'])) $open_bal = $open_row['bal'];
                            echo number_format($open_bal,2);
                            ?>
                        </div>
                    </div>
                </td>
                <td><div class="grid-3"><div></div><div></div><div></div></div></td>
            </tr>
            <?php
            // Group accounts under same balance
            $accounts = [];
            $acc_sql = mysqli_query($connect, "SELECT id, acc_name, acc_branch, acc_no FROM mop_accounts ORDER BY acc_name");
            while ($row = mysqli_fetch_assoc($acc_sql)) {
                $accounts[$row['id']] = $row;
            }

            // Cash In: loan payments, client savings, account_transactions deposits
            $cash_in = [];
            $loan_sql = mysqli_query($connect, "SELECT pay_date, accTo, amount_paid FROM loan_payments WHERE pay_date = '".mysqli_real_escape_string($connect,$date)."'");
            while ($row = mysqli_fetch_assoc($loan_sql)) {
              $acc = isset($accounts[$row['accTo']]) ? $accounts[$row['accTo']]['acc_name'] : 'Unknown';
              $cash_in[] = ['date'=>$row['pay_date'], 'account'=>$acc, 'amount'=>$row['amount_paid'], 'type'=>'Loan Payment'];
            }
            $savings_sql = mysqli_query($connect, "SELECT depo_date, acc_to, amount_depo FROM client_savings WHERE depo_date = '".mysqli_real_escape_string($connect,$date)."'");
            while ($row = mysqli_fetch_assoc($savings_sql)) {
              $acc = isset($accounts[$row['acc_to']]) ? $accounts[$row['acc_to']]['acc_name'] : 'Unknown';
              $cash_in[] = ['date'=>$row['depo_date'], 'account'=>$acc, 'amount'=>$row['amount_depo'], 'type'=>'Savings Deposit'];
            }
            $acct_tx_sql = mysqli_query($connect, "SELECT date, acc_to, amount, trans_type FROM account_transactions WHERE date = '".mysqli_real_escape_string($connect,$date)."' AND trans_type = 'deposit'");
            while ($row = mysqli_fetch_assoc($acct_tx_sql)) {
              $acc = isset($accounts[$row['acc_to']]) ? $accounts[$row['acc_to']]['acc_name'] : 'Unknown';
              $cash_in[] = ['date'=>$row['date'], 'account'=>$acc, 'amount'=>$row['amount'], 'type'=>'Account Deposit'];
            }

            // Cash Out: expenses, cash withdrawals, account_transactions withdrawals
            $cash_out = [];
            $expense_sql = mysqli_query($connect, "SELECT entry_date, acc_from, amount, qty FROM expense WHERE entry_date = '".mysqli_real_escape_string($connect,$date)."'");
            while ($row = mysqli_fetch_assoc($expense_sql)) {
              $acc = isset($accounts[$row['acc_from']]) ? $accounts[$row['acc_from']]['acc_name'] : 'Unknown';
              $cash_out[] = ['date'=>$row['entry_date'], 'account'=>$acc, 'amount'=>$row['amount']*$row['qty'], 'type'=>'Expense'];
            }
            $withdraw_sql = mysqli_query($connect, "SELECT withdraw_date, acc_to, amount_withdraw FROM client_withdraw WHERE withdraw_date = '".mysqli_real_escape_string($connect,$date)."'");
            while ($row = mysqli_fetch_assoc($withdraw_sql)) {
              $acc = isset($accounts[$row['acc_to']]) ? $accounts[$row['acc_to']]['acc_name'] : 'Unknown';
              $cash_out[] = ['date'=>$row['withdraw_date'], 'account'=>$acc, 'amount'=>$row['amount_withdraw'], 'type'=>'Client Withdrawal'];
            }
            $acct_tx_sql_out = mysqli_query($connect, "SELECT date, accFrom, amount, trans_type FROM account_transactions WHERE date = '".mysqli_real_escape_string($connect,$date)."' AND trans_type = 'withdrawal'");
            while ($row = mysqli_fetch_assoc($acct_tx_sql_out)) {
              $acc = isset($accounts[$row['accFrom']]) ? $accounts[$row['accFrom']]['acc_name'] : 'Unknown';
              $cash_out[] = ['date'=>$row['date'], 'account'=>$acc, 'amount'=>$row['amount'], 'type'=>'Account Withdrawal'];
            }

            // Display Cash In
            foreach ($cash_in as $ci) {
              echo '<tr><td><div class="grid-3"><div>'.htmlspecialchars($ci['date']).'</div><div>'.htmlspecialchars($ci['account']).'</div><div>'.number_format($ci['amount'],2).'</div><div style="font-size:11px;color:#888;">'.htmlspecialchars($ci['type']).'</div></div></td><td></td></tr>';
            }
            // Display Cash Out
            foreach ($cash_out as $co) {
              echo '<tr><td></td><td><div class="grid-3"><div>'.htmlspecialchars($co['date']).'</div><div>'.htmlspecialchars($co['account']).'</div><div>'.number_format($co['amount'],2).'</div><div style="font-size:11px;color:#888;">'.htmlspecialchars($co['type']).'</div></div></td></tr>';
            }

            // Totals
            $total_in = array_sum(array_column($cash_in,'amount'));
            $total_out = array_sum(array_column($cash_out,'amount'));
            $closing_balance = $open_bal + $total_in - $total_out;
            ?>
            <tr style="font-weight:bold;">
                <td><div class="grid-3"><div>Total</div><div></div><div><?php echo number_format($total_in,2); ?></div></div></td>
                <td><div class="grid-3"><div></div><div></div><div><?php echo number_format($total_out,2); ?></div></div></td>
            </tr>
            <tr style="font-weight:bold;">
                <td></td>
                <td><div class="grid-3"><div></div><div>Closing Balance</div><div><?php echo number_format($closing_balance,2); ?></div></div></td>
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