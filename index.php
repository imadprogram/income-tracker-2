<?php
include('connect.php')
?>
<?php
session_start();
// Login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $email = $_POST['already-email'];
        $password = $_POST['already-pass'];
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if (password_verify($password, $row['password'])) {
                $_SESSION['user-id'] = $row['id'];
                header('Location: dashboard.php');
            } else {
                echo 'pass is wrong';
            }
        } else {
            echo 'there is no email like that';
        }
    } else if (isset($_POST['signup'])) {
        $name = $_POST['user-name'];
        $email = $_POST['user-email'];
        $password = $_POST['user-pass'];
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $search = mysqli_query($connect, "SELECT * FROM users WHERE email = '$email'");

        if (mysqli_num_rows($search) == 1) {
            echo 'the email is already in use';
        } else {
            $sql = "INSERT INTO users(name , email, password) VALUES('$name', '$email','$hash')";
            mysqli_query($connect, $sql);

            // gets you the current id of the operation u did , so the last id has inserted
            $new_id = mysqli_insert_id($connect);

            $_SESSION['user-id'] = $new_id;

            header('Location: dashboard.php');

            echo 'you are now registered';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <title>Document</title>
</head>

<body>
    <section class="w-full h-svh flex justify-center items-center bg-[url('img/money.jpg')] bg-cover">
        <div class="forms-wrapper [font-family:'Poppins']">

            <!-- Login form -->

            <form id="login-form" method="post" action="index.php" class="flex flex-col gap-10 shadow-xl w-fit px-10 py-7 rounded-lg items-center bg-white ">
                <h1 class="text-green-600 text-3xl">LOGIN</h1>
                <div class="flex flex-col">
                    <label for="">Email:</label>
                    <input required name="already-email" type="email" placeholder="someone@domain" class="pl-4 w-72 h-10 rounded-lg bg-gray-300">
                </div>
                <div class="flex flex-col">
                    <label for="">Password:</label>
                    <input required name="already-pass" type="password" placeholder="your password" class="pl-4 w-72 h-10 rounded-lg bg-gray-300">
                </div>
                <input name="login" type="submit" value="Login" class="w-72 h-10 rounded-lg bg-green-600 text-white cursor-pointer">
                <p>don't have an account? <span class="text-blue-500 cursor-pointer">Register</span></p>
            </form>

            <!-- Register form -->

            <form id="register-form" method="post" action="index.php" class="flex flex-col gap-10 shadow-xl w-fit px-10 py-7 rounded-lg items-center bg-white hidden">
                <h1 class="text-green-600 text-3xl">SIGN UP</h1>
                <div class="flex flex-col">
                    <label for="">Name:</label>
                    <input required name="user-name" type="text" placeholder="john doe" class="pl-4 w-72 h-10 rounded-lg bg-gray-300">
                </div>
                <div class="flex flex-col">
                    <label for="">Email:</label>
                    <input required name="user-email" type="email" placeholder="someone@domain" class="pl-4 w-72 h-10 rounded-lg bg-gray-300">
                </div>
                <div class="flex flex-col">
                    <label for="">Password:</label>
                    <input required name="user-pass" type="password" placeholder="your password" class="pl-4 w-72 h-10 rounded-lg bg-gray-300">
                </div>
                <input name="signup" type="submit" value="Sign up" class="w-72 h-10 rounded-lg bg-green-600 text-white cursor-pointer">
                <p>Aleady have an account? <span class="text-blue-500 cursor-pointer">Login</span></p>
            </form>
        </div>
    </section>
</body>
<script src="login.js"></script>

</html>