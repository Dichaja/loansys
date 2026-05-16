<?php

//ini_set('display_errors',1);
//ini_set('log_errors',1);
//ini_set('error_log',__DIR__.'/error_log');
//error_reporting(E_ALL);

require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess();

function sql_check($result,$connect){
    if(!$result){
        die("SQL Errors : ".mysqli_error($connect) . ' in ' . $result);
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


/* -----------------------------
ACCOUNT TRANSACTIONS
-----------------------------*/

$tx_query="SELECT * FROM account_transactions WHERE 1=1";

if(!$show_all && isset($accounts[$selected_account])){

    $acc_no=mysqli_real_escape_string($connect,$accounts[$selected_account]['acc_no']);

    $tx_query.=" AND (acc_to='$acc_no' OR accFrom='$acc_no')";
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
        'credit'=>$row['trans_type']=='deposit' ? $row['amount'] : 0
    ];
}


/* -----------------------------
CLIENT SAVINGS DEPOSITS
-----------------------------*/

$q="SELECT s.*,c.first_name,c.last_name
FROM client_savings s
LEFT JOIN clients c ON c.id=s.client
WHERE 1=1";

if($date_from) $q.=" AND depo_date>='$date_from'";
if($date_to) $q.=" AND depo_date<='$date_to'";

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

$q="SELECT * FROM expense WHERE 1=1";

if($date_from) $q.=" AND entry_date>='$date_from'";
if($date_to) $q.=" AND entry_date<='$date_to'";

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

$q="SELECT * FROM loan_payments WHERE 1=1";

if($date_from) $q.=" AND pay_date>='$date_from'";
if($date_to) $q.=" AND pay_date<='$date_to'";

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
LEFT JOIN clients c ON c.id=cw.client";

if($date_from) $q.=" WHERE withdraw_date>='$date_from'";
if($date_to) $q.=" AND withdraw_date<='$date_to'";

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
SORT TRANSACTIONS
-----------------------------*/

usort($transactions,function($a,$b){
return strtotime($a['date'])<=>strtotime($b['date']);
});


/* -----------------------------
BALANCES
-----------------------------*/

$running_balance=0;
$total_deposit=0;
$total_withdrawal=0;

foreach($transactions as &$tx){

$running_balance += $tx['credit'] - $tx['debit'];

$tx['balance']=$running_balance;

$total_deposit+=$tx['credit'];
$total_withdrawal+=$tx['debit'];

}

$closing_balance=$running_balance;

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

<div>

Total Deposits :
<b><?php echo number_format($total_deposit,2); ?></b>

&nbsp;&nbsp;

Total Withdrawals :
<b><?php echo number_format($total_withdrawal,2); ?></b>

&nbsp;&nbsp;

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
<th>Debit</th>
<th>Credit</th>
<th>Balance</th>
</tr>

<?php foreach($transactions as $tx){ ?>

<tr>

<td><?php echo $tx['date']; ?></td>

<td><?php echo $tx['type']; ?></td>

<td><?php echo htmlspecialchars($tx['description']); ?></td>

<td><?php echo $tx['ref']; ?></td>

<td><?php echo $tx['debit'] ? number_format($tx['debit'],2) : ''; ?></td>

<td><?php echo $tx['credit'] ? number_format($tx['credit'],2) : ''; ?></td>

<td><?php echo number_format($tx['balance'],2); ?></td>

</tr>

<?php } ?>

</table>

</div>

<?php echo footer_sec(); ?>

</div>

</body>
</html>