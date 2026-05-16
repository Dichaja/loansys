 <ul id="sub_main_menu">
   <li><a href="../data_files/dash.php">Home</a></li>
   <li class="expand_link" >Human Resource
    <ul>      
      <li class="<?php if($_SESSION['user_type']=='admin'){ echo 'on'; }else{ echo 'off'; } ?>"><a href="../data_files/staff_reg.php">Staff</a></li>
      <li class="<?php if($_SESSION['user_type']=='admin' OR $_SESSION['user_type']=='director'){ echo 'on'; }else{ echo 'off'; } ?>"><a href="../data_rp/staff_list.php">View Staff</a></li>
      <li><a href="../data_rp/staff_performance.php">Loan Officer Performance</a></li>
     </ul>
   </li>
   <li class="expand_link on">Members
      <ul>
         <li><a href="../data_files/client_reg.php" >Add Member</a></li>
         <li><a href="../data_rp/client_list.php">Member List</a></li>
     </ul>
   </li>
   <li class="expand_link on">Expenses
      <ul>
         <li><a href="../data_files/expense_reg.php">Set Expenses</a></li>
         <li><a href="../data_files/expense_entries.php">Expense Entries</a></li>
         <li><a href="../data_rp/expense_report.php">Expenses Report</a></li>
     </ul>
   </li>
   <li class="expand_link on">Loans Management
          <ul>
             <li class="<?php if($_SESSION['user_type']=='director'){ echo 'off'; } ?>"><a href="../data_rp/client_list.php">Assign Loans</a></li>
             <li><a href="../data_rp/loan_activity.php">Manage Loans</a></li>
             <li><a href="../data_rp/loan_payments.php">Payments Report</a></li>
             <li><a href="../data_rp/loan_analysis.php">Annual Overview</a></li>
          </ul>
     </li>
     <li class="expand_link on">Financial Reports
       <ul>
          <li><a href="../data_rp/summary.php">Summary Report</a></li>
          <li><a href="../data_rp/income_statement.php">Income Statement</a></li>
       </ul>
     </li>
     <li class="expand_link <?php if($_SESSION['general_user']){ echo 'on'; }else{ echo 'off'; } ?>">Preferences
      <ul>
         <li><a href="../data_files/mop_account.php">Mode of Payments</a></li>
         <li><a href="../data_files/branch_reg.php">Branch Settings</a></li>
         <li><a href="../data_files/header_settings.php">Set Notes Header</a></li>
         <li class="<?php if($_SESSION['user_type']=='admin'){ echo 'on'; }else{ echo 'off'; } ?>"><a href="../data_files/add_user.php">Manage Users</a></li>
       </ul>
     </li>  

     <li><a href="../logout.php">Log Out</a></li>
</ul>
<div style="margin:5px auto;width:110%;color:#fff;font-size:12px;border-top:solid 1px #fff;"><span style="">&copy;<?php echo date('Y') ?>,&nbsp;GEP Finance</span></div>
