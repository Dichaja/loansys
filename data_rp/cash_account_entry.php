<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');
check_sess();
error_reporting(0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['account_id'], $_POST['type'], $_POST['amount'], $_POST['date'])) {
    $account_id = $_POST['account_id'];
    $type = $_POST['type'];
    $amount = floatval($_POST['amount']);
    $date = $_POST['date'];
    $desc = isset($_POST['description']) ? $_POST['description'] : '';
    $sql = "INSERT INTO account_transactions (account_id, type, amount, date, description) VALUES ('" . mysqli_real_escape_string($connect, $account_id) . "', '" . mysqli_real_escape_string($connect, $type) . "', '" . $amount . "', '" . mysqli_real_escape_string($connect, $date) . "', '" . mysqli_real_escape_string($connect, $desc) . "')";
    if (mysqli_query($connect, $sql)) {
        echo '<div style="color:green;text-align:center;">Transaction recorded successfully.</div>';
    } else {
        echo '<div style="color:red;text-align:center;">Error: ' . mysqli_error($connect) . '</div>';
    }
}

// Fetch accounts for dropdown
$accounts_sql = mysqli_query($connect, "SELECT a.id, a.acc_name, a.acc_branch, a.acc_no, m.name as mop_name FROM mop_accounts a, mop m WHERE m.id=a.mop ORDER BY a.acc_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cash Account Transaction Entry</title>
    <?php include('../data_files/link_docs.php'); ?>
    <style>
        .entry-form { max-width:400px; margin:32px auto; background:#fff; border-radius:8px; box-shadow:0 2px 8px #eee; padding:24px; }
        .entry-form label { display:block; margin-bottom:6px; font-weight:bold; }
        .entry-form input, .entry-form select, .entry-form textarea { width:100%; margin-bottom:16px; padding:8px; border:1px solid #ccc; border-radius:4px; }
        .entry-form button { background:#28a745; color:#fff; border:none; border-radius:4px; padding:10px 18px; font-weight:bold; cursor:pointer; }
    </style>
</head>
<body>
<div class="main_bd_wrap">
    <div class="form_header">Cash Account Transaction Entry</div>
    <form class="entry-form" method="post" action="">
        <label for="account_id">Account</label>
        <select name="account_id" id="account_id" required>
            <option value="">Select Account</option>
            <?php while($acc = mysqli_fetch_assoc($accounts_sql)) { ?>
                <option value="<?php echo $acc['id']; ?>">
                    <?php echo htmlspecialchars($acc['acc_name']) . ' - ' . htmlspecialchars($acc['acc_branch']) . ' (' . htmlspecialchars($acc['mop_name']) . ')'; ?>
                </option>
            <?php } ?>
        </select>
        <label for="type">Transaction Type</label>
        <select name="type" id="type" required>
            <option value="deposit">Deposit</option>
            <option value="withdrawal">Withdrawal</option>
        </select>
        <label for="amount">Amount</label>
        <input type="number" name="amount" id="amount" min="0.01" step="0.01" required />
        <label for="date">Date</label>
        <input type="date" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" required />
        <label for="description">Description</label>
        <textarea name="description" id="description" rows="2"></textarea>
        <button type="submit">Submit Transaction</button>
    </form>
</div>
</body>
</html>
