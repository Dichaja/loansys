<?php
session_start();
error_reporting(E_ALL ^ E_NOTICE);
require("../xsert/connect.php");
require_once('../data_files/sys_function.php');
require_once('../data_files/page_settings.php');

check_sess(); //check user login

// Filters
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$member = isset($_GET['member']) ? $_GET['member'] : '';

// Build query

$where = [];
if($date_from) $where[] = "depo_date >= '".mysqli_real_escape_string($connect, $date_from)."'";
if($date_to) $where[] = "depo_date <= '".mysqli_real_escape_string($connect, $date_to)."'";
if($member) $where[] = "client = '".mysqli_real_escape_string($connect, $member)."'";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$savings_sql = mysqli_query($connect, "SELECT s.id, s.client, s.amount_depo, m.name, s.acc_to, s.acc_from, s.depo_date, p.acc_no FROM client_savings s LEFT JOIN mop m ON s.mop = m.id, mop_accounts p WHERE p.id = s.acc_to $where_sql ORDER BY s.depo_date DESC");
$withdraw_sql = mysqli_query($connect, "SELECT w.id, w.client, w.amount_withdraw, w.mop, w.acc_to, w.acc_from, w.withdraw_date, w.remarks FROM client_withdraw w ".($where ? str_replace(['depo_date','amount_depo'], ['withdraw_date','amount_withdraw'], $where_sql) : '')." ORDER BY w.withdraw_date DESC");
$total_savings_sql = mysqli_query($connect, "SELECT SUM(amount_depo) as total FROM client_savings $where_sql");
$total_withdraw_sql = mysqli_query($connect, "SELECT SUM(amount_withdraw) as total FROM client_withdraw ".($where ? str_replace(['depo_date','amount_depo'], ['withdraw_date','amount_withdraw'], $where_sql) : ''));
$total_savings_row = mysqli_fetch_array($total_savings_sql);
$total_withdraw_row = mysqli_fetch_array($total_withdraw_sql);
$total_savings = isset($total_savings_row['total']) ? $total_savings_row['total'] : 0;
$total_withdraw = isset($total_withdraw_row['total']) ? $total_withdraw_row['total'] : 0;

// Fetch client names for dropdown
$clients = mysqli_query($connect, "SELECT id, first_name, last_name FROM clients ORDER BY first_name, last_name");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Savings Report</title>
  <?php include('../data_files/link_docs.php') ?>
  <style>
    .report-table { width:100%; border-collapse:collapse; margin-top:18px; }
    .report-table th, .report-table td { border:1px solid #ccc; padding:8px; text-align:left; }
    .report-table th { background:#f7f7f7; }
    .filter-form { margin-bottom:18px; }
    .filter-form input, .filter-form select { padding:6px; margin-right:8px; }
    .total-row { font-weight:bold; background:#f7f7f7; }
  </style>
</head>
<body>
<div class="main_bd_wrap">
  <?php tp_hdr(); side_menu_content(); ?>
  <div class="main-sidebar col-lg-9">
    <div class="main_bd_wrap">
      <h2>Savings Report</h2>
      <form class="filter-form" method="get" action="savings_report.php">
        <label>Date From: <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" /></label>
        <label>Date To: <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" /></label>
        <label>Member:
          <select name="member">
            <option value="">All</option>
            <?php while($row = mysqli_fetch_array($clients)) { ?>
              <option value="<?php echo $row['id']; ?>" <?php if($member==$row['id']) echo 'selected'; ?>><?php echo ucfirst($row['first_name']).' '.ucfirst($row['last_name']); ?></option>
            <?php } ?>
          </select>
        </label>
        <button type="submit" class="button_search">Filter</button>
      </form>
      <table class="report-table">
        <tr>
          <th>Client</th>
          <th>Date</th>
          <th>Deposit</th>
          <th>Withdraw</th>
          <th>Mode of Pay</th>
          <th>Account To</th>
          <th>Account From</th>
          <th>Type</th>
        </tr>
        <?php
        // Merge deposits and withdrawals
        $rows = [];
        while($saving = mysqli_fetch_array($savings_sql)) {
          $client_sql = mysqli_query($connect, "SELECT first_name, last_name FROM clients WHERE id='".$saving['client']."'");
          $client_row = mysqli_fetch_array($client_sql);
          $client_name = $client_row ? ucfirst($client_row['first_name']).' '.ucfirst($client_row['last_name']) : $saving['client'];
          $rows[] = [
            'client' => $client_name,
            'date' => $saving['depo_date'],
            'deposit' => $saving['amount_depo'],
            'withdraw' => 0,
            'mop' => $saving['name'],
            'acc_to' => $saving['acc_to'],
            'acc_from' => $saving['acc_from'],
            'type' => 'Deposit'
          ];
        }
        while($withdraw = mysqli_fetch_array($withdraw_sql)) {
          $client_sql = mysqli_query($connect, "SELECT first_name, last_name FROM clients WHERE id='".$withdraw['client']."'");
          $client_row = mysqli_fetch_array($client_sql);
          $client_name = $client_row ? ucfirst($client_row['first_name']).' '.ucfirst($client_row['last_name']) : $withdraw['client'];
          $rows[] = [
            'client' => $client_name,
            'date' => $withdraw['withdraw_date'],
            'deposit' => 0,
            'withdraw' => $withdraw['amount_withdraw'],
            'mop' => $withdraw['mop'],
            'acc_to' => $withdraw['acc_to'],
            'acc_from' => $withdraw['acc_from'],
            'type' => $withdraw['remarks'] ? $withdraw['remarks'] : 'Withdraw'  
          ];
        }
        // Sort by date descending
        usort($rows, function($a, $b) {
          return strtotime($b['date']) - strtotime($a['date']);
        });
        foreach($rows as $row) {
        ?>
        <tr>
          <td><?php echo $row['client']; ?></td>
          <td><?php echo date('d-m-Y H:i', strtotime($row['date'])); ?></td>
          <td><?php echo $row['deposit'] ? number_format($row['deposit']) : ''; ?></td>
          <td><?php echo $row['withdraw'] ? number_format($row['withdraw']) : ''; ?></td>
          <td><?php echo htmlspecialchars($row['mop']); ?></td>
          <td><?php echo htmlspecialchars($row['acc_to']); ?></td>
          <td><?php echo htmlspecialchars($row['acc_from']); ?></td>
          <td><?php echo $row['type']; ?></td>
        </tr>
        <?php } ?>
        <tr class="total-row">
          <td colspan="2">Total Balance</td>
          <td colspan="6"><?php echo number_format($total_savings - $total_withdraw); ?></td>
        </tr>
      </table>
    </div>
  </div>
  <?php echo footer_sec(); ?>
</div>
</body>
</html>
