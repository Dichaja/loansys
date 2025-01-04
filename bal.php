<?php
// Sample account data
$accountData = [
    [
        "account_number" => "001",
        "account_name" => "John Doe",
        "previous_balance" => 1000,
        "transactions" => [
            ["date" => "2023-06-01", "description" => "Deposit", "amount" => 5000],
            ["date" => "2023-06-05", "description" => "Withdrawal", "amount" => -2000],
            ["date" => "2023-06-10", "description" => "Deposit", "amount" => 3000],
            ["date" => "2023-06-15", "description" => "Withdrawal", "amount" => -1500],
        ],
    ],
    // Add more account data as needed
];

// Sample bank transactions
$bankTransactions = [
    ["date" => "2023-06-01", "description" => "Deposit", "amount" => 5000],
    ["date" => "2023-06-05", "description" => "Withdrawal", "amount" => -2000],
    ["date" => "2023-06-08", "description" => "Bank Fee", "amount" => -50],
    ["date" => "2023-06-12", "description" => "Deposit", "amount" => 1000],
    ["date" => "2023-06-15", "description" => "Withdrawal", "amount" => -1500],
];

// Function to generate bank statement for an account and perform bank reconciliation
function generateBankStatement($accountNumber, $accountData, $bankTransactions)
{
    $statement = "Bank Statement<br/>";
    $statement .= "Account Number: $accountNumber<br/><br/>";

    $previousBalance = 0;
    foreach ($accountData as $account) {
        if ($account['account_number'] === $accountNumber) {
            $previousBalance = $account['previous_balance'];
            $statement .= "Previous Balance: $previousBalance<br/><br/>";
            break;
        }
    }

    $statement .= "Date\t\tDescription\tAmount<br/>";

    $reconciledTransactions = [];

    foreach ($accountData as $account) {
        if ($account['account_number'] === $accountNumber) {
            foreach ($account['transactions'] as $transaction) {
                $date = $transaction['date'];
                $description = $transaction['description'];
                $amount = $transaction['amount'];
                $statement .= "$date\t$description\t$amount<br/>";

                // Check if the transaction exists in the bank transactions
                $matchingTransaction = array_filter($bankTransactions, function ($bankTransaction) use ($date, $description, $amount) {
                    return $bankTransaction['date'] === $date &&
                        $bankTransaction['description'] === $description &&
                        $bankTransaction['amount'] === $amount;
                });

                // If a matching transaction is found, mark it as reconciled
                if (!empty($matchingTransaction)) {
                    $reconciledTransactions[] = $matchingTransaction;
                }
            }
            break;
        }
    }

    $totalBalance = $previousBalance + array_sum(array_column($account['transactions'], 'amount'));
    $statement .= "<br/>Total Balance: $totalBalance<br/>";

    // Perform bank reconciliation
    $reconciledCount = count($reconciledTransactions);
    $unreconciledCount = count($bankTransactions) - $reconciledCount;
    $statement .= "<br/>Reconciled Transactions: $reconciledCount<br/>";
    $statement .= "Unreconciled Transactions: $unreconciledCount<br/>";

    return $statement;
}

// Generate bank statement and perform bank reconciliation for account number "001"
$accountNumber = "001";
$bankStatement = generateBankStatement($accountNumber, $accountData, $bankTransactions);

// Display the bank statement
echo $bankStatement;
?>
