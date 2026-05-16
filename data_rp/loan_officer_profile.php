<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
check_sess();

$officer_id = $_GET['officer_id'] ?? '';
$officer = null;
$loans = [];

if ($officer_id) {
    $officer_sql = mysqli_query($connect, "SELECT s.id, s.first_name, s.last_name, s.contacts, s.gender, s.email, s.residence, b.branch_name, j.job_title FROM staff s LEFT JOIN branches b ON s.branch_id = b.id LEFT JOIN staff_job j ON j.id = s.job WHERE s.id = '".mysqli_real_escape_string($connect, $officer_id)."'");
    $officer = mysqli_fetch_assoc($officer_sql);
    $loan_sql = mysqli_query($connect, "SELECT e.id, e.client, e.loan_amount, e.interest, e.period, e.date_entry, e.status, g.guarantor, s.security FROM loan_entries e, loan_guarantor g, loan_security s WHERE e.id = g.loan AND e.id = s.loan AND e.loan_officer = '".mysqli_real_escape_string($connect, $officer_id)."' ORDER BY e.date_entry DESC");
    $total_balance_due = 0;
    $total_loan_amount = 0;
    $total_amount_paid = 0;
    while ($row = mysqli_fetch_assoc($loan_sql)) {
        $client_sql = mysqli_query($connect, "SELECT first_name, last_name FROM clients WHERE id = '".mysqli_real_escape_string($connect, $row['client'])."'");
        $client = mysqli_fetch_assoc($client_sql);
        $row['client_name'] = $client ? $client['first_name'].' '.$client['last_name'] : 'Unknown';
        // Calculate amount paid and balance due
        $paid_sql = mysqli_query($connect, "SELECT SUM(amount_paid) as paid FROM loan_payments WHERE loan='".mysqli_real_escape_string($connect, $row['id'])."'");
        $paid_row = mysqli_fetch_assoc($paid_sql);
        $row['amount_paid'] = $paid_row && $paid_row['paid'] ? $paid_row['paid'] : 0;
        $row['balance_due'] = $row['loan_amount'] - $row['amount_paid'];
        $total_balance_due += $row['balance_due'];
        $total_loan_amount += $row['loan_amount'];
        $total_amount_paid += $row['amount_paid'];
        // Period count
        $row['period_count'] = $row['period'];
        // Running period count for active loans
        if ($row['status']=='00') {
            $row['running_period'] = return_period($row['date_entry'], 'month');
        } else {
            $row['running_period'] = '-';
        }
        // Guarantor and security details
        $row['guarantor'] = $row['guarantor'] ?? '';
        $row['security'] = $row['security'] ?? '';
        $loans[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Loan Officer Profile</title>
    <?php include('../data_files/link_docs.php'); ?>
    <style>
        .profile-header { font-size: 18px; font-weight: bold; margin-bottom: 18px; }
        .profile-details { margin-bottom: 24px; }
        .loan-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .loan-table th, .loan-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .loan-table th { background: #f7f7f7; }
        .status-active { color: #2E8B57; font-weight: bold; background: #E6FFE6; border-radius: 4px; padding: 2px 8px; }
        .status-defaulter { color: #D32F2F; font-weight: bold; background: #FFE6E6; border-radius: 4px; padding: 2px 8px; }
        .status-closed { color: #1976D2; font-weight: bold; background: #E6F0FF; border-radius: 4px; padding: 2px 8px; }
    </style>
</head>
<body>
<div class="main_bd_wrap">
<?php tp_hdr(); side_menu_content(); ?>
<div class="main-sidebar col-lg-9">
    <div class="profile-header">Loan Officer Profile</div>
    <?php if ($officer): ?>
    <div class="profile-details">
        <b>Name:</b> <?php echo htmlspecialchars($officer['first_name'].' '.$officer['last_name']); ?><br>
        <b>Job Title:</b> <?php echo htmlspecialchars($officer['job_title']); ?><br>
        <b>Branch:</b> <?php echo htmlspecialchars($officer['branch_name']); ?><br>
        <b>Contacts:</b> <?php echo htmlspecialchars($officer['contacts']); ?><br>
        <b>Email:</b> <?php echo htmlspecialchars($officer['email']); ?><br>
        <b>Residence:</b> <?php echo htmlspecialchars($officer['residence']); ?><br>
        <b>Gender:</b> <?php echo htmlspecialchars($officer['gender']); ?><br>
    </div>
    <h3>Loan Summary</h3>
    <table class="loan-table">
        <tr>
            <th>No</th>
            <th>Client Name</th>
            <th>Loan Amount</th>
            <th>Amount Paid</th>
            <th>Balance Due</th>
            <th>Interest</th>
            <th>Period Count</th>
            <th>Running Period</th>
            <th>Date Issued</th>
            <th>Guarantor</th>
            <th>Security</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php $count = 0; foreach ($loans as $loan): $count++; ?>
        <tr>
            <td><?php echo $count; ?></td>
            <td><?php echo htmlspecialchars($loan['client_name']); ?></td>
            <td><?php echo number_format($loan['loan_amount'],2); ?></td>
            <td><?php echo number_format($loan['amount_paid'],2); ?></td>
            <td><?php echo number_format($loan['balance_due'],2); ?></td>
            <td><?php echo htmlspecialchars($loan['interest']); ?></td>
            <td><?php echo htmlspecialchars($loan['period_count']); ?></td>
            <td><?php echo htmlspecialchars($loan['running_period']); ?></td>
            <td><?php echo htmlspecialchars($loan['date_entry']); ?></td>
            <td><?php echo htmlspecialchars($loan['guarantor']); ?></td>
            <td><?php echo htmlspecialchars($loan['security']); ?></td>
            <td>
                <?php
                if ($loan['status']=='00') {
                    echo '<span class="status-active"><i class="fa fa-check-circle"></i> Active</span>';
                } else if ($loan['status']=='02') {
                    echo '<span class="status-defaulter"><i class="fa fa-exclamation-triangle"></i> Defaulter</span>';
                } else {
                    echo '<span class="status-closed"><i class="fa fa-lock"></i> Closed</span>';
                }
                ?>
            </td>
            <td><a href="loan_activity.php?loan=<?php echo $loan['id']; ?>" title="View Loan Activity" style="color:#1976D2;"><i class="fa fa-link"></i></a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($loans)): ?>
        <tr><td colspan="13" style="text-align:center;">No loans found for this officer.</td></tr>
        <?php endif; ?>
        <tr style="font-weight:bold;background:#f7f7f7;">
            <td colspan="2" style="text-align:right;">Total Loan Amount:</td>
            <td><?php echo number_format($total_loan_amount,2); ?></td>
            <td><?php echo number_format($total_amount_paid,2); ?></td>
            <td><?php echo number_format($total_balance_due,2); ?></td>
            <td colspan="8"></td>
        </tr>
    </table>
    <?php else: ?>
    <div style="color:#F00;">No officer selected or officer not found.</div>
    <?php endif; ?>
</div>
<?php echo footer_sec(); ?>
</div>
</body>
</html>
