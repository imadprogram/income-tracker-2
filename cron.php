<?php
$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "smart_wallet_2";

try {
    $connect = mysqli_connect($hostname, $username, $password, $dbname);
} catch (mysqli_sql_exception) {
    echo "failed connecting !!!";
}

$current_day = date('j');    
$today_date = date('Y-m-d');   

echo "--- Cron Job Running: $today_date ---\n";

$query = "SELECT * FROM recurring_transactions WHERE day_of_month = $current_day AND (last_run_date IS NULL OR last_run_date != '$today_date')";

$result = mysqli_query($connect, $query);

if (mysqli_num_rows($result) > 0) {
    $count = 0;
    while ($rule = mysqli_fetch_assoc($result)) {

        $insert_history = "INSERT INTO transactions (user_id, card_id, description, amount, date, type) VALUES ({$rule['user_id']}, {$rule['card_id']}, '{$rule['description']}', {$rule['amount']}, '$today_date', '{$rule['type']}')";

        if (mysqli_query($connect, $insert_history)) {

            $update_run = "UPDATE recurring_transactions SET last_run_date = '$today_date' WHERE id = {$rule['id']}";
            mysqli_query($connect, $update_run);

            echo "[SUCCESS] Processed: {$rule['description']} ($$rule[amount]) for User ID {$rule['user_id']}\n";
            $count++;
        } else {
            echo "[ERROR] Could not process Rule ID {$rule['id']}: " . mysqli_error($connect) . "\n";
        }
    }
    echo "--- Finished. Total processed: $count ---\n";
} else {
    echo "--- No recurring transactions found for today ($current_day). ---\n";
}

mysqli_close($connect);
