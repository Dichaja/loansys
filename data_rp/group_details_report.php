<?php
require_once('../xsert/connect.php');
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
check_sess();
error_reporting(0);

// Filter form handling
$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : '';
$group_name = isset($_GET['group_name']) ? $_GET['group_name'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build group filter

$group_filter = '';
if ($group_id) {
    $group_filter = "id='" . mysqli_real_escape_string($connect, $group_id) . "'";
} else if ($group_name) {
    $group_filter = "group_name LIKE '%" . mysqli_real_escape_string($connect, $group_name) . "%'";
}
if (!$group_filter) {
    echo 'No group selected.';
    exit;
}

// Fetch group details
$group_sql = "SELECT * FROM groups WHERE $group_filter";
$group_result = mysqli_query($connect, $group_sql);
$group = mysqli_fetch_assoc($group_result);
if (!$group) {
    echo 'Group not found.';
    exit;
}

// Fetch group members
$members_sql = "SELECT * FROM clients WHERE group_id='" . $group['id'] . "' AND status='01'";
$members_result = mysqli_query($connect, $members_sql);

// ...existing code...
$total_loans = 0;
$total_paid = 0;
$total_balance = 0;
$total_defaulting = 0;
$total_running = 0;
$member_rows = [];

while ($member = mysqli_fetch_assoc($members_result)) {
    $client_id = $member['id'];
    // Loans for this client, filter by date if set
    $loan_sql = "SELECT * FROM loan_entries WHERE client='$client_id'";
    if ($date_from) {
        $loan_sql .= " AND date_entry >= '" . mysqli_real_escape_string($connect, $date_from) . "'";
    }
    if ($date_to) {
        $loan_sql .= " AND date_entry <= '" . mysqli_real_escape_string($connect, $date_to) . "'";
    }
    $loans_result = mysqli_query($connect, $loan_sql);
    $member_loans = 0;
    $member_paid = 0;
    $member_balance = 0;
    $member_defaulting = 0;
    $member_running = 0;
    while ($loan = mysqli_fetch_assoc($loans_result)) {
        $member_loans += $loan['loan_amount'];
        // Payments for this loan
        $pay_sql = "SELECT SUM(amount_paid) as paid FROM loan_payments WHERE loan='" . $loan['id'] . "'";
        $pay_result = mysqli_query($connect, $pay_sql);
        $pay_row = mysqli_fetch_assoc($pay_result);
        $paid = $pay_row['paid'] ? $pay_row['paid'] : 0;
        $member_paid += $paid;
        $balance = $loan['loan_amount'] - $paid;
        $member_balance += $balance;
        // Defaulting: status != '01' (running)
        if ($loan['status'] != '01') {
            $member_defaulting++;
        }
        // Running: status == '01'
        if ($loan['status'] == '01') {
            $member_running++;
        }
    }
    $total_loans += $member_loans;
    $total_paid += $member_paid;
    $total_balance += $member_balance;
    $total_defaulting += $member_defaulting;
    $total_running += $member_running;
    $member_rows[] = [
        'member' => $member,
        'loans' => $member_loans,
        'paid' => $member_paid,
        'balance' => $member_balance,
        'defaulting' => $member_defaulting,
        'running' => $member_running
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Group Report - <?php echo htmlspecialchars($group['group_name']); ?></title>
    <?php include('../data_files/link_docs.php'); ?>
</head>
<body>
<div class="main_bd_wrap">
    <?php tp_hdr(); side_menu_content(); ?>
    <div class="main-sidebar col-lg-9">
        <div class="report_wrap">
            <div class="form_header">Group Report: <?php echo htmlspecialchars($group['group_name']); ?></div>
            <form method="get" action="group_details_report.php" style="margin-bottom:18px;display:flex;gap:16px;align-items:center;">
                <!--<input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group['id']); ?>" />-->
                <label>Group Name: <input type="text" name="group_name" value="<?php echo htmlspecialchars($group_name); ?>" class="text-input" placeholder="Enter group name" /></label>
                <label>Date From: <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" class="text-input" /></label>
                <label>Date To: <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" class="text-input" /></label>
                <button type="submit" class="button_search">Filter</button>
            </form>
            <div style="margin-bottom:20px;">
                <b>Type:</b> <?php echo htmlspecialchars($group['group_type']); ?> | 
                <b>Date Created:</b> <?php echo htmlspecialchars($group['date_created']); ?> | 
                <b>Village:</b> <?php echo htmlspecialchars($group['village']); ?> | 
                <b>Meeting Day:</b> <?php echo htmlspecialchars($group['meeting_day']); ?> | 
                <b>Contact:</b> <?php echo htmlspecialchars($group['contact_number']); ?> | 
                <b>Contact Person:</b> <?php echo htmlspecialchars($group['contact_person']); ?>
            </div>
            <table class="report_display" width="100%" cellpadding="5" cellspacing="0">
                <tr style="text-align: left;">
                    <th>#</th>
                    <th>Member</th>
                    <th>Total Loans Disbursed</th>
                    <th>Amount Paid</th>
                    <th>Balance</th>
                    <th>Defaulting Loans</th>
                    <th>Running Loans</th>
                </tr>
                <?php
                $idx = 1;
                foreach ($member_rows as $row) {
                    $member = $row['member'];
                    echo '<tr>';
                    echo '<td>' . ($idx++) . '</td>';
                    echo '<td style="text-transform:capitalize;"><a href="../data_rp/loan_activity.php?client_id='.$row['member']['id'].'" style="color:#145FA7;font-weight:bold;">' . htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) . '</td>';
                    echo '<td>' . number_format($row['loans'], 2) . '</td>';
                    echo '<td>' . number_format($row['paid'], 2) . '</td>';
                    echo '<td>' . number_format($row['balance'], 2) . '</td>';
                    echo '<td>' . $row['defaulting'] . '</td>';
                    echo '<td>' . $row['running'] . '</td>';
                    echo '</tr>';
                }
                ?>
                <tr style="font-weight:bold;background:#f0f0f0;">
                    <td colspan="2">Totals</td>
                    <td><?php echo number_format($total_loans, 2); ?></td>
                    <td><?php echo number_format($total_paid, 2); ?></td>
                    <td><?php echo number_format($total_balance, 2); ?></td>
                    <td><?php echo $total_defaulting; ?></td>
                    <td><?php echo $total_running; ?></td>
                </tr>
            </table>
        </div>
        <?php echo footer_sec(); ?>
    </div>
</div>
</body>
</html>
