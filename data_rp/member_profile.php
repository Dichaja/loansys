<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
error_reporting(0);
check_sess(); //check user loggin
$member_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if(!$member_id) {
  echo '<div style="color:red;text-align:center;">Invalid member ID.</div>';
  exit;
}

// Fetch member details
$member_sql = mysqli_query($connect, "SELECT c.*, b.branch_name FROM clients c LEFT JOIN branches b ON c.branch_id = b.id WHERE c.id='$member_id'");
$member = mysqli_fetch_array($member_sql);
if(!$member) {
  echo '<div style="color:red;text-align:center;">Member not found.</div>';
  exit;
}

// Fetch group name
$group_name = '';
if(!empty($member['group_id'])) {
  $group_sql = mysqli_query($connect, "SELECT group_name FROM groups WHERE id='".$member['group_id']."'");
  if($group_row = mysqli_fetch_array($group_sql)) {
    $group_name = $group_row['group_name'];
  }
}

// Fetch loans for member
$loans_sql = mysqli_query($connect, "SELECT * FROM loan_entries WHERE client='$member_id' ORDER BY date_entry DESC");

// Fetch savings for member

$savings_sql = mysqli_query($connect, "SELECT s.id, s.amount_depo, m.name, p.acc_name, s.acc_from, s.depo_date, p.acc_no FROM client_savings s LEFT JOIN mop m ON s.mop = m.id, mop_accounts p WHERE p.id = s.acc_to AND s.client='$member_id' ORDER BY s.depo_date DESC");
$withdraw_sql = mysqli_query($connect, "SELECT w.id, w.amount_withdraw, m.name, p.acc_name, w.acc_from, w.withdraw_date, w.date_entry, w.remarks FROM client_withdraw w LEFT JOIN mop m ON w.mop = m.id, mop_accounts p WHERE p.id = w.acc_to AND w.client='$member_id' ORDER BY w.withdraw_date DESC");
$total_savings_sql = mysqli_query($connect, "SELECT SUM(amount_depo) as total FROM client_savings WHERE client='$member_id'");
$total_withdraw_sql = mysqli_query($connect, "SELECT SUM(amount_withdraw) as total FROM client_withdraw WHERE client='$member_id'");
$total_savings_row = mysqli_fetch_array($total_savings_sql);
$total_withdraw_row = mysqli_fetch_array($total_withdraw_sql);
$total_savings = isset($total_savings_row['total']) ? $total_savings_row['total'] : 0;
$total_withdraw = isset($total_withdraw_row['total']) ? $total_withdraw_row['total'] : 0;

?>
<!DOCTYPE html>
<html lang="en">
  <head>
   <meta content="charset=utf-8" /> 
    
   <title>Member Profile - <?php echo ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']); ?></title>
   <?php include('../data_files/link_docs.php') ?>

<script type="text/javascript">

$(document).on('change','select[name="mode_of_pay"]',function(){
   
    var val = $(this).val(),
        id = $(this).attr('id'),
        split = id.split('_'),
        selectText = $(this).find('option:selected').text();;

      $.ajax({
         type: 'POST',
         url : '../data_files/post_data.php',
         data:{
           'returnMop': val
         },
         success:function(d){
           $('#accTo_1').html(d);
       }
    })
})

$(document).on('click', '.disburse-loan-btn', function(e) {
            e.preventDefault();
             var clientId = $(this).data('client-id');
             var savings_balance = $(this).data('savings');
             var modal = $('#myModal').html();
             var form_display = $("#loan_form").html();
             // Trigger the assign loan action
             $('#pop_' + clientId).trigger('click');
             $('#myModal').css('display','block');
             $('#myModal').html(modal);
          
             $('.modal-content').removeClass('modal-large-size').toggleClass('modal-small-size');
             $("#display").html(form_display);
             $('#step_1').addClass('current-item')
             $("#client_id").val(clientId);
             $('#savings_bal').val(savings_balance);
         });


$(document).on('click', '#new_loan', function() {
  $.ajax({
      type: 'POST',
      url: '../data_files/data_src.php',
      data:{
        'check_loan': 1,
        'check_client': select[1] 
      },
      beforeSend:function(){
        $('#myModal_2').css({'display':'block','z-index':'6'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
      },
      success: function(d){

       $('#myModal_2').css('display','none');
          $('#myModal').css('display','block');
          $('#myModal').html(modal);

          if(d!='success'){          
          
             $('.modal-content').removeClass('modal-large-size').toggleClass('modal-small-size');
             $("#display").html(form_display);
             $('#step_1').addClass('current-item')
             $("#client_id").val(select[1]);
          }else{

              var content = $('#warning_wrap').html();
              $('.modal-content').removeClass('modal-large-size').toggleClass('modal-min-size');
              $("#display").html(content);
          }
      }
  })
});

$(document).on('click', '#submit_loan', function(event) {
  event.preventDefault();
  
  let form = $("#form2");
  let essentialForm = $("#essential").html();
  let errorFields = [];
  
  // Validate form fields
  if ($('input[name="name_search"]').val() === '') {
    errorFields.push('name_search');
  }
  if ($('#loan_amount').val() === '' || $('#loan_amount').val() === '0') {
    errorFields.push('loan_amount');
  }
  if ($('select[name="duration"]').val() === '') {
    errorFields.push('duration');
  }
  if ($('input[name="period"]').val() === '') {
    errorFields.push('period');
  }
  if ($('input[name="interest"]').val() === '') {
    errorFields.push('interest');
  }
  
  if (errorFields.length > 0) {
    // Apply error styling to the fields with errors
    errorFields.forEach(field => {
      $('#' + field).css('border', 'solid 1px #F00').focus();
    });
  } else {
    $.ajax({
      type: "POST",
      url: "../data_files/data_src.php",
      data: form.serialize(),
      beforeSend: function() {
        $('#myModal_2').css({'z-index':'10','display':'block'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>');
      },
      success: function(response) {
        $('#myModal_2').css('display','none');
        let [status, loanId, clientId] = response.split("_");
        
        if (status === '1') {
          $('#step_1').toggleClass('current-item');
          $('#step_2').addClass('current-item');
          $("#form_wrap").html(essentialForm);
          $("#client_id2").val(clientId);
          $("#loan_id").val(loanId);
        } else {
          alert("Unsuccessful. Something went wrong!");
          console.log(response);
        }
        
        form.trigger("reset");
      }
    });
  }
})


$(document).on('click','#security_button',function(event){

  event.preventDefault();
  var guarantor_form = $("#guarantor_content").html();

  $.ajax({
    type:"POST",
    url:"../data_files/data_src.php",
    data:$("#form").serialize(),
    beforeSend:function(){
     $('#myModal_2').css({'display':'block','z-index':'10'}).html('<div class="modal-spin-wrap"><div class="modal-img-spin"><img src="../img_file/loading.gif" /></div></div>')
    },
    success:function(d){
      $('#myModal_2').css('display','none');
           
      var split = d.split("_");

          if(split[0]==1){
            $('#step_2').toggleClass('current-item');
            $('#step_3').addClass('current-item');
            $("#form_wrap").html(guarantor_form);
            $("#client_id3").val(split[2]);
            $("#loan_id2").val(split[1]);                 
          }else{
            alert("Something Went Wrong.Please Try Again..!!!");
          }
      $("#form").trigger("reset");
    }
  })

})

$(document).on('click','#guarantor_button',function(event){

  event.preventDefault();
  var content='', load_content='';

  $.ajax({
     type:'POST',
     url:'../data_files/data_src.php',
     data:$("#guarantor").serialize(),
     beforeSend:function(){
         load_content = $('#loading_wrap').html();
         $('.modal-content').removeClass('modal-small-size').toggleClass('modal-min-size');
         $('#display').html(load_content)
       },
       success:function(d){
         if(d=='success'){
             content = $('#success_wrap').html();
           }else{
             content = $('#error_wrap').html();
           }
         $('#display').html(content);
       }
  })

})

</script>
<style>
    .profile-header { font-size:1.5em; font-weight:bold; margin-bottom:10px; }
    .profile-section { margin-bottom:30px; }
    .loan-table { width:100%; border-collapse:collapse; margin-top:10px; }
    .loan-table th, .loan-table td { border:1px solid #ccc; padding:8px; text-align:left; }
    .loan-table th { background:#f7f7f7; }
    .action-btn { background:#28a745; color:#fff; border:none; border-radius:4px; padding:6px 14px; cursor:pointer; font-weight:bold; margin-right:6px; }
    .action-btn.pay { background:#007bff; }
    .action-btn.disburse { background:#fd7e14; }
  </style>
</head>

<body>
   

<!-- Main Content Wrapper -->
<div class="main_bd_wrap">
  <?php tp_hdr(); side_menu_content(); ?>
  <div class="main-sidebar col-lg-9">
    <div class="main_bd_wrap">
      <div class="profile-header">Member Profile: <?php echo ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']); ?></div>
      <div class="profile-section">
        <h3>Member Details</h3>
        <div style="display:flex;align-items:center;gap:32px;">
          <div style="min-width:120px;max-width:120px;">
            <?php if(!empty($member['photo_dir']) && file_exists('../img_file/profile/'.$member['photo_dir'])) { ?>
              <img src="../img_file/profile/<?php echo $member['photo_dir']; ?>" alt="Member Photo" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:1px solid #ccc;" />
            <?php } else { ?>
              <div style="width:120px;height:120px;background:#eee;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#aaa;font-size:2em;">No Photo</div>
            <?php } ?>
          </div>
          <table>
            <tr><td><b>ID:</b></td><td><?php echo $member['id']; ?></td></tr>
            <tr><td><b>Name:</b></td><td><?php echo ucfirst($member['first_name']) . ' ' . ucfirst($member['last_name']); ?></td></tr>
            <tr><td><b>Group:</b></td><td><?php echo $group_name ? $group_name : '-'; ?></td></tr>
            <tr><td><b>Contacts:</b></td><td><?php echo $member['contacts']; ?></td></tr>
            <tr><td><b>Email:</b></td><td><?php echo $member['email']; ?></td></tr>
            <tr><td><b>Residence:</b></td><td><?php echo $member['residance']; ?></td></tr>
            <tr><td><b>Business:</b></td><td><?php echo $member['business_name']; ?></td></tr>
            <tr><td><b>Branch:</b></td><td><?php echo $member['branch_name']; ?></td></tr>
            <tr><td><b>Date Registered: </b></td><td> <?php echo date('d/m/Y', strtotime($member['date_created'])); ?></td></tr>
          </table>
        </div>
        <a href="../data_rp/client_list.php?client_id=<?php echo $member_id; ?>" class="action-btn disburse">Disburse Loan</a>
      </div>

      <!-- Tab Navigation -->
      <div class="profile-section">
        <div style="margin-bottom:18px;">
          <button id="tabLoan" class="action-btn" style="background:#145FA7;" onclick="showTab('loan')">Loan Performance</button>
          <button id="tabSavings" class="action-btn" style="background:#28a745;" onclick="showTab('savings')">Member Savings</button>
        </div>
        <div id="tabLoanContent">
          <h3>Loan Performance History</h3>
          <table class="loan-table">
            <tr>
              <th>Loan ID</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Payments</th>
              <th>Balance Due</th>
              <th>Actions</th>
            </tr>
            <?php mysqli_data_seek($loans_sql, 0); while($loan = mysqli_fetch_array($loans_sql)) {
              $loan_id = $loan['id'];
              $payments_sql = mysqli_query($connect, "SELECT SUM(amount_paid) as paid FROM loan_payments WHERE loan='$loan_id'");
              $payments = mysqli_fetch_array($payments_sql);
              $paid = $payments['paid'] ? $payments['paid'] : 0;
              $balance = $loan['loan_amount'] - $paid;
              $default = ($loan['status'] == '03') ? 'Defaulted' : (($balance <= 0) ? 'Cleared' : 'Active');
            ?>
            <tr>
              <td><?php echo $loan_id; ?></td>
              <td><?php echo date('d-m-Y', strtotime($loan['date_entry'])); ?></td>
              <td><?php echo number_format($loan['loan_amount']); ?></td>
              <td><?php echo $default; ?></td>
              <td><?php echo number_format($paid); ?></td>
              <td><?php echo number_format($balance); ?></td>
              <td>
                <?php if($loan['status'] != '03' && $balance > 0) { ?>
                  <a href="../data_rp/loan_activity.php?loan=<?php echo $loan_id; ?>" class="action-btn pay">Make Payment</a>
                <?php } ?>
              </td>
            </tr>
            <?php } ?>
          </table>
        </div>
        <div id="tabSavingsContent" style="display:none;">
        <div style="margin-bottom:18px;text-align:right;">
          <button class="action-btn" style="background:#145FA7;" onclick="openUnifiedSavingsModal(<?php echo $member_id; ?>)">Add/Withdraw Savings</button>
        </div>
        <div id="savingsModal" class="modal" style="display:none;align-items:center;justify-content:center;z-index:1000;">
          <div class="modal-content modal-small-size" id="savingsModalContent"></div>
        </div>
        <script>
        function openUnifiedSavingsModal(memberId) {
          var formHtml = '<form id="savingsUnifiedForm">'
            + '<h3 style="margin-bottom:16px;">Savings Transaction</h3>'
            + '<div class="form-group"><input type="hidden" name="client" value="'+memberId+'" class="text-input" /></div>'
            + '<div class="form-group"><label>Transaction Type</label>'
            + '<select name="trans_type" id="transType" class="text-input" required>'
            + '<option value="deposit">Deposit</option>'
            + '<option value="withdraw">Withdraw</option>'
            + '</select></div>'
            + '<div class="form-group" id="amountDepositGroup"><label>Amount Deposited</label><input type="number" name="amount_depo" class="text-input" min="1" /></div>'
            + '<div class="form-group" id="amountWithdrawGroup" style="display:none;"><label>Amount Withdrawn</label><input type="number" name="amount_withdraw" class="text-input" min="1" /></div>'
            + '<div class="form-group" id="dateDepositGroup"><label>Deposit Date</label><input type="date" name="depo_date" class="text-input" /></div>'
            + '<div class="form-group" id="dateWithdrawGroup" style="display:none;"><label>Withdraw Date</label><input type="date" name="withdraw_date" class="text-input" /></div>'
            + '<div class="form-group"><label>Mode of Pay</label><select name="mode_of_pay" class="text-input" required id="mop_1"><option value="">Select</option><option value="01">Cash</option><option value="02">Bank</option><option value="03">Mobile Money</option></select></div>'
            + '<div class="form-group"><label>Target Account</label><select name="account_to" id="accTo_1" class="text-input">'
            +'<option value="" selected="selected">Select</option>'
            +'</select></div>'
            + '<div class="form-group"><label>Account From</label><input type="text" name="account_from" class="text-input" /></div>'
            + '<div class="form-group" style="margin-top:18px;text-align:right;"><button type="submit" class="button-input" style="background:#145FA7;color:#fff;">Submit</button></div>'
            + '</form>';
          $('#savingsModalContent').html(formHtml);
          $('#savingsModal').show();
        }

        $(document).on('change', '#transType', function() {
          var type = $(this).val();
          if(type === 'deposit') {
            $('#amountDepositGroup').show();
            $('#dateDepositGroup').show();
            $('#amountWithdrawGroup').hide();
            $('#dateWithdrawGroup').hide();
            $("input[name='amount_depo']").prop('required', true);
            $("input[name='depo_date']").prop('required', true);
            $("input[name='amount_withdraw']").prop('required', false);
            $("input[name='withdraw_date']").prop('required', false);
          } else {
            $('#amountDepositGroup').hide();
            $('#dateDepositGroup').hide();
            $('#amountWithdrawGroup').show();
            $('#dateWithdrawGroup').show();
            $("input[name='amount_depo']").prop('required', false);
            $("input[name='depo_date']").prop('required', false);
            $("input[name='amount_withdraw']").prop('required', true);
            $("input[name='withdraw_date']").prop('required', true);
          }
        });

        $(document).on('click', '#savingsModal', function(e) {
          if(e.target.id === 'savingsModal') {
            $('#savingsModal').hide();
          }
        });

        $(document).on('submit', '#savingsUnifiedForm', function(e) {
          e.preventDefault();
          var formData = $(this).serialize();
          var type = $('#transType').val();
          var uniqueId = (type === 'deposit' ? 'SAV' : 'WDR') + Date.now() + Math.floor(Math.random()*1000);
          var action = (type === 'deposit') ? 'add_savings_entry' : 'add_withdraw_entry';
          $.ajax({
            type: 'POST',
            url: '../data_files/post_data.php',
            data: formData + '&action=' + action + '&id=' + uniqueId,
            beforeSend: function() {
              $('#savingsModalContent').html('<div style="text-align:center;padding:40px;">Processing...</div>');
            },
            success: function(resp) {
              if(resp.trim() == 'success') {
                $('#savingsModalContent').html('<div style="color:green;text-align:center;padding:40px;">Transaction successful.</div>');
                 setTimeout(function(){
                       location.reload();
                     }, 1000);
                //setTimeout(function(){ $('#savingsModal').hide(); location.reload(); }, 1200);
              } else {
                $('#savingsModalContent').html('<div style="color:red;text-align:center;padding:40px;">Failed: '+resp+'</div>');
              }
            },
            error: function() {
              $('#savingsModalContent').html('<div style="color:red;text-align:center;padding:40px;">Server error.</div>');
            }
          });
        });
        </script>
          <h3>Member Savings Transactions</h3>
          <table class="loan-table">
            <tr>
              <th>Date</th>
              <th>Deposit</th>
              <th>Withdraw</th>
              <th>Mode of Pay</th>
              <th>Target Account</th>
              <th>Account From</th>
              <th>Type</th>
            </tr>
            <?php
            // Merge deposits and withdrawals by date
            $savings = [];
            while($row = mysqli_fetch_array($savings_sql)) {
              $savings[] = [
                'id' => $row['id'],
                'date' => $row['depo_date'],
                'deposit' => $row['amount_depo'],
                'withdraw' => 0,
                'mop' => $row['name'],
                'acc_to' => $row['acc_name'],
                'acc_from' => $row['acc_from'],
                'type' => 'Deposit'
              ];
            }
            while($row = mysqli_fetch_array($withdraw_sql)) {
              $savings[] = [
                'date' => $row['withdraw_date'],
                'deposit' => 0,
                'withdraw' => $row['amount_withdraw'],
                'mop' => $row['name'],
                'acc_to' => $row['acc_name'],
                'acc_from' => $row['acc_from'],
                'type' => $row['remarks'] ? $row['remarks'] : 'Withdraw'
              ];
            }
            // Sort by date descending
            usort($savings, function($a, $b) {
              return strtotime($b['date']) - strtotime($a['date']);
            });
            foreach($savings as $row) {
            ?>
            <tr>
              <td><?php echo date('d-m-Y H:i', strtotime($row['date'])); ?></td>
              <td><?php echo $row['deposit'] ? number_format($row['deposit']) : ''; ?></td>
              <td><?php echo $row['withdraw'] ? number_format($row['withdraw']) : ''; ?></td>
              <td><?php echo htmlspecialchars($row['mop']); ?></td>
              <td><?php echo htmlspecialchars($row['acc_to']); ?></td>
              <td><?php echo htmlspecialchars($row['acc_from']); ?></td>
              <td><?php echo $row['type']; ?></td>
              <td><?php if($row['deposit'] && isset($row['id']) && $row['type'] === 'Deposit') { ?>
                  <button class="action-btn" style="background:#dc3545;padding:4px 10px;font-size:0.9em;" onclick="deleteSavings('<?php echo $row['id']; ?>', this)">Delete</button>
                <?php } 
                 if($row['withdraw'] && isset($row['id']) && $row['type'] !== 'Deposit') { ?>
                  <button class="action-btn" style="background:#dc3545;padding:4px 10px;font-size:0.9em;" onclick="deleteWithdraw('<?php echo $row['id']; ?>', this)">Delete</button>
                <?php } ?></td>
            </tr>
            <?php } ?>
            <script>
            function deleteWithdraw(id, btn) {
              if(confirm('Delete this withdraw transaction?')) {
                $.ajax({
                  type: 'POST',
                  url: '../data_files/data_src.php',
                  data: { del_withdraw: id },
                  beforeSend: function() {
                    $(btn).prop('disabled', true).text('Deleting...');
                  },
                  success: function(resp) {
                    if(resp == '1') {
                      $(btn).closest('tr').fadeOut(300, function(){ $(this).remove(); });
                    } else {
                      alert('Delete failed: ' + resp);
                      $(btn).prop('disabled', false).text('Delete');
                    }
                  },
                  error: function() {
                    alert('Delete failed.');
                    $(btn).prop('disabled', false).text('Delete');
                  }
                });
              }
            }

            function deleteSavings(id, btn) {
              if(confirm('Delete this savings transaction?')) {
                $.ajax({
                  type: 'POST',
                  url: '../data_files/data_src.php',
                  data: { del_savings: id },
                  beforeSend: function() {
                    $(btn).prop('disabled', true).text('Deleting...');
                  },
                  success: function(resp) {
                    if(resp == '1') {
                      $(btn).closest('tr').fadeOut(300, function(){ $(this).remove(); });
                    } else {
                      alert('Delete failed: ' + resp);
                      $(btn).prop('disabled', false).text('Delete');
                    }
                  },
                  error: function() {
                    alert('Delete failed.');
                    $(btn).prop('disabled', false).text('Delete');
                  }
                });
              }
            }
            </script>
            <tr style="font-weight:bold;background:#f7f7f7;">
              <td colspan="2">Total Balance</td>
              <td colspan="5"><?php echo number_format($total_savings - $total_withdraw); ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php echo footer_sec(); ?>
</div>
<script>
function showTab(tab) {
  document.getElementById('tabLoanContent').style.display = (tab === 'loan') ? '' : 'none';
  document.getElementById('tabSavingsContent').style.display = (tab === 'savings') ? '' : 'none';
  document.getElementById('tabLoan').style.background = (tab === 'loan') ? '#145FA7' : '#ccc';
  document.getElementById('tabSavings').style.background = (tab === 'savings') ? '#28a745' : '#ccc';
}
showTab('loan');
</script>