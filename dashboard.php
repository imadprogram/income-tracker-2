<?php
include("connect.php");
?>
<?php
    session_start();

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
$result = mysqli_query($connect,$sql);
$row = mysqli_fetch_assoc($result);
$the_name = $row['name'];
// echo $the_name;
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

<body>

    <header class="bg-gray-900 text-3xl font-bold pl-9 py-3 text-white flex justify-between">
        Welcome <?php echo $the_name ?>
        <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded">Logout</a>
    </header>
    <section class="pt-3 bg-black pb-10 flex flex-col gap-8 px-12">
        <div class="flex flex-wrap gap-5 justify-between">
            <h1 class="text-4xl font-bold text-white">Dashboard<br><span class="text-sm font-sans text-gray-500">manage you spends in one page.</span></h1>
            <div>
                <button id="income-btn" class="cursor-pointer px-6 py-2 rounded-full text-white bg-[linear-gradient(to_top,#52c234,#061700)]">Add income</button>
                <button id="expense-btn" class="cursor-pointer px-6 py-2 rounded-full border-2 text-white border-green-700">Add expense</button>
            </div>
        </div>
        <!-- cards -->
        <div class="flex flex-wrap gap-5 md:gap-30 md:px-24 pt-4">
            <div
                class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 grow">
                <div
                    class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p
                        class="mb-2 text-md font-medium text-gray-600 dark:text-gray-400">
                        income balance
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-700 dark:text-gray-200">
                        $ <?php echo $income_total + 0 ?>
                    </p>
                </div>
            </div>
            <!-- card -->
            <div
                class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 grow">
                <div
                    class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p
                        class="mb-2 text-md font-medium text-gray-600 dark:text-gray-400">
                        expense balance
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-700 dark:text-gray-200">
                        $ <?php echo $expense_total + 0 ?>
                    </p>
                </div>
            </div>
            <!-- card -->
            <div
                class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800 grow">
                <div
                    class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <p
                        class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                        sold balance
                    </p>
                    <p
                        class="text-2xl font-bold text-gray-700 dark:text-gray-200">
                        $ <?php echo $income_total - $expense_total + 0 ?>
                    </p>
                </div>
            </div>
        </div>
        <!-- lists section -->
        <div class="flex flex-wrap md:flex-nowrap gap-30">
            <!-- incomes Table -->
            <div id="incomes" class="w-full overflow-y-scroll [scrollbar-width:none] rounded-lg shadow-xs h-80 bg-gray-700 text-white relative">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody
                            class=" divide-y divide-gray-700 bg-gray-800">
                            <?php
                            $incomes = "SELECT * FROM income WHERE MONTH(date) = MONTH(CURRENT_DATE) AND YEAR(date) = YEAR(CURRENT_DATE) AND user_id = {$_SESSION['user-id']}";
                            $the_result = mysqli_query($connect, $incomes);
                            if (mysqli_num_rows($the_result) > 0) {
                                while ($row = mysqli_fetch_assoc($the_result)) {
                                    echo "
                                        <tr data-id=" . $row['id'] . " class='text-white element'>
                                            <td class='px-4 py-3 text-sm'>
                                                $ <span class='text-green-500 font-semibold'>" . $row['amount'] . "</span>
                                            </td>
                                            <td class='px-4 py-3 text-sm flex justify-between'> 
                                                <p>" . $row['date'] . "</p>
                                                <i class='fa-solid fa-pen edit cursor-pointer'></i>
                                                <i class='fa-solid fa-trash text-red-500 cursor-pointer bin'></i>
                                            </td>
                                        </tr>
                                    ";
                                }
                            } else {
                                echo "<h2 class='absolute top-1/2 left-1/2 translate-x-[-50%] translate-y-[-50%] text-gray-300 text-lg'>there is nothing to show</h2>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- expenses Table -->
            <div id="expenses" class="w-full overflow-y-scroll [scrollbar-width:none] rounded-lg shadow-xs h-80 bg-gray-700 text-white relative">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                            <tr
                                class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                <th class="px-4 py-3">Amount</th>
                                <th class="px-4 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody
                            class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                            <?php
                            $expenses = "SELECT * FROM expense WHERE MONTH(date) = MONTH(CURRENT_DATE) AND YEAR(date) = YEAR(CURRENT_DATE) AND user_id = {$_SESSION['user-id']}";
                            $the_result = mysqli_query($connect, $expenses);
                            if (mysqli_num_rows($the_result) > 0) {
                                while ($row = mysqli_fetch_assoc($the_result)) {
                                    echo "
                                        <tr data-id=" . $row['id'] . " class='text-white element'>
                                            <td class='px-4 py-3 text-sm'>
                                                $ <span class='text-green-500 font-semibold'>" . $row['amount'] . "</span>
                                            </td>
                                            <td class='px-4 py-3 text-sm flex justify-between dates'> 
                                                " . $row['date'] . "
                                                <i class='fa-solid fa-pen edit cursor-pointer'></i>
                                                <i class='fa-solid fa-trash text-red-500 cursor-pointer bin'></i>
                                            </td>
                                            </tr>
                                    ";
                                }
                            } else {
                                echo "<h2 class='absolute top-1/2 left-1/2 translate-x-[-50%] translate-y-[-50%] text-gray-300 text-lg'>there is nothing to show</h2>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <!-- chart js section -->
    <section class="bg-black">
        <h1 class="text-center font-bold text-3xl text-white py-16">Statistics</h1>
        <div class="h-80 md:px-16">
            <canvas id="myChart"></canvas>
        </div>
    </section>
    <!-- income form -->
    <div class="income-form-bg  hidden  bg-[rgba(0,0,0,.1)] fixed backdrop-blur-sm w-full h-[100vh] flex justify-center items-center top-0">
        <form action="<?php $_SERVER['PHP_SELF']?>" method="post" class="top-1/2 left-1/2  bg-white shadow-xl rounded-lg px-6 py-4 flex flex-col gap-1">
            <label>amount:</label>
            <input required type="text" name="income-amount" placeholder="100.00" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
            <label>description:</label>
            <input required type="text" name="income-description" placeholder="from ?" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
            <label>date:</label>
            <input type="date" name="income-date" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
            <input type="submit" name="income-submit" class="bg-green-400 rounded-lg w-80 py-2 text-white">
        </form>
    </div>
    <!-- expense form -->
    <div class="expense-form-bg  hidden  bg-[rgba(0,0,0,.1)] fixed backdrop-blur-sm w-full h-[100vh] flex justify-center items-center top-0">
        <form action="<?php $_SERVER['PHP_SELF']?>" method="post" class="top-1/2 left-1/2  bg-white shadow-xl rounded-lg px-6 py-4 flex flex-col gap-1">
            <label>amount:</label>
            <input required type="text" name="expense-amount" placeholder="100.00" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
            <label>description:</label>
            <input required type="text" name="expense-description" placeholder="from ?" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
            <label>date:</label>
            <input type="date" name="expense-date" class="bg-gray-200 rounded-lg w-80 py-2 pl-3"><br><br>
            <input type="submit" name="expense-submit" class="bg-green-400 rounded-lg w-80 py-2 text-white">
        </form>
    </div>

    <?php
    // 1. Re-run queries to fetch all records for the current month
    // You need ID, amount, and date for the chart to work correctly.
    $income_chart_sql = "SELECT id, amount, date FROM income WHERE MONTH(date) = MONTH(CURRENT_DATE) AND YEAR(date) = YEAR(CURRENT_DATE) AND user_id = {$_SESSION['user-id']} ORDER BY date ASC";
    $expense_chart_sql = "SELECT id, amount, date FROM expense WHERE MONTH(date) = MONTH(CURRENT_DATE) AND YEAR(date) = YEAR(CURRENT_DATE) AND user_id = {$_SESSION['user-id']} ORDER BY date ASC";

    $income_chart_result = mysqli_query($connect, $income_chart_sql);
    $expense_chart_result = mysqli_query($connect, $expense_chart_sql);

    $income_chart_data = [];
    while ($row = mysqli_fetch_assoc($income_chart_result)) {
        // Collect data, ensuring 'amount' is treated as a number in JS
        $income_chart_data[] = ['date' => $row['date'], 'amount' => (float)$row['amount']];
    }

    $expense_chart_data = [];
    while ($row = mysqli_fetch_assoc($expense_chart_result)) {
        $expense_chart_data[] = ['date' => $row['date'], 'amount' => (float)$row['amount']];
    }

    // 2. Encode PHP arrays to JSON strings
    $json_incomes = json_encode($income_chart_data);
    $json_expenses = json_encode($expense_chart_data);
    ?>
    <script>
        const incomeDataFromPHP = <?php echo $json_incomes; ?>;
        const expenseDataFromPHP = <?php echo $json_expenses; ?>;
    </script>
</body>
<script src="script.js"></script>

</html>
<?php
mysqli_close($connect);
?>