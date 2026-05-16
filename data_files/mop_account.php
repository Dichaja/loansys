<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
error_reporting(0);
check_sess(); //check user loggin

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    
   <title><?php echo sys_tab_hdr() ?></title>
   <?php include('link_docs.php') ?>
<script type="text/javascript">

</script>
</head>

<body>
<?php


   if(isset($_POST['mop'])){
     
 $inst = mysqli_query($connect," INSERT INTO mop_accounts VALUES('".rand(10000,99999)."','".$_POST['name']."','".$_POST['branch']."','".$_POST['accNo']."','".$_POST['mop']."','".date('Y-m-d')."') ");
       if($inst){
         ?>
         <script type="text/javascript">
           location.replace('mop_account.php')
         </script>
        <?php
       }
   }
   if(isset($_POST['type']) && isset($_POST['amount']) && isset($_POST['date'])) {

    $account_id = $_POST['account_id'];
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $date = $_POST['date'];
    $account_to = isset($_POST['account_to']) ? $_POST['account_to'] : '';
    $account_from = isset($_POST['account_from']) ? $_POST['account_from'] : '';
    $desc = isset($_POST['description']) ? $_POST['description'] : '';
    $sql = "INSERT INTO account_transactions (id, trans_type, amount, date, acc_to, accFrom, description) VALUES ('" . mysqli_real_escape_string($connect, $account_id) . "', '" . mysqli_real_escape_string($connect, $type) . "', '" . $amount . "', '" . mysqli_real_escape_string($connect, $date) . "', '" . mysqli_real_escape_string($connect, $account_to) . "', '" . mysqli_real_escape_string($connect, $account_from) . "', '" . mysqli_real_escape_string($connect, $desc) . "')";
    if (mysqli_query($connect, $sql)) {
        echo '<div style="color:green;text-align:center;">Transaction recorded successfully.</div>';
        /*?>
        <script type="text/javascript">
          document.getElementById('modalTxResult').innerHTML = '<div style="color:green;text-align:center;padding:40px;">Transaction successful.</div>';  
                 setTimeout(function(){
                       location.reload();
                     }, 1000);
         </script>
    <?php */
    } else {
        echo '<div style="color:red;text-align:center;">Error: ' . mysqli_error($connect) . '</div>';
    }
    exit;
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
            <div class="form_wrap_min">
    <div class="form_header">Set Mode of Payment Account</div>
  <form name="form1" id="form1" method="post" >
    <table width="100%" cellspacing="5" cellpadding="5" align="center" class="form-display">
      <tr>
        <td>Account Category </td>
        <td>
          <select name="mop" class="text-input" id="mop">
            <option selected="selected" value="">Select</option>
             <?php
               $select_mop = mysqli_query($connect,"SELECT * FROM mop");
               if(mysqli_num_rows($select_mop)){              
                    while($row_mop = mysqli_fetch_array($select_mop)) {
                      echo '<option value="'.$row_mop[0].'">'.$row_mop[1].'</option>';
                   }        
                }else{
                  echo '<option value="">Not Found</option>'; 
                }
               ?>
              </select></td>
      </tr>
       <tr>
        <td>Branch / Mobile Operator </td>
        <td><input type="text" name="branch" id="branch" class="text-input" /></td>
      </tr>
      <tr>
        <td>Account Name </td>
        <td><input type="text" name="name" id="name" class="text-input" /></td>
      </tr>
      <tr>
        <td>Account No </td>
        <td><input type="text" name="accNo" id="accNo" class="text-input" /></td>
      </tr>
     
      <tr>
        <td>&nbsp;</td>
        <td><input type="submit" name="submit" id="submit" value="Submit" class="button-input" /></td>
      </tr>
    </table>
  </form>
  </div>

<div style="width:80%;margin:18px auto">
  <div style="display: flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
    <div style="font-weight:bold;font-size:14px;">Account Details</div>
    <button class="manage-account-btn" data-account="'.htmlspecialchars($account_id).'" style="padding:6px 14px;background:#007bff;color:#fff;border:none;border-radius:4px;cursor:pointer;">Manage Account</button>
  </div>
  <table width="100%" align="center" cellspacing="0" cellpadding="5" class="report_display">
            <tr>
             <td>No</td>
             <td>Account</td>
             <td>Name</td>
             <td>Branch</td>
             <td>Account No</td>
             <td>Cash Balance</td>
             <td></td>
            </tr>
 <?php
  $get_bank = mysqli_query($connect,"SELECT a.id, a.acc_name, a.acc_branch, a.acc_no, m.name FROM mop_accounts a, mop m WHERE m.id=a.mop");   
    if(mysqli_num_rows($get_bank))
    {
      $count = 0;
      while($rw = mysqli_fetch_array($get_bank))
        {
          $count+=1;
          $account_id = $rw[0];
          $acc_no = $rw['id'];
          $balance = 0;
          // Account transactions
          $tx_sql = mysqli_query($connect, "SELECT trans_type, amount, acc_to, accFrom FROM account_transactions WHERE acc_to='".$account_id."' OR accFrom='".$account_id."'");
          while($tx = mysqli_fetch_array($tx_sql)) {
            if($tx['acc_to'] == $account_id && $tx['trans_type'] == 'deposit') $balance += $tx['amount'];
            if($tx['accFrom'] == $account_id && $tx['trans_type'] == 'withdrawal') $balance -= $tx['amount'];
          }
          
          // Savings deposits
          $savings_sql = mysqli_query($connect, "SELECT amount_depo FROM client_savings WHERE acc_to='".$acc_no."'");
          while($sv = mysqli_fetch_array($savings_sql)) {
            $balance += $sv['amount_depo'];
          }
          // Expenses
          $expense_sql = mysqli_query($connect, "SELECT amount, qty FROM expense WHERE acc_from='".$acc_no."'");
          while($ex = mysqli_fetch_array($expense_sql)) {
            $balance -= ($ex['amount'] * $ex['qty']);
          }
          // Loan payments
          $loan_sql = mysqli_query($connect, "SELECT amount_paid FROM loan_payments WHERE accTo='".$acc_no."'");
          while($lp = mysqli_fetch_array($loan_sql)) {
            $balance += $lp['amount_paid'];
          }
          // Client withdraw
          $withdraw_sql = mysqli_query($connect, "SELECT amount_withdraw FROM client_withdraw WHERE acc_to='".$acc_no."'");
          while($wd = mysqli_fetch_array($withdraw_sql)) {
            $balance -= $wd['amount_withdraw'];
          }
          echo '<tr>
                   <td class="table_content">'.$count.'</td>
                   <td class="table_content">'.$rw[4].'</td>
                   <td class="table_content">'.$rw[1].'</td>
                   <td class="table_content">'.$rw[2].'</td>
                   <td class="table_content">'.$rw[3].'</td>
                   <td class="table_content">'.number_format($balance,2).'</td>
                   <td class="table_content">
                      <select name="action" class="text-input" style="width:80px;height:30px;" id="'.$count.'">
                                <option selected="selected" value="">Action</option>
                                <option value="'.$rw[0].'_edit">Edit</option>
                                <option value="'.$rw[0].'_delete">Delete</option>
                            </select></td>
                 </tr>';
       } 
      
    } else {
       echo '<tr>
               <td colspan="7" class="table_content">No Result(s) Found</td>
             </tr>';  
    }
?>
<!-- Modal for Manage Account -->
<div id="accountModal" class="modal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1000;align-items:center;justify-content:center;">
  <div class="modal-content" style="background:#fff;padding:32px 24px;border-radius:8px;max-width:420px;width:90%;box-shadow:0 2px 8px #aaa;position:relative;">
    <span id="closeAccountModal" style="position:absolute;top:8px;right:12px;font-size:22px;cursor:pointer;">&times;</span>
    <div class="form_header">Account Transaction Entry</div>
    <div id="modalTxResult" style="margin-top:12px;font-size:15px;"></div>
    <form id="accountTxForm" method="post">
      <input type="hidden" name="account_id" id="modal_account_id" />
      <div class="form-group">
      <label>Transaction Type</label>
      <select name="type" id="modal_type" class="text-input" required>
        <option value="deposit">Deposit</option>
        <option value="withdrawal">Withdrawal</option>
      </select>
      </div>
      <div class="form-group">
           <label>Target Account</label>
             <select name="account_to" id="account_to" class="text-input">
                <option value="" selected="selected">Select</option>
                  <?php 
                  $sql = mysqli_query($connect, "SELECT m.id, m.acc_name, m.acc_no FROM mop p, mop_accounts m WHERE p.id = m.mop ORDER BY p.id ASC");
                     while($r = mysqli_fetch_array($sql)){
                       ?><option value="<?php echo $r['id'] ?>"><?php echo $r['acc_name']; ?></option><?php
                     }
                  ?>
            </select>
      </div>
      <div class="form-group">
           <label>Account From</label>
             <select name="account_from" id="account_from" class="text-input">
                <option value="" selected="selected">Select</option>
                  <?php 
                  $sql = mysqli_query($connect, "SELECT m.id, m.acc_name, m.acc_no FROM mop p, mop_accounts m WHERE p.id = m.mop ORDER BY p.id ASC");
                     while($r = mysqli_fetch_array($sql)){
                       ?><option value="<?php echo $r['id'] ?>"><?php echo $r['acc_name']; ?></option><?php
                     }
                  ?>
            </select>
      </div>
       
      <div class="form-group">
        <label>Amount</label>
        <input type="number" name="amount" id="modal_amount" min="0.01" step="0.01" class="text-input" required />
      </div>
      <div class="form-group">
        <label>Date</label>
        <input type="date" name="date" id="modal_date" required class="text-input" />
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" id="modal_description" placeholder="Description" class="text-input"></textarea>
      </div>
      <div class="form-group">
        <button type="submit" class="button-input" style="background:#145FA7;color:#fff;">Submit Payment</button>
      </div>
    </form>
  </div>
</div>
<script type="text/javascript">
// Modal logic
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.manage-account-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var uniqueId = Date.now()+Math.floor(100 + Math.random() * 900);
        document.getElementById('accountModal').style.display = 'flex';
        document.getElementById('modal_account_id').value = uniqueId; //btn.getAttribute('data-account');
        document.getElementById('modalTxResult').innerHTML = '';
      });
    });
    document.getElementById('closeAccountModal').onclick = function() {
      document.getElementById('accountModal').style.display = 'none';
    };
    document.getElementById('accountTxForm').onsubmit = function(e) {
      let from = document.getElementById("account_from").value;
      let to = document.getElementById("account_to").value;
      e.preventDefault();
if(from !== "" && to !== "" && from === to){
        alert("Cannot transfer to the same account.");
     }else{
      var formData = new FormData(this);
      fetch('mop_account.php', {
        method: 'POST',
        body: formData
      }).then(resp => resp.text()).then(function(data) {
        document.getElementById('modalTxResult').innerHTML = data;
      });
    };
  }
  });

document.getElementById("modal_type").addEventListener("change", function(){

let type = this.value;
let from = document.getElementById("account_from");
let to = document.getElementById("account_to");

if(type === "deposit"){
    from.disabled = false;
    to.disabled = false;
}

if(type === "withdrawal"){
    from.disabled = false;
    to.disabled = true;
    to.value = "";
}

});
</script>
   <tr>
       <td colspan="6"><div style="height:90px">&nbsp;</div></td>
   </tr>
 </table>
</div>
</div>

        </div>
    <?php
      echo footer_sec(); //footer section
    ?>
  </div>
  </body>
</html>