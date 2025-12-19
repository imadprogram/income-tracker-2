<?php
// require 'connect.php';
include('connect.php');

session_start();

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
    header('Location: dashboard.php');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <title>add your Card</title>
</head>

<body class="bg-gray-100 h-screen flex items-center justify-center font-['Space_Mono']">

    <form action="mainCard.php" method="POST" class="flex flex-col gap-6 w-full max-w-md p-4">

        <input type="hidden" name="create_card" value="1">

        <h2 class="text-2xl font-bold text-gray-800 text-center font-sans">Set Up Your Card</h2>

        <div class="w-full h-56 bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-2xl p-6 relative overflow-hidden text-white flex flex-col justify-between">

            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 rounded-full bg-white opacity-5"></div>
            <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 rounded-full bg-white opacity-5"></div>

            <div class="flex justify-between items-center z-10">
                <div class="w-12 h-9 border border-gray-500 rounded flex items-center justify-center bg-yellow-600/20 overflow-hidden relative">
                    <div class="w-full h-[1px] bg-gray-500 absolute top-2"></div>
                    <div class="w-full h-[1px] bg-gray-500 absolute bottom-2"></div>
                    <div class="h-full w-[1px] bg-gray-500 absolute left-4"></div>
                    <div class="h-full w-[1px] bg-gray-500 absolute right-4"></div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                </svg>
            </div>

            <div class="z-10 mt-4">
                <label class="text-[10px] uppercase text-gray-400 block mb-1">Card Number</label>
                <input
                    type="text"
                    name="card_number"
                    id="card_number"
                    placeholder="0000 0000 0000 0000"
                    maxlength="19"
                    class="w-full bg-transparent border-none text-2xl placeholder-gray-600 text-white focus:ring-0 focus:outline-none tracking-widest"
                    required>
            </div>

            <div class="flex justify-between items-end z-10 mt-2">

                <div class="w-1/2">
                    <label class="text-[10px] uppercase text-gray-400 block">Card Holder</label>
                    <input
                        type="text"
                        name="card_name"
                        placeholder="YOUR NAME"
                        class="w-full bg-transparent border-none text-sm placeholder-gray-600 text-white focus:ring-0 focus:outline-none uppercase"
                        required>
                </div>

                <div class="w-1/4">
                    <label class="text-[10px] uppercase text-gray-400 block">Expires</label>
                    <input
                        type="text"
                        name="ex_date"
                        id="ex_date"
                        placeholder="MM/YY"
                        maxlength="5"
                        class="w-full bg-transparent border-none text-sm placeholder-gray-600 text-white focus:ring-0 focus:outline-none text-center"
                        required>
                </div>

                <div class="w-1/6">
                    <label class="text-[10px] uppercase text-gray-400 block">CVC</label>
                    <input
                        type="password"
                        name="cvc"
                        placeholder="123"
                        maxlength="3"
                        class="w-full bg-transparent border-none text-sm placeholder-gray-600 text-white focus:ring-0 focus:outline-none text-right"
                        required>
                </div>

            </div>
        </div>

        <button type="submit" name="save_card" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg transition-all font-sans">
            Save Card
        </button>

    </form>

    <script>
        // Auto-space for Card Number
        document.getElementById('card_number').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
        });

        // Auto-slash for Date
        document.getElementById('ex_date').addEventListener('input', function(e) {
            var input = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (input.length > 2) {
                e.target.value = input.substring(0, 2) + '/' + input.substring(2, 4);
            } else {
                e.target.value = input;
            }
        });
    </script>
</body>

</html>