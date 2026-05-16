<?php

ini_set('display_errors',1);
ini_set('log_errors',1);
ini_set('error_log',__DIR__.'/error_log');
error_reporting(E_ALL);

require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess();

function sql_check($result,$connect){
    if(!$result){
        die("SQL Error : ".mysqli_error($connect));
    }
}

/* -----------------------------
FETCH ACCOUNTS
-----------------------------*/

$accounts=[];
$q=mysqli_query($connect,"SELECT a.id,a.acc_name,a.acc_branch,a.acc_no,m.name as mop_name
FROM mop_accounts a
JOIN mop m ON m.id=a.mop
ORDER BY a.acc_name");

sql_check($q,$connect);

while($row=mysqli_fetch_assoc($q)){
    $accounts[$row['id']]=$row;
}

/* -----------------------------
FILTERS
-----------------------------*/

$selected_account=$_GET['account_id'] ?? 'all';
$show_all = ($selected_account=='all');

$date_from=$_GET['date_from'] ?? '';
$date_to=$_GET['date_to'] ?? '';

$transactions=[];

// Get selected account ID if not "all"
$selected_acc_id = '';
if (!$show_all && isset($accounts[$selected_account])) {
    $selected_acc_id = mysqli_real_escape_string($connect, $accounts[$selected_account]['id']);
}

/* -----------------------------
ACCOUNT TRANSACTIONS
-----------------------------*/

$tx_query="SELECT * FROM account_transactions WHERE 1=1";

if(!$show_all && $selected_acc_id){
    $tx_query.=" AND (acc_to='$selected_acc_id' OR accFrom='$selected_acc_id')";
}

if($date_from){
    $df=mysqli_real_escape_string($connect,$date_from);
    $tx_query.=" AND date>='$df'";
}

if($date_to){
    $dt=mysqli_real_escape_string($connect,$date_to);
    $tx_query.=" AND date<='$dt'";
}

$tx_query.=" ORDER BY date ASC,id ASC";

$q=mysqli_query($connect,$tx_query);
sql_check($q,$connect);

while($row=mysqli_fetch_assoc($q)){

    $transactions[]=[
        'date'=>$row['date'],
        'type'=>ucfirst($row['trans_type']),
        'description'=>$row['description'],
        'ref'=>$row['id'],
        'debit'=>$row['trans_type']=='withdrawal' ? $row['amount'] : 0,
        'credit'=>$row['trans_type']=='deposit' ? $row['amount'] : 0,
        'account_id'=>$selected_acc_id // Store for debugging if needed
    ];
}


/* -----------------------------
CLIENT SAVINGS DEPOSITS
-----------------------------*/

$q="SELECT s.*,c.first_name,c.last_name
FROM client_savings s
LEFT JOIN clients c ON c.id=s.client
WHERE 1=1 ";

if(!$show_all && $selected_acc_id){
    $q .= " AND s.acc_to = '$selected_acc_id'";
}

if($date_from){
    $df=mysqli_real_escape_string($connect,$date_from);
    $q.=" AND s.depo_date>='$df'";
}
if($date_to){
    $dt=mysqli_real_escape_string($connect,$date_to);
    $q.=" AND s.depo_date<='$dt'";
}

$res=mysqli_query($connect,$q);
sql_check($res,$connect);

while($row=mysqli_fetch_assoc($res)){

    $client=ucfirst($row['first_name']).' '.ucfirst($row['last_name']);

    $transactions[]=[
        'date'=>$row['depo_date'],
        'type'=>'Savings Deposit',
        'description'=>"Client Deposit - $client",
        'ref'=>$row['id'],
        'debit'=>0,
        'credit'=>$row['amount_depo']
    ];

}


/* -----------------------------
EXPENSES
-----------------------------*/

$q="SELECT e.*, ea.item as expense_item
FROM expense e
LEFT JOIN expense_items ea ON ea.id = e.expense
WHERE 1=1";

if(!$show_all && $selected_acc_id){
    $q .= " AND e.acc_from = '$selected_acc_id'";
}

if($date_from){
    $df=mysqli_real_escape_string($connect,$date_from);
    $q.=" AND e.entry_date>='$df'";
}
if($date_to){
    $dt=mysqli_real_escape_string($connect,$date_to);
    $q.=" AND e.entry_date<='$dt'";
}

$res=mysqli_query($connect,$q);
sql_check($res,$connect);

while($row=mysqli_fetch_assoc($res)){

    $transactions[]=[
        'date'=>$row['entry_date'],
        'type'=>'Expense',
        'description'=>$row['expense_item'],
        'ref'=>$row['id'],
        'debit'=>$row['amount']*$row['qty'],
        'credit'=>0
    ];

}


/* -----------------------------
LOAN PAYMENTS
-----------------------------*/

$q="SELECT lp.* FROM loan_payments lp
WHERE 1=1";

if(!$show_all && $selected_acc_id){
    $q .= " AND lp.accTo = '$selected_acc_id'";
}

if($date_from){
    $df=mysqli_real_escape_string($connect,$date_from);
    $q.=" AND lp.pay_date>='$df'";
}
if($date_to){
    $dt=mysqli_real_escape_string($connect,$date_to);
    $q.=" AND lp.pay_date<='$dt'";
}

$res=mysqli_query($connect,$q);
sql_check($res,$connect);

while($row=mysqli_fetch_assoc($res)){

    $transactions[]=[
        'date'=>$row['pay_date'],
        'type'=>'Loan Payment',
        'description'=>'Loan Repayment',
        'ref'=>$row['receipt_no'],
        'debit'=>0,
        'credit'=>$row['amount_paid']
    ];

}


/* -----------------------------
CLIENT WITHDRAWALS
-----------------------------*/

$q="SELECT cw.*,c.first_name,c.last_name
FROM client_withdraw cw
LEFT JOIN clients c ON c.id=cw.client
WHERE 1=1";

if(!$show_all && $selected_acc_id){
    $q .= " AND cw.acc_to = '$selected_acc_id'";
}

if($date_from){
    $df=mysqli_real_escape_string($connect,$date_from);
    $q.=" AND cw.withdraw_date>='$df'";
}
if($date_to){
    $dt=mysqli_real_escape_string($connect,$date_to);
    $q.=" AND cw.withdraw_date<='$dt'";
}

$res=mysqli_query($connect,$q);
sql_check($res,$connect);

while($row=mysqli_fetch_assoc($res)){

    $client=ucfirst($row['first_name']).' '.ucfirst($row['last_name']);

    $transactions[]=[
        'date'=>$row['withdraw_date'],
        'type'=>'Client Withdrawal',
        'description'=>"Withdrawal - $client",
        'ref'=>$row['id'],
        'debit'=>$row['amount_withdraw'],
        'credit'=>0
    ];

}


/* -----------------------------
SORT TRANSACTIONS BY DATE AND ID
-----------------------------*/

// First sort by date
usort($transactions, function($b, $a) {
    // Compare dates
    $dateCompare = strtotime($a['date']) <=> strtotime($b['date']);
    
    // If dates are equal, maintain original order (by ID/ref)
    // This ensures transactions on the same day are processed in correct order
    if ($dateCompare == 0) {
        // For same date, sort by reference number if numeric
        if (is_numeric($a['ref']) && is_numeric($b['ref'])) {
            return $a['ref'] <=> $b['ref'];
        }
        return 0;
    }
    
    return $dateCompare;
});


/* -----------------------------
BALANCES - Calculate with proper running balance
-----------------------------*/

$running_balance = 0;
$total_deposit = 0;
$total_withdrawal = 0;

// Get opening balance before the date range if date_from is specified
if ($date_from && !$show_all && $selected_acc_id) {
    // Query to get balance before the start date
    $opening_query = "
        SELECT 
            (SELECT IFNULL(SUM(amount), 0) FROM account_transactions 
             WHERE trans_type='deposit' AND date < '$date_from' 
             AND (acc_to='$selected_acc_id' OR accFrom='$selected_acc_id')) as deposits_before,
            (SELECT IFNULL(SUM(amount), 0) FROM account_transactions 
             WHERE trans_type='withdrawal' AND date < '$date_from' 
             AND (acc_to='$selected_acc_id' OR accFrom='$selected_acc_id')) as withdrawals_before
    ";
    
    $res = mysqli_query($connect, $opening_query);
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $running_balance = $row['deposits_before'] - $row['withdrawals_before'];
    }
}

// Calculate running balance for all transactions in the date range
foreach($transactions as &$tx) {
    $running_balance += $tx['credit'] - $tx['debit'];
    $tx['balance'] = $running_balance;
    
    $total_deposit += $tx['credit'];
    $total_withdrawal += $tx['debit'];
}

$closing_balance = $running_balance;

// For "All Accounts" view, we need a different approach
if ($show_all) {
    // Reset calculations for all accounts
    $running_balance = 0;
    $total_deposit = 0;
    $total_withdrawal = 0;
    
    foreach($transactions as &$tx) {
        $running_balance += $tx['credit'] - $tx['debit'];
        $tx['balance'] = $running_balance;
        
        $total_deposit += $tx['credit'];
        $total_withdrawal += $tx['debit'];
    }
    $closing_balance = $running_balance;
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Cash Account Statement</title>

<?php include('../data_files/link_docs.php'); ?>

<style>

.statement-table{
width:100%;
border-collapse:collapse;
}

.statement-table th,
.statement-table td{
border:1px solid #ccc;
padding:8px;
}

.statement-table th{
background:#f5f5f5;
}
.statement-table { width:100%; border-collapse:collapse; margin-bottom:24px; }
        .statement-table th, .statement-table td { border:1px solid #ccc; padding:8px; text-align:left; }
        .statement-table th { background:#f7f7f7; }
        .filter-form { display:flex; align-items:center; gap:12px; margin-bottom:18px; flex-wrap: wrap; }
        .filter-form input, .filter-form select { padding:6px; margin-right:8px; }
        .summary { margin:18px 0; font-weight:bold; }
        .account-group { margin-top:32px; }
        .text-right { text-align: right; }
</style>

</head>

<body>

<div class="main_bd_wrap">

<?php
tp_hdr();
side_menu_content();
?>

<div class="main-sidebar col-lg-9">

<h2>Cash Account Statement</h2>
<form class="filter-form" method="get" action="">
        <label for="account_id">Account:</label>
        <select name="account_id" id="account_id">
            <option value="all">All Accounts</option>
            <?php foreach($accounts as $acc_id => $acc) { ?>
                <option value="<?php echo $acc_id; ?>" <?php if($selected_account == $acc_id) echo 'selected'; ?>><?php echo htmlspecialchars($acc['acc_name']) . ' - ' . htmlspecialchars($acc['acc_branch']) . ' (' . htmlspecialchars($acc['mop_name']) . ')'; ?></option>
            <?php } ?>
        </select>
        <label for="date_from">From:</label>
        <input type="date" name="date_from" id="date_from" value="<?php echo htmlspecialchars($date_from); ?>" />
        <label for="date_to">To:</label>
        <input type="date" name="date_to" id="date_to" value="<?php echo htmlspecialchars($date_to); ?>" />
        <button type="submit" class="button_search">Filter</button>
    </form>
    
    <?php if (!$show_all && $selected_acc_id): ?>
    <div style="margin: 10px 0; padding: 10px; background: #f0f0f0; border-radius: 5px;">
        <strong>Selected Account:</strong> <?php echo htmlspecialchars($accounts[$selected_account]['acc_name']); ?> - 
        <?php echo htmlspecialchars($accounts[$selected_account]['acc_branch']); ?> 
        (<?php echo htmlspecialchars($accounts[$selected_account]['acc_no']); ?>)
    </div>
    <?php endif; ?>

<div class="summary">

Total Deposits :
<b><?php echo number_format($total_deposit,2); ?></b>

&nbsp;&nbsp;&nbsp;&nbsp;

Total Withdrawals :
<b><?php echo number_format($total_withdrawal,2); ?></b>

&nbsp;&nbsp;&nbsp;&nbsp;

Closing Balance :
<b><?php echo number_format($closing_balance,2); ?></b>

</div>

<br>

<table class="statement-table">

<tr>
<th>Date</th>
<th>Type</th>
<th>Description</th>
<th>Reference</th>
<th class="text-right">Debit (₦)</th>
<th class="text-right">Credit (₦)</th>
<th class="text-right">Balance (₦)</th>
</tr>

<?php if(empty($transactions)): ?>
<tr>
<td colspan="7" style="text-align:center;">No transactions found for the selected criteria</td>
</tr>
<?php else: ?>
<?php foreach($transactions as $tx){ ?>
<tr>
<td><?php echo $tx['date']; ?></td>
<td><?php echo $tx['type']; ?></td>
<td><?php echo htmlspecialchars($tx['description']); ?></td>
<td><?php echo $tx['ref']; ?></td>
<td class="text-right"><?php echo $tx['debit'] ? number_format($tx['debit'],2) : '-'; ?></td>
<td class="text-right"><?php echo $tx['credit'] ? number_format($tx['credit'],2) : '-'; ?></td>
<td class="text-right"><?php echo number_format($tx['balance'],2); ?></td>
</tr>
<?php } ?>
<?php endif; ?>

</table>

<?php if (!empty($transactions)): ?>
<div style="margin-top: 20px; font-size: 0.9em; color: #666;">
    <p><strong>Note:</strong> Balance is calculated as: Opening Balance + Total Credits - Total Debits</p>
    <p>Total Transactions: <?php echo count($transactions); ?></p>
</div>
<?php endif; ?>

</div>

<?php echo footer_sec(); ?>

</div>

</body>
</html>