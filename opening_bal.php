<?php
// Opening Accounts Report
function generateOpeningAccountsReport() {
  // Retrieve opening account data from database or any data source
  $openingAccounts = [
    ["account_number" => "001", "account_name" => "Savings Account", "balance" => 5000],
    ["account_number" => "002", "account_name" => "Checking Account", "balance" => 10000],
    ["account_number" => "003", "account_name" => "Investment Account", "balance" => 25000],
  ];

  // Generate the report
  $report = "Opening Accounts Report\n\n";
  $report .= "Account Number\tAccount Name\tBalance\n";
  foreach ($openingAccounts as $account) {
    $report .= $account['account_number'] . "\t\t" . $account['account_name'] . "\t" . $account['balance'] . "<br/>";
  }

  return $report;
}

// Closing Accounts Report
function generateClosingAccountsReport() {
  // Retrieve closing account data from database or any data source
  $closingAccounts = [
    ["account_number" => "001", "account_name" => "Savings Account", "balance" => 7000],
    ["account_number" => "002", "account_name" => "Checking Account", "balance" => 12000],
    ["account_number" => "003", "account_name" => "Investment Account", "balance" => 28000],
  ];

  // Generate the report
  $report = "Closing Accounts Report\n\n";
  $report .= "Account Number\tAccount Name\tBalance\n";
  foreach ($closingAccounts as $account) {
    $report .= $account['account_number'] . "\t\t" . $account['account_name'] . "\t" . $account['balance'] . "<br/>";
  }

  return $report;
}

// Generate and display the reports
$openingReport = generateOpeningAccountsReport();
$closingReport = generateClosingAccountsReport();

echo $openingReport;
echo "\n\n";
echo $closingReport;

?>
