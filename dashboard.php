<?php
include("connect.php");
?>
<?php
session_start();



if (!isset($_SESSION['user-id'])) {
    header('Location: index.php');
}

if (isset($_POST["income-submit"])) {
    try {
        $amount = mysqli_real_escape_string($connect, $_POST["income-amount"]);
        $description = mysqli_real_escape_string($connect, $_POST["income-description"]);
        $date = empty($_POST["income-date"]) ? date('y-m-d') : mysqli_real_escape_string($connect, $_POST["income-date"]);
        $sql_insert = "INSERT INTO income (user_id ,amount , description,  date) VALUES({$_SESSION['user-id']} ,{$amount}, '{$description}', '{$date}')";

        mysqli_query($connect, $sql_insert);
    } catch (mysqli_sql_exception $e) {
        echo $e->getMessage();
    }
}
if (isset($_POST["expense-submit"])) {
    try {
        $amount = mysqli_real_escape_string($connect, $_POST["expense-amount"]);
        $description = mysqli_real_escape_string($connect, $_POST["expense-description"]);
        $date = empty($_POST["expense-date"]) ? date('y-m-d') : mysqli_real_escape_string($connect, $_POST["expense-date"]);
        $sql_insert = "INSERT INTO expense (user_id ,amount , description,  date) VALUES({$_SESSION['user-id']},{$amount}, '{$description}', '{$date}')";

        mysqli_query($connect, $sql_insert);
    } catch (mysqli_sql_exception $e) {
        echo $e->getMessage();
    }
}
// income total amount
$income_sum = "SELECT sum(amount) AS total_income FROM income WHERE user_id = {$_SESSION['user-id']}";

$result_income = mysqli_query($connect, $income_sum);
$income_total = 0;
if ($result_income) {
    $row = mysqli_fetch_assoc($result_income);
    $income_total = $row['total_income'];
}
// expense total amount
$expense_sum = "SELECT sum(amount) AS total_expense FROM expense WHERE user_id = {$_SESSION['user-id']}";

$result_expense = mysqli_query($connect, $expense_sum);
$expense_total = 0;
if ($result_expense) {
    $row = mysqli_fetch_assoc($result_expense);
    $expense_total = $row['total_expense'];
}

// update infos of income
if (!empty($_POST['income-new-submit'])) {
    $amount = $_POST['income-new-amount'];
    $description = $_POST['income-new-description'];
    $date = $_POST['income-new-date'];
    $id = $_POST['id'];
    $sql = "UPDATE income SET amount = $amount WHERE id = $id AND user_id = {$_SESSION['user-id']}";

    mysqli_query($connect, $sql);
}
// update infos of expense
if (!empty($_POST['expense-new-submit'])) {
    $amount = $_POST['expense-new-amount'];
    $description = $_POST['expense-new-description'];
    $date = $_POST['expense-new-date'];
    $id = $_POST['id'];
    $sql = "UPDATE expense SET amount = $amount WHERE id = $id AND user_id = {$_SESSION['user-id']}";

    mysqli_query($connect, $sql);
}

// delete infos of income
if (!empty($_POST['income-delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM income WHERE id = $id AND user_id = {$_SESSION['user-id']}";

    mysqli_query($connect, $sql);
}
// delete infos of expense
if (!empty($_POST['expense-delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM expense WHERE id = $id AND user_id = {$_SESSION['user-id']}";

    mysqli_query($connect, $sql);
}

// get the name of the user
$sql = "SELECT name FROM users WHERE id = {$_SESSION['user-id']}";
$result = mysqli_query($connect, $sql);
$row = mysqli_fetch_assoc($result);
$the_name = $row['name'];
// echo $the_name;

// card infos
$infos = mysqli_query($connect, "SELECT * FROM cards WHERE user_id = {$_SESSION['user-id']}");
$row = mysqli_fetch_assoc($infos);
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
                    <span class="text-sm font-medium text-gray-500">Welcome, <span class="text-gray-900 font-bold">Imad</span></span>
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
                <div class="relative h-56 w-full bg-gradient-to-br from-indigo-600 to-blue-800 rounded-2xl shadow-xl overflow-hidden text-white transition-transform hover:scale-[1.02] duration-300">
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
                            <p class="text-3xl font-bold tracking-tight">$ <?php echo $row['balance'] ?></p>
                        </div>

                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs text-indigo-200 uppercase">Card Holder</p>
                                <p class="font-medium tracking-wide"><?php echo $row['card_holder'] ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-indigo-200 uppercase text-right">Expires</p>
                                <p class="font-medium tracking-widest"><?php echo $row['ex_date'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-1 lg:col-span-1 flex flex-col">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Add Card</h2>
                <button class="h-56 w-full border-2 border-dashed border-gray-300 rounded-2xl flex flex-col items-center justify-center text-gray-400 hover:border-indigo-500 hover:text-indigo-500 hover:bg-indigo-50 transition-all duration-200 group bg-white">
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

                <button onclick="document.getElementById('transaction-modal').classList.remove('hidden')" class="h-56 w-full bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-300 transition-all duration-300 group flex flex-col items-center justify-center gap-4 cursor-pointer relative overflow-hidden">
                    <div class="absolute inset-0 bg-indigo-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center z-10 group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>

                    <div class="text-center z-10">
                        <span class="block font-bold text-lg text-gray-800 group-hover:text-indigo-700">Add Transaction</span>
                        <span class="text-sm text-gray-500">Income or Expense</span>
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Upwork Revenue</p>
                                    <p class="text-xs text-gray-500">Freelancing</p>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-600">Income</td>
                            <td class="py-4 px-6 text-sm text-gray-600">Oct 24, 2025</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Completed
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm font-bold text-emerald-600 text-right">+ $850.00</td>
                        </tr>
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


    <div id="transaction-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity duration-300">

        <form action="" method="post" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

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
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="category" value="income" class="peer sr-only" checked>
                            <div class="rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-600 hover:bg-gray-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition-all flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                                </svg>
                                Income
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="category" value="expense" class="peer sr-only">
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
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Title</label>
                    <input required type="text" name="description" placeholder="e.g. Salary, Grocery Shopping..."
                        class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input required type="number" step="0.01" name="amount" placeholder="0.00"
                            class="w-full rounded-xl border-gray-200 bg-gray-50 pl-8 pr-4 py-3 text-gray-800 placeholder-gray-400 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-bold text-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Date</label>
                    <input type="date" name="date"
                        class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-3 text-gray-800 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all outline-none font-medium">
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
</body>
<script src="script.js"></script>

</html>
<?php
mysqli_close($connect);
?>