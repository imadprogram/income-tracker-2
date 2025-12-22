<?php
include("connect.php");
?>
<?php
session_start();



if (!isset($_SESSION['user-id'])) {
    header('Location: index.php');
}


// get the name of the user
$sql = "SELECT name FROM users WHERE id = {$_SESSION['user-id']}";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);
$the_name = $row['name'];
// echo $the_name;

// first card infos
$first_info = mysqli_query($connect, "SELECT * FROM cards WHERE user_id = {$_SESSION['user-id']} AND card_index = 1");
$first_row = mysqli_fetch_assoc($first_info);

// card balance
$income_balance = mysqli_query($connect, "SELECT sum(amount) AS sum FROM transactions WHERE user_id = {$_SESSION['user-id']} AND type = 'income'");
$income_sum = mysqli_fetch_assoc($income_balance);

$expense_balance = mysqli_query($connect, "SELECT sum(amount) AS sum FROM transactions WHERE user_id = {$_SESSION['user-id']} AND type = 'expense'");
$expense_sum = mysqli_fetch_assoc($expense_balance);
/////////////

// UPDATE THE CATEGORY LIMIT
if (isset($_POST['save_budget'])) {
    $budget_category = $_POST['budget_category'];
    $budget_amount = $_POST['budget_amount'];
    $user_id = $_SESSION['user-id'];

    $check_limit = mysqli_query($connect, "SELECT amount FROM budgets WHERE user_id = $user_id AND title = '$budget_category'");

    if (mysqli_num_rows($check_limit) > 0) {
        mysqli_query($connect, "UPDATE budgets SET amount = $budget_amount WHERE title = '$budget_category' AND  user_id = $user_id");
        echo "<script>alert('the limit is updated !')</script>";
    } else {
        mysqli_query($connect, "INSERT INTO budgets(user_id , title , amount) VALUES($user_id , '$budget_category' , $budget_amount)");
        echo "<script>alert('the limit is set !')</script>";
    }
}

// SUBMIT TRANSACTION
if (isset($_POST['save_transaction'])) {
    $transaction_category = $_POST['category'];
    $transaction_amount = $_POST['amount'];
    $transaction_date = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
    $user_id = $_SESSION['user-id'];
    $card_id = $_POST['card_id'];
    $type = $_POST['type'];

    $allow_insert = true;

    if ($type == 'expense') {
        $budget_check = mysqli_query($connect, "SELECT amount FROM budgets WHERE user_id = $user_id AND title = '$transaction_category'");

        if (mysqli_num_rows($budget_check) > 0) {
            $budget_row = mysqli_fetch_assoc($budget_check);
            $limit_amount = $budget_row['amount'];

            $current_month = date('m', strtotime($transaction_date));
            $current_year = date('Y', strtotime($transaction_date));

            $sum_result = mysqli_query($connect, "SELECT sum(amount) as total FROM transactions WHERE user_id = $user_id AND card_id = $card_id AND type = 'expense' AND description = '$transaction_category' AND MONTH(date) = '$current_month' AND YEAR(date) = '$current_year'");

            $sum_row = mysqli_fetch_assoc($sum_result);
            $current_spent = $sum_row['total'] ?? 0;

            if (($current_spent + $transaction_amount) > $limit_amount) {
                $allow_insert = false;
                echo "<script>alert('you can't insert , the amount is bigger than the rest you have!')</script>";
            }
        }
    }

    if ($allow_insert) {
        mysqli_query($connect, "INSERT INTO transactions(user_id , card_id , description , amount , date, type) VALUES($user_id ,$card_id ,'$transaction_category' , $transaction_amount , '$transaction_date', '$type')");
    }
    ////////
}

// ADD ANOTHER CARD
if (isset($_POST['save_card'])) {
    $card_index = $_POST['create_card'];
    $card_number = $_POST['card_number'];
    $card_name = $_POST['card_name'];
    $card_date = $_POST['ex_date'];
    $card_cvc = $_POST['cvc'];
    $user_id = $_SESSION['user-id'];

    mysqli_query($connect, "INSERT INTO cards(  card_index,
                                                card_holder,
                                                card_number,
                                                ex_date,
                                                CVC,
                                                user_id) VALUES($card_index,
                                                                '$card_name',
                                                                '$card_number',
                                                                '$card_date',
                                                                '$card_cvc',
                                                                $user_id)");
}

// SEND MONEY
if (isset($_POST['perform_send_money'])) {
    $recipient_email = $_POST['recipient_email'];
    $amount_sent = $_POST['send_amount'];
    // $note_sent = $_POST['send_note'] ?? "";

    $get_receiver_id = mysqli_query($connect, "SELECT id FROM users WHERE email = '$recipient_email'");
    if (mysqli_num_rows($get_receiver_id) == 0) {
        echo "no one";
        exit();
    } else {
        $receiver_id = mysqli_fetch_assoc($get_receiver_id);

        $get_mainCard_id = mysqli_query($connect, "SELECT * FROM cards WHERE card_index = 1 AND user_id = {$receiver_id['id']}");
        $mainCard_id = mysqli_fetch_assoc($get_mainCard_id);

        $date = date('Y-m-d');

        $sender = mysqli_query($connect, "SELECT * FROM cards WHERE card_index = 1 and user_id = {$_SESSION['user-id']}");
        $sender_card = mysqli_fetch_assoc($sender);

        mysqli_query($connect, "INSERT INTO transactions(user_id , card_id , description, amount , date , type) VALUES({$receiver_id['id']} , {$mainCard_id['id']} , 'Recieved' , $amount_sent , '$date' , 'income')");

        mysqli_query($connect, "INSERT INTO transactions(user_id , card_id , description, amount , date , type) VALUES({$_SESSION['user-id']} , {$sender_card['id']} , 'Sent' , $amount_sent , '$date' , 'expense')");
    }
}

/// save recurrent
if (isset($_POST['save_recurring'])) {
    $rec_card_id = $_POST['rec_card_id'];
    $rec_type = $_POST['rec_type'];
    $rec_category = $_POST['rec_category'];
    $rec_day = $_POST['rec_day'];
    $rec_amount = $_POST['rec_amount'];
    $user_id = $_SESSION['user-id'];

    $query = "INSERT INTO recurring_transactions (user_id, card_id, description, amount, type, day_of_month, last_run_date) VALUES ($user_id, $rec_card_id, '$rec_category', $rec_amount, '$rec_type', $rec_day, NULL)";



// SAVE RECURRING TRANSACTION
if (isset($_POST['save_recurring'])) {
    $rec_card_id = $_POST['rec_card_id'];
    $rec_type = $_POST['rec_type'];
    $rec_category = $_POST['rec_category'];
    $rec_day = $_POST['rec_day'];
    $rec_amount = $_POST['rec_amount'];
    $user_id = $_SESSION['user-id'];

    // Insert into DB with last_run_date as NULL (It hasn't run yet)
    $query = "INSERT INTO recurring_transactions (user_id, card_id, description, amount, type, day_of_month, last_run_date) VALUES ($user_id, $rec_card_id, '$rec_category', $rec_amount, '$rec_type', $rec_day, NULL)";
    
    if(mysqli_query($connect, $query)){
        echo "<script>alert('Auto-payment scheduled successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($connect) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://kit.fontawesome.com/559afa4763.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <title>Smart Wallet 2</title>
</head>
<style type="text/tailwindcss">
    @theme {
        --color-clifford: #da373d;
      }

      @keyframes form-animation {
        from{
            opacity: 0;
            transform: translateY(-30px);
        }to{
            transform: translateY(0);
            opacity: 1;
        }
      }
    </style>

<body class="bg-gray-50 text-slate-800 font-sans antialiased">

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <span class="font-bold text-xl tracking-tight text-indigo-900">Smart Wallet</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-500 hidden sm:block">Welcome,
                        <span class="text-gray-900 font-bold">
                            <?php
                            $sqll = "SELECT * FROM users WHERE id = {$_SESSION['user-id']}";
                            $resultt = mysqli_query($connect, $sql);
                            $roww = mysqli_fetch_assoc($resultt);
                            echo $roww['name'] ?>
                        </span>
                    </span>

                    <button onclick="document.getElementById('budget-modal').classList.remove('hidden')" class="bg-white text-gray-600 border border-gray-300 hover:bg-gray-50 px-3 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 cursor-pointer shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                        </svg>
                        <span class="hidden sm:inline">Set Limit</span>
                    </button>

                    <button onclick="document.getElementById('recurring-modal').classList.remove('hidden')" class="bg-white text-purple-600 border border-purple-200 hover:bg-purple-50 px-3 py-2 rounded-lg text-sm font-bold transition-all flex items-center gap-2 cursor-pointer shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span class="hidden sm:inline">Recurring</span>
                    </button>

                    <button onclick="document.getElementById('send-money-modal').classList.remove('hidden')" class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2 transform hover:-translate-y-0.5 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                        <span class="hidden sm:inline">Send Money</span>
                    </button>

                    <a href="logout.php" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        <span>Logout</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Manage your cards and track your transactions.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

            <div class="col-span-1 lg:col-span-1">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Main Card</h2>

                <div onclick="openTransactionModal('<?php echo $first_row['id']; ?>')" class="relative h-56 w-full bg-gradient-to-br from-indigo-600 to-blue-800 rounded-2xl shadow-xl overflow-hidden text-white transition-transform hover:scale-[1.02] duration-300 cursor-pointer group">

                    <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-20">
                        <span class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-full text-xs font-bold border border-white/30">Click to Add Transaction</span>
                    </div>

                    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 rounded-full bg-white opacity-10 blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 rounded-full bg-white opacity-10 blur-2xl"></div>

                    <div class="p-6 flex flex-col justify-between h-full relative z-10">
                        <div class="flex justify-between items-start">
                            <div class="w-12 h-9 border border-yellow-400/50 bg-yellow-400/20 rounded flex items-center justify-center">
                                <div class="w-8 h-6 border border-yellow-400/60 rounded-sm grid grid-cols-2 gap-1"></div>
                            </div>
                            <span class="font-bold italic text-lg opacity-80">VISA</span>
                        </div>

                        <div class="mt-4">
                            <p class="text-xs text-indigo-200 mb-1">Current Balance</p>
                            <p class="text-3xl font-bold tracking-tight">$ <?php echo $income_sum['sum'] - $expense_sum['sum'] ?></p>
                        </div>

                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs text-indigo-200 uppercase">Card Holder</p>
                                <p class="font-medium tracking-wide"><?php echo $first_row['card_holder'] ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-indigo-200 uppercase text-right">Expires</p>
                                <p class="font-medium tracking-widest"><?php echo $first_row['ex_date'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $infos = mysqli_query($connect, "SELECT * FROM cards WHERE user_id = {$_SESSION['user-id']} AND card_index = 2");

            if (mysqli_num_rows($infos) > 0) {
                while ($row = mysqli_fetch_assoc($infos)) {

                    $current_card_id = $row['id'];

                    $income_query = mysqli_query($connect, "SELECT sum(amount) AS sum FROM transactions WHERE card_id = $current_card_id  AND type = 'income'");
                    $income_data = mysqli_fetch_assoc($income_query);
                    $income_total = $income_data['sum'] ?? 0;


                    $expense_query = mysqli_query($connect, "SELECT sum(amount) AS sum FROM transactions WHERE card_id = $current_card_id  AND type = 'expense'");
                    $expense_data = mysqli_fetch_assoc($expense_query);
                    $expense_total = $expense_data['sum'] ?? 0;

                    $final_balance = $income_total - $expense_total;

                    echo "
                <div class='col-span-1 lg:col-span-1'>
                    <h2 class='text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4'>Second Card</h2>
                    
                    <div onclick=\"openTransactionModal('" . $row['id'] . "')\" class='relative h-56 w-full bg-gradient-to-br from-indigo-600 to-blue-800 rounded-2xl shadow-xl overflow-hidden text-white transition-transform hover:scale-[1.02] duration-300 cursor-pointer group'>
                        
                         <div class='absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-20'>
                            <span class='bg-white/20 backdrop-blur-md px-4 py-2 rounded-full text-xs font-bold border border-white/30'>Click to Add Transaction</span>
                        </div>

                        <div class='absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 rounded-full bg-white opacity-10 blur-2xl'></div>
                        <div class='absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 rounded-full bg-white opacity-10 blur-2xl'></div>
    
                        <div class='p-6 flex flex-col justify-between h-full relative z-10'>
                            <div class='flex justify-between items-start'>
                                <div class='w-12 h-9 border border-yellow-400/50 bg-yellow-400/20 rounded flex items-center justify-center'>
                                    <div class='w-8 h-6 border border-yellow-400/60 rounded-sm grid grid-cols-2 gap-1'></div>
                                </div>
                                <span class='font-bold italic text-lg opacity-80'>VISA</span>
                            </div>
    
                            <div class='mt-4'>
                                <p class='text-xs text-indigo-200 mb-1'>Current Balance</p>
                                <p class='text-3xl font-bold tracking-tight'>$ " . $final_balance . "</p>
                            </div>
    
                            <div class='flex justify-between items-end'>
                                <div>
                                    <p class='text-xs text-indigo-200 uppercase'>Card Holder</p>
                                    <p class='font-medium tracking-wide'>" . $row['card_holder'] . "</p>
                                </div>
                                <div>
                                    <p class='text-xs text-indigo-200 uppercase text-right'>Expires</p>
                                    <p class='font-medium tracking-widest'>" . $row['ex_date'] . "</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    ";
                }
            }
            ?>
            <div class="col-span-1 lg:col-span-1 flex flex-col">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Add Card</h2>
                <button onclick="document.getElementById('add-card-modal').classList.remove('hidden')" class="h-56 w-full border-2 border-dashed border-gray-300 rounded-2xl flex flex-col items-center justify-center text-gray-400 hover:border-indigo-500 hover:text-indigo-500 hover:bg-indigo-50 transition-all duration-200 group bg-white cursor-pointer">
                    <div class="w-12 h-12 rounded-full bg-gray-100 group-hover:bg-indigo-100 flex items-center justify-center mb-3 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="font-medium text-sm">Add New Card</span>
                </button>
            </div>

            <div class="col-span-1 lg:col-span-1 flex flex-col">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Actions</h2>

                <button onclick="openTransactionModal('<?php echo $first_row['id']; ?>')" class="h-56 w-full bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-300 group flex flex-col items-center justify-center gap-4 cursor-pointer relative overflow-hidden">
                    <div class="absolute inset-0 bg-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center z-10 group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>

                    <div class="text-center z-10">
                        <span class="block font-bold text-lg text-gray-800 group-hover:text-indigo-700">Add Transaction</span>
                        <span class="text-sm text-gray-500">Income or Expense (Main Card)</span>
                    </div>
                </button>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Recent Transactions</h3>
                <button class="text-indigo-600 text-sm font-medium hover:text-indigo-800 hover:underline">View All</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="py-4 px-6 text-xs font-semibold uppercase text-gray-500 tracking-wider">Transaction</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase text-gray-500 tracking-wider">Category</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase text-gray-500 tracking-wider">Date</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase text-gray-500 tracking-wider">Status</th>
                            <th class="py-4 px-6 text-xs font-semibold uppercase text-gray-500 tracking-wider text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php
                        $user_id = $_SESSION['user-id'];
                        $the_results = mysqli_query($connect, "SELECT * FROM transactions WHERE user_id = $user_id ORDER BY id DESC LIMIT 5");
                        if (mysqli_num_rows($the_results) > 0) {
                            while ($row = mysqli_fetch_assoc($the_results)) {
                                echo "
                        <tr class='hover:bg-gray-50 transition-colors'>
                            <td class='py-4 px-6 flex items-center gap-3'>
                                <div class='w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center'>
                                    <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='currentColor' class='w-6 h-6'>
                                        <path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33' />
                                    </svg>
                                </div>
                                <div>
                                    <p class='text-sm font-semibold text-gray-900'>" . $row['description'] . "</p>
                                </div>
                            </td>
                            <td class='py-4 px-6 text-sm text-gray-600'>" . $row['type'] . "</td>
                            <td class='py-4 px-6 text-sm text-gray-600'>" . $row['date'] . "</td>
                            <td class='py-4 px-6'>
                                <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'>
                                    Completed
                                </span>
                            </td>
                            ";
                                if ($row['type'] == 'income') {
                                    echo "<td class='py-4 px-6 text-sm font-bold text-emerald-600 text-right'>+ $" . $row['amount'] . "</td>";
                                } else if ($row['type'] == 'expense') {
                                    echo "<td class='py-4 px-6 text-sm font-bold text-red-600 text-right'>- $" . $row['amount'] . "</td>";
                                }
                            }
                        }
                        ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 text-sm text-gray-400 italic text-center" colspan="5">
                                No more recent transactions to show
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <div id="budget-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
        <form action="" method="post" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-800">Set Category Limit</h3>
                <button type="button" onclick="document.getElementById('budget-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-8 py-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                    <div class="relative">
                        <select required name="budget_category" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 outline-none font-medium appearance-none cursor-pointer">
                            <option value="" disabled selected>Select category</option>
                            <option value="Food">Food & Dining</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Transport">Transport</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Bills">Bills & Utilities</option>
                            <option value="Health">Health</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Monthly Limit Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input required type="number" step="0.01" name="budget_amount" placeholder="e.g. 1500.00" class="w-full rounded-xl border-gray-200 bg-gray-50 pl-8 pr-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 outline-none font-bold text-lg">
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="document.getElementById('budget-modal').classList.add('hidden')" class="w-1/3 rounded-xl border border-gray-300 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" name="save_budget" class="w-2/3 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-700 transition-all">Set Limit</button>
            </div>
        </form>
    </div>

    <div id="recurring-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
        <form action="" method="post" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-800">Recurring Transaction</h3>
                <button type="button" onclick="document.getElementById('recurring-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-8 py-6 space-y-5">
                <p class="text-xs text-gray-500 font-medium">This transaction will be automatically added every month on the day you select.</p>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Card</label>
                    <div class="relative">
                        <select required name="rec_card_id" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 outline-none font-medium appearance-none cursor-pointer">
                            <?php
                            $cards_q = mysqli_query($connect, "SELECT * FROM cards WHERE user_id = {$_SESSION['user-id']}");
                            if (mysqli_num_rows($cards_q) > 0) {
                                while ($c_row = mysqli_fetch_assoc($cards_q)) {
                                    echo "<option value='{$c_row['id']}'>{$c_row['card_holder']} (.. " . substr($c_row['card_number'], -4) . ")</option>";
                                }
                            } else {
                                echo "<option disabled>No cards found</option>";
                            }
                            ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="rec_type" value="income" class="peer sr-only" checked>
                        <div class="rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all">Income</div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="rec_type" value="expense" class="peer sr-only">
                        <div class="rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition-all">Expense</div>
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                    <div class="relative">
                        <select required name="rec_category" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 outline-none font-medium appearance-none cursor-pointer">
                            <option value="Salary">Salary</option>
                            <option value="Rent">Rent</option>
                            <option value="Internet">Internet</option>
                            <option value="Bills">Bills & Utilities</option>
                            <option value="Food">Food & Dining</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Day (1-31)</label>
                        <input required type="number" min="1" max="31" name="rec_day" placeholder="e.g. 25" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 outline-none font-bold text-center">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                            <input required type="number" step="0.01" name="rec_amount" placeholder="0.00" class="w-full rounded-xl border-gray-200 bg-gray-50 pl-8 pr-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 outline-none font-bold">
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="document.getElementById('recurring-modal').classList.add('hidden')" class="w-1/3 rounded-xl border border-gray-300 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" name="save_recurring" class="w-2/3 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-700 transition-all">Enable Automation</button>
            </div>
        </form>
    </div>

    <div id="transaction-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
        <form action="" method="post" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

            <input type="hidden" name="card_id" id="transaction_card_id" value="">

            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-800">New Transaction</h3>
                <button type="button" onclick="document.getElementById('transaction-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-8 py-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="income" class="peer sr-only" checked>
                            <div class="rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                                Income
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="expense" class="peer sr-only">
                            <div class="rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50 peer-checked:border-rose-500 peer-checked:bg-rose-50 peer-checked:text-rose-700 transition-all flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6" />
                                </svg>
                                Expense
                            </div>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                    <div class="relative">
                        <select required name="category" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium appearance-none cursor-pointer">
                            <option value="" disabled selected>Select a category</option>
                            <option value="Salary">Salary</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Food">Food & Dining</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Transport">Transport</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Bills">Bills & Utilities</option>
                            <option value="Health">Health</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input required type="number" step="0.01" name="amount" placeholder="0.00" class="w-full rounded-xl border-gray-200 bg-gray-50 pl-8 pr-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-bold text-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Date</label>
                    <input type="date" name="date" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium">
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="document.getElementById('transaction-modal').classList.add('hidden')" class="w-1/3 rounded-xl border border-gray-300 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" name="save_transaction" class="w-2/3 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all transform active:scale-[0.98]">
                    Save Transaction
                </button>
            </div>
        </form>
    </div>

    <div id="add-card-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
        <form action="" method="post" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <input type="hidden" name="create_card" value="2">
            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-800">Add New Card</h3>
                <button type="button" onclick="document.getElementById('add-card-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-8 py-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Card Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <input required type="text" id="modal_card_number" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 pl-11 pr-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium tracking-widest">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Card Holder</label>
                    <input required type="text" name="card_name" placeholder="YOUR NAME"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium uppercase">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Expires</label>
                        <input required type="text" id="modal_ex_date" name="ex_date" placeholder="MM/YY" maxlength="5"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium text-center">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">CVC</label>
                        <div class="relative">
                            <input required type="password" name="cvc" placeholder="123" maxlength="3"
                                class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium text-center tracking-widest">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="document.getElementById('add-card-modal').classList.add('hidden')" class="w-1/3 rounded-xl border border-gray-300 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" name="save_card" class="w-2/3 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all transform active:scale-[0.98]">
                    Save Card
                </button>
            </div>
        </form>
    </div>

    <div id="send-money-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">
        <form action="" method="post" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

            <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-800">Send Money</h3>
                <button type="button" onclick="document.getElementById('send-money-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-8 py-6 space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Recipient Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input required type="email" name="recipient_email" placeholder="friend@example.com"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 pl-11 pr-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Amount to Send</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input required type="number" step="0.01" name="send_amount" placeholder="0.00"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 pl-8 pr-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-bold text-lg">
                    </div>
                </div>

            </div>

            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex gap-3">
                <button type="button" onclick="document.getElementById('send-money-modal').classList.add('hidden')" class="w-1/3 rounded-xl border border-gray-300 bg-white py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" name="perform_send_money" class="w-2/3 rounded-xl bg-indigo-600 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-700 hover:shadow-indigo-500/30 transition-all transform active:scale-[0.98] flex justify-center items-center gap-2">
                    <span>Send Now</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <script>
        // 1. OPEN TRANSACTION MODAL & SET CARD ID
        function openTransactionModal(cardId) {
            // Set the hidden input value
            document.getElementById('transaction_card_id').value = cardId;
            // Show the modal
            document.getElementById('transaction-modal').classList.remove('hidden');
            // Optional: Log to console to verify
            console.log("Opening transaction form for Card ID: " + cardId);
        }

        // 2. FORM FORMATTING
        const cardInput = document.getElementById('modal_card_number');
        if (cardInput) {
            cardInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
            });
        }
        const dateInput = document.getElementById('modal_ex_date');
        if (dateInput) {
            dateInput.addEventListener('input', function(e) {
                var input = e.target.value.replace(/\D/g, '');
                if (input.length > 2) {
                    e.target.value = input.substring(0, 2) + '/' + input.substring(2, 4);
                } else {
                    e.target.value = input;
                }
            });
        }
    </script>
</body>
<script src="script.js"></script>

</html>
<?php
mysqli_close($connect);
?>